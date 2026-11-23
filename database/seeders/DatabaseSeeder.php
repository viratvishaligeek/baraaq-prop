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
        $this->call([
            RolePermissionSeeder::class,
            HomepageTemplateSeeder::class,
            DefaultThemeSeeder::class,
        ]);

        $email = env('CMS_ADMIN_EMAIL');
        $password = env('CMS_ADMIN_PASSWORD');

        if ($email && $password) {
            $user = User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => env('CMS_ADMIN_NAME', 'Site Administrator'),
                    'password' => Hash::make($password),
                    'is_active' => true,
                ]
            );

            $administratorId = Role::query()->where('slug', 'administrator')->value('id');

            if ($administratorId) {
                $user->roles()->syncWithoutDetaching([$administratorId]);
            }
        }

        // Demo records are useful in local/test environments but must never be
        // inserted into a production database by an ordinary db:seed invocation.
        // Production/staging operators can opt in explicitly with SEED_DEMO_CONTENT=true.
        if (app()->environment(['local', 'testing']) || filter_var(env('SEED_DEMO_CONTENT', false), FILTER_VALIDATE_BOOLEAN)) {
            $this->call([PillerDemoContentSeeder::class]);
        }
    }
}
