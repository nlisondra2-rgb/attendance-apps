<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fine_excuses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fine_id')->nullable()->constrained('fines')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();
            $table->text('reason');
            $table->string('document_path', 255)->nullable();
            $table->string('status', 30)->default('pending'); // pending, approved, rejected
            $table->text('reviewer_notes')->nullable();
            $table->unsignedBigInteger('reviewed_by')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fine_excuses');
    }
};
