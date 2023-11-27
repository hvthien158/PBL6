<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;

use App\Models\Department;
use Gate;
use http\Url;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\TimeKeeping;
use App\Policies\TimeKeepingPolicy;
use App\Models\User;
use App\Policies\DepartmentPolicy;
use App\Policies\UserPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        TimeKeeping::class => TimeKeepingPolicy::class,
        User::class => UserPolicy::class,
        Department::class => DepartmentPolicy::class
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        VerifyEmail::toMailUsing(function (object $notifiable, string $url) {
            return (new MailMessage)
                ->subject('Verify Email Address')
                ->line('Click the button below to verify your email address.')
                ->action('Verify Email Address', $url);
        });
        ResetPassword::createUrlUsing(function (User $user, string $token) {
            return 'http://localhost:5173/reset-password/'.$token;
        });
        $this->registerPolicies();
    }
}
