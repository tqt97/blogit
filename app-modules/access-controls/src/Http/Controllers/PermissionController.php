<?php

namespace Modules\AccessControls\Http\Controllers;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function index()
    {
        $permissions = Permission::latest()->paginate(15);
        $permissions->getCollection()->transform(function ($permission) {
            return [
                'id' => $permission->id,
                'name' => $permission->name,
                'description' => $permission->description,
                'created_at' => Carbon::parse($permission->created_at)->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::parse($permission->updated_at)->format('Y-m-d H:i:s'),
            ];
        });

        return Inertia::render('permissions/index', [
            'permissions' => $permissions,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name',
        ]);

        Permission::create(['name' => $request->name, 'description' => $request->description]);

        return to_route('permissions.index')->with('message', 'Permission created successfully.');
    }

    public function update(Request $request, Permission $permission)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name,'.$permission->id,
        ]);

        $permission->update(['name' => $request->name, 'description' => $request->description]);

        return to_route('permissions.index')->with('message', 'Permission updated successfully.');
    }

    public function destroy(Permission $permission)
    {
        $permission->delete();

        return to_route('permissions.index')->with('message', 'Permission deleted successfully.');
    }
}
