<?php

namespace App\Providers;

use App\Repositories\Department\DepartmentRepository;
use App\Repositories\Department\DepartmentRepositoryInterface;
use App\Repositories\Meeting\MeetingRepository;
use App\Repositories\Meeting\MeetingRepositoryInterface;
use App\Repositories\MeetingAttendee\MeetingAttendeeRepository;
use App\Repositories\MeetingAttendee\MeetingAttendeeRepositoryInterface;
use App\Repositories\Message\MessageRepository;
use App\Repositories\Message\MessageRepositoryInterface;
use App\Repositories\Shift\ShiftRepository;
use App\Repositories\Shift\ShiftRepositoryInterface;
use App\Repositories\Systemtime\SystemtimeRepository;
use App\Repositories\Systemtime\SystemtimeRepositoryInterface;
use App\Repositories\TimeKeeping\TimeKeepingRepository;
use App\Repositories\TimeKeeping\TimeKeepingRepositoryInterface;
use App\Repositories\User\UserRepository;
use App\Repositories\User\UserRepositoryInterface;
use App\Repositories\UserDeviceToken\UserDeviceTokenRepository;
use App\Repositories\UserDeviceToken\UserDeviceTokenRepositoryInterface;
use App\Services\Department\DepartmentService;
use App\Services\Department\DepartmentServiceInterface;
use App\Services\Meeting\MeetingService;
use App\Services\Meeting\MeetingServiceInterface;
use App\Services\MeetingAttendee\MeetingAttendeeService;
use App\Services\MeetingAttendee\MeetingAttendeeServiceInterface;
use App\Services\Message\MessageService;
use App\Services\Message\MessageServiceInterface;
use App\Services\Shift\ShiftService;
use App\Services\Shift\ShiftServiceInterface;
use App\Services\Systemtime\SystemtimeService;
use App\Services\Systemtime\SystemtimeServiceInterface;
use App\Services\TimeKeeping\TimekeepingService;
use App\Services\TimeKeeping\TimekeepingServiceInterface;
use App\Services\User\UserService;
use App\Services\User\UserServiceInterface;
use App\Services\UserDeviceToken\UserDeviceTokenService;
use App\Services\UserDeviceToken\UserDeviceTokenServiceInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //Repository
        $this->app->singleton(DepartmentRepositoryInterface::class, DepartmentRepository::class);
        $this->app->singleton(MessageRepositoryInterface::class, MessageRepository::class);
        $this->app->singleton(ShiftRepositoryInterface::class, ShiftRepository::class);
        $this->app->singleton(TimeKeepingRepositoryInterface::class, TimeKeepingRepository::class);
        $this->app->singleton(UserRepositoryInterface::class, UserRepository::class);
        $this->app->singleton(UserDeviceTokenRepositoryInterface::class, UserDeviceTokenRepository::class);
        $this->app->singleton(SystemtimeRepositoryInterface::class, SystemtimeRepository::class);
        $this->app->singleton(MeetingRepositoryInterface::class, MeetingRepository::class);
        $this->app->singleton(MeetingAttendeeRepositoryInterface::class, MeetingAttendeeRepository::class);

        //Service
        $this->app->singleton(DepartmentServiceInterface::class, DepartmentService::class);
        $this->app->singleton(MessageServiceInterface::class, MessageService::class);
        $this->app->singleton(ShiftServiceInterface::class, ShiftService::class);
        $this->app->singleton(TimekeepingServiceInterface::class, TimekeepingService::class);
        $this->app->singleton(UserServiceInterface::class, UserService::class);
        $this->app->singleton(UserDeviceTokenServiceInterface::class, UserDeviceTokenService::class);
        $this->app->singleton(SystemtimeServiceInterface::class, SystemtimeService::class);
        $this->app->singleton(MeetingServiceInterface::class, MeetingService::class);
        $this->app->singleton(MeetingAttendeeServiceInterface::class, MeetingAttendeeService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
