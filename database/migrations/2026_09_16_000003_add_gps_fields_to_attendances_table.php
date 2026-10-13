<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->foreignId('session_id')
                ->nullable()
                ->after('event_id')
                ->constrained('attendance_sessions')
                ->nullOnDelete();
            $table->decimal('student_lat', 10, 7)->nullable()->after('checked_in_at');
            $table->decimal('student_lng', 10, 7)->nullable()->after('student_lat');
            $table->unsignedInteger('distance_meters')->nullable()->after('student_lng');
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropForeign(['session_id']);
            $table->dropColumn(['session_id', 'student_lat', 'student_lng', 'distance_meters']);
        });
    }
};
