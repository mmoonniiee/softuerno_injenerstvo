<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\User;
use App\Models\Course;
use Illuminate\Http\Request;

class EnrollmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $enrollments = Enrollment::with(['user', 'course'])->get();
        return view('enrollments.index', compact('enrollments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = User::all();
        $courses = Course::all();
        return view('enrollments.create', compact('users', 'courses'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'course_id' => 'required|exists:courses,id',
            'enrollment_date' => 'required|date',
        ]);

        Enrollment::create($request->all());
        return redirect()->route('enrollments.index')->with('success', 'Enrollment created successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Enrollment $enrollment)
    {
        $enrollment->delete();
        return redirect()->route('enrollments.index')->with('success', 'Enrollment deleted successfully.');
    }
}
