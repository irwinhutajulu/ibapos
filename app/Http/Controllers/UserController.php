<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UserController extends Controller
{
    public function index()
    {
        $showTrashed = request()->boolean('show_trashed');
        $query = User::with('roles');
        if ($showTrashed) {
            $query = $query->withTrashed();
        }
        $users = $query->paginate(20)->appends(['show_trashed' => $showTrashed]);
        return view('users.index', compact('users'));
    }

    public function restore(Request $request, $id)
    {
        $user = User::withTrashed()->findOrFail($id);
        if (!$user->trashed()) {
            if ($request->wantsJson() || $request->ajax() || $request->expectsJson()) {
                return response()->json(['message' => 'User is not deleted'], 400);
            }
            return redirect()->route('admin.users.index')->with('error', 'User is not deleted');
        }

        $user->restore();

        if ($request->wantsJson() || $request->ajax() || $request->expectsJson()) {
            return response()->json(['message' => 'User restored']);
        }

        return redirect()->route('admin.users.index')->with('success', 'User restored');
    }

    public function create()
    {
    $roles = Role::all();
    $permissions = Permission::all();
    return view('users.form', ['user' => new User(), 'roles' => $roles, 'permissions' => $permissions]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'nullable|string|min:6|confirmed',
            'roles' => 'array',
            'roles.*' => 'integer|exists:roles,id',
            'permissions' => 'array',
            'permissions.*' => 'string|exists:permissions,name',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => isset($data['password']) ? Hash::make($data['password']) : Hash::make(str()->random(12)),
        ]);

        if (!empty($data['roles'])) {
            $user->syncRoles($data['roles']);
        }
        // sync direct permissions if provided
        if (!empty($data['permissions'])) {
            $user->syncPermissions($data['permissions']);
        }

        return redirect()->route('admin.users.index')->with('success','User created');
    }

    public function edit(User $user)
    {
    $roles = Role::all();
    $permissions = Permission::all();
    return view('users.form', compact('user','roles','permissions'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6|confirmed',
            'roles' => 'array',
            'roles.*' => 'integer|exists:roles,id',
            'permissions' => 'array',
            'permissions.*' => 'string|exists:permissions,name',
        ]);

        $user->name = $data['name'];
        $user->email = $data['email'];
        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }
        $user->save();

    $user->syncRoles($data['roles'] ?? []);
    // sync direct permissions
    $user->syncPermissions($data['permissions'] ?? []);

        return redirect()->route('admin.users.index')->with('success','User updated');
    }

    public function destroy(Request $request, User $user)
    {
        try {
            $user->delete();

            if ($request->wantsJson() || $request->ajax() || $request->expectsJson()) {
                return response()->json(['message' => 'User deleted']);
            }

            return redirect()->route('admin.users.index')->with('success','User deleted');
        } catch (\Illuminate\Database\QueryException $e) {
            // Likely a foreign key constraint (related records). Return a friendlier message.
            logger()->warning('User delete failed: ' . $e->getMessage());
            $msg = 'Cannot delete user: related records exist';
            if ($request->wantsJson() || $request->ajax() || $request->expectsJson()) {
                return response()->json(['message' => $msg], 409);
            }
            return redirect()->route('admin.users.index')->with('error', $msg);
        } catch (\Exception $e) {
            logger()->error('User delete error: ' . $e->getMessage());
            $msg = 'Failed to delete user';
            if ($request->wantsJson() || $request->ajax() || $request->expectsJson()) {
                return response()->json(['message' => $msg], 500);
            }
            return redirect()->route('admin.users.index')->with('error', $msg);
        }
    }
}
