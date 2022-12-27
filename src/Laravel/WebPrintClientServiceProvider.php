<?php

namespace KDuma\WebPrintClient\Laravel;

use Illuminate\Support\ServiceProvider;
use KDuma\WebPrintClient\HttpClient\GuzzleHttp7Client;
use KDuma\WebPrintClient\HttpClient\HttpClientInterface;
use KDuma\WebPrintClient\WebPrintApi;
use KDuma\WebPrintClient\WebPrintApiInterface;

class WebPrintClientServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../../config/config.php' => config_path('webprint.php'),
            ], 'config');
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../../config/config.php', 'webprint');

        $this->app->bind(HttpClientInterface::class, fn () => new GuzzleHttp7Client(
            config('webprint.endpoint'),
            config('webprint.token'),
        ));

        $this->app->bind(LaravelWebPrintApiInterface::class, LaravelWebPrintApi::class);
        $this->app->bind(WebPrintApiInterface::class, LaravelWebPrintApi::class);
        $this->app->bind(WebPrintApi::class, LaravelWebPrintApi::class);
    }
}
