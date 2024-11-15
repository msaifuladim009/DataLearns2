<?php

namespace App\Http\Controllers;
use App\Models\Materi;
use App\Models\Quiz;
use App\Models\ProjectTask;
use App\Models\Video;
use Illuminate\Http\Request;
use App\Models\QuizAttempt;
use App\Models\Project;
use App\Models\Group;
use App\Models\Task;

class SiswaController extends Controller
{
    public function index()
    {
        // Mengambil semua data materi, quiz, dan tugas akhir

        // Kirim data ke view menggunakan compact
        return view('siswa.index');
    }
    //Fungsi Materi Siswa
    public function materi()
    {
        $materis = Materi::all();
        return view('siswa.materi.index', compact('materis'));
    }
    //Fungsi Detail Materi
    public function detailm($id)
    {
        $materi = Materi::findOrFail($id);
        return view('siswa.materi.detail', compact('materi'));
    }

    //Fungsi Video Materi
    public function video()
    {
        $videos = Video::all();
        return view('siswa.video.index', compact('videos'));
    }

    //Fungsi Detail Video Materi
    public function detailv($id)
    {
        $videos = Video::findOrFail($id);
        return view('siswa.video.detail', compact('videos'));
    }

    //Fungsi Tugas Projek
    public function tugas()
    {
        $projects = ProjectTask::all();
        return view('siswa.Tugasprojek.index', compact('projects'));
    }
    // Menyimpan tugas yang di-submit
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'file' => 'nullable|file|mimes:pdf,docx,zip|max:2048', // Validasi file upload
            'link' => 'nullable|url', // Validasi URL
            'deadline' => 'required|date', // Validasi deadline
        ]);

        // Simpan data tugas
        $tugasProject = new ProjectTask();
        $tugasProject->title = $request->title;
        $tugasProject->deadline = $request->deadline;

        // Jika file diunggah
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('Tugasprojek');
            $tugasProject->file_path = $filePath;
        }

        // Jika link disubmit
        if ($request->link) {
            $tugasProject->link = $request->link;
        }

        $tugasProject->save();

        return redirect()->route('siswa.Tugasprojek.index')->with('success', 'Tugas berhasil dikumpulkan.');
    }
    // Menampilkan detail tugas
    public function detailT($id)
    {
        $tugas = ProjectTask::findOrFail($id);
        return view('siswa.Tugasprojek.detail', compact('tugas'));
    }
    

    // controller siswa menuju Quiz
    // Menampilkan daftar Materi
    public function quiz()
    {
        $quizzes = Quiz::all();
        return view('siswa.quiz.quiz', compact('quizzes'));
    }

    //menampilkan halaman quiz
    public function show($id)
    {
        $quiz = Quiz::with('questions')->findOrFail($id);
        return view('siswa.quiz.viewquiz', compact('quiz'));
    }
    //menampilkan submit quiz
    public function submit(Request $request, $id)
    {
        $quiz = Quiz::findOrFail($id);
        $score = 0;

        foreach ($quiz->questions as $question) {
            $answer = $request->input('question_'.$question->id);
            
            // Ambil jawaban yang dipilih oleh siswa
            $selectedAnswer = $request->input('question_'.$question->id);

            // cek apakah jawaban yyang telah dipilih sesuai dengan jawaban yang benar
            $answer = $request->input('question_'.$question->id);
            if ($answer == $question->correct_answer) {
                $score++;
            }
        }
        QuizAttempt::create([
            'quiz_id' => $quiz->id,
            'user_id' => auth()->id(),
            'score' => $score,
        ]);
        return redirect()->route('siswa.quiz.show', $quiz->id)->with('success', "You scored $score out of 10.");
    }
    // fungsi halaman project
    public function projects()
    {
        $projects = Project::all();
        return view('siswa.projects.projects', compact('projects'));
    }

    public function createprojects()
    {
        return view('siswa.projects.create');
    }

    // Menyimpan proyek baru ke database
    public function storeprojects(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
        ]);

        Project::create($data);
        return redirect()->route('projects.projects')->with('success', 'Project created successfully');
    }

    // Menampilkan form untuk edit proyek
    public function editprojects(Project $project)
    {
        return view('siswa/projects.edit', compact('project'));
    }
    
    // Memperbarui proyek di database
    public function updateprojects(Request $request, Project $project)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
        ]);

        $project->update($data);
        return redirect()->route('projects.projects')->with('success', 'Project updated successfully');
    }

    // Menghapus proyek dari database
    public function destroyprojects(Project $project)
    {
        $project->delete();
        return redirect()->route('projects.projects')->with('success', 'Project deleted successfully');
    }

    public function showprojects(Project $project)
    {
        $groups = $project->groups;
        return view('siswa.projects.show', compact('project', 'groups'));
    }
    // fungsi halaman task
    // Menampilkan Kanban board dengan semua tugas berdasarkan kelompok
    public function kanban(Group $group)
    {
        $tasks = $group->tasks()->with('user')->orderBy('status')->get()->groupBy('status');

        // Mengambil anggota (members) dari group
        $members = $group->members;

        return view('siswa.kanban.kanban', compact('group', 'members' ,'tasks'));
    }

    // Menyimpan tugas baru
    public function storekanban(Request $request, Group $group)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:role,to_do,in_progress,drafting,in_review,revisi,done',
            'due_date' => 'nullable|date',
        ]);

        $data['group_id'] = $group->id;  // Pastikan tugas terkait dengan kelompok ini
        Task::create($data);

        return redirect()->route('kanban.kanban', $group)->with('success', 'Task created successfully');
    }

    // Memperbarui tugas yang ada
    public function updatekanban(Request $request, Group $group, Task $task)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:role,to_do,in_progress,drafting,in_review,revisi,done',
            'due_date' => 'nullable|date',
            'attachments' => 'nullable|file|mimes:jpg,jpeg,png,pdf,docx|max:2048', // Maksimal 2MB dan tipe file tertentu    
            'color' => 'nullable|string|max:20',
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
        return redirect()->route('kanban.kanban', $group)->with('success', 'Task updated successfully');
    }

    // Menghapus tugas
    public function destroykanban(Group $group, Task $task)
    {
        $task->delete();
        return redirect()->route('kanban.kanban', $group)->with('success', 'Task deleted successfully');
    }
    // fungsi halaman group
    // Menampilkan daftar kelompok di proyek tertentu
    public function group(Project $project)
    {
        $groups = $project->groups;
        return view('siswa.groups.groups', compact('project', 'groups'));
    }

    // Menampilkan form untuk membuat kelompok baru
    public function creategroup(Project $project)
    {
        return view('siswa.groups.create', compact('project'));
    }

    // Menyimpan kelompok baru di database
    public function storegroup(Request $request, Project $project)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $project->groups()->create($data);
        return redirect()->route('groups.group', $project)->with('success', 'Group created successfully');
    }

    // Menampilkan form untuk mengedit kelompok
    public function editgroup(Project $project, Group $group)
    {
        return view('siswa.groups.edit', compact('project', 'group'));
    }

    // Memperbarui kelompok di database
    public function updategroup(Request $request, Project $project, Group $group)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $group->update($data);
        return redirect()->route('groups.group', $project)->with('success', 'Group updated successfully');
    }

    // Menghapus kelompok dari database
    public function destroygroup(Project $project, Group $group)
    {
        $group->delete();
        return redirect()->route('groups.group', $project)->with('success', 'Group deleted successfully');
    }
}
