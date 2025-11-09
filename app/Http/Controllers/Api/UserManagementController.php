<?php

namespace App\Http\Controllers\Api;

use App\Models\Role;
use App\Models\User;
use App\Models\Permission;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with(['roles', 'permissions']);

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->has('role')) {
            $query->whereHas('roles', function ($q) use ($request) {
                $q->where('name', $request->role);
            });
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        return response()->json($query->paginate($request->get('per_page', 15)));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => ['required', 'confirmed', Password::min(8)],
            'phone' => 'nullable|string|max:50',
            'job_title' => 'nullable|string|max:100',
            'department' => 'nullable|string|max:100',
            'status' => 'nullable|in:active,inactive',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id',
        ]);

        $user = User::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'] ?? null,
            'job_title' => $validated['job_title'] ?? null,
            'department' => $validated['department'] ?? null,
            'status' => $validated['status'] ?? 'active',
        ]);

        if (!empty($validated['roles'])) {
            $user->roles()->attach($validated['roles']);
        }

        activity()
            ->causedBy($request->user())
            ->performedOn($user)
            ->log('Created user: ' . $user->full_name);

        return response()->json($user->load('roles'), 201);
    }

    public function show(User $user)
    {
        return response()->json($user->load(['roles.permissions', 'permissions']));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:50',
            'job_title' => 'nullable|string|max:100',
            'department' => 'nullable|string|max:100',
            'status' => 'nullable|in:active,inactive',
        ]);

        $user->update($validated);

        activity()
            ->causedBy($request->user())
            ->performedOn($user)
            ->log('Updated user: ' . $user->full_name);

        return response()->json($user->load('roles'));
    }

    public function destroy(Request $request, User $user)
    {
        if ($user->id === $request->user()->id) {
            return response()->json([
                'message' => 'You cannot delete your own account'
            ], 400);
        }

        activity()
            ->causedBy($request->user())
            ->performedOn($user)
            ->log('Deleted user: ' . $user->full_name);

        $user->delete();

        return response()->json(null, 204);
    }

    public function assignRoles(Request $request, User $user)
    {
        $validated = $request->validate([
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,id',
        ]);

        $user->roles()->sync($validated['roles']);

        activity()
            ->causedBy($request->user())
            ->performedOn($user)
            ->log('Assigned roles to user: ' . $user->full_name);

        return response()->json($user->load('roles'));
    }

    public function removeRole(Request $request, User $user, Role $role)
    {
        $user->removeRole($role);

        activity()
            ->causedBy($request->user())
            ->performedOn($user)
            ->log("Removed role '{$role->name}' from user: " . $user->full_name);

        return response()->json($user->load('roles'));
    }

    public function givePermission(Request $request, User $user)
    {
        $validated = $request->validate([
            'permission' => 'required|exists:permissions,name',
            'granted' => 'nullable|boolean',
        ]);

        $user->givePermissionTo(
            $validated['permission'],
            $validated['granted'] ?? true
        );

        activity()
            ->causedBy($request->user())
            ->performedOn($user)
            ->log("Granted permission '{$validated['permission']}' to user: " . $user->full_name);

        return response()->json($user->load('permissions'));
    }

    public function revokePermission(Request $request, User $user)
    {
        $validated = $request->validate([
            'permission' => 'required|exists:permissions,name',
        ]);

        $user->revokePermissionTo($validated['permission']);

        activity()
            ->causedBy($request->user())
            ->performedOn($user)
            ->log("Revoked permission '{$validated['permission']}' from user: " . $user->full_name);

        return response()->json($user->load('permissions'));
    }

    public function updatePassword(Request $request, User $user)
    {
        $validated = $request->validate([
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        activity()
            ->causedBy($request->user())
            ->performedOn($user)
            ->log('Changed password for user: ' . $user->full_name);

        return response()->json(['message' => 'Password updated successfully']);
    }

    public function getUserPermissions(User $user)
    {
        return response()->json([
            'all_permissions' => $user->getAllPermissions(),
            'role_permissions' => Permission::whereHas('roles', function ($query) use ($user) {
                $query->whereIn('roles.id', $user->roles->pluck('id'));
            })->get(),
            'direct_permissions' => $user->permissions,
        ]);
    }
}