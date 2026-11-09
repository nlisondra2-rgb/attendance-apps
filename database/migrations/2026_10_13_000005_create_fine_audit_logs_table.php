<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fine_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fine_id')->constrained('fines')->cascadeOnDelete();
            $table->string('action', 50); // created, reviewed, payment_recorded, waived, adjusted, dispute_submitted, dispute_resolved
            $table->string('performed_by_type', 20)->default('system'); // system, admin, student
            $table->unsignedBigInteger('performed_by_id')->nullable();
            $table->string('performed_by_name', 100)->nullable();
            $table->json('details')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fine_audit_logs');
    }
};
