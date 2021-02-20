<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //
        //Passport::routes();
        Passport::tokensExpireIn(now()->addDays(30));
        Passport::refreshTokensExpireIn(now()->addDays(90));
        Passport::personalAccessTokensExpireIn(now()->addMonths(12));

        $this->registerPolicies();

        Gate::define('user', function ($user, $perm_str) {
            return $user->perm($perm_str, config('constants.permissions.User'));
        });

        Gate::define('user-role', function ($user, $perm_str) {
            return $user->perm($perm_str, config('constants.permissions.User Role'));
        });

        Gate::define('post', function ($user, $perm_str) {
            return $user->perm($perm_str, config('constants.permissions.Post'));
        });

        Gate::define('comment', function ($user, $perm_str) {
            return $user->perm($perm_str, config('constants.permissions.Comment'));
        });
    }
}
