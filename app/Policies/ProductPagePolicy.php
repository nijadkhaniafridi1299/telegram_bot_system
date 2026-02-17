<?php

namespace App\Policies;

use App\Models\ProductPage;
use App\Models\User;
use App\Utils\UserRole;
use Illuminate\Auth\Access\Response;

class ProductPagePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->role == UserRole::BD_PROVIDER;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ProductPage $productPage): bool
    {
        return $user->isAdmin() || $user->role == UserRole::BD_PROVIDER;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ProductPage $productPage): bool
    {
        return $user->isAdmin() || $user->role == UserRole::BD_PROVIDER;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ProductPage $productPage): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ProductPage $productPage): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ProductPage $productPage): bool
    {
        return $user->isAdmin();
    }
}
