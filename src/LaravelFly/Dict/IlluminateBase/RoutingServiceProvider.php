<?php

namespace LaravelFly\Dict\IlluminateBase;

use Symfony\Component\Routing\Router;

class RoutingServiceProvider extends \Illuminate\Routing\RoutingServiceProvider
{

    /**
     * Override
     */
    protected function registerRouter()
    {
        $this->app->singleton('router', function ($app) {
            return new Router($app['events'], $app);
        });
    }
}