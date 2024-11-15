<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    protected $fillable = ['project_id', 'name'];

    // relasi ke project
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    // relasi ke tasks
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    // Relasi ke pengguna
    public function members()
    {
        return $this->hasMany(User::class);
    }
}
