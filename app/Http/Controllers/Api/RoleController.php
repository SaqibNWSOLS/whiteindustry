<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index()
    {
        return response()->json(Role::with('permissions')->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:roles,name|max:255',
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'level' => 'required|integer|min:1',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role = Role::create([
            'name' => $validated['name'],
            'display_name' => $validated['display_name'],
            'description' => $validated['description'] ?? null,
            'level' => $validated['level'],
            'is_system_role' => false,
        ]);

        if (!empty($validated['permissions'])) {
            $role->permissions()->attach($validated['permissions']);
        }

        activity()
            ->causedBy($request->user())
            ->performedOn($role)
            ->log('Created role: ' . $role->display_name);

        return response()->json($role->load('permissions'), 201);
    }

    public function show(Role $role)
    {
        return response()->json($role->load(['permissions', 'users']));
    }

    public function update(Request $request, Role $role)
    {
        if ($role->is_system_role) {
            return response()->json([
                'message' => 'System roles cannot be modified'
            ], 400);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'level' => 'required|integer|min:1',
        ]);

        $role->update($validated);

        activity()
            ->causedBy($request->user())
            ->performedOn($role)
            ->log('Updated role: ' . $role->display_name);

        return response()->json($role->load('permissions'));
    }

    public function destroy(Request $request, Role $role)
    {
        if ($role->is_system_role) {
            return response()->json([
                'message' => 'System roles cannot be deleted'
            ], 400);
        }

        if ($role->users()->count() > 0) {
            return response()->json([
                'message' => 'Cannot delete role with assigned users'
            ], 400);
        }

        activity()
            ->causedBy($request->user())
            ->performedOn($role)
            ->log('Deleted role: ' . $role->display_name);

        $role->delete();

        return response()->json(null, 204);
    }

    public function assignPermissions(Request $request, Role $role)
    {
        $validated = $request->validate([
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role->permissions()->sync($validated['permissions']);

        activity()
            ->causedBy($request->user())
            ->performedOn($role)
            ->log('Updated permissions for role: ' . $role->display_name);

        return response()->json($role->load('permissions'));
    }
}
