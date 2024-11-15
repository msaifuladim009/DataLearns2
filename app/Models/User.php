<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    const ADMIN_ROLE = 'admin';
    const GURU_ROLE = 'guru';
    const SISWA_ROLE = 'siswa';

    public function isAdmin()
    {
        return $this->role === self::ADMIN_ROLE;
    }

    public function isGuru()
    {
        return $this->role === self::GURU_ROLE;
    }

    public function isSiswa()
    {
        return $this->role === self::SISWA_ROLE;
    }

    public function quizAttempts()
    {
        return $this->hasMany(QuizAttempt::class);
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    // Relasi ke Task
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}
