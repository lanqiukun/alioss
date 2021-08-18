<?php

namespace Lanqiukun\Alioss;

use Illuminate\Support\ServiceProvider;

class AliossProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
        $this->app->singleton("alioss_direct",function (){
            return new Direct();
        });
    }
}
