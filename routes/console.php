<?php

use App\Services\AttendanceAndFineService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/**
 * Artisan command to automatically finalize absences and generate fines for ended events.
 */
Artisan::command('attendance:finalize', function (AttendanceAndFineService $service) {
    $this->info('Starting automated absence finalization and fine processing...');
    $results = $service->finalizeAllEndedEvents();
    $this->info('Completed. Processed ' . count($results) . ' ended events.');
    foreach ($results as $res) {
        $absences = $res['absences']['absences_created'] ?? 0;
        $fines = $res['fines']['fines_created'] ?? 0;
        $this->line(" - Event #{$res['event_id']} ({$res['name']}): {$absences} absences, {$fines} fines created.");
    }
})->purpose('Finalize absences and process fine policies for ended events');

// Run every 15 minutes automatically
Schedule::command('attendance:finalize')->everyFifteenMinutes();
