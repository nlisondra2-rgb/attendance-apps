<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Fine;
use App\Models\FineExcuse;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class FineController extends Controller
{
    // =========================================================================
    // STUDENT ENDPOINTS
    // =========================================================================

    /**
     * GET /api/students/{studentId}/fines — List fines for authenticated student
     */
    public function studentFines(Request $request, string $studentId)
    {
        $student = Student::where('student_id', $studentId)->firstOrFail();

        $fines = Fine::with(['event', 'excuses'])
            ->where('student_id', $student->id)
            ->orderByDesc('issue_date')
            ->orderByDesc('id')
            ->get();

        // Calculate total outstanding balance (only payable fines: unpaid & approved review)
        $outstandingBalance = $fines
            ->where('payment_status', 'unpaid')
            ->where('review_status', 'approved')
            ->sum('balance');

        $formatted = $fines->map(function (Fine $f) {
            return [
                'id'              => $f->id,
                'reference_no'    => $f->reference_no,
                'amount'          => (float) $f->amount,
                'balance'         => (float) $f->balance,
                'reason'          => $f->reason,
                'issue_date'      => Carbon::parse($f->issue_date)->toDateString(),
                'status'          => $f->status,
                'review_status'   => $f->review_status,
                'payment_status'  => $f->payment_status,
                'paid_at'         => optional($f->paid_at)->toIso8601String(),
                'waived_at'       => optional($f->waived_at)->toIso8601String(),
                'waiver_reason'   => $f->waiver_reason,
                'event' => $f->event ? [
                    'id'    => $f->event->id,
                    'name'  => $f->event->name,
                    'date'  => $f->event->date ? Carbon::parse($f->event->date)->toDateString() : null,
                    'venue' => $f->event->venue,
                ] : null,
                'excuses'         => $f->excuses,
                'has_pending_dispute' => $f->excuses->where('status', 'pending')->count() > 0,
            ];
        });

        return response()->json([
            'student_id'          => $student->student_id,
            'outstanding_balance' => (float) $outstandingBalance,
            'fines'               => $formatted,
        ]);
    }

    /**
     * POST /api/students/{studentId}/fines/{fine}/dispute — Submit excuse/dispute
     */
    public function submitDispute(Request $request, string $studentId, Fine $fine)
    {
        $student = Student::where('student_id', $studentId)->firstOrFail();

        // Security check: student can only dispute their own fines
        if ($fine->student_id !== $student->id) {
            return response()->json(['message' => 'Unauthorized action.'], 403);
        }

        $validated = $request->validate([
            'reason'        => ['required', 'string', 'min:5', 'max:1000'],
            'document_path' => ['nullable', 'string', 'max:255'],
        ]);

        $excuse = DB::transaction(function () use ($fine, $student, $validated) {
            $created = FineExcuse::create([
                'fine_id'       => $fine->id,
                'student_id'    => $student->id,
                'event_id'      => $fine->event_id,
                'reason'        => $validated['reason'],
                'document_path' => $validated['document_path'] ?? null,
                'status'        => 'pending',
            ]);

            $fine->update(['status' => 'disputed']);

            $fine->recordAudit(
                'dispute_submitted',
                'student',
                $student->id,
                $student->name,
                ['reason' => $validated['reason']]
            );

            return $created;
        });

        return response()->json([
            'message' => 'Excuse / dispute successfully submitted for administrative review.',
            'excuse'  => $excuse,
        ], 201);
    }

    // =========================================================================
    // ADMIN ENDPOINTS
    // =========================================================================

    /**
     * GET /api/fines — Admin fine list with search & filters
     */
    public function index(Request $request)
    {
        $query = Fine::with(['student', 'event', 'excuses', 'auditLogs']);

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->string('payment_status'));
        }

        if ($request->filled('review_status')) {
            $query->where('review_status', $request->string('review_status'));
        }

        if ($request->filled('event_id')) {
            $query->where('event_id', $request->integer('event_id'));
        }

        if ($request->filled('search')) {
            $search = '%' . $request->string('search') . '%';
            $query->where(function ($q) use ($search) {
                $q->where('reference_no', 'like', $search)
                  ->orWhereHas('student', function ($sq) use ($search) {
                      $sq->where('name', 'like', $search)
                        ->orWhere('student_id', 'like', $search);
                  })
                  ->orWhereHas('event', function ($eq) use ($search) {
                      $eq->where('name', 'like', $search);
                  });
            });
        }

        $fines = $query->orderByDesc('id')->get();

        $totalFinesIssued = $fines->sum('amount');
        $totalPaid        = $fines->where('payment_status', 'paid')->sum('amount');
        $totalOutstanding = $fines->where('payment_status', 'unpaid')->where('review_status', 'approved')->sum('balance');
        $totalWaived      = $fines->where('payment_status', 'waived')->sum('amount');

        return response()->json([
            'summary' => [
                'total_fines_issued' => (float) $totalFinesIssued,
                'total_paid'         => (float) $totalPaid,
                'total_outstanding'  => (float) $totalOutstanding,
                'total_waived'       => (float) $totalWaived,
                'count'              => $fines->count(),
            ],
            'fines' => $fines,
        ]);
    }

    /**
     * GET /api/fines/{fine} — Admin single fine details
     */
    public function show(Fine $fine)
    {
        $fine->load(['student', 'event', 'attendance', 'excuses', 'auditLogs']);
        return $fine;
    }

    /**
     * PATCH /api/fines/{fine}/record-payment — Admin records verified payment
     */
    public function recordPayment(Request $request, Fine $fine)
    {
        $validated = $request->validate([
            'amount'          => ['nullable', 'numeric', 'min:0.01'],
            'admin_name'      => ['nullable', 'string', 'max:100'],
            'admin_id'        => ['nullable', 'integer'],
            'notes'           => ['nullable', 'string', 'max:500'],
        ]);

        if ($fine->payment_status === 'paid') {
            return response()->json(['message' => 'This fine is already paid in full.'], 422);
        }

        if ($fine->payment_status === 'waived') {
            return response()->json(['message' => 'This fine has been waived and cannot accept payment.'], 422);
        }

        $payAmount = isset($validated['amount']) ? (float) $validated['amount'] : (float) $fine->balance;
        $newBalance = max(0, (float) $fine->balance - $payAmount);
        $newPaymentStatus = $newBalance <= 0 ? 'paid' : 'unpaid';
        $newStatus = $newBalance <= 0 ? 'paid' : $fine->status;

        DB::transaction(function () use ($fine, $newBalance, $newPaymentStatus, $newStatus, $payAmount, $validated) {
            $fine->update([
                'balance'        => $newBalance,
                'payment_status' => $newPaymentStatus,
                'status'         => $newStatus,
                'paid_at'        => $newPaymentStatus === 'paid' ? now() : null,
            ]);

            $fine->recordAudit(
                'payment_recorded',
                'admin',
                $validated['admin_id'] ?? null,
                $validated['admin_name'] ?? 'Administrator',
                [
                    'paid_amount'   => $payAmount,
                    'remaining_bal' => $newBalance,
                    'notes'         => $validated['notes'] ?? null,
                ]
            );
        });

        $fine->load(['student', 'event', 'auditLogs']);

        return response()->json([
            'message' => 'Payment successfully recorded.',
            'fine'    => $fine,
        ]);
    }

    /**
     * PATCH /api/fines/{fine}/waive — Admin issues authorized waiver
     */
    public function waive(Request $request, Fine $fine)
    {
        $validated = $request->validate([
            'waiver_reason' => ['required', 'string', 'min:5', 'max:500'],
            'admin_name'    => ['nullable', 'string', 'max:100'],
            'admin_id'      => ['nullable', 'integer'],
        ]);

        if ($fine->payment_status === 'waived') {
            return response()->json(['message' => 'This fine is already waived.'], 422);
        }

        DB::transaction(function () use ($fine, $validated) {
            $fine->update([
                'status'         => 'waived',
                'payment_status' => 'waived',
                'balance'        => 0.00,
                'waived_at'      => now(),
                'waiver_reason'  => $validated['waiver_reason'],
            ]);

            $fine->recordAudit(
                'waived',
                'admin',
                $validated['admin_id'] ?? null,
                $validated['admin_name'] ?? 'Administrator',
                ['reason' => $validated['waiver_reason']]
            );
        });

        $fine->load(['student', 'event', 'auditLogs']);

        return response()->json([
            'message' => 'Fine successfully waived.',
            'fine'    => $fine,
        ]);
    }

    /**
     * PATCH /api/fines/{fine}/review — Admin review decision
     */
    public function review(Request $request, Fine $fine)
    {
        $validated = $request->validate([
            'review_status' => ['required', 'string', 'in:approved,rejected'],
            'admin_name'    => ['nullable', 'string', 'max:100'],
            'admin_id'      => ['nullable', 'integer'],
            'notes'         => ['nullable', 'string', 'max:500'],
        ]);

        $newReviewStatus = $validated['review_status'];
        $newStatus = $newReviewStatus === 'approved' ? 'unpaid' : 'waived';

        DB::transaction(function () use ($fine, $newReviewStatus, $newStatus, $validated) {
            $fine->update([
                'review_status' => $newReviewStatus,
                'status'        => $newStatus,
                'reviewed_at'   => now(),
                'reviewed_by'   => $validated['admin_id'] ?? null,
            ]);

            $fine->recordAudit(
                'review_status_changed',
                'admin',
                $validated['admin_id'] ?? null,
                $validated['admin_name'] ?? 'Administrator',
                ['decision' => $newReviewStatus, 'notes' => $validated['notes'] ?? null]
            );
        });

        return response()->json([
            'message' => "Fine review decision recorded as {$newReviewStatus}.",
            'fine'    => $fine,
        ]);
    }

    // =========================================================================
    // EXCUSE REVIEW ENDPOINTS
    // =========================================================================

    /**
     * GET /api/fine-excuses — Admin list of submitted excuses
     */
    public function listExcuses(Request $request)
    {
        $query = FineExcuse::with(['student', 'event', 'fine']);

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        return $query->orderByDesc('id')->get();
    }

    /**
     * PATCH /api/fine-excuses/{excuse}/review — Admin approves or rejects excuse
     */
    public function reviewExcuse(Request $request, FineExcuse $excuse)
    {
        $validated = $request->validate([
            'status'         => ['required', 'string', 'in:approved,rejected'],
            'reviewer_notes' => ['nullable', 'string', 'max:500'],
            'admin_name'     => ['nullable', 'string', 'max:100'],
            'admin_id'       => ['nullable', 'integer'],
        ]);

        $status = $validated['status'];

        DB::transaction(function () use ($excuse, $status, $validated) {
            $excuse->update([
                'status'         => $status,
                'reviewer_notes' => $validated['reviewer_notes'] ?? null,
                'reviewed_at'    => now(),
                'reviewed_by'    => $validated['admin_id'] ?? null,
            ]);

            // If excuse approved: waive associated fine (if any) and mark attendance as excused
            if ($status === 'approved') {
                if ($excuse->fine) {
                    $excuse->fine->update([
                        'status'         => 'waived',
                        'payment_status' => 'waived',
                        'balance'        => 0.00,
                        'waived_at'      => now(),
                        'waiver_reason'  => 'Excuse approved by admin: ' . ($validated['reviewer_notes'] ?? 'Valid excuse accepted'),
                    ]);

                    $excuse->fine->recordAudit(
                        'waived',
                        'admin',
                        $validated['admin_id'] ?? null,
                        $validated['admin_name'] ?? 'Administrator',
                        ['reason' => 'Excuse approved']
                    );
                }

                // Also update attendance status to excused
                $attendance = \App\Models\Attendance::where('student_id', $excuse->student_id)
                    ->where('event_id', $excuse->event_id)
                    ->first();

                if ($attendance) {
                    $attendance->update([
                        'status'        => 'excused',
                        'excuse_reason' => $excuse->reason,
                    ]);
                }
            } else {
                // If rejected, restore fine status to unpaid if it was disputed
                if ($excuse->fine && $excuse->fine->payment_status === 'unpaid') {
                    $excuse->fine->update(['status' => 'unpaid']);
                    $excuse->fine->recordAudit(
                        'dispute_resolved',
                        'admin',
                        $validated['admin_id'] ?? null,
                        $validated['admin_name'] ?? 'Administrator',
                        ['decision' => 'Excuse rejected']
                    );
                }
            }
        });

        $excuse->load(['student', 'event', 'fine']);

        return response()->json([
            'message' => "Excuse marked as {$status}.",
            'excuse'  => $excuse,
        ]);
    }

    /**
     * GET /api/admin/student-fines-summary — Get overall fines per student
     */
    public function studentFinesSummary(Request $request)
    {
        $query = Student::whereHas('fines');

        if ($request->filled('search')) {
            $search = '%' . $request->string('search') . '%';
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', $search)
                  ->orWhere('student_id', 'like', $search)
                  ->orWhere('course', 'like', $search);
            });
        }

        $students = $query->with('fines')->get();

        $data = $students->map(function (Student $student) {
            $unpaidFines = $student->fines->where('payment_status', '!=', 'paid');
            $paidFines = $student->fines->where('payment_status', 'paid');
            $overallUnpaidBalance = $unpaidFines->sum('balance');
            $overallTotalAmount = $student->fines->sum('amount');
            $status = $overallUnpaidBalance <= 0 ? 'paid' : 'unpaid';

            return [
                'id'                 => $student->id,
                'student_id'         => $student->student_id,
                'name'               => $student->name,
                'course'             => $student->course,
                'year_level'         => $student->year_level,
                'email'              => $student->email,
                'fines_count'        => $student->fines->count(),
                'unpaid_fines_count' => $unpaidFines->count(),
                'overall_fines'      => (float) $overallUnpaidBalance,
                'total_fines_amount' => (float) $overallTotalAmount,
                'status'             => $status,
            ];
        });

        if ($request->filled('status')) {
            $filterStatus = $request->string('status');
            $data = $data->filter(fn ($item) => $item['status'] === $filterStatus)->values();
        }

        return response()->json([
            'students' => $data,
            'summary'  => [
                'total_students_with_fines' => $students->count(),
                'students_with_unpaid'      => $data->where('status', 'unpaid')->count(),
                'total_unpaid_amount'       => (float) $data->sum('overall_fines'),
            ],
        ]);
    }

    /**
     * POST /api/admin/students/{studentId}/mark-fines-paid — Admin marks student's overall fines as paid
     */
    public function markStudentFinesPaid(Request $request, string $studentId)
    {
        $student = Student::where('student_id', $studentId)
            ->orWhere('id', $studentId)
            ->firstOrFail();

        $unpaidFines = Fine::where('student_id', $student->id)
            ->where('payment_status', '!=', 'paid')
            ->get();

        if ($unpaidFines->isEmpty()) {
            return response()->json(['message' => 'This student has no unpaid fines.'], 422);
        }

        DB::transaction(function () use ($unpaidFines, $student) {
            foreach ($unpaidFines as $fine) {
                $fine->update([
                    'balance'        => 0.00,
                    'payment_status' => 'paid',
                    'status'         => 'paid',
                    'paid_at'        => now(),
                ]);
                $fine->recordAudit(
                    'payment_recorded',
                    'admin',
                    null,
                    'Administrator',
                    ['action' => 'marked_overall_paid', 'student' => $student->name]
                );
            }
        });

        return response()->json([
            'message'    => "All fines for {$student->name} have been marked as paid.",
            'student_id' => $student->student_id,
        ]);
    }

    /**
     * DELETE /api/students/{studentId}/fines/{fine} — Student deletes paid fine record
     */
    public function destroyStudentFine(string $studentId, Fine $fine)
    {
        $student = Student::where('student_id', $studentId)->firstOrFail();

        if ($fine->student_id !== $student->id) {
            return response()->json(['message' => 'Unauthorized action.'], 403);
        }

        if ($fine->payment_status !== 'paid') {
            return response()->json(['message' => 'Only paid fines can be deleted.'], 422);
        }

        $fine->delete();

        return response()->json(['message' => 'Paid fine record deleted successfully.']);
    }
}
