<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use App\Mail\VerifyMail;
use Gate;
use http\Url;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Mail\Mailable;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\TimeKeeping;
use App\Policies\TimeKeepingPolicy;
use App\Models\User;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Mail;

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
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        ResetPassword::createUrlUsing(function (User $user, string $token) {
            return 'http://localhost:5173/reset-password/'.$token;
        });
        $this->registerPolicies();
    }
}
