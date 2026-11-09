<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AttendanceSession;
use App\Models\Event;
use App\Models\Student;
use App\Services\AttendanceAndFineService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    protected AttendanceAndFineService $fineService;

    public function __construct(AttendanceAndFineService $fineService)
    {
        $this->fineService = $fineService;
    }

    // -------------------------------------------------------
    // Haversine distance in metres between two GPS coordinates
    // -------------------------------------------------------
    private function haversineMeters(
        float $lat1, float $lng1,
        float $lat2, float $lng2
    ): float {
        $earthRadius = 6371000; // metres
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) ** 2
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;

        return $earthRadius * 2 * asin(sqrt($a));
    }

    // -------------------------------------------------------
    // Helper: Window [starts_at, ends_at]
    // -------------------------------------------------------
    private function eventWindow(Event $event): array
    {
        $start = $event->starts_at
            ? Carbon::parse($event->starts_at)
            : Carbon::parse($event->date)->startOfDay();

        $end = $event->ends_at
            ? Carbon::parse($event->ends_at)
            : Carbon::parse($event->date)->endOfDay();

        return [$start, $end];
    }

    // -------------------------------------------------------
    // GET /api/attendances  — list (admin or student view)
    // -------------------------------------------------------
    public function index(Request $request)
    {
        $query = Attendance::query()->with(['student', 'event']);

        if ($request->filled('student_id')) {
            $query->whereHas('student', function ($q) use ($request) {
                $q->where('student_id', $request->string('student_id'));
            });
        }

        if ($request->filled('event_id')) {
            $query->where('event_id', $request->integer('event_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        if ($request->filled('course')) {
            $query->whereHas('student', function ($q) use ($request) {
                $q->where('course', $request->string('course'));
            });
        }

        if ($request->filled('year_level')) {
            $query->whereHas('student', function ($q) use ($request) {
                $q->where('year_level', $request->string('year_level'));
            });
        }

        if ($request->filled('search')) {
            $search = '%' . $request->string('search') . '%';
            $query->where(function ($q) use ($search) {
                $q->whereHas('student', function ($sq) use ($search) {
                    $sq->where('name', 'like', $search)
                      ->orWhere('student_id', 'like', $search);
                })->orWhereHas('event', function ($eq) use ($search) {
                    $eq->where('name', 'like', $search);
                });
            });
        }

        return $query
            ->orderByDesc('checked_in_at')
            ->orderByDesc('id')
            ->get()
            ->map(function (Attendance $a) {
                return [
                    'id'              => $a->id,
                    'checked_in_at'   => optional($a->checked_in_at)->toIso8601String(),
                    'status'          => $a->status,
                    'excuse_reason'   => $a->excuse_reason,
                    'distance_meters' => $a->distance_meters,
                    'student_lat'     => $a->student_lat,
                    'student_lng'     => $a->student_lng,
                    'corrected_at'    => optional($a->corrected_at)->toIso8601String(),
                    'correction_reason'=> $a->correction_reason,
                    'student' => $a->student ? [
                        'id'         => $a->student->id,
                        'student_id' => $a->student->student_id,
                        'name'       => $a->student->name,
                        'email'      => $a->student->email,
                        'course'     => $a->student->course,
                        'year_level' => $a->student->year_level,
                    ] : null,
                    'event' => $a->event ? [
                        'id'         => $a->event->id,
                        'name'       => $a->event->name,
                        'status'     => $a->event->status,
                        'date'       => $a->event->date ? Carbon::parse($a->event->date)->toDateString() : null,
                        'starts_at'  => optional($a->event->starts_at)->toIso8601String(),
                        'ends_at'    => optional($a->event->ends_at)->toIso8601String(),
                        'venue'      => $a->event->venue,
                        'description'=> $a->event->description,
                        'deadline'   => optional($a->event->checkInDeadline())->toIso8601String(),
                    ] : null,
                ];
            });
    }

    // -------------------------------------------------------
    // POST /api/attendances/verify-checkin  — SECURE student check-in
    // -------------------------------------------------------
    public function verifyAndCheckIn(Request $request)
    {
        $validated = $request->validate([
            'student_id'  => ['required', 'string', 'exists:students,student_id'],
            'event_id'    => ['required', 'integer', 'exists:events,id'],
            'qr_token'    => ['required', 'string'],
            'student_lat' => ['nullable', 'numeric', 'between:-90,90'],
            'student_lng' => ['nullable', 'numeric', 'between:-180,180'],
        ]);

        $student = Student::where('student_id', $validated['student_id'])->firstOrFail();
        $event   = Event::findOrFail($validated['event_id']);

        // 1. Check Event Status (published and not cancelled)
        if ($event->status === 'cancelled') {
            return response()->json(['message' => 'This event has been cancelled.'], 422);
        }
        if ($event->status !== 'published') {
            return response()->json(['message' => 'This event is not currently open for attendance.'], 422);
        }

        // 2. Check Student Eligibility
        if (! $event->isStudentEligible($student)) {
            return response()->json([
                'message' => 'You are not eligible to attend this event based on course or year level requirements.',
            ], 403);
        }

        // 3. Check Event Start and Strict Attendance Deadline
        [$start, $end] = $this->eventWindow($event);
        $now = now();

        if ($now->lt($start)) {
            return response()->json([
                'message' => 'Attendance for this event has not started yet. Event starts at ' . $start->format('h:i A') . '.',
            ], 422);
        }

        $deadline = $event->checkInDeadline();
        if ($deadline && $now->gt($deadline)) {
            return response()->json([
                'message' => 'The attendance check-in deadline has passed (' . $deadline->format('h:i A') . '). Check-in is now closed.',
            ], 422);
        }

        if ($now->gt($end)) {
            return response()->json([
                'message' => 'This event has already ended.',
            ], 422);
        }

        // 4. Validate QR token -> find session
        $session = AttendanceSession::where('token', $validated['qr_token'])->first();
        if (! $session) {
            return response()->json(['message' => 'Invalid attendance QR code.'], 422);
        }

        if ($session->event_id !== $event->id) {
            return response()->json(['message' => 'This QR code does not belong to this event.'], 422);
        }

        if ($session->expires_at->isPast()) {
            return response()->json(['message' => 'This attendance QR code has expired.'], 422);
        }

        if (! $session->is_active) {
            return response()->json(['message' => 'Attendance session has been closed by the administrator.'], 422);
        }

        // 5. Check duplicate attendance
        $existing = Attendance::where('student_id', $student->id)
            ->where('event_id', $event->id)
            ->first();

        if ($existing && $existing->status === 'present') {
            return response()->json(['message' => 'You have already recorded your attendance for this event.'], 409);
        }

        // 6. GPS / location validation
        $distanceMeters = null;
        if ($event->latitude !== null && $event->longitude !== null) {
            if (! isset($validated['student_lat']) || ! isset($validated['student_lng'])) {
                return response()->json(['message' => 'Location coordinates are required for this event.'], 422);
            }

            $eventLat = (float) $event->latitude;
            $eventLng = (float) $event->longitude;
            $studentLat = (float) $validated['student_lat'];
            $studentLng = (float) $validated['student_lng'];

            $distanceMeters = (int) round(
                $this->haversineMeters($eventLat, $eventLng, $studentLat, $studentLng)
            );

            $allowedRadius = $event->radius_meters ?? 100;

            if ($distanceMeters > $allowedRadius) {
                return response()->json([
                    'message'          => "You are outside the allowed attendance area. You are {$distanceMeters} metres away (maximum allowed is {$allowedRadius} metres).",
                    'distance_meters'  => $distanceMeters,
                    'allowed_radius'   => $allowedRadius,
                ], 422);
            }
        }

        // 7. Atomic Database Write
        $attendance = DB::transaction(function () use ($existing, $student, $event, $session, $validated, $distanceMeters, $now) {
            $record = $existing ?? new Attendance([
                'student_id' => $student->id,
                'event_id'   => $event->id,
            ]);

            $record->session_id      = $session->id;
            $record->checked_in_at   = $now;
            $record->status          = 'present';
            $record->student_lat     = $validated['student_lat'] ?? null;
            $record->student_lng     = $validated['student_lng'] ?? null;
            $record->distance_meters = $distanceMeters;
            $record->save();

            return $record;
        });

        return response()->json([
            'message'         => 'Attendance successfully recorded.',
            'status'          => 'present',
            'distance_meters' => $distanceMeters,
            'checked_in_at'   => $attendance->checked_in_at->toIso8601String(),
        ], 201);
    }

    // -------------------------------------------------------
    // POST /api/attendances  — manual check-in (admin / test)
    // -------------------------------------------------------
    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => ['required', 'string', 'exists:students,student_id'],
            'event_id'   => ['required', 'integer', 'exists:events,id'],
            'status'     => ['sometimes', 'string', 'in:present,absent,excused'],
            'is_admin'   => ['sometimes', 'boolean'],
        ]);

        $student = Student::where('student_id', $validated['student_id'])->firstOrFail();
        $event   = Event::findOrFail($validated['event_id']);

        $isAdmin = $request->boolean('is_admin', false);

        // If not explicit admin override, enforce standard window & deadline
        if (! $isAdmin) {
            if ($event->status === 'cancelled') {
                return response()->json(['message' => 'Event is cancelled.'], 422);
            }

            [$start, $end] = $this->eventWindow($event);
            $now = now();

            if ($now->lt($start)) {
                return response()->json(['message' => 'Attendance has not started yet.'], 422);
            }

            $deadline = $event->checkInDeadline();
            if ($deadline && $now->gt($deadline)) {
                return response()->json(['message' => 'Check-in deadline has passed.'], 422);
            }
        }

        $attendance = Attendance::firstOrNew([
            'student_id' => $student->id,
            'event_id'   => $event->id,
        ]);

        $targetStatus = $validated['status'] ?? 'present';

        if ($attendance->exists && $attendance->status === $targetStatus) {
            return response()->json(['message' => 'Attendance is already recorded with this status.'], 409);
        }

        $attendance->status        = $targetStatus;
        $attendance->checked_in_at = $targetStatus === 'present' ? now() : null;
        $attendance->save();

        $attendance->load(['student', 'event']);

        return response()->json($attendance, 201);
    }

    // -------------------------------------------------------
    // PATCH /api/attendances/{attendance}  — Admin correction with audit
    // -------------------------------------------------------
    public function update(Request $request, Attendance $attendance)
    {
        $validated = $request->validate([
            'status'            => ['required', 'string', 'in:present,absent,excused'],
            'correction_reason' => ['required', 'string', 'max:500'],
            'corrected_by_user_id' => ['nullable', 'integer'],
        ]);

        $oldStatus = $attendance->status;

        $attendance->status = $validated['status'];
        if ($validated['status'] === 'present' && ! $attendance->checked_in_at) {
            $attendance->checked_in_at = now();
        } elseif ($validated['status'] !== 'present') {
            $attendance->checked_in_at = null;
        }

        $attendance->corrected_at          = now();
        $attendance->correction_reason     = $validated['correction_reason'];
        $attendance->corrected_by_user_id  = $validated['corrected_by_user_id'] ?? null;
        $attendance->save();

        // If status changed to present/excused and there is an outstanding fine, waive or reverse it
        if (in_array($validated['status'], ['present', 'excused'])) {
            $fine = $attendance->fine;
            if ($fine && $fine->payment_status === 'unpaid') {
                $fine->update([
                    'status'         => 'waived',
                    'payment_status' => 'waived',
                    'waived_at'      => now(),
                    'waiver_reason'  => "Attendance record corrected from {$oldStatus} to {$validated['status']}: " . $validated['correction_reason'],
                ]);
                $fine->recordAudit('waived', 'admin', $validated['corrected_by_user_id'] ?? null, 'Administrator', [
                    'reason' => 'Attendance corrected to ' . $validated['status'],
                ]);
            }
        }

        $attendance->load(['student', 'event']);

        return response()->json([
            'message'    => 'Attendance record successfully updated.',
            'attendance' => $attendance,
        ]);
    }

    // -------------------------------------------------------
    // GET /api/students/{studentId}/attendance-summary
    // -------------------------------------------------------
    public function studentSummary(Request $request, string $studentId)
    {
        $student = Student::where('student_id', $studentId)->firstOrFail();

        // Finalize completed events for this student if needed
        $this->fineService->finalizeAllEndedEvents();

        // All non-cancelled events student is eligible for
        $eligibleEvents = Event::where('status', '!=', 'cancelled')->get()->filter(function ($ev) use ($student) {
            return $ev->isStudentEligible($student);
        });

        $eligibleEventIds = $eligibleEvents->pluck('id');

        $attendances = Attendance::where('student_id', $student->id)
            ->whereIn('event_id', $eligibleEventIds)
            ->get();

        $attendedCount = $attendances->where('status', 'present')->count();
        $absentCount   = $attendances->where('status', 'absent')->count();
        $excusedCount  = $attendances->where('status', 'excused')->count();
        $totalEvents   = $eligibleEvents->count();

        // Calculate rate based on attended / eligible events (or 0 if 0 events)
        $ratePercentage = $totalEvents > 0
            ? round(($attendedCount / $totalEvents) * 100)
            : 100;

        return response()->json([
            'student_id'      => $student->student_id,
            'student_name'    => $student->name,
            'total_events'    => $totalEvents,
            'events_attended' => $attendedCount,
            'events_absent'   => $absentCount,
            'events_excused'  => $excusedCount,
            'attendance_rate' => $ratePercentage . '%',
            'is_rate_warning' => $totalEvents > 0 && ($attendedCount / $totalEvents) < 0.8,
        ]);
    }

    // -------------------------------------------------------
    // POST /api/events/{event}/finalize-absences
    // -------------------------------------------------------
    public function finalizeEvent(Event $event)
    {
        $absencesRes = $this->fineService->finalizeAbsencesForEvent($event);
        $finesRes    = $this->fineService->processFinesForEvent($event);

        return response()->json([
            'message'  => 'Attendance and fines finalized successfully.',
            'absences' => $absencesRes,
            'fines'    => $finesRes,
        ]);
    }

    // -------------------------------------------------------
    // DELETE /api/attendances/{attendance}
    // -------------------------------------------------------
    public function destroy(Attendance $attendance)
    {
        $attendance->delete();
        return response()->noContent();
    }
}
