<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class StudentAuthController extends Controller
{
    public function login(Request $request)
    {
        $validated = $request->validate([
            'student_id' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $student = Student::where('student_id', $validated['student_id'])->first();

        if (!$student || !Hash::check($validated['password'], $student->password)) {
            return response()->json([
                'message' => 'Invalid student ID or password.',
            ], 401);
        }

        return response()->json([
            'message' => 'Login successful.',
            'student' => $student,
        ]);
    }
}
