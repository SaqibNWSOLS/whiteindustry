<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = ['name', 'display_name', 'description', 'level', 'is_system_role'];

    protected $casts = [
        'is_system_role' => 'boolean',
    ];

    public function permissions()
    {
        return $this->belongsToMany(Permission::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    public function givePermissionTo($permission)
    {
        $permissionModel = is_string($permission) 
            ? Permission::where('name', $permission)->firstOrFail()
            : $permission;

        $this->permissions()->syncWithoutDetaching([$permissionModel->id]);
    }

    public function revokePermissionTo($permission)
    {
        $permissionModel = is_string($permission)
            ? Permission::where('name', $permission)->firstOrFail()
            : $permission;

        $this->permissions()->detach($permissionModel->id);
    }
}