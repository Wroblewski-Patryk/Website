<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Inertia\Inertia;

class RoleController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Roles/Index', [
            'roles' => Role::with('permissions')->paginate(10)
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Roles/Create', [
            'permissions' => Permission::all()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:roles,name',
            'permissions' => 'array'
        ]);

        $role = Role::create(['name' => $request->name]);
        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }

        return redirect()->route('admin.roles.index')->with('success', 'admin.roles.created');
    }

    public function edit(Role $role)
    {
        return Inertia::render('Admin/Roles/Edit', [
            'role' => $role->load('permissions'),
            'permissions' => Permission::all()
        ]);
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|string|unique:roles,name,' . $role->id,
            'permissions' => 'array'
        ]);

        $role->update(['name' => $request->name]);
        $role->syncPermissions($request->permissions);

        return redirect()->route('admin.roles.index')->with('success', 'admin.roles.updated');
    }

    public function destroy(Role $role)
    {
        if ($role->name === 'admin') {
            return back()->with('error', 'admin.roles.cannot_delete_admin');
        }

        $role->delete();
        return redirect()->route('admin.roles.index')->with('success', 'admin.roles.deleted');
    }
}
