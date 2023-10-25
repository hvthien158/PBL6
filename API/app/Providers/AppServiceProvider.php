<?php

namespace App\Providers;

use App\Repositories\Department\DepartmentRepository;
use App\Repositories\Department\DepartmentRepositoryInterface;
use App\Repositories\Message\MessageRepository;
use App\Repositories\Message\MessageRepositoryInterface;
use App\Repositories\Shift\ShiftRepository;
use App\Repositories\Shift\ShiftRepositoryInterface;
use App\Repositories\TimeKeeping\TimeKeepingRepository;
use App\Repositories\TimeKeeping\TimeKeepingRepositoryInterface;
use App\Repositories\User\UserRepository;
use App\Repositories\User\UserRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(DepartmentRepositoryInterface::class, DepartmentRepository::class);
        $this->app->singleton(MessageRepositoryInterface::class, MessageRepository::class);
        $this->app->singleton(ShiftRepositoryInterface::class, ShiftRepository::class);
        $this->app->singleton(TimeKeepingRepositoryInterface::class, TimeKeepingRepository::class);
        $this->app->singleton(UserRepositoryInterface::class, UserRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
