<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Project;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    // Menampilkan daftar kelompok di proyek tertentu
    public function index(Project $project)
    {
        $groups = $project->groups;
        return view('admin.groups.groups', compact('project', 'groups'));
    }

    // Menampilkan form untuk membuat kelompok baru
    public function create(Project $project)
    {
        return view('admin.groups.create', compact('project'));
    }

    // Menyimpan kelompok baru di database
    public function store(Request $request, Project $project)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $project->groups()->create($data);
        return redirect()->route('groups.index', $project)->with('success', 'Group created successfully');
    }

    // Menampilkan form untuk mengedit kelompok
    public function edit(Project $project, Group $group)
    {
        return view('admin.groups.edit', compact('project', 'group'));
    }

    // Memperbarui kelompok di database
    public function update(Request $request, Project $project, Group $group)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $group->update($data);
        return redirect()->route('groups.index', $project)->with('success', 'Group updated successfully');
    }

    // Menghapus kelompok dari database
    public function destroy(Project $project, Group $group)
    {
        $group->delete();
        return redirect()->route('groups.index', $project)->with('success', 'Group deleted successfully');
    }

}
