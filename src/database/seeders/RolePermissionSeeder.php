<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Role;
use App\Models\Module;
use App\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // Step 1: Clean old data
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('role_permissions')->truncate();
        DB::table('permissions')->truncate();
        DB::table('modules')->truncate();
        DB::table('roles')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Step 2: Create Roles
        $adminRole = Role::create(['name' => 'Admin']);
        $userRole  = Role::create(['name' => 'User']);

        // Step 3: Create Modules
        $userModule         = Module::create(['name' => 'User Management']);
        $roleModule         = Module::create(['name' => 'Role']);
        $fileTransferModule = Module::create(['name' => 'File Transfer Management']);

        // Step 4: Define permissions
        $actions = ['view','add','edit','delete'];
        $permissions = [];

        foreach ([$userModule,$roleModule,$fileTransferModule] as $module) {
            foreach ($actions as $action) {
                $permissions[] = Permission::create([
                    'module_id' => $module->id,
                    'action'    => $action,
                ]);
            }
        }

        // Step 5: Assign all permissions to Admin
        $adminRole->permissions()->sync(array_column($permissions, 'id'));

        // Step 6: Assign limited permissions to User
        $userPermissions = Permission::where(function ($query) use ($fileTransferModule, $userModule) {
            $query->where('module_id', $fileTransferModule->id)
                  ->orWhere(function ($subQuery) use ($userModule) {
                      $subQuery->where('module_id', $userModule->id)
                               ->where('action', 'view');
                  });
        })->pluck('id')->toArray();

        $userRole->permissions()->sync($userPermissions);
    }
}
