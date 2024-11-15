<?php

namespace App\Http\Controllers;

use App\Models\ProjectTask;
use Illuminate\Http\Request;

class ProjectTaskController extends Controller
{
    // Menampilkan daftar semua tugas proyek
    public function index()
    {
        $tasks = ProjectTask::all();
        return view('tasks.index', compact('tasks'));
    }

    // Menampilkan form untuk membuat tugas proyek baru
    public function create()
    {
        return view('tasks.create');
    }

    // Menyimpan tugas proyek baru
    public function store(Request $request)
    {
        $request->validate([
            'judul_tugas' => 'required',
            'deskripsi' => 'nullable',
            'deadline' => 'required|date',
        ]);

        ProjectTask::create([
            'judul_tugas' => $request->judul_tugas,
            'deskripsi' => $request->deskripsi,
            'deadline' => $request->deadline,
        ]);

        return redirect()->route('tasks.index')->with('success', 'Tugas proyek berhasil dibuat.');
    }
    // Menampilkan form untuk mengedit tugas proyek
    public function edit(ProjectTask $task)
    {
        return view('tasks.edit', compact('task'));
    }

    // Mengupdate tugas proyek yang sudah ada
    public function update(Request $request, ProjectTask $task)
    {
        $request->validate([
            'judul_tugas' => 'required',
            'deskripsi' => 'nullable',
            'deadline' => 'required|date',
        ]);

        $task->update([
            'judul_tugas' => $request->judul_tugas,
            'deskripsi' => $request->deskripsi,
            'deadline' => $request->deadline,
        ]);

        return redirect()->route('tasks.index')->with('success', 'Tugas proyek berhasil diperbarui.');
    }

    // Menghapus tugas proyek
    public function destroy(ProjectTask $task)
    {
        $task->delete();
        return redirect()->route('tasks.index')->with('success', 'Tugas proyek berhasil dihapus.');
    }

}
