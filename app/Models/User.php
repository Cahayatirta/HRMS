<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use App\Traits\SoftDeleteBoolean;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, SoftDeleteBoolean, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_deleted',
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

    public function employee()
    {
        return $this->hasOne(Employee::class, 'user_id');
    }

    // public function tasks()
    // {
    //     return $this->belongsToMany(Task::class, 'user_tasks', 'user_id', 'task_id')->withTimestamps();
    // }

    // public function workhourPlans()
    // {
    //     return $this->hasMany(WorkhourPlan::class, 'user_id');
    // }

    public function meetings()
    {
        return $this->belongsToMany(Meeting::class, 'meeting_users', 'user_id', 'meeting_id')->withTimestamps();
    }

    public function softDelete(Request $request)
    {
        $this->is_deleted = true;
        $this->save();
    }

    public function softCascades()
    {
        return ['employee', 'tasks', 'workhourPlans', 'meetings'];
    }

    // Method untuk akses panel Filament
    public function canAccessPanel(Panel $panel): bool
    {
        // Admin bisa akses semua
        if ($this->role === 'admin') {
            return true;
        }

        // User bisa akses jika punya employee record dan active
        return $this->employee && $this->employee->status === 'active';
    }

    // Method helper untuk mendapatkan divisi user
    public function getDivision()
    {
        return $this->employee?->division;
    }

    
}
