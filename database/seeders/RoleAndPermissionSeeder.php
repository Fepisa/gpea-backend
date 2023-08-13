<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Permission::create(['name' => 'manage-users']);
        Permission::create(['name' => 'manage-laudos']);
        Permission::create(['name' => 'view-laudos']);

        $adminRole = Role::create(['name' => 'Admin']);
        $viewerRole = Role::create(['name' => 'Viewer']);

        $adminRole->givePermissionTo([
            'manage-users',
            'manage-laudos',
            'view-laudos',
        ]);

        $viewerRole->givePermissionTo([
            'view-laudos'
        ]);
        
    }
}
