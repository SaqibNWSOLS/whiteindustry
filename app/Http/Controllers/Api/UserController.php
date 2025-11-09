<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // middleware may be applied in routes; ensure only admins/managers can access
    protected function authorizeAdminManager(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            // not authenticated
            abort(401, 'Unauthenticated.');
        }

        if (!($user->hasRole('administrator') || $user->hasRole('manager'))) {
            abort(403, 'Forbidden');
        }
    }

    public function index(Request $request)
    {
        $this->authorizeAdminManager($request);

        $query = User::with('roles');
        if ($request->has('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('first_name', 'like', "%{$s}%")
                  ->orWhere('last_name', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%");
            });
        }

        return response()->json($query->paginate($request->get('per_page', 15)));
    }

    public function store(Request $request)
    {
        $this->authorizeAdminManager($request);

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'role_id' => 'nullable|exists:roles,id',
            'status' => 'nullable|in:active,inactive',
            'phone' => 'nullable|string|max:50',
            'job_title' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
        ]);

        $user = User::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'status' => $validated['status'] ?? 'active',
            'phone' => $validated['phone'] ?? null,
            'job_title' => $validated['job_title'] ?? null,
            'department' => $validated['department'] ?? null,
        ]);

        if (!empty($validated['role_id'])) {
            $role = Role::find($validated['role_id']);
            if ($role) {
                // Prevent non-admins from assigning the administrator role
                $assigningAdminRole = $role->name === 'administrator';
                if ($assigningAdminRole && !$request->user()->hasRole('administrator')) {
                    return response()->json(['message' => 'Only administrators can assign the administrator role'], 403);
                }
                $user->roles()->sync([$role->id]);
            }
        }

        activity()
            ->causedBy($request->user())
            ->performedOn($user)
            ->log('Created user: ' . $user->email);

        return response()->json($user->load('roles'), 201);
    }

    public function show(Request $request, User $user)
    {
        $this->authorizeAdminManager($request);
        return response()->json($user->load('roles'));
    }

    public function update(Request $request, User $user)
    {
        $this->authorizeAdminManager($request);

        $validated = $request->validate([
            'first_name' => 'sometimes|required|string|max:255',
            'last_name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8',
            'role_id' => 'nullable|exists:roles,id',
            'status' => 'nullable|in:active,inactive',
            'phone' => 'nullable|string|max:50',
            'job_title' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
        ]);

        if (isset($validated['password']) && $validated['password']) {
            $user->password = $validated['password'];
        }

        $user->fill($validated);
        $user->save();

        if (array_key_exists('role_id', $validated)) {
            if ($validated['role_id']) {
                $role = Role::find($validated['role_id']);
                if ($role) {
                    if ($role->name === 'administrator' && !$request->user()->hasRole('administrator')) {
                        return response()->json(['message' => 'Only administrators can assign the administrator role'], 403);
                    }
                    $user->roles()->sync([$validated['role_id']]);
                }
            } else {
                $user->roles()->detach();
            }
        }

        activity()
            ->causedBy($request->user())
            ->performedOn($user)
            ->log('Updated user: ' . $user->email);

        return response()->json($user->load('roles'));
    }

    public function destroy(Request $request, User $user)
    {
        $this->authorizeAdminManager($request);

        if ($user->isAdmin()) {
            return response()->json(['message' => 'Cannot delete administrator user'], 400);
        }

        activity()
            ->causedBy($request->user())
            ->performedOn($user)
            ->log('Deleted user: ' . $user->email);

        $user->delete();

        return response()->json(null, 204);
    }
}
