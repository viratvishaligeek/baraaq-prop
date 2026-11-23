<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(RolePermissionSeeder::class);

        $email = config('cms.admin.email');
        $password = config('cms.admin.password');

        if ($email && $password) {
            $user = User::query()->firstOrCreate(
                ['email' => $email],
                [
                    'name' => config('cms.admin.name', 'Site Administrator'),
                    'password' => Hash::make($password),
                    'is_active' => true,
                ]
            );

            $administratorId = Role::query()->where('slug', 'administrator')->value('id');

            if ($administratorId) {
                $user->roles()->syncWithoutDetaching([$administratorId]);
            }
        }

        $this->call([
            HomepageTemplateSeeder::class,
            DefaultThemeSeeder::class,
            BuraaqhContentSeeder::class,
            BuraaqhDemoPropertySeeder::class,
        ]);
    }
}
