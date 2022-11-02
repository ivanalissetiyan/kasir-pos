<?php

namespace App\Http\Controllers\Apps;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Inertia\Inertia;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //get users menggunakan eloquent
        $users = User::when(request()->q, function ($users) {
            $users = $users->where('name', 'like', '%' . request()->q . '%');
        })->with('roles')->latest()->paginate(5);

        // Render with inertia
        return Inertia('Apps/Users/Index', [
            'users' => $users,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //get Roles
        $roles = Role::all();

        // render with inertia
        return Inertia::render('Apps/Users/Create', [
            'roles' => $roles
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
        /**
         * Validate request
         */
        $this->validate($request, [
            'name'     => 'required',
            'email'    => 'required|unique:users',
            'password' => 'required|confirmed'
        ]);

        /**
         * Create user
         */
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => bcrypt($request->password)
        ]);

        //assign roles to user
        $user->assignRole($request->roles);

        //redirect
        return redirect()->route('apps.users.index');
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
        // Get User
        $user = User::with('roles')->findOrFail($id);

        // Get Roles All
        $roles = Role::all();

        // Rander with inertia
        return Inertia::render('Apps/Users/Edit', [
            'user' => $user,
            'roles' => $roles,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        // Validate reqeust
        $this->validate($request, [
            'name'      => 'required',
            'email' => 'required|unique:users,email,' . $user->id,
            'password' => 'nullable|confirmed',
        ]);

        // Check Password is empty
        if ($request->password == '') {

            // Update data tanpa password
            $user->update([
                'name' => $request->name,
                'email' => $request->email
            ]);
        } else {

            // Update data dengan password baru
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password)
            ]);
        }

        // Assign roles to users
        $user->syncRoles($request->roles);

        // redirect
        return redirect()->route('apps.users.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // Find User
        $user = User::findOrFail($id);

        // Delete user
        $user->delete();

        // redirect
        return redirect()->route('apps.users.index');
    }
}
