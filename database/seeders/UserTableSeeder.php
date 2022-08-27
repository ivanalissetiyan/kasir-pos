<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;


class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //create user
        $user = User::create([
            'name' => 'Admin',
            'email' => 'Admin@gmail.com',
            'password' => bcrypt('12345678'),
        ]);

        // Get all Permissions
        $permissions = Permission::all();

        // Get role Admin
        $role = Role::find(1);

        // Assign Permission to role
        $role->syncPermissions($permissions);

        //Assign Role to User
        $user->assignRole($role);
    }
}
