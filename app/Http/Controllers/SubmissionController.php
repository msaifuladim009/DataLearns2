<?php

namespace App\Http\Controllers;

use App\Models\ProjectTask;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubmissionController extends Controller
{
    // Form pengumpulan tugas
    public function create(ProjectTask $task)
    {
        if ($task->isDeadlinePassed()) {
            return redirect()->back()->with('error', 'Deadline telah terlewati. Anda tidak dapat mengumpulkan tugas.');
        }

        return view('submissions.create', compact('task'));
    }

    // Simpan pengumpulan tugas
    public function store(Request $request)
    {
        $task = ProjectTask::find($request->project_task_id);

        if ($task->isDeadlinePassed()) {
            return redirect()->back()->with('error', 'Deadline telah terlewati. Anda tidak dapat mengumpulkan tugas.');
        }

        $request->validate([
            'project_task_id' => 'required|exists:project_tasks,id',
            'file' => 'nullable|file|mimes:pdf|max:2048',
            'link' => 'nullable|url',
        ]);

        $file = null;
        if ($request->hasFile('file')) {
            $file = $request->file('file')->store('submissions');
        }

        Submission::create([
            'project_task_id' => $request->project_task_id,
            'user_id' => Auth::id(),
            'file' => $file,
            'link' => $request->link,
        ]);

        return redirect()->route('tasks.index')->with('success', 'Tugas berhasil dikumpulkan!');
    }

    // Form edit pengumpulan tugas
    public function edit(Submission $submission)
    {
        if ($submission->projectTask->isDeadlinePassed()) {
            return redirect()->back()->with('error', 'Deadline telah terlewati. Anda tidak dapat mengedit tugas.');
        }

        return view('submissions.edit', compact('submission'));
    }

    // Update pengumpulan tugas
    public function update(Request $request, Submission $submission)
    {
        if ($submission->projectTask->isDeadlinePassed()) {
            return redirect()->back()->with('error', 'Deadline telah terlewati. Anda tidak dapat mengedit tugas.');
        }

        $request->validate([
            'file' => 'nullable|file|mimes:pdf|max:2048',
            'link' => 'nullable|url',
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file')->store('submissions');
            $submission->file = $file;
        }

        if ($request->link) {
            $submission->link = $request->link;
        }

        $submission->save();

        return redirect()->route('tasks.index')->with('success', 'Tugas berhasil diperbarui!');
    }

    // Menampilkan daftar submissions untuk tugas tertentu
    public function index(ProjectTask $task)
    {
        $submissions = $task->submissions()->with('user')->get();
        return view('submissions.index', compact('task', 'submissions'));
    }
}
