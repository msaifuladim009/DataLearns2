<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'description', 'status', 'group_id', 'due_date', 'user_id','attachments','color'];

    // relasi ke group
    public function group()
    {
        return $this->belongsTo(Group::class);
    }
    
    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
