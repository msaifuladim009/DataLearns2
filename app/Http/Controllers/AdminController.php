<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Admin;
use App\Models\Materi;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function index()
    {
        return view('Admin.index');
    }
    public function Materi()
    {
        return view('Admin.Materi', [
            'Materis' => Materi::get(),
        ]);
    }
    public function Add()
    {
        return view('Admin.Add');
    }
    public function Store(Request $request)
    {
        $materi = new Materi();

        $materi->Judul_Materi = $request->Judul_Materi;
        $materi->Deskripsi = $request->Deskripsi;
        $materi->Isi_Materi = $request->Isi_Materi;
        $materi->id_quiz = $request->id_quiz;

        $materi->save();
        return redirect()->route('Materi.Index');
    }
}
