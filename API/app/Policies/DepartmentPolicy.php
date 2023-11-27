<?php

namespace App\Policies;

use App\Common\Role;
use App\Models\Department;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class DepartmentPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     */
    public function viewUser(User $user, Department $department): bool
    {
        return ($user->role == Role::ADMIN && $department->users    ->count() != 0) ? true : false;
    }
    /**
     * @param User $user
     * 
     * @return bool
     */
    public function viewDepartment(User $user): bool
    {
        return $user->role == Role::ADMIN ? true : false;
    }
    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return ($user->role == Role::ADMIN) ? true : false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user): bool
    {
        return ($user->role == Role::ADMIN) ? true : false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Department $department): bool
    {
        return ($user->role == Role::ADMIN && $department->users->count() == 0) ? true : false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Department $department): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Department $department): bool
    {
        //
    }
}
