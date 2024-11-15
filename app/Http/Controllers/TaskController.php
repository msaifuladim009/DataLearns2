<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Group;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    // Menampilkan Kanban board dengan semua tugas berdasarkan kelompok
    public function index(Group $group)
    {
        $tasks = $group->tasks()->with('user')->orderBy('status')->get()->groupBy('status');

        // Mengambil anggota (members) dari group
        $members = $group->members;

        return view('admin.kanban.kanban', compact('group', 'members' ,'tasks'));
    }

    // Menyimpan tugas baru
    public function store(Request $request, Group $group)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:role,to_do,in_progress,drafting,in_review,revisi,done',
            'due_date' => 'nullable|date',
        ]);

        $data['group_id'] = $group->id;  // Pastikan tugas terkait dengan kelompok ini
        Task::create($data);

        return redirect()->route('kanban.index', $group)->with('success', 'Task created successfully');
    }

    // Memperbarui tugas yang ada
    public function update(Request $request, Group $group, Task $task)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:role,to_do,in_progress,drafting,in_review,revisi,done',
            'due_date' => 'nullable|date',
            'attachments' => 'nullable|file|mimes:jpg,jpeg,png,pdf,docx|max:2048', // Maksimal 2MB dan tipe file tertentu    
            'color' => 'nullable|string|max:20',
            'user_id' => 'nullable|string',
        ]);

        // Jika ada file baru yang diunggah, hapus file lama dan simpan file baru
        if ($request->hasFile('attachments')) {
            // Hapus file lama jika ada
            if ($task->attachments) {
                \Storage::disk('public')->delete($task->attachments);
            }

            $file = $request->file('attachments');
            $filePath = $file->store('attachments', 'public'); // Menyimpan file di folder 'attachments' di storage
            $data['attachments'] = $filePath;
        }

        $task->update($data);
        return redirect()->route('kanban.index', $group)->with('success', 'Task updated successfully');
    }

    // Menghapus tugas
    public function destroy(Group $group, Task $task)
    {
        $task->delete();
        return redirect()->route('kanban.index', $group)->with('success', 'Task deleted successfully');
    }
}
