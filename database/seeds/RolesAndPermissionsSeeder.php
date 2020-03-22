<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\User;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $superAdmin = Role::create(['name' => 'super-admin']);
        $customer = Role::create(['name' => 'customer']);
        $driver = Role::create(['name' => 'driver']);

        $user = User::create([
            'fname'     =>  'Super',
            'lname'     =>  'Admin',
            'email'     =>  'superadmin@admin.com',
            'password'  =>  Hash::make('admin12345')
        ]);

        $user->assignRole('super-admin');
    }
}
