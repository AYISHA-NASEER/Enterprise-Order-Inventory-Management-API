<?php

namespace App\Models;

use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

#[Fillable(['name', 'email', 'password', 'role', 'is_active'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,   // Enum casting
            'is_active' => 'boolean',
        ];
    }

    // Relationships
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    // Role helpers
    public function isAdmin(): bool
    {
        return $this->role === UserRole::Admin;
    }

    public function isManager(): bool
    {
        return $this->role === UserRole::Manager;
    }

    public function isWarehouse(): bool
    {
        return $this->role === UserRole::Warehouse;
    }

    public function isCustomer(): bool
    {
        return $this->role === UserRole::Customer;
    }

    public function hasRole(UserRole|string ...$roles): bool
    {
        $current = $this->role instanceof UserRole
            ? $this->role->value
            : $this->role;

        foreach ($roles as $role) {
            $value = $role instanceof UserRole ? $role->value : $role;
            if ($current === $value) {
                return true;
            }
        }

        return false;
    }
}