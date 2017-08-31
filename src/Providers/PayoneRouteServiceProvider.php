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
     */
    public function map(Router $router)
    {
        $router->get('payone/config/', 'Payone\Controllers\ConfigController@index');
        $router->any('payone/test/', 'Payone\Controllers\ConfigController@test');
        $router->any('payone/test2/', 'Payone\Controllers\ConfigController@test2');
        $router->any('payone/test3/', 'Payone\Controllers\ConfigController@test3');
        $router->any('payone/test4/', 'Payone\Controllers\ConfigController@test4');
        $router->any('payone/testRequestData/', 'Payone\Controllers\ConfigController@testRequestData');
        $router->get('payone/doPreCheck', 'Payone\Controllers\ConfigController@doPreCheck');

        $router->post('payone/status/', 'Payone\Controllers\StatusController@index');
    }
}
