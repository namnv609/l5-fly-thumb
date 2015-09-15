<?php

namespace NNV\L5FlyThumb;

use Illuminate\Support\ServiceProvider;

class L5FlyThumbServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/config.php' => config_path('l5flythumb.php'),
        ], 'config');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['modules.handler', 'modules'];
    }


    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $packageConfigFile = __DIR__ . '/../config/config.php';

        $this->mergeConfigFrom(
            $packageConfigFile, 'l5flythumb'
        );

        $this->app['l5flythumb'] = $this->app->share(
            function ()
            {
                return new L5FlyThumb();
            }
        );
    }
}
