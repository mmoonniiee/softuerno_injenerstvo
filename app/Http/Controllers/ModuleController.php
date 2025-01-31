<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\Course;
use Illuminate\Http\Request;

class ModuleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $modules = Module::with('course')->get();
        return view('modules.index', compact('modules'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $courses = Course::all();
        return view('modules.create', compact('courses'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'course_id' => 'required|exists:courses,id',
            'content' => 'nullable|string',
        ]);

        Module::create($request->all());
        return redirect()->route('modules.index')->with('success', 'Module created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Module $module)
    {
        $courses = Course::all();
        return view('modules.edit', compact('module', 'courses'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Module $module)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'course_id' => 'required|exists:courses,id',
            'content' => 'nullable|string',
        ]);

        $module->update($request->all());
        return redirect()->route('modules.index')->with('success', 'Module updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $module->delete();
        return redirect()->route('modules.index')->with('success', 'Module deleted successfully.');
    }
}
