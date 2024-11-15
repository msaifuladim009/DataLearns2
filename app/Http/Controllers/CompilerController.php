<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CompilerController extends Controller
{
    // Menampilkan halaman editor
    public function compiler()
    {
        return view('siswa.compiler.compiler');
    }

    // Mengeksekusi kode HTML, CSS, dan JavaScript
    public function execute(Request $request)
    {
        $html = $request->input('html');
        $css = $request->input('css');
        $js = $request->input('js');

        // Menggabungkan HTML, CSS, dan JS ke dalam satu dokumen
        $output = "
            <html>
            <head>
                <style>{$css}</style>
            </head>
            <body>
                {$html}
                <script>{$js}</script>
            </body>
            </html>
        ";

        return response($output);
    }
}
