<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Video;
use Illuminate\Support\Facades\DB;

class VideoController extends Controller
{
    // Menampilkan daftar video
    public function video()
    {
        $videos = video::all();
        return view('admin.VideoMateri', compact('videos'));
    }

    // Menampilkan halaman video berdasar id
    public function show($id)
    {
        $videos = video::findOrFail($id);
        return view('video.show', compact('videos'));
    }

    // Menampilkan form untuk menambah video
    public function create()
    {
        return view('admin.addvideo');
    }

    // Menyimpan video baru
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|max:255',
            'url' => 'required',
        ]);
        
        video::create($validatedData);

        // Redirect ke halaman daftar video dengan pesan sukses
        return redirect()->route('admin.VideoMateri')->with('success', 'Materi berhasil ditambahkan.');  
    }

    // fungsi melakukan edit

    public function edit($id)
    {
        $videos = video::findOrFail($id);
        return view('admin.editvideo', compact('videos'));
    }

    // fungsi memperbarui data video
    public function update(Request $request, $id)
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
        return redirect()->route('admin.VideoMateri')->with('success', 'Materi berhasil diperbarui.');
    }

    // Menghapus video
    public function destroy($id)
    {
        $videos = video::findOrFail($id);
        $videos->delete();

        return redirect()->route('admin.VideoMateri')->with('success', 'Materi berhasil dihapus.');
    }
}
