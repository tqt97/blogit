<?php

namespace Modules\AccessControls\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index()
    {
        return Inertia::render(
            'roles/index',
            [
                'roles' => Role::with('permissions')->paginate(10)->through(function ($role) {
                    return [
                        'id' => $role->id,
                        'name' => $role->name,
                        'permissions' => $role->permissions->map(function ($permission) {
                            return [
                                'id' => $permission->id,
                                'name' => $permission->name,
                                'description' => $permission->description,
                                'created_at' => $permission->created_at->format('d-m-Y'),
                                'updated_at' => $permission->updated_at->format('d-m-Y'),
                            ];
                        }),
                    ];
                }),
            ]
        );
    }

    public function create()
    {
        return Inertia::render(
            'roles/create',
            [
                'permissions' => Permission::select('id', 'name', 'description')->get(),
            ]
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:roles,name',
            'description' => 'nullable|string',
            'permissions' => 'array',
            'permissions.*' => 'string|exists:permissions,name',
        ]);

        $role = Role::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
        ]);

        if ($request->has('permissions')) {
            $role->syncPermissions($validated['permissions']);
        }

        return to_route('roles.index')->with('message', 'Role created successfully.');
    }

    public function edit(Role $role)
    {
        return Inertia::render(
            'roles/edit',
            [
                'role' => [
                    'id' => $role->id,
                    'name' => $role->name,
                    'description' => $role->description,
                    'permissions' => $role->permissions->map(function ($permission) {
                        return [
                            'id' => $permission->id,
                            'name' => $permission->name,
                            'description' => $permission->description,
                        ];
                    }),
                ],
                'permissions' => Permission::select('id', 'name', 'description')->get(),
            ]
        );
    }

    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:roles,name,'.$role->id,
            'description' => 'nullable|string',
            'permissions' => 'array',
            'permissions.*' => 'string|exists:permissions,name',
        ]);

        $role->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
        ]);

        if ($request->has('permissions')) {
            $role->syncPermissions($validated['permissions']);
        }

        return to_route('roles.index')->with('message', 'Role updated successfully.');
    }

    public function destroy(Role $role)
    {
        $role->delete();

        return to_route('roles.index')->with('message', 'Role deleted successfully.');
    }
}
