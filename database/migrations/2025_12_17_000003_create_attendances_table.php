<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();
            $table->timestamp('checked_in_at')->nullable();
            $table->timestamps();

            $table->unique(['student_id', 'event_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
