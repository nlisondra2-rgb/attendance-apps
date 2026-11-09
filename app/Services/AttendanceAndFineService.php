<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Event;
use App\Models\Fine;
use App\Models\FineExcuse;
use App\Models\Student;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AttendanceAndFineService
{
    /**
     * Finalize absences for all eligible students of a completed event.
     * Idempotent: Can be run multiple times safely.
     *
     * @return array Summary of processed, created absences
     */
    public function finalizeAbsencesForEvent(Event $event): array
    {
        // Cancelled events do not generate absences
        if ($event->status === 'cancelled') {
            return ['status' => 'skipped', 'reason' => 'Event is cancelled'];
        }

        // Only events that have ended can be finalized
        $now = now();
        $eventEnd = $event->ends_at
            ? Carbon::parse($event->ends_at)
            : Carbon::parse($event->date)->endOfDay();

        if ($now->lt($eventEnd)) {
            return ['status' => 'skipped', 'reason' => 'Event has not ended yet'];
        }

        $eligibleStudents = Student::all()->filter(function (Student $student) use ($event) {
            return $event->isStudentEligible($student);
        });

        $absencesCreated = 0;
        $alreadyRecorded = 0;

        DB::transaction(function () use ($eligibleStudents, $event, &$absencesCreated, &$alreadyRecorded) {
            foreach ($eligibleStudents as $student) {
                // Check if attendance record exists
                $attendance = Attendance::where('student_id', $student->id)
                    ->where('event_id', $event->id)
                    ->first();

                if ($attendance) {
                    $alreadyRecorded++;
                    continue;
                }

                // Check if an approved excuse already exists
                $hasApprovedExcuse = FineExcuse::where('student_id', $student->id)
                    ->where('event_id', $event->id)
                    ->where('status', 'approved')
                    ->exists();

                $status = $hasApprovedExcuse ? 'excused' : 'absent';

                Attendance::create([
                    'student_id'    => $student->id,
                    'event_id'      => $event->id,
                    'checked_in_at' => null,
                    'status'        => $status,
                ]);

                $absencesCreated++;
            }
        });

        return [
            'status'            => 'success',
            'event_id'          => $event->id,
            'eligible_count'    => $eligibleStudents->count(),
            'absences_created'  => $absencesCreated,
            'already_recorded'  => $alreadyRecorded,
        ];
    }

    /**
     * Automatically process fines for an event after absence finalization.
     * Idempotent: Will not create duplicate fines.
     *
     * @return array Summary of fines created
     */
    public function processFinesForEvent(Event $event): array
    {
        // Cancelled events never generate fines
        if ($event->status === 'cancelled') {
            return ['status' => 'skipped', 'reason' => 'Event is cancelled'];
        }

        // If no fine amount configured, skip
        if ((float) $event->fine_amount <= 0) {
            return ['status' => 'skipped', 'reason' => 'No fine configured for this event'];
        }

        // Must have ended
        $now = now();
        $eventEnd = $event->ends_at
            ? Carbon::parse($event->ends_at)
            : Carbon::parse($event->date)->endOfDay();

        if ($now->lt($eventEnd)) {
            return ['status' => 'skipped', 'reason' => 'Event has not ended yet'];
        }

        // First ensure absences are finalized
        $this->finalizeAbsencesForEvent($event);

        // Find all unexcused absences for this event
        $absences = Attendance::where('event_id', $event->id)
            ->where('status', 'absent')
            ->get();

        $finesCreated = 0;
        $alreadyExisted = 0;

        DB::transaction(function () use ($absences, $event, &$finesCreated, &$alreadyExisted) {
            foreach ($absences as $attendance) {
                // Check if an approved excuse exists
                $hasApprovedExcuse = FineExcuse::where('student_id', $attendance->student_id)
                    ->where('event_id', $event->id)
                    ->where('status', 'approved')
                    ->exists();

                if ($hasApprovedExcuse) {
                    continue;
                }

                // Check if fine already exists
                $existingFine = Fine::where('student_id', $attendance->student_id)
                    ->where('event_id', $event->id)
                    ->first();

                if ($existingFine) {
                    $alreadyExisted++;
                    continue;
                }

                $referenceNo = 'FINE-' . date('Ymd') . '-' . strtoupper(Str::random(6));

                $fine = Fine::create([
                    'reference_no'    => $referenceNo,
                    'student_id'      => $attendance->student_id,
                    'event_id'        => $event->id,
                    'attendance_id'   => $attendance->id,
                    'amount'          => $event->fine_amount,
                    'balance'         => $event->fine_amount,
                    'reason'          => "Unexcused absence for event: {$event->name}",
                    'issue_date'      => now()->toDateString(),
                    'status'          => 'unpaid',
                    'review_status'   => 'approved',
                    'payment_status'  => 'unpaid',
                ]);

                $fine->recordAudit(
                    'created',
                    'system',
                    null,
                    'System Auto-Finalizer',
                    ['amount' => $event->fine_amount, 'policy' => $event->fine_policy]
                );

                $finesCreated++;
            }
        });

        return [
            'status'          => 'success',
            'event_id'        => $event->id,
            'fines_created'   => $finesCreated,
            'already_existed' => $alreadyExisted,
        ];
    }

    /**
     * Run finalization across all ended events.
     */
    public function finalizeAllEndedEvents(): array
    {
        $now = now();
        $endedEvents = Event::where('status', '!=', 'cancelled')
            ->where(function ($q) use ($now) {
                $q->whereNotNull('ends_at')->where('ends_at', '<', $now)
                  ->orWhere(function ($q2) use ($now) {
                      $q2->whereNull('ends_at')->where('date', '<', $now->toDateString());
                  });
            })
            ->get();

        $results = [];
        foreach ($endedEvents as $event) {
            $absencesRes = $this->finalizeAbsencesForEvent($event);
            $finesRes = $this->processFinesForEvent($event);
            $results[] = [
                'event_id'   => $event->id,
                'name'       => $event->name,
                'absences'   => $absencesRes,
                'fines'      => $finesRes,
            ];
        }

        return $results;
    }
}
