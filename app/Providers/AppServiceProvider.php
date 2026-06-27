<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Gunakan view pagination custom (vendor/pagination/asistio.blade.php)
        // sebagai pengganti default Tailwind, karena project ini tidak memakai Tailwind.
        // Tanpa ini, tombol Previous/Next tampil sebagai ikon SVG raksasa tak ber-style.
        Paginator::defaultView('pagination::asistio');
        Paginator::defaultSimpleView('pagination::asistio');
    }
}