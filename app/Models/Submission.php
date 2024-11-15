<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Submission extends Model
{
    use HasFactory;
    protected $fillable = ['project_task_id', 'user_id', 'file', 'link'];

    // Relasi ke ProjectTask
    public function projectTask()
    {
        return $this->belongsTo(ProjectTask::class);
    }

    // Relasi ke User (Siswa)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
