<?php

namespace App\Providers;

use App\Services\GeocoderInterface;
use App\Services\YandexGeocoder;
use Illuminate\Http\Client\Factory;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(GeocoderInterface::class, function ($app)
        {
            return new YandexGeocoder(
                $app->make(Factory::class),
                config('services.yandex_geocoder.key'),
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
