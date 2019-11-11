<?php

namespace Bjornvoesten\CipherSweet;

use Illuminate\Support\ServiceProvider;

class CipherSweetServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     * @throws \Exception
     */
    public function register()
    {
        $this->app->singleton(
            'Bjornvoesten\CipherSweet\Contracts\Encrypter',
            'Bjornvoesten\CipherSweet\Encrypter'
        );

        $this->commands([
            'Bjornvoesten\CipherSweet\Console\Commands\KeyGenerate',
        ]);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/ciphersweet.php',
            'encryption'
        );

        $this->publishes([
            __DIR__ . '/../config/ciphersweet.php' => config_path('ciphersweet.php')
        ], 'ciphersweet-config');
    }
}
