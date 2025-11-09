<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

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

    public function roles()
    {
        return $this->belongsToMany(Role::class)->withTimestamps();
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class)
            ->withPivot('granted')
            ->withTimestamps();
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    // Check if user has a specific role
    public function hasRole($role)
    {
        if (is_string($role)) {
            return $this->roles->contains('name', $role);
        }

        return (bool) $role->intersect($this->roles)->count();
    }

    // Check if user has any of the given roles
    public function hasAnyRole(...$roles)
    {
        return $this->roles->whereIn('name', $roles)->isNotEmpty();
    }

    // Assign role to user
    public function assignRole($role)
    {
        $roleModel = is_string($role)
            ? Role::where('name', $role)->firstOrFail()
            : $role;

        $this->roles()->syncWithoutDetaching([$roleModel->id]);
        
        return $this;
    }

    // Remove role from user
    public function removeRole($role)
    {
        $roleModel = is_string($role)
            ? Role::where('name', $role)->firstOrFail()
            : $role;

        $this->roles()->detach($roleModel->id);
        
        return $this;
    }

    // Check if user has permission
    public function hasPermission($permission)
    {
        $permissionName = is_string($permission) ? $permission : $permission->name;

        // Check direct user permissions first
        $directPermission = $this->permissions()
            ->where('name', $permissionName)
            ->first();

        if ($directPermission) {
            return $directPermission->pivot->granted;
        }

        // Check role permissions
        return $this->roles()
            ->whereHas('permissions', function ($query) use ($permissionName) {
                $query->where('name', $permissionName);
            })
            ->exists();
    }

    // Check if user has any of the permissions
    public function hasAnyPermission(...$permissions)
    {
        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission)) {
                return true;
            }
        }

        return false;
    }

    // Check if user has all permissions
    public function hasAllPermissions(...$permissions)
    {
        foreach ($permissions as $permission) {
            if (!$this->hasPermission($permission)) {
                return false;
            }
        }

        return true;
    }

    // Give permission directly to user
    public function givePermissionTo($permission, $granted = true)
    {
        $permissionModel = is_string($permission)
            ? Permission::where('name', $permission)->firstOrFail()
            : $permission;

        $this->permissions()->syncWithoutDetaching([
            $permissionModel->id => ['granted' => $granted]
        ]);

        return $this;
    }

    // Revoke permission from user
    public function revokePermissionTo($permission)
    {
        return $this->givePermissionTo($permission, false);
    }

    // Get all user permissions (from roles + direct)
    public function getAllPermissions()
    {
        // Get permissions from roles
        $rolePermissions = Permission::whereHas('roles', function ($query) {
            $query->whereIn('roles.id', $this->roles->pluck('id'));
        })->get();

        // Get direct permissions
        $directPermissions = $this->permissions()
            ->wherePivot('granted', true)
            ->get();

        // Get revoked permissions
        $revokedPermissions = $this->permissions()
            ->wherePivot('granted', false)
            ->pluck('id');

        // Merge and filter
        return $rolePermissions
            ->merge($directPermissions)
            ->whereNotIn('id', $revokedPermissions)
            ->unique('id');
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