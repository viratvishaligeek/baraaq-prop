<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'phone', 'password', 'is_active'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    public function hasPermission(string $slug): bool
    {
        $this->loadMissing('roles.permissions');

        return $this->roles->contains(
            fn($role) => $role->permissions->contains('slug', $slug)
        );
    }

    public function permissionSlugs(): array
    {
        $this->loadMissing('roles.permissions');

        return $this->roles
            ->flatMap(fn($role) => $role->permissions->pluck('slug'))
            ->unique()
            ->values()
            ->all();
    }

    public function isAdministrator(): bool
    {
        $this->loadMissing('roles');

        return $this->roles->contains('slug', 'administrator');
    }
}
