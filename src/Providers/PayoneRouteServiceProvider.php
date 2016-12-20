<?php

namespace Payone\Providers;

use Plenty\Plugin\RouteServiceProvider;
use Plenty\Plugin\Routing\Router;

class PayoneRouteServiceProvider extends RouteServiceProvider
{
    public function map(Router $router)
    {
        $router->get('payone/config', 'Payone\Controllers\ConfigController@index');
    }
}
