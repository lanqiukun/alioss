<?php

namespace Lanqiukun\Alioss;

use Illuminate\Support\ServiceProvider;

class AliossProvider extends ServiceProvider
{
    protected $defer = true;
    
    public function register()
    {
        $this->app->singleton(Direct::class, function(){
            return new Direct;
        });

        $this->app->alias(Direct::class, 'direct');
    }


    public function boot()
    {



    }

    public function provides()
    {
        return [Direct::class, 'direct'];
    }
}
