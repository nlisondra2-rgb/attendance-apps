<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Event;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class EventController extends Controller
{
    /**
     * GET /api/events  — list with filters and attendance counts
     */
    public function index(Request $request)
    {
        $query = Event::query();

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        if ($request->filled('search')) {
            $search = '%' . $request->string('search') . '%';
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', $search)
                  ->orWhere('venue', 'like', $search)
                  ->orWhere('description', 'like', $search);
            });
        }

        if ($request->filled('target_year_level') && $request->string('target_year_level') !== 'All') {
            $query->where('target_year_level', $request->string('target_year_level'));
        }

        if ($request->filled('timeframe')) {
            $now = now();
            if ($request->string('timeframe') === 'upcoming') {
                $query->where('starts_at', '>', $now);
            } elseif ($request->string('timeframe') === 'ongoing') {
                $query->where('starts_at', '<=', $now)
                      ->where('ends_at', '>=', $now);
            } elseif ($request->string('timeframe') === 'past') {
                $query->where('ends_at', '<', $now);
            }
        }

        $events = $query
            ->orderBy('date')
            ->orderBy('starts_at')
            ->orderBy('name')
            ->get();

        $allStudents = Student::all();

        return $events->map(function (Event $event) use ($allStudents) {
            $eligibleStudents = $allStudents->filter(fn($s) => $event->isStudentEligible($s));
            $attendances = Attendance::where('event_id', $event->id)->get();

            $presentCount = $attendances->where('status', 'present')->count();
            $absentCount  = $attendances->where('status', 'absent')->count();
            $excusedCount = $attendances->where('status', 'excused')->count();
            $totalEligible = $eligibleStudents->count();
            $pendingCount = max(0, $totalEligible - ($presentCount + $absentCount + $excusedCount));

            $deadline = $event->checkInDeadline();

            return [
                'id'                   => $event->id,
                'name'                 => $event->name,
                'status'               => $event->status ?? 'published',
                'date'                 => $event->date ? Carbon::parse($event->date)->toDateString() : null,
                'starts_at'            => optional($event->starts_at)->toIso8601String(),
                'ends_at'              => optional($event->ends_at)->toIso8601String(),
                'grace_period_minutes' => $event->grace_period_minutes ?? 30,
                'fine_amount'          => (float) ($event->fine_amount ?? 0),
                'fine_policy'          => $event->fine_policy ?? 'standard_absence',
                'description'          => $event->description,
                'target_year_level'    => $event->target_year_level ?? 'All',
                'target_course'        => $event->target_course ?? 'All',
                'venue'                => $event->venue,
                'latitude'             => $event->latitude ? (float) $event->latitude : null,
                'longitude'            => $event->longitude ? (float) $event->longitude : null,
                'radius_meters'        => $event->radius_meters ?? 100,
                'deadline'             => optional($deadline)->toIso8601String(),
                'is_checkin_open'      => $event->isCheckInOpen(),
                'stats'                => [
                    'eligible_students'=> $totalEligible,
                    'present_students' => $presentCount,
                    'absent_students'  => $absentCount,
                    'excused_students' => $excusedCount,
                    'pending_students' => $pendingCount,
                ],
            ];
        });
    }

    /**
     * GET /api/students/{studentId}/events  — Categorized eligible events for student dashboard
     */
    public function studentEvents(Request $request, string $studentId)
    {
        $student = Student::where('student_id', $studentId)->firstOrFail();
        $now = now();

        $allEvents = Event::where('status', '!=', 'cancelled')
            ->orderBy('date')
            ->orderBy('starts_at')
            ->get();

        $eligibleEvents = $allEvents->filter(fn($ev) => $ev->isStudentEligible($student));

        $attendances = Attendance::where('student_id', $student->id)
            ->whereIn('event_id', $eligibleEvents->pluck('id'))
            ->get()
            ->keyBy('event_id');

        $activeSessionTokens = [];
        foreach ($eligibleEvents as $ev) {
            $session = $ev->activeSession();
            if ($session) {
                $activeSessionTokens[$ev->id] = $session->token;
            }
        }

        $formatted = $eligibleEvents->map(function (Event $ev) use ($now, $attendances, $activeSessionTokens) {
            $att = $attendances->get($ev->id);
            $deadline = $ev->checkInDeadline();
            $isOpen = $ev->isCheckInOpen($now);

            // Compute check-in status
            $checkinStatus = 'not_started';
            if ($att && $att->status === 'present') {
                $checkinStatus = 'attended';
            } elseif ($ev->status === 'cancelled') {
                $checkinStatus = 'cancelled';
            } elseif ($ev->ends_at && $now->gt($ev->ends_at)) {
                $checkinStatus = 'ended';
            } elseif ($deadline && $now->gt($deadline)) {
                $checkinStatus = 'deadline_passed';
            } elseif ($isOpen) {
                $checkinStatus = 'open';
            }

            // Timeline category
            $category = 'upcoming';
            if ($ev->starts_at && $ev->ends_at) {
                if ($now->gt($ev->ends_at)) {
                    $category = 'past';
                } elseif ($now->gte($ev->starts_at) && $now->lte($ev->ends_at)) {
                    $category = 'ongoing';
                }
            } elseif ($ev->date) {
                $eventDate = Carbon::parse($ev->date);
                if ($now->isAfter($eventDate->endOfDay())) {
                    $category = 'past';
                } elseif ($now->isSameDay($eventDate)) {
                    $category = 'ongoing';
                }
            }

            return [
                'id'                   => $ev->id,
                'name'                 => $ev->name,
                'status'               => $ev->status ?? 'published',
                'date'                 => $ev->date ? Carbon::parse($ev->date)->toDateString() : null,
                'starts_at'            => optional($ev->starts_at)->toIso8601String(),
                'ends_at'              => optional($ev->ends_at)->toIso8601String(),
                'grace_period_minutes' => $ev->grace_period_minutes ?? 30,
                'deadline'             => optional($deadline)->toIso8601String(),
                'venue'                => $ev->venue,
                'description'          => $ev->description,
                'fine_amount'          => (float) ($ev->fine_amount ?? 0),
                'fine_policy'          => $ev->fine_policy,
                'category'             => $category,
                'checkin_status'       => $checkinStatus,
                'is_checkin_open'      => $isOpen,
                'has_active_session'   => isset($activeSessionTokens[$ev->id]),
                'session_token'        => $activeSessionTokens[$ev->id] ?? null,
                'my_attendance'        => $att ? [
                    'status'        => $att->status,
                    'checked_in_at' => optional($att->checked_in_at)->toIso8601String(),
                ] : null,
            ];
        })->values();

        return response()->json([
            'upcoming' => $formatted->where('category', 'upcoming')->sortBy('starts_at')->values(),
            'ongoing'  => $formatted->where('category', 'ongoing')->sortBy('starts_at')->values(),
            'past'     => $formatted->where('category', 'past')->sortByDesc('date')->values(),
            'all'      => $formatted,
        ]);
    }

    /**
     * GET /api/events/{event}/participants — Student attendance roster for event
     */
    public function participants(Event $event)
    {
        $allStudents = Student::orderBy('student_id')->get();
        $eligibleStudents = $allStudents->filter(fn($s) => $event->isStudentEligible($s));

        $attendances = Attendance::where('event_id', $event->id)->get()->keyBy('student_id');

        $list = $eligibleStudents->map(function (Student $student) use ($attendances) {
            $att = $attendances->get($student->id);

            return [
                'id'            => $student->id,
                'student_id'    => $student->student_id,
                'name'          => $student->name,
                'email'         => $student->email,
                'course'        => $student->course,
                'year_level'    => $student->year_level,
                'status'        => $att ? $att->status : 'pending',
                'checked_in_at' => $att && $att->checked_in_at ? $att->checked_in_at->toIso8601String() : null,
                'distance_meters'=> $att ? $att->distance_meters : null,
                'excuse_reason' => $att ? $att->excuse_reason : null,
                'attendance_id' => $att ? $att->id : null,
            ];
        })->values();

        return response()->json([
            'event'             => $event,
            'total_eligible'    => $eligibleStudents->count(),
            'present_count'     => $list->where('status', 'present')->count(),
            'absent_count'      => $list->where('status', 'absent')->count(),
            'excused_count'     => $list->where('status', 'excused')->count(),
            'pending_count'     => $list->where('status', 'pending')->count(),
            'participants'      => $list,
        ]);
    }

    /**
     * POST /api/events — Create new event
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'                 => ['required', 'string', 'max:255'],
            'status'               => ['sometimes', 'string', 'in:draft,published,cancelled'],
            'date'                 => ['required', 'date'],
            'starts_at'            => ['nullable', 'date'],
            'ends_at'              => ['nullable', 'date', 'after:starts_at'],
            'grace_period_minutes' => ['nullable', 'integer', 'min:0', 'max:1440'],
            'fine_amount'          => ['nullable', 'numeric', 'min:0', 'max:10000'],
            'fine_policy'          => ['nullable', 'string', 'max:100'],
            'description'          => ['nullable', 'string', 'max:1000'],
            'target_year_level'    => ['sometimes', 'string', 'max:50'],
            'target_course'        => ['sometimes', 'string', 'max:100'],
            'venue'                => ['nullable', 'string', 'max:255'],
            'latitude'             => ['nullable', 'numeric', 'between:-90,90'],
            'longitude'            => ['nullable', 'numeric', 'between:-180,180'],
            'radius_meters'        => ['nullable', 'integer', 'min:10', 'max:10000'],
        ]);

        if (! isset($validated['status'])) {
            $validated['status'] = 'published';
        }
        if (! isset($validated['grace_period_minutes'])) {
            $validated['grace_period_minutes'] = 30;
        }

        $event = Event::create($validated);

        return response()->json($event, 201);
    }

    /**
     * GET /api/events/{event}
     */
    public function show(Event $event)
    {
        return $event;
    }

    /**
     * PUT/PATCH /api/events/{event} — Update event
     */
    public function update(Request $request, Event $event)
    {
        $validated = $request->validate([
            'name'                 => ['sometimes', 'required', 'string', 'max:255'],
            'status'               => ['sometimes', 'string', 'in:draft,published,cancelled'],
            'date'                 => ['sometimes', 'required', 'date'],
            'starts_at'            => ['sometimes', 'nullable', 'date'],
            'ends_at'              => ['sometimes', 'nullable', 'date', 'after:starts_at'],
            'grace_period_minutes' => ['sometimes', 'nullable', 'integer', 'min:0', 'max:1440'],
            'fine_amount'          => ['sometimes', 'nullable', 'numeric', 'min:0', 'max:10000'],
            'fine_policy'          => ['sometimes', 'nullable', 'string', 'max:100'],
            'description'          => ['sometimes', 'nullable', 'string', 'max:1000'],
            'target_year_level'    => ['sometimes', 'string', 'max:50'],
            'target_course'        => ['sometimes', 'string', 'max:100'],
            'venue'                => ['sometimes', 'nullable', 'string', 'max:255'],
            'latitude'             => ['sometimes', 'nullable', 'numeric', 'between:-90,90'],
            'longitude'            => ['sometimes', 'nullable', 'numeric', 'between:-180,180'],
            'radius_meters'        => ['sometimes', 'nullable', 'integer', 'min:10', 'max:10000'],
        ]);

        $event->update($validated);

        return $event;
    }

    /**
     * PATCH /api/events/{event}/status — Quick publish / unpublish / cancel
     */
    public function changeStatus(Request $request, Event $event)
    {
        $validated = $request->validate([
            'status' => ['required', 'string', 'in:draft,published,cancelled'],
        ]);

        $event->update(['status' => $validated['status']]);

        return response()->json([
            'message' => "Event status updated to {$validated['status']}.",
            'event'   => $event,
        ]);
    }

    /**
     * DELETE /api/events/{event}
     */
    public function destroy(Event $event)
    {
        $event->delete();

        return response()->noContent();
    }
}
