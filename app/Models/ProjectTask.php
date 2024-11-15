<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectTask extends Model
{
    use HasFactory;
    protected $fillable = ['judul_tugas', 'deskripsi', 'deadline'];
    protected $casts = [
        'deadline' => 'datetime',  // Casting kolom 'deadline' menjadi datetime
    ];
    // Relasi ke tabel Submission
    public function submissions()
    {
        return $this->hasMany(Submission::class);
    }

    // Cek apakah deadline telah terlewati
    public function isDeadlinePassed()
    {
        return now()->greaterThan($this->deadline);
    }
}
