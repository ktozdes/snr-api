<?php

namespace App\Providers;

use App\Models\RolePermission;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Laravel\Passport\Passport;
use Illuminate\Support\Str;

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

        $permissions = RolePermission::all();
        foreach ($permissions as $singlePermission) {
            Gate::define(Str::lower($singlePermission->name), function ($user, $action) use ($permissions, $singlePermission) {
                $tmpPermission = $permissions
                    ->where('name', $singlePermission->name)
                    ->where('user_role_id', $user->user_role_id)
                    ->first();
                $result = RolePermission::checkPermission($action,
                    (isset($tmpPermission->permissions) ? $tmpPermission->permissions : 0)
                );
                //echo $singlePermission->name. '--' . $user->user_role_id . '--'.$action .'--'. $tmpPermission->permissions. '=='. $result;
                return $result;
            });
        }
    }
}
