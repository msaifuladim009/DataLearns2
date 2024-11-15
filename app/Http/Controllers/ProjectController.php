<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Group;
use App\Models\Task;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::all();
        return view('admin.projects.projects', compact('projects'));
    }

    public function create()
    {
        return view('admin.projects.create');
    }

    // Menyimpan proyek baru ke database
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
        ]);

        Project::create($data);
        return redirect()->route('projects.index')->with('success', 'Project created successfully');
    }

    // Menampilkan form untuk edit proyek
    public function edit(Project $project)
    {
        return view('admin/projects.edit', compact('project'));
    }
    
    // Memperbarui proyek di database
    public function update(Request $request, Project $project)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
        ]);

        $project->update($data);
        return redirect()->route('projects.index')->with('success', 'Project updated successfully');
    }

    // Menghapus proyek dari database
    public function destroy(Project $project)
    {
        $project->delete();
        return redirect()->route('projects.index')->with('success', 'Project deleted successfully');
    }

    public function show(Project $project)
    {
        $groups = $project->groups;
        return view('admin.projects.show', compact('project', 'groups'));
    }

}
