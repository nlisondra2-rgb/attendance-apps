<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AttendanceSession;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AttendanceSessionController extends Controller
{
    /**
     * Start a new attendance session for an event.
     * The QR token expires at the event end time (or 8 hours from now if not set).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'event_id' => ['required', 'integer', 'exists:events,id'],
        ]);

        $event = Event::findOrFail($validated['event_id']);

        // Deactivate any previous sessions for this event
        AttendanceSession::where('event_id', $event->id)
            ->where('is_active', true)
            ->update(['is_active' => false]);

        // Expiry = event end time, or 8 hours from now
        $expiresAt = $event->ends_at ?? now()->addHours(8);

        $session = AttendanceSession::create([
            'event_id'   => $event->id,
            'token'      => Str::random(48),
            'expires_at' => $expiresAt,
            'is_active'  => true,
        ]);

        return response()->json([
            'id'         => $session->id,
            'event_id'   => $session->event_id,
            'token'      => $session->token,
            'expires_at' => $session->expires_at->toIso8601String(),
            'is_active'  => $session->is_active,
        ], 201);
    }

    /**
     * Stop (deactivate) an attendance session.
     */
    public function stop(AttendanceSession $session)
    {
        $session->update(['is_active' => false]);

        return response()->json(['message' => 'Attendance session stopped.']);
    }

    /**
     * Get the current active session for an event (student uses this to check if attendance is open).
     */
    public function activeForEvent(int $eventId)
    {
        $event = Event::findOrFail($eventId);
        $session = $event->activeSession();

        if (! $session) {
            return response()->json(['active' => false, 'session' => null]);
        }

        return response()->json([
            'active'  => true,
            'session' => [
                'id'         => $session->id,
                'token'      => $session->token,
                'expires_at' => $session->expires_at->toIso8601String(),
            ],
        ]);
    }
}
