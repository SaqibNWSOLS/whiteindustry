<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes,HasRoles;

    protected $fillable = [
        'first_name', 'last_name', 'email', 'phone', 'job_title',
        'department', 'status', 'password',
    ];

    protected $hidden = ['password', 'remember_token'];

    // Ensure computed full_name is included when the model is serialized to array/json
    protected $appends = ['full_name'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];


    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }


    // Check if user is admin
    public function isAdmin()
    {
        return $this->hasRole('administrator');
    }

    // Check if user is manager
    public function isManager()
    {
        return $this->hasRole('manager');
    }

    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }
}