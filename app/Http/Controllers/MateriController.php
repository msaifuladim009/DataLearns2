<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Materi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class MateriController extends Controller
{
    // Menampilkan daftar Materi
    public function index()
    {
        $materis = Materi::all();
        return view('admin.materi', compact('materis'));
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
        return view('admin.add');
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
        return redirect()->route('admin.materi')->with('success', 'Materi berhasil ditambahkan.');  
    }

    // fungsi melakukan edit

    public function edit($id)
    {
        $materi = Materi::findOrFail($id);
        return view('admin.edit', compact('materi'));
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
        return redirect()->route('admin.materi')->with('success', 'Materi berhasil diupdate.');
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

        return redirect()->route('admin.materi')->with('success', 'Materi berhasil dihapus.');
    }
    public function video()
    {
        return view('admin.VideoMateri');
    }
}
