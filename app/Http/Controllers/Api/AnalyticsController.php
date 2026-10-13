<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Event;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function index()
    {
        $totalEvents = Event::count();
        $totalStudents = Student::count();
        $presentCount = Attendance::count();
        // For 'Absent', we calculate students who haven't attended ANY event? 
        // Or per event? Let's say students minus those who attended at least one event for simplicity in this overview card.
        $absentCount = $totalStudents - Attendance::distinct('student_id')->count();

        // Attendance by Event
        $attendanceByEvent = Event::withCount('attendances')
            ->orderBy('date')
            ->get(['id', 'name', 'date'])
            ->map(fn($e) => [
                'name' => $e->name,
                'count' => $e->attendances_count
            ]);

        // Attendance by Year Level
        $attendanceByYearLevel = Student::join('attendances', 'students.id', '=', 'attendances.student_id')
            ->select('students.year_level', DB::raw('count(*) as count'))
            ->groupBy('students.year_level')
            ->get()
            ->map(fn($item) => [
                'label' => $item->year_level ?: 'Unspecified',
                'count' => $item->count
            ]);

        return response()->json([
            'cards' => [
                ['label' => 'Total Events', 'value' => $totalEvents],
                ['label' => 'Present', 'value' => $presentCount],
                ['label' => 'Absent', 'value' => $absentCount],
                ['label' => 'Total Students', 'value' => $totalStudents],
            ],
            'attendanceByEvent' => $attendanceByEvent,
            'attendanceByYearLevel' => $attendanceByYearLevel,
        ]);
    }
}
