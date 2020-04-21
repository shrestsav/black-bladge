<?php

use App\Permission;
use App\Role;
use App\User;
use App\UserDetail;
use Illuminate\Database\Seeder;

class UserAndRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->delete();

        // Create Super Admin Role and Super Admin User
        $role = [
            'name'         => 'superAdmin', 
            'display_name' => 'Super Admin', 
            'description'  => 'Super Admin Role'
        ];
        $role = Role::create($role);
        $permission = Permission::get();
        foreach ($permission as $key => $value) {
            $role->attachPermission($value);
        }
        $user = [
            'fname'    => 'Super', 
            'lname'    => 'Admin', 
            'email'    => 'superadmin@admin.com', 
            'password' => Hash::make('admin12345')
        ];
        $user = User::create($user);
        $user->attachRole($role);

        //Add Customer and Driver Roles
        $other_roles = [
            [
                'name' => 'customer', 
                'display_name' => 'Customer', 
                'description' => 'Customer Level Role'
            ],
            [
                'name' => 'driver', 
                'display_name' => 'Driver', 
                'description' => 'Driver Level Role'
            ],
        ];
        
        foreach ($other_roles as $key => $value) {
            Role::create($value);
        }

        // Create Test Customer and Attach Role
        $testCustomer = [
            'fname' => 'Customer', 
            'lname' => 'Test', 
            'email' => 'customer@bladge.com', 
            'phone' => 'CUSTOMER'
        ];
        $testCustomer = User::create($testCustomer);
        
        $testCustomer->attachRole(Role::where('name','customer')->first()->id);
        
        // Create Test Driver and Attach Role
        $testDriver = [
            'fname'    => 'Driver', 
            'lname'    => 'Test', 
            'username' => 'DRIVER', 
            'password' => Hash::make('1234')
        ];
        $testDriver = User::create($testDriver);
       
        $testDriver->attachRole(Role::where('name','driver')->first()->id);
    }
}
