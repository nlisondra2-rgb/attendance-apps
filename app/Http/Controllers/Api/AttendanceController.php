<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AttendanceSession;
use App\Models\Event;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AttendanceController extends Controller
{
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
    // Helpers
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

    private function finalizeAbsencesForStudent(Student $student): void
    {
        $now = now();
        $events = Event::query()->get();

        foreach ($events as $event) {
            [$start, $end] = $this->eventWindow($event);
            if ($end->greaterThanOrEqualTo($now)) {
                continue;
            }

            $exists = Attendance::query()
                ->where('student_id', $student->id)
                ->where('event_id', $event->id)
                ->exists();

            if ($exists) {
                continue;
            }

            Attendance::create([
                'student_id'  => $student->id,
                'event_id'    => $event->id,
                'checked_in_at' => null,
                'status'      => 'absent',
            ]);
        }
    }

    // -------------------------------------------------------
    // GET /api/attendances  — list (admin view)
    // -------------------------------------------------------
    public function index(Request $request)
    {
        $query = Attendance::query()->with(['student', 'event']);

        if ($request->filled('student_id')) {
            $student = Student::where('student_id', $request->string('student_id'))->first();
            if ($student) {
                $this->finalizeAbsencesForStudent($student);
            }

            $query->whereHas('student', function ($q) use ($request) {
                $q->where('student_id', $request->string('student_id'));
            });
        }

        if ($request->filled('event_id')) {
            $query->where('event_id', $request->integer('event_id'));
        }

        return $query
            ->orderByDesc('checked_in_at')
            ->get()
            ->map(function (Attendance $a) {
                return [
                    'id'             => $a->id,
                    'checked_in_at'  => optional($a->checked_in_at)->toIso8601String(),
                    'status'         => $a->status,
                    'distance_meters'=> $a->distance_meters,
                    'student_lat'    => $a->student_lat,
                    'student_lng'    => $a->student_lng,
                    'student' => $a->student ? [
                        'student_id' => $a->student->student_id,
                        'name'       => $a->student->name,
                        'email'      => $a->student->email,
                        'course'     => $a->student->course,
                    ] : null,
                    'event' => $a->event ? [
                        'id'         => $a->event->id,
                        'name'       => $a->event->name,
                        'date'       => $a->event->date,
                        'starts_at'  => optional($a->event->starts_at)->toIso8601String(),
                        'ends_at'    => optional($a->event->ends_at)->toIso8601String(),
                        'description'=> $a->event->description,
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
            'student_lat' => ['required', 'numeric', 'between:-90,90'],
            'student_lng' => ['required', 'numeric', 'between:-180,180'],
        ]);

        $student = Student::where('student_id', $validated['student_id'])->firstOrFail();
        $event   = Event::findOrFail($validated['event_id']);

        // 1. Check event time window
        [$start, $end] = $this->eventWindow($event);
        $now = now();
        if ($now->lt($start)) {
            return response()->json(['message' => 'Attendance for this event has not started yet.'], 422);
        }
        if ($now->gt($end)) {
            return response()->json(['message' => 'Attendance for this event is already closed.'], 422);
        }

        // 2. Validate QR token → find session
        $session = AttendanceSession::where('token', $validated['qr_token'])->first();
        if (! $session) {
            return response()->json(['message' => 'Invalid attendance QR code.'], 422);
        }

        // 3. Check session belongs to this event
        if ($session->event_id !== $event->id) {
            return response()->json(['message' => 'This QR code does not belong to this event.'], 422);
        }

        // 4. Check session not expired
        if ($session->expires_at->isPast()) {
            return response()->json(['message' => 'This attendance QR code has expired.'], 422);
        }

        // 5. Check session is active (admin hasn't stopped it)
        if (! $session->is_active) {
            return response()->json(['message' => 'Attendance session has been closed by the administrator.'], 422);
        }

        // 6. Check duplicate attendance
        $existing = Attendance::where('student_id', $student->id)
            ->where('event_id', $event->id)
            ->first();

        if ($existing && $existing->status === 'present') {
            return response()->json(['message' => 'You have already recorded your attendance for this event.'], 409);
        }

        // 7. GPS / location validation (server-side Haversine)
        $eventLat = (float) $event->latitude;
        $eventLng = (float) $event->longitude;
        $studentLat = (float) $validated['student_lat'];
        $studentLng = (float) $validated['student_lng'];

        if (! $event->latitude || ! $event->longitude) {
            // Event has no GPS set — skip location check (fallback)
            $distanceMeters = null;
        } else {
            $distanceMeters = (int) round(
                $this->haversineMeters($eventLat, $eventLng, $studentLat, $studentLng)
            );

            $allowedRadius = $event->radius_meters ?? 100;

            if ($distanceMeters > $allowedRadius) {
                return response()->json([
                    'message'          => "You are outside the allowed attendance area. You are {$distanceMeters} metres from the event location. You must be within {$allowedRadius} metres.",
                    'distance_meters'  => $distanceMeters,
                    'allowed_radius'   => $allowedRadius,
                ], 422);
            }
        }

        // 8. All checks passed — record attendance
        $attendance = $existing ?? new Attendance([
            'student_id' => $student->id,
            'event_id'   => $event->id,
        ]);

        $attendance->session_id     = $session->id;
        $attendance->checked_in_at  = now();
        $attendance->status         = 'present';
        $attendance->student_lat    = $studentLat;
        $attendance->student_lng    = $studentLng;
        $attendance->distance_meters = $distanceMeters;
        $attendance->save();

        return response()->json([
            'message'         => 'Attendance successfully recorded.',
            'status'          => 'present',
            'distance_meters' => $distanceMeters,
            'checked_in_at'   => $attendance->checked_in_at->toIso8601String(),
        ], 201);
    }

    // -------------------------------------------------------
    // POST /api/attendances  — legacy admin manual check-in (kept for backward compat)
    // -------------------------------------------------------
    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => ['required', 'string', 'exists:students,student_id'],
            'event_id'   => ['required', 'integer', 'exists:events,id'],
        ]);

        $student = Student::where('student_id', $validated['student_id'])->firstOrFail();
        $event   = Event::findOrFail($validated['event_id']);

        [$start, $end] = $this->eventWindow($event);
        $now = now();
        if ($now->lt($start) || $now->gt($end)) {
            return response()->json([
                'message' => 'Check-in is not allowed outside the event time window.',
            ], 422);
        }

        $attendance = Attendance::firstOrNew([
            'student_id' => $student->id,
            'event_id'   => $event->id,
        ]);

        if ($attendance->exists && $attendance->status === 'present') {
            return response()->json(['message' => 'Already marked present.'], 409);
        }

        $attendance->checked_in_at = now();
        $attendance->status        = 'present';
        $attendance->save();

        $attendance->load(['student', 'event']);

        return response()->json($attendance, 201);
    }

    // -------------------------------------------------------
    // DELETE /api/attendances/{id}
    // -------------------------------------------------------
    public function destroy(Attendance $attendance)
    {
        $attendance->delete();
        return response()->noContent();
    }
}
