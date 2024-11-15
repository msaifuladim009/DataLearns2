<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class KelompokController extends Controller
{
    public function kelompok($kelompok)
    {
        return view('kelompok', compact('kelompok'));
    }
}
