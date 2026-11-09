<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->text('excuse_reason')->nullable()->after('status');
            $table->unsignedBigInteger('corrected_by_user_id')->nullable()->after('excuse_reason');
            $table->timestamp('corrected_at')->nullable()->after('corrected_by_user_id');
            $table->text('correction_reason')->nullable()->after('corrected_at');
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn([
                'excuse_reason',
                'corrected_by_user_id',
                'corrected_at',
                'correction_reason',
            ]);
        });
    }
};
