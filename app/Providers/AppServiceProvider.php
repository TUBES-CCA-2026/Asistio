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
        Paginator::defaultView('pagination::asistio');
        Paginator::defaultSimpleView('pagination::asistio');

        // Kirim daftar kelas ke sidebar asisten tanpa mengubah controller
        \Illuminate\Support\Facades\View::composer(
            'layouts.partials.sidebar',
            function ($view) {
                if (auth()->check() && auth()->user()->role_name === 'asisten') {
                    $asisten = auth()->user()->asisten;
                    $sidebarKelas = $asisten
                        ? $asisten->semuaPraktikum()->sortBy('nama_kelas')
                        : collect();
                } else {
                    $sidebarKelas = collect();
                }
                $view->with('sidebarKelas', $sidebarKelas);
            }
        );
    }
}