<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Materi;
use App\Models\video;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\ProjectTask;
use App\Models\Submission;
use App\Models\Project;
use App\Models\Group;
use App\Models\Task;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;


class GuruController extends Controller
{
    public function index()
    {
        return view('guru.index');
    }

    // Fungsi Materi Untuk Guru

    public function Materi()
    {
        $materis = Materi::all();
        return view('Guru.Materi.index', compact('materis'));
    }

    // Menampilkan halaman materi berdasar id
    public function show($id)
    {
        $materi = Materi::findOrFail($id);
        return view('materi.show', compact('materi'));
    }

    // Menampilkan form untuk menambah materi
    public function create()
    {
        return view('Guru.Materi.Create');
    }

    // Menyimpan materi baru
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|max:255',
            'Konten_1' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Validasi file gambar
            'html_code' => 'required',
        ]);

        // Simpan data
        $materi = new Materi();
        $materi->title = $request->title;
        $materi->Konten_1 = $request->Konten_1;
        $materi->html_code = $request->html_code;
        
        // Jika ada gambar yang diunggah
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('materi_images', 'public'); // Simpan di folder materi_images
            $materi->image = $imagePath; // Simpan path gambar
        }

        $materi->save();

        // Redirect ke halaman daftar materi dengan pesan sukses
        return redirect()->route('guru.materi')->with('success', 'Materi berhasil ditambahkan.');  
    }

    // fungsi melakukan edit
    public function edit($id)
    {
        $materi = Materi::findOrFail($id);
        return view('Guru.Materi.edit', compact('materi'));
    }

    // fungsi memperbarui data
    public function update(Request $request, $id)
    {
        $materi = Materi::findOrFail($id);
        // Validasi input
        $request->validate([
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Validasi file gambar
            'title' => 'required|max:255',
            'Konten_1' => 'required',
            'html_code' => 'required',
        ]);

        // Update title dan Konten_1
        $materi->title = $request->title;
        $materi->Konten_1 = $request->Konten_1;
        $materi->html_code = $request->html_code;

        // Jika ada gambar baru yang diunggah
        if ($request->hasFile('image')) {
            // Hapus gambar lama jika ada
            if ($materi->image) {
                Storage::delete('public/' . $materi->image);
            }
            $imagePath = $request->file('image')->store('materi_images', 'public');
            $materi->image = $imagePath;
        }
        $materi->save(); // Simpan perubahan
        return redirect()->route('guru.materi')->with('success', 'Materi berhasil diupdate.');
    }

    // Menghapus materi
    public function destroy($id)
    {
        $materi = Materi::findOrFail($id);
        // Hapus gambar dari storage jika ada
        if ($materi->image) {
            Storage::delete('public/' . $materi->image);
        }
        $materi->delete();

        return redirect()->route('guru.materi')->with('success', 'Materi berhasil dihapus.');
    }
    
    // fungsi video materi

    // Menampilkan daftar video
    public function video()
    {
        $videos = video::paginate(6);
        return view('guru.video_Materi.index', compact('videos'));
    }
    // Menampilkan halaman video berdasar id
    public function videoshow($id)
    {
        $videos = video::findOrFail($id);
        return view('video.show', compact('videos'));
    }
    // Menampilkan form untuk menambah video
    public function createvideo()
    {
        return view('guru.video_Materi.create');
    }
    // Menyimpan video baru
    public function storevideo(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|max:255',
            'url' => 'required',
        ]);
        
        video::create($validatedData);

        // Redirect ke halaman daftar video dengan pesan sukses
        return redirect()->route('guru.VideoMateri')->with('success', 'Materi berhasil ditambahkan.');  
    }
    // fungsi melakukan edit

    public function editvideo($id)
    {
        $videos = video::findOrFail($id);
        return view('guru.video_Materi.edit', compact('videos'));
    }
    // fungsi memperbarui data video
    public function updatevideo(Request $request, $id)
    {
        // Validasi input
        $validatedData = $request->validate([
            'title' => 'required|max:255',
            'url' => 'required',
        ]);

        // Temukan materi berdasarkan ID dan perbarui
        $videos = video::findOrFail($id);
        $videos->update($validatedData);

        // Redirect ke halaman daftar materi dengan pesan sukses
        return redirect()->route('guru.VideoMateri')->with('success', 'Materi berhasil diperbarui.');
    }
    
    // Menghapus video
    public function destroyvideo($id)
    {
        $videos = video::findOrFail($id);
        $videos->delete();

        return redirect()->route('guru.VideoMateri')->with('success', 'Materi berhasil dihapus.');
    }

    // fungsi Kuis

    // Menampilkan daftar quiz
    public function quiz()
    {
        $quizzes = Quiz::all();
        return view('guru.quiz.index', compact('quizzes'));
    }
    public function kunciquiz($id)
    {
        $quiz = Quiz::with('questions')->findOrFail($id);
        return view('guru.quiz.kunci', compact('quiz')); 
    }
    //menampilkan halaman quiz
    public function showquiz($id)
    {
        $quiz = Quiz::with('questions')->findOrFail($id);
        return view('guru.quiz.view', compact('quiz')); 
    }
    //menampilkan submit quiz
    public function submitquiz(Request $request, $id)
    {
        $quiz = Quiz::findOrFail($id);
        $score = 0;

        // mengulangi setiap pertanyaan dari kuis di database
        foreach ($quiz->questions as $question) {
            // Ambil jawaban yang dipilih oleh siswa
            $selectedAnswer = $request->input('question_'.$question->id);
            
            // cek apakah jawaban yyang telah dipilih sesuai dengan jawaban yang benar
            $answer = $request->input('question_'.$question->id);
            if ($answer == $question->correct_answer) {
                $score++;
            }
        }
        // Simpan skor ke database
        QuizAttempt::create([
            'quiz_id' => $quiz->id,
            'user_id' => auth()->id(),
            'score' => $score,
        ]);
        return redirect()->route('guru.quiz.show', $quiz->id)->with('success', "You scored $score out of 10.");
    }

    //menampilkan form quiz
    public function createquiz()
    {
        return view('guru.quiz.create');
    }

    // Menyimpan kuis baru ke database
    public function storequiz(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'questions.*.question' => 'required|string',
            'questions.*.option_a' => 'required|string',
            'questions.*.option_b' => 'required|string',
            'questions.*.option_c' => 'required|string',
            'questions.*.option_d' => 'required|string',
            'questions.*.correct_answer' => 'required|in:option_a,option_b,option_c,option_d',
        ]);

        $quiz = Quiz::create(['title' => $validatedData['title']]);

        foreach ($validatedData['questions'] as $question) {
            $quiz->questions()->create($question);
        }

        return redirect()->route('guru.quiz.show', $quiz->id)->with('success', 'Quiz successfully created.');
    }
    // Menghapus Kuis
    public function destroyquiz($id)
    {
        $quiz = Quiz::findOrFail($id);
        $quiz->delete();

        return redirect()->route('guru.quiz')->with('success', 'Materi berhasil dihapus.');
    }

    // menampilkan nilai hasil kuis untuk halaman raport
    public function showResults($id)
    {
        $quiz = Quiz::with('attempts.user')->findOrFail($id);
        
        // Mengambil semua hasil (attempts) dari kuis ini
        $attempts = $quiz->attempts;

        return view('guru.quiz.results', compact('quiz', 'attempts'));
    }

    //menampilkan halaman tugas

    // Menampilkan daftar semua tugas proyek
    public function task()
    {
        $tasks = ProjectTask::all();
        return view('guru.task.task', compact('tasks'));
    }
    // Menampilkan form untuk membuat tugas proyek baru
    public function createtask()
    {
        return view('guru.task.create');
    }
    // Menyimpan tugas proyek baru
    public function storetask(Request $request)
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

        return redirect()->route('guru.task.task')->with('success', 'Tugas proyek berhasil dibuat.');
    }

    // Menampilkan form untuk mengedit tugas proyek
    // Menampilkan form untuk mengedit tugas proyek
    public function edittask($id)
    {
        // Temukan task berdasarkan ID
        $task = ProjectTask::findOrFail($id);
        
        // Return view dengan data task
        return view('guru.task.edit', compact('task'));
    }

    // Mengupdate tugas proyek yang sudah ada
    // Mengupdate tugas proyek yang sudah ada
    public function updatetask(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'judul_tugas' => 'required',
            'deskripsi' => 'nullable',
            'deadline' => 'required|date',
        ]);

        // Temukan task berdasarkan ID
        $task = ProjectTask::findOrFail($id);

        // Update data task
        $task->update([
            'judul_tugas' => $request->input('judul_tugas'),
            'deskripsi' => $request->input('deskripsi'),
            'deadline' => $request->input('deadline'),
        ]);

        // Redirect setelah update berhasil
        return redirect()->route('guru.task.task')->with('success', 'Tugas proyek berhasil diperbarui.');
    }
    // Menghapus tugas proyek
    public function destroytask(ProjectTask $task)
    {
        $task->delete();
        return redirect()->route('guru.task.task')->with('success', 'Tugas proyek berhasil dihapus.');
    }
    //halaman daftar tugas yang dikumpulkan siswa
    public function tugas(ProjectTask $task)
    {
        $submissions = $task->submissions()->with('user')->get();
        return view('guru.tugas.tugas', compact('task', 'submissions'));
    }
    public function createtugas(ProjectTask $task)
    {
        if ($task->isDeadlinePassed()) {
            return redirect()->back()->with('error', 'Deadline telah terlewati. Anda tidak dapat mengumpulkan tugas.');
        }

        return view('guru.tugas.create', compact('task'));
    }
    // Simpan pengumpulan tugas
    public function storetugas(Request $request)
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

        return redirect()->route('guru.tugas.index')->with('success', 'Tugas berhasil dikumpulkan!');
    }
    // Form edit pengumpulan tugas
    public function edittugas(Submission $submission)
    {
        if ($submission->projectTask->isDeadlinePassed()) {
            return redirect()->back()->with('error', 'Deadline telah terlewati. Anda tidak dapat mengedit tugas.');
        }

        return view('guru.tugas.edit', compact('submission'));
    }
    // Update pengumpulan tugas
    public function updatetugas(Request $request, Submission $submission)
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

        return redirect()->route('guru.tugas.index')->with('success', 'Tugas berhasil diperbarui!');
    }

    // fungsi halaman project
    public function projects()
    {
        $projects = Project::all();
        return view('guru.projects.projects', compact('projects'));
    }

    public function createprojects()
    {
        return view('guru.projects.create');
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
        return redirect()->route('guru.projects')->with('success', 'Project created successfully');
    }

    // Menampilkan form untuk edit proyek
    public function editprojects(Project $project)
    {
        return view('guru/projects.edit', compact('project'));
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
        return redirect()->route('guru.projects')->with('success', 'Project updated successfully');
    }

    // Menghapus proyek dari database
    public function destroyprojects(Project $project)
    {
        $project->delete();
        return redirect()->route('guru.projects')->with('success', 'Project deleted successfully');
    }

    public function showprojects(Project $project)
    {
        $groups = $project->groups;
        return view('guru.projects.show', compact('project', 'groups'));
    }
    // fungsi halaman task
    // Menampilkan Kanban board dengan semua tugas berdasarkan kelompok
    public function kanban(Group $group)
    {
        $tasks = $group->tasks()->with('user')->orderBy('status')->get()->groupBy('status');

        // Mengambil anggota (members) dari group
        $members = $group->members;

        return view('guru.kanban.kanban', compact('group', 'members' ,'tasks'));
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

        return redirect()->route('guru.kanban', $group)->with('success', 'Task created successfully');
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
        return redirect()->route('guru.kanban', $group)->with('success', 'Task updated successfully');
    }

    // Menghapus tugas
    public function destroykanban(Group $group, Task $task)
    {
        $task->delete();
        return redirect()->route('guru.kanban', $group)->with('success', 'Task deleted successfully');
    }
    // fungsi halaman group
    // Menampilkan daftar kelompok di proyek tertentu
    public function group(Project $project)
    {
        $groups = $project->groups;
        return view('guru.groups.groups', compact('project', 'groups'));
    }

    // Menampilkan form untuk membuat kelompok baru
    public function creategroup(Project $project)
    {
        return view('guru.groups.create', compact('project'));
    }

    // Menyimpan kelompok baru di database
    public function storegroup(Request $request, Project $project)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $project->groups()->create($data);
        return redirect()->route('guru.group', $project)->with('success', 'Group created successfully');
    }

    // Menampilkan form untuk mengedit kelompok
    public function editgroup(Project $project, Group $group)
    {
        return view('guru.groups.edit', compact('project', 'group'));
    }

    // Memperbarui kelompok di database
    public function updategroup(Request $request, Project $project, Group $group)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $group->update($data);
        return redirect()->route('guru.group', $project)->with('success', 'Group updated successfully');
    }

    // Menghapus kelompok dari database
    public function destroygroup(Project $project, Group $group)
    {
        $group->delete();
        return redirect()->route('guru.group', $project)->with('success', 'Group deleted successfully');
    }
}
