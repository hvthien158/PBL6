<?php

namespace App\Policies;

use App\Common\Role;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function adminView(User $user): bool
    {
        return ($user->role == Role::ADMIN) ? true : false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        //
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(): bool
    {
        return (auth()->user()->role == Role::ADMIN) ? true : false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $loggedInUser): bool
    {   
        return ($loggedInUser->role == Role::ADMIN) ? true : false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $loggedInUser, User $user): bool
    {
        return ($user->role == Role::USER && $loggedInUser->role == Role::ADMIN ) ? true : false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, User $model): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $model): bool
    {
        //
    }
}
