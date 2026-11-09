<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fines', function (Blueprint $table) {
            $table->id();
            $table->string('reference_no', 50)->unique();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();
            $table->foreignId('attendance_id')->nullable()->constrained('attendances')->nullOnDelete();
            $table->decimal('amount', 10, 2);
            $table->decimal('balance', 10, 2);
            $table->string('reason', 500);
            $table->date('issue_date');
            $table->string('status', 30)->default('unpaid'); // pending_review, unpaid, paid, waived, disputed
            $table->string('review_status', 30)->default('approved'); // pending, approved, rejected
            $table->string('payment_status', 30)->default('unpaid'); // unpaid, paid, waived
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('waived_at')->nullable();
            $table->text('waiver_reason')->nullable();
            $table->unsignedBigInteger('reviewed_by')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();

            // Prevent duplicate fines for the same student on the same event
            $table->unique(['student_id', 'event_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fines');
    }
};
