<?php
namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Group;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
       // Buat 6 kelompok
        for ($i = 1; $i <= 6; $i++) {
            Group::create(['name' => "Kelompok $i"]);
        }

        // Ambil semua pengguna dan bagi menjadi 6 kelompok, masing-masing berisi 6 orang
        $users = User::all();
        $groups = Group::all();
        $counter = 0;

        foreach ($users as $user) {
            $user->group_id = $groups[$counter % 6]->id;
            $user->save();
            $counter++;
        }
    }
}
