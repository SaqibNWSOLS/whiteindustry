<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserController extends Controller
{
    
    public function index(Request $request): View
    {
        

        $query = User::with('roles')->whereDoesntHave('roles', function ($q) {
            $q->where('name', 'customer');
        });

        if ($request->has('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('first_name', 'like', "%{$s}%")
                  ->orWhere('last_name', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%");
            });
        }

        $users = $query->paginate($request->get('per_page', 15));
        $roles = Role::all();

        return view('users.index', compact('users', 'roles'));
    }

    public function create(Request $request): View
    {
        
        
        $roles = Role::all();
        return view('users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        

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
            'password' => Hash::make($validated['password']),
            'status' => $validated['status'] ?? 'active',
            'phone' => $validated['phone'] ?? null,
            'job_title' => $validated['job_title'] ?? null,
            'department' => $validated['department'] ?? null,
        ]);

        if (!empty($validated['role_id'])) {
            $role = Role::find($validated['role_id']);
            if ($role) {
                if ($role->name === 'administrator' && !$request->user()->hasRole('administrator')) {
                    return redirect()->back()->withErrors(['role_id' => 'Only administrators can assign the administrator role'])->withInput();
                }
                $user->roles()->sync([$role->id]);
            }
        }

        activity()
            ->causedBy($request->user())
            ->performedOn($user)
            ->log('Created user: ' . $user->email);

        return redirect()->route('users.index')->with('success', 'User created successfully');
    }

    public function show(Request $request, User $user): View
    {
        
        return view('users.show', compact('user'));
    }

    public function edit(Request $request, User $user): View
    {
        
        
        $roles = Role::all();
        return view('users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        

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
            $user->password = Hash::make($validated['password']);
        }

        $user->fill($validated);
        $user->save();

        if (array_key_exists('role_id', $validated)) {
            if ($validated['role_id']) {
                $role = Role::find($validated['role_id']);
                if ($role) {
                    if ($role->name === 'administrator' && !$request->user()->hasRole('administrator')) {
                        return redirect()->back()->withErrors(['role_id' => 'Only administrators can assign the administrator role'])->withInput();
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

        return redirect()->route('users.index')->with('success', 'User updated successfully');
    }

    public function destroy(Request $request, User $user)
    {
        

        if ($user->isAdmin()) {
            return redirect()->back()->with('error', 'Cannot delete administrator user');
        }

        activity()
            ->causedBy($request->user())
            ->performedOn($user)
            ->log('Deleted user: ' . $user->email);

        $user->delete();

        return redirect()->route('users.index')->with('success', 'User deleted successfully');
    }
}