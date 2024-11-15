<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Account;
use Illuminate\Support\Facades\DB;

class AccountController extends Controller
{
    // Menampilkan daftar User
    public function index()
    {
        $users = Account::all(); 
        return view('admin.account', compact('users'));
    }

    // Menampilkan halaman User berdasar id
    public function show($id)
    {
        $users = Account::findOrFail($id);
        return view('admin.Accountdetail', compact('users'));
    }

    // Menghapus user
    public function destroy($id)
    {
        $users = Account::findOrFail($id);
        $users->delete();

        return redirect()->route('admin.account')->with('success', 'akun berhasil dihapus.');
    }
    
}
