<?php

namespace App\Providers;

use App\Models\TitikWisata;
use App\Observers\TitikWisataObserver;
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
        TitikWisata::observe(TitikWisataObserver::class);
    }
}
