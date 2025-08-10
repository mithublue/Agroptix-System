<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', User::class);

        // Collect filters
        $filters = $request->only(['q', 'role', 'is_active', 'is_approved', 'per_page']);

        // Base query: show all users (bypass global scope) with roles eager loaded
        $query = User::withoutGlobalScope('activeApproved')->with('roles');

        // Search by name OR email
        if ($request->filled('q')) {
            $q = trim($request->q);
            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%");
            });
        }

        // Filter by role (Spatie roles)
        if ($request->filled('role')) {
            $roleId = (int) $request->role;
            $query->whereHas('roles', function ($r) use ($roleId) {
                $r->where('roles.id', $roleId);
            });
        }

        // Filter by activity status
        if ($request->filled('is_active') && in_array($request->is_active, ['0', '1'], true)) {
            $query->where('is_active', (int) $request->is_active);
        }

        // Filter by approval status (1, 0, or null)
        if ($request->filled('is_approved')) {
            if ($request->is_approved === 'null') {
                $query->whereNull('is_approved');
            } elseif (in_array($request->is_approved, ['0', '1'], true)) {
                $query->where('is_approved', (int) $request->is_approved);
            }
        }

        $perPage = (int) ($request->input('per_page') ?? 10);
        if (!in_array($perPage, [10, 25, 50, 100], true)) {
            $perPage = 10;
        }
        $users = $query->latest()->paginate($perPage)->withQueryString();
        $roles = Role::all(['id', 'name']);

        return view('admin.users.index', compact('users', 'roles', 'filters'));
    }

    public function create()
    {
        Gate::authorize('create', User::class);
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', User::class);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'roles' => ['required', 'array'],
            'roles.*' => ['exists:roles,id']
        ]);

        $user = User::withoutGlobalScope('activeApproved')->create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $user->roles()->sync($validated['roles']);

        return redirect()->route('admin.users.index')
            ->with('success', 'User created successfully');
    }

    public function edit($id)
    {
        $user = User::withoutGlobalScope('activeApproved')->findOrFail($id);
        Gate::authorize('update', $user);
        $roles = Role::all();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, $id)
    {
        $user = User::withoutGlobalScope('activeApproved')->findOrFail($id);
        $this->authorize('update', $user);
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'roles' => ['required', 'array'],
            'roles.*' => ['exists:roles,id']
        ]);
        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'] ? Hash::make($validated['password']) : $user->password,
        ]);
        $user->roles()->sync($validated['roles']);
        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully');
    }

    public function destroy($id)
    {
        $user = User::withoutGlobalScope('activeApproved')->findOrFail($id);
        $this->authorize('delete', $user);
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'You cannot delete your own account');
        }
        $user->delete();
        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully');
    }

    public function updateStatus(Request $request, $id)
    {
        $user = User::withoutGlobalScope('activeApproved')->findOrFail($id);
        $this->authorize('update', $user);
        if (!auth()->user()->can('manage_users')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $request->validate([
            'field' => 'required|in:is_active,is_approved',
            'value' => 'required|in:0,1,null',
        ]);
        $field = $request->field;
        $value = $request->value === 'null' ? null : (int)$request->value;
        $user->$field = $value;
        $user->save();
        return response()->json(['success' => true, 'field' => $field, 'value' => $value]);
    }

    public function bulkDestroy(Request $request)
    {
        // Only allow for users with manage_users permission
        if (!auth()->user()->can('manage_users')) {
            return redirect()->back()->with('error', 'You are not authorized to perform bulk deletion.');
        }

        $data = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['integer', 'distinct']
        ]);

        $ids = collect($data['ids'])
            ->filter() // remove nulls
            ->unique()
            ->reject(fn($id) => (int)$id === (int)auth()->id()) // don't allow deleting self
            ->values();

        if ($ids->isEmpty()) {
            return redirect()->back()->with('error', 'No valid users selected for deletion.');
        }

        // Fetch users without global scopes and delete
        $users = User::withoutGlobalScope('activeApproved')
            ->whereIn('id', $ids)
            ->get();

        // Optional: enforce policy per user
        $deletable = 0;
        foreach ($users as $user) {
            if (auth()->user()->can('delete', $user)) {
                $user->delete();
                $deletable++;
            }
        }

        if ($deletable === 0) {
            return redirect()->back()->with('error', 'You do not have permission to delete the selected users.');
        }

        return redirect()->back()->with('success', $deletable . ' user' . ($deletable === 1 ? '' : 's') . ' deleted successfully');
    }
}
