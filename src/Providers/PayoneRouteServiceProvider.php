<?php

namespace Payone\Providers;

use Plenty\Plugin\RouteServiceProvider;
use Plenty\Plugin\Routing\Router;

/**
 * Class PayoneRouteServiceProvider
 */
class PayoneRouteServiceProvider extends RouteServiceProvider
{
    /**
     * @param Router $router
     * @return void
     */
    public function map(Router $router)
    {
        $router->get('payone/config/', 'Payone\Controllers\ConfigController@index');
        $router->any('payone/test/', 'Payone\Controllers\ConfigController@test');
        $router->any('payone/test2/', 'Payone\Controllers\ConfigController@test2');
        $router->any('payone/test3/', 'Payone\Controllers\ConfigController@test3');
        $router->any('payone/test4/', 'Payone\Controllers\ConfigController@test4');
        $router->post('payone/status/', 'Payone\Controllers\StatusController@index');
        $router->get('payone/migrate', 'Payone\Controllers\StatusController@migrate');
    }
}
