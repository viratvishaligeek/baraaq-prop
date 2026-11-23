<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'dashboard.view' => 'View dashboard',
            'analytics.view' => 'View analytics',
        ];

        $modules = [
            'properties' => 'Properties',
            'posts' => 'Blog posts',
            'pages' => 'Pages',
            'categories' => 'Categories',
            'banners' => 'Banners',
            'team' => 'Team members',
            'services' => 'Services',
            'testimonials' => 'Testimonials',
            'comments' => 'Comments',
            'menus' => 'Navigation menus',
            'media' => 'Media library',
            'enquiries' => 'Enquiries',
            'newsletters' => 'Newsletter subscribers',
            'settings' => 'Website settings',
            'users' => 'Users and roles',
            'tags' => 'Blog tags',
            'gallery' => 'Gallery items',
            'faqs' => 'Frequently asked questions',
            'neighborhood-guides' => 'Neighborhood guides',
        ];

        foreach ($modules as $slug => $label) {
            $permissions[$slug.'.manage'] = 'Manage '.$label;

            foreach (['view' => 'View', 'create' => 'Create', 'edit' => 'Edit', 'delete' => 'Delete', 'publish' => 'Publish'] as $action => $verb) {
                $permissions[$slug.'.'.$action] = $verb.' '.$label;
            }
        }

        foreach ($permissions as $slug => $name) {
            Permission::updateOrCreate(['slug' => $slug], [
                'name' => $name,
                'group' => strtok($slug, '.'),
            ]);
        }

        $administrator = Role::firstOrCreate(['slug' => 'administrator'], [
            'name' => 'Administrator',
            'description' => 'Full CMS access',
        ]);
        $administrator->permissions()->syncWithoutDetaching(Permission::pluck('id')->all());

        $editor = Role::firstOrCreate(['slug' => 'editor'], [
            'name' => 'Editor',
            'description' => 'Content editing access',
        ]);
        $editor->permissions()->syncWithoutDetaching(Permission::whereIn('slug', [
            'dashboard.view',
            'posts.view', 'posts.create', 'posts.edit', 'posts.publish',
            'pages.view', 'pages.create', 'pages.edit', 'pages.publish',
            'categories.view', 'categories.create', 'categories.edit',
            'media.manage',
        ])->pluck('id')->all());
    }
}
