<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->string('status', 30)->default('published')->after('name');
            $table->unsignedInteger('grace_period_minutes')->default(30)->after('ends_at');
            $table->decimal('fine_amount', 10, 2)->default(0.00)->after('grace_period_minutes');
            $table->string('fine_policy', 100)->default('standard_absence')->after('fine_amount');
            $table->string('target_course', 100)->default('All')->after('target_year_level');
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn([
                'status',
                'grace_period_minutes',
                'fine_amount',
                'fine_policy',
                'target_course',
            ]);
        });
    }
};
