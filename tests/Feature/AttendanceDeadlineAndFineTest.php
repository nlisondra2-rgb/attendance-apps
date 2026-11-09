<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\AttendanceSession;
use App\Models\Event;
use App\Models\Fine;
use App\Models\FineExcuse;
use App\Models\Student;
use App\Services\AttendanceAndFineService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Tests\TestCase;

class AttendanceDeadlineAndFineTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Ensure consistent timezone Asia/Manila
        date_default_timezone_set('Asia/Manila');
    }

    private function createStudent(array $overrides = []): Student
    {
        return Student::create(array_merge([
            'student_id' => '2023-' . rand(10000, 99999),
            'name'       => 'Test Student',
            'email'      => 'student' . rand(1000, 9999) . '@ssct.edu.ph',
            'course'     => 'BSIS',
            'year_level' => '3rd Year',
            'password'   => bcrypt('password123'),
        ], $overrides));
    }

    private function createEvent(array $overrides = []): Event
    {
        $start = Carbon::parse('2026-10-15 13:00:00', 'Asia/Manila');
        $end   = Carbon::parse('2026-10-15 17:00:00', 'Asia/Manila');

        return Event::create(array_merge([
            'name'                 => 'University General Assembly',
            'status'               => 'published',
            'date'                 => '2026-10-15',
            'starts_at'            => $start,
            'ends_at'              => $end,
            'grace_period_minutes' => 30,
            'fine_amount'          => 50.00,
            'fine_policy'          => 'standard_absence',
            'target_year_level'    => 'All',
            'target_course'        => 'All',
            'venue'                => 'Gymnasium',
        ], $overrides));
    }

    private function createSession(Event $event): AttendanceSession
    {
        return AttendanceSession::create([
            'event_id'   => $event->id,
            'token'      => Str::random(48),
            'expires_at' => Carbon::parse('2026-10-15 17:00:00', 'Asia/Manila'),
            'is_active'  => true,
        ]);
    }

    /**
     * 1. Check-in before start time is rejected.
     */
    public function test_checkin_before_start_time_is_rejected()
    {
        $student = $this->createStudent();
        $event   = $this->createEvent();
        $session = $this->createSession($event);

        // Frozen time at 12:59:59 PM (1 second before 1:00 PM)
        Carbon::setTestNow(Carbon::parse('2026-10-15 12:59:59', 'Asia/Manila'));

        $response = $this->postJson('/api/attendances/verify-checkin', [
            'student_id' => $student->student_id,
            'event_id'   => $event->id,
            'qr_token'   => $session->token,
        ]);

        $response->assertStatus(422)
                 ->assertJsonFragment(['message' => 'Attendance for this event has not started yet. Event starts at 01:00 PM.']);

        $this->assertDatabaseMissing('attendances', [
            'student_id' => $student->id,
            'event_id'   => $event->id,
        ]);
    }

    /**
     * 2. Check-in exactly at start time is accepted.
     */
    public function test_checkin_exactly_at_start_time_is_accepted()
    {
        $student = $this->createStudent();
        $event   = $this->createEvent();
        $session = $this->createSession($event);

        // Frozen time at exactly 1:00:00 PM
        Carbon::setTestNow(Carbon::parse('2026-10-15 13:00:00', 'Asia/Manila'));

        $response = $this->postJson('/api/attendances/verify-checkin', [
            'student_id' => $student->student_id,
            'event_id'   => $event->id,
            'qr_token'   => $session->token,
        ]);

        $response->assertStatus(201)
                 ->assertJson(['status' => 'present']);

        $this->assertDatabaseHas('attendances', [
            'student_id' => $student->id,
            'event_id'   => $event->id,
            'status'     => 'present',
        ]);
    }

    /**
     * 3. Check-in at exactly the deadline (1:30:00 PM) is accepted.
     */
    public function test_checkin_at_exact_deadline_is_accepted()
    {
        $student = $this->createStudent();
        $event   = $this->createEvent(['grace_period_minutes' => 30]);
        $session = $this->createSession($event);

        // Exactly 1:30:00 PM
        Carbon::setTestNow(Carbon::parse('2026-10-15 13:30:00', 'Asia/Manila'));

        $response = $this->postJson('/api/attendances/verify-checkin', [
            'student_id' => $student->student_id,
            'event_id'   => $event->id,
            'qr_token'   => $session->token,
        ]);

        $response->assertStatus(201)
                 ->assertJson(['status' => 'present']);
    }

    /**
     * 4. Check-in one second after deadline (1:30:01 PM) is rejected.
     */
    public function test_checkin_one_second_after_deadline_is_rejected()
    {
        $student = $this->createStudent();
        $event   = $this->createEvent(['grace_period_minutes' => 30]);
        $session = $this->createSession($event);

        // 1:30:01 PM
        Carbon::setTestNow(Carbon::parse('2026-10-15 13:30:01', 'Asia/Manila'));

        $response = $this->postJson('/api/attendances/verify-checkin', [
            'student_id' => $student->student_id,
            'event_id'   => $event->id,
            'qr_token'   => $session->token,
        ]);

        $response->assertStatus(422)
                 ->assertJsonFragment(['message' => 'The attendance check-in deadline has passed (01:30 PM). Check-in is now closed.']);

        $this->assertDatabaseMissing('attendances', [
            'student_id' => $student->id,
            'event_id'   => $event->id,
        ]);
    }

    /**
     * 5. Check-in at 1:31 PM is rejected.
     */
    public function test_checkin_at_1_31_pm_is_rejected()
    {
        $student = $this->createStudent();
        $event   = $this->createEvent(['grace_period_minutes' => 30]);
        $session = $this->createSession($event);

        Carbon::setTestNow(Carbon::parse('2026-10-15 13:31:00', 'Asia/Manila'));

        $response = $this->postJson('/api/attendances/verify-checkin', [
            'student_id' => $student->student_id,
            'event_id'   => $event->id,
            'qr_token'   => $session->token,
        ]);

        $response->assertStatus(422);
    }

    /**
     * 6. Ineligible student is rejected with 403.
     */
    public function test_ineligible_student_is_rejected()
    {
        $student = $this->createStudent(['year_level' => '1st Year']);
        $event   = $this->createEvent(['target_year_level' => '4th Year']);
        $session = $this->createSession($event);

        Carbon::setTestNow(Carbon::parse('2026-10-15 13:15:00', 'Asia/Manila'));

        $response = $this->postJson('/api/attendances/verify-checkin', [
            'student_id' => $student->student_id,
            'event_id'   => $event->id,
            'qr_token'   => $session->token,
        ]);

        $response->assertStatus(403)
                 ->assertJsonFragment(['message' => 'You are not eligible to attend this event based on course or year level requirements.']);
    }

    /**
     * 7. Invalid or expired QR token is rejected.
     */
    public function test_invalid_qr_token_is_rejected()
    {
        $student = $this->createStudent();
        $event   = $this->createEvent();

        Carbon::setTestNow(Carbon::parse('2026-10-15 13:10:00', 'Asia/Manila'));

        $response = $this->postJson('/api/attendances/verify-checkin', [
            'student_id' => $student->student_id,
            'event_id'   => $event->id,
            'qr_token'   => 'non-existent-token',
        ]);

        $response->assertStatus(422)
                 ->assertJsonFragment(['message' => 'Invalid attendance QR code.']);
    }

    /**
     * 8. Duplicate check-in returns 409 conflict.
     */
    public function test_duplicate_checkin_returns_conflict()
    {
        $student = $this->createStudent();
        $event   = $this->createEvent();
        $session = $this->createSession($event);

        Carbon::setTestNow(Carbon::parse('2026-10-15 13:10:00', 'Asia/Manila'));

        // First check-in
        $this->postJson('/api/attendances/verify-checkin', [
            'student_id' => $student->student_id,
            'event_id'   => $event->id,
            'qr_token'   => $session->token,
        ])->assertStatus(201);

        // Second check-in
        $response = $this->postJson('/api/attendances/verify-checkin', [
            'student_id' => $student->student_id,
            'event_id'   => $event->id,
            'qr_token'   => $session->token,
        ]);

        $response->assertStatus(409)
                 ->assertJsonFragment(['message' => 'You have already recorded your attendance for this event.']);

        $this->assertEquals(1, Attendance::where('student_id', $student->id)->where('event_id', $event->id)->count());
    }

    /**
     * 9. Cancelled event does not generate absences or fines.
     */
    public function test_cancelled_event_does_not_generate_fines()
    {
        $student = $this->createStudent();
        $event   = $this->createEvent([
            'status'      => 'cancelled',
            'fine_amount' => 100.00,
        ]);

        // Move to post-event time
        Carbon::setTestNow(Carbon::parse('2026-10-15 18:00:00', 'Asia/Manila'));

        $service = app(AttendanceAndFineService::class);
        $res = $service->processFinesForEvent($event);

        $this->assertEquals('skipped', $res['status']);
        $this->assertDatabaseMissing('fines', ['event_id' => $event->id]);
    }

    /**
     * 10. Completed event generates absence and fine for unattended eligible student.
     */
    public function test_completed_event_generates_absence_and_fine()
    {
        $student = $this->createStudent();
        $event   = $this->createEvent(['fine_amount' => 75.00]);

        Carbon::setTestNow(Carbon::parse('2026-10-15 18:00:00', 'Asia/Manila'));

        $service = app(AttendanceAndFineService::class);
        $service->processFinesForEvent($event);

        $this->assertDatabaseHas('attendances', [
            'student_id' => $student->id,
            'event_id'   => $event->id,
            'status'     => 'absent',
        ]);

        $this->assertDatabaseHas('fines', [
            'student_id'     => $student->id,
            'event_id'       => $event->id,
            'amount'         => 75.00,
            'payment_status' => 'unpaid',
        ]);
    }

    /**
     * 11. Repeated scheduled jobs do not generate duplicate fines (idempotent).
     */
    public function test_repeated_jobs_do_not_duplicate_fines()
    {
        $student = $this->createStudent();
        $event   = $this->createEvent(['fine_amount' => 50.00]);

        Carbon::setTestNow(Carbon::parse('2026-10-15 18:00:00', 'Asia/Manila'));

        $service = app(AttendanceAndFineService::class);

        // Run twice
        $service->processFinesForEvent($event);
        $service->processFinesForEvent($event);

        $this->assertEquals(1, Fine::where('student_id', $student->id)->where('event_id', $event->id)->count());
    }

    /**
     * 12. Approved excuse waives fine and updates attendance.
     */
    public function test_approved_excuse_waives_fine()
    {
        $student = $this->createStudent();
        $event   = $this->createEvent(['fine_amount' => 50.00]);

        Carbon::setTestNow(Carbon::parse('2026-10-15 18:00:00', 'Asia/Manila'));

        $service = app(AttendanceAndFineService::class);
        $service->processFinesForEvent($event);

        $fine = Fine::where('student_id', $student->id)->where('event_id', $event->id)->firstOrFail();

        // Student disputes
        $disputeRes = $this->postJson("/api/students/{$student->student_id}/fines/{$fine->id}/dispute", [
            'reason' => 'Medical emergency with medical certificate',
        ]);
        $disputeRes->assertStatus(201);

        $excuse = FineExcuse::where('fine_id', $fine->id)->firstOrFail();

        // Admin approves excuse
        $reviewRes = $this->patchJson("/api/fine-excuses/{$excuse->id}/review", [
            'status'         => 'approved',
            'reviewer_notes' => 'Medical certificate verified',
        ]);
        $reviewRes->assertStatus(200);

        $fine->refresh();
        $this->assertEquals('waived', $fine->status);
        $this->assertEquals('waived', $fine->payment_status);
        $this->assertEquals(0, (float) $fine->balance);

        $attendance = Attendance::where('student_id', $student->id)->where('event_id', $event->id)->first();
        $this->assertEquals('excused', $attendance->status);
    }
}
