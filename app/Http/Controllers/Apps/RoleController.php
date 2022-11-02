<?php

namespace App\Http\Controllers\Apps;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Get roles
        $roles = Role::when(request()->q, function ($roles) {
            $roles = $roles->where('name', 'like', '%' . request()->q . '%');
        })->with('permissions')->latest()->paginate(5);

        // Render with inertia
        return Inertia('Apps/Roles/Index', [
            'roles' => $roles,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //get permission all
        $permissions = Permission::all();

        // render with inertia
        return inertia('Apps/Roles/Create', [
            'permissions' => $permissions
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // validate request
        $this->validate($request, [
            'name'      => 'required',
            'permissions' => 'required',
        ]);

        // Create role
        $role = Role::create(['name' => $request->name]);

        // Assign permissions to role
        $role->givePermissionTo($request->permissions);

        // Redirect
        return redirect()->route('apps.roles.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // Get Role
        $role = Role::with('permissions')->findOrFail($id);

        // Get Permissions All
        $permissions = Permission::all();

        // Rander with inertia
        return inertia('Apps/Roles/Edit', [
            'role' => $role,
            'permissions' => $permissions,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Role $role)
    {
        // Validate reqeust
        $this->validate($request, [
            'name' => 'required',
            'permissions' => 'required',
        ]);

        // Update role
        $role->update(['name' => $request->name]);

        // sync permissions
        $role->syncPermissions($request->permissions);

        // redirect
        return redirect()->route('apps.roles.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // Find or file by ID
        $role = Role::findOrFail($id);

        // Delete role
        $role->delete();

        // redirect
        return redirect()->route('apps.roles.index');
    }
}
