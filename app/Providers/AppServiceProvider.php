<?php

namespace App\Providers;

use App\Adapters\PythonParserAdapter;
use App\Interfaces\ParserInterface;
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
        $this->app->bind(ParserInterface::class, function ($app) {
            switch ( config('app.api', 'python') ) {
                case 'python':
                    return new PythonParserAdapter();
                default:
                    throw new \RuntimeException("Unknown API");
            }
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
