<?php

namespace AbuseIO\Findcontact\Cpanel;

use Illuminate\Support\ServiceProvider;

class CpanelServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     * merge the config
     *
     * @return void
     */
    public function boot()
    {
        // Publish config
        $this->mergeConfigFrom(base_path('vendor/abuseio/findcontact-cpanel').'/config/Cpanel.php', 'Findcontact');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}