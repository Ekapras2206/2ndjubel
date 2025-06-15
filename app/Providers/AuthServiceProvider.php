<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate; // ++ TAMBAHKAN BARIS INI
use App\Models\User; // ++ TAMBAHKAN BARIS INI (Pastikan path ke model User Anda benar)
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies(); // Baris ini biasanya sudah ada, biarkan saja

        // ++ TAMBAHKAN DEFINISI GATE DI SINI ++
        Gate::define('access-admin-panel', function (User $user) {
            return $user->isAdmin(); // Menggunakan metode isAdmin() dari model User Anda
        });
    }
}
