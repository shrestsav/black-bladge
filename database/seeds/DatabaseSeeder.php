<?php

use App\AppDefault;
use App\Category;
use App\MainArea;
use App\Permission;
use App\Role;
use App\Service;
use App\User;
use App\UserDetail;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
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
            'phone' => '+971999999999'
        ];
        $testCustomer = User::create($testCustomer);
        $testCustomerDetails = UserDetail::create([
                                'user_id' => $testCustomer->id,
                                'referral_id' => 'YUD09EFG'
                            ]);
        $testCustomer->attachRole(Role::where('name','customer')->first()->id);
        
        // Create Test Driver and Attach Role
        $testDriver = [
            'fname' => 'Driver', 
            'lname' => 'Test', 
            'email' => 'driver@bladge.com', 
            'phone' => '+971985112417',
        ];
        $testDriver = User::create($testDriver);
        $testDriverDetails = UserDetail::create([
                                'user_id' => $testDriver->id,
                                'area_id' => 1,
                                'referral_id' => 'TRD76EFG'
                            ]);
        $testDriver->attachRole(Role::where('name','driver')->first()->id);

        //Add Services
        $services = [
            [
                'name'        => 'Press Only', 
                'price'       => '40', 
                'description' => 'Clothes will be Pressed Only',
                'status'      => 1
            ],
            [
                'name'        => 'Clean & Press', 
                'price'       => '50', 
                'description' => 'Clothes will be cleaned and then pressed',
                'status'      => 1
            ],
            [
                'name'        => 'Wash & Press', 
                'price'       => '60', 
                'description' => 'Clothes will be washed and then pressed',
                'status'      => 1
            ],
        ];

        foreach ($services as $service) {
            Service::create($service);
        }        

        //Add Categories
        $categories = [
            [
                'name'        => 'Tops', 
                'icon'       => 'ico', 
                'description' => 'Top clothings like T-Shirt, Shirt, Blouse, Tops and what else',
                'status'      => 1
            ],
            [
                'name'        => 'Bottoms', 
                'icon'       => 'ico', 
                'description' => 'Bottom clothings like Pant, Skirt, Half Pant',
                'status'      => 1
            ],
            [
                'name'        => 'Undergarments', 
                'icon'       => 'ico', 
                'description' => '',
                'status'      => 1
            ],
            [
                'name'        => 'Coats', 
                'icon'       => 'ico', 
                'description' => '',
                'status'      => 1
            ],
            [
                'name'        => 'Miscellaneous', 
                'icon'       => 'ico', 
                'description' => 'Clothes will be washed and then pressed',
                'status'      => 1
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        } 
    }
}
