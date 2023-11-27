<?php

namespace App\Policies;

use App\Common\Role;
use App\Models\TimeKeeping;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Auth\Access\HandlesAuthorization;
use Carbon\Carbon;

class TimeKeepingPolicy
{
    use HandlesAuthorization;
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->role === Role::ADMIN;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, TimeKeeping $timeKeeping): bool
    {
        //
    }

    /**
     * Determine whether the user can create model.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, TimeKeeping $timeKeeping): bool
    {
        $timezone = 'Asia/Ho_Chi_Minh';
        $currentDate = Carbon::now()->setTimezone($timezone)->toDateString();
        $existingTimeKeeping = TimeKeeping::where('user_id', $user->id)
            ->whereDate('date', $currentDate)
            ->first();
        if ($existingTimeKeeping) {
            if (auth()->user()->role === Role::ADMIN || auth()->user()->id === $existingTimeKeeping->id);
            return true;
        }
        return  false;
    }
    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, TimeKeeping $timeKeeping): bool
    {
        $timezone = 'Asia/Ho_Chi_Minh';
        $currentDate = Carbon::now()->setTimezone($timezone)->toDateString();
        $existingTimeKeeping = TimeKeeping::where('user_id', $user->id)
            ->whereDate('date', $currentDate)
            ->first();
        if ($existingTimeKeeping) {
            if (auth()->user()->role === Role::ADMIN || auth()->user()->id === $existingTimeKeeping->id);
            return true;
        }
        return  false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, TimeKeeping $timeKeeping): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, TimeKeeping $timeKeeping): bool
    {
        //
    }
}
