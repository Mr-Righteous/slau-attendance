<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Traits\HasFacultyScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable, HasRoles;
    use HasFacultyScope;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'purpose',
        'password',
        'department_id',
        'password_changed',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    // Relationships
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function coursesTeaching()
    {
        return $this->hasMany(CourseUnit::class, 'lecturer_id');
    }

    

    public function attendanceRecords()
    {
        return $this->hasMany(AttendanceRecord::class, 'student_id');
    }

    // Scopes
    public function scopeStudents($query)
    {
        return $query->where('role', 'student');
    }

    public function scopeLecturers($query)
    {
        return $query->where('role', 'lecturer');
    }

    public function scopeAdmins($query)
    {
        return $query->where('role', 'admin');
    }

    // Helper methods
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isLecturer()
    {
        return $this->role === 'lecturer';
    }

    public function isStudent()
    {
        return $this->role === 'student';
    }

    public function student()
    {
        return $this->hasOne(Student::class);
    }

    public function myFaculty()
    {
        if (!$this->hasAnyRole('super-admin','big-admin'))
        {
            return $this->department->faculty;
        }
    }

    public function taughtSessions()
    {
        return $this->hasMany(ClassSession::class, 'lecturer_id');
    }

    public function scopeForUserRole(Builder $query, User $user)
    {
        if ($user->hasRole('dpt-hod'))
        {
            $myFacultyId = Faculty::findOrFail($user->department->faculty_id)->id;
            return $query->where('department_id', $user->department_id);
        } elseif ($user->hasRole('faculty-dean')) {
            $myFacultyId = Faculty::findOrFail($user->department->faculty_id)->id;
            return $query->inFaculty($myFacultyId);
        } else {
            return $query;
        }
    }
}


