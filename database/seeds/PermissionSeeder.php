<?php

use Illuminate\Database\Seeder;
use App\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permission = [
            [
                'name' => 'create-role',
                'display_name' => 'Create Role',
                'description' => 'Create New Role'
            ],
        ];
        foreach ($permission as $key => $value) {
            Permission::create($value);
        }
    }
}
