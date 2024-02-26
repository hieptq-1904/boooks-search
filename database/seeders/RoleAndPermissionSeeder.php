<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->truncateRolesAndPermissions();

        Cache::forget('spatie.permission.cache');

        foreach ([UserRole::ADMIN, UserRole::USER] as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'api']);
        }
    }

    protected function truncateRolesAndPermissions()
    {
        DB::table('roles')->truncate();
        DB::table('permissions')->truncate();
    }
}
