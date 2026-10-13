<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index()
    {
        return Student::query()
            ->orderBy('student_id')
            ->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => ['required', 'string', 'max:50', 'unique:students,student_id'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'course' => ['required', 'string', 'max:50'],
            'year_level' => ['nullable', 'string', 'max:50'],
            'password' => ['required', 'string', 'min:6'],
            'school_qr_raw' => ['nullable', 'string', 'max:255', 'unique:students,school_qr_raw'],
        ]);

        $data = $validated;
        if (isset($data['password'])) {
            $data['password'] = \Illuminate\Support\Facades\Hash::make($data['password']);
        }
        $student = Student::create($data);

        return response()->json($student, 201);
    }

    public function show(Student $student)
    {
        return $student;
    }

    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'student_id' => ['sometimes', 'required', 'string', 'max:50', 'unique:students,student_id,' . $student->id],
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => ['sometimes', 'required', 'email', 'max:255'],
            'course' => ['sometimes', 'required', 'string', 'max:50'],
            'year_level' => ['sometimes', 'nullable', 'string', 'max:50'],
            'password' => ['sometimes', 'nullable', 'string', 'min:6'],
            'school_qr_raw' => ['sometimes', 'nullable', 'string', 'max:255', 'unique:students,school_qr_raw,' . $student->id],
        ]);

        $data = $validated;
        if (!empty($data['password'])) {
            $data['password'] = \Illuminate\Support\Facades\Hash::make($data['password']);
        } else {
            unset($data['password']);
        }
        $student->update($data);

        return $student;
    }

    public function showByQr(Request $request)
    {
        $raw = $request->string('raw');

        $student = Student::where('school_qr_raw', $raw)->firstOrFail();

        return $student;
    }

    public function destroy(Student $student)
    {
        $student->delete();

        return response()->noContent();
    }
}
