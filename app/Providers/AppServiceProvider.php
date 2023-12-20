<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        Gate::define('isAdmin', function ($user) {
            return $user->role_id == 1;
        });
        Gate::define('isSalesman', function ($user) {
            return $user->role_id == 2;
        });
        Gate::define('isTeamLeader', function ($user) {
            return $user->role_id == 3;
        });

        Gate::define('isAdminOrTeamLeader', function ($user) {
            return in_array($user->role_id, [1, 3]);
        });

        Gate::define('isSalesmanOrTeamLeader', function ($user) {
            return in_array($user->role_id, [2, 3]);
        });
    }
}
