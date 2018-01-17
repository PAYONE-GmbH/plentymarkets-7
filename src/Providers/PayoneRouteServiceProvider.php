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
        $router->get('payone/test/', 'Payone\Controllers\ConfigController@test');
        $router->get('payone/test2/', 'Payone\Controllers\ConfigController@test2');
        $router->get('payone/test3/', 'Payone\Controllers\ConfigController@test3');
        $router->get('payone/test4/', 'Payone\Controllers\ConfigController@test4');
        $router->get('payone/testRequestData/', 'Payone\Controllers\ConfigController@testRequestData');
        $router->get('payone/doPreCheck', 'Payone\Controllers\ConfigController@doPreCheck');
        $router->get('payone/printShippingProfiles', 'Payone\Controllers\ConfigController@printShippingProfiles');
        $router->get('payone/printItemShippingProfiles', 'Payone\Controllers\ConfigController@printItemShippingProfiles');
        $router->get('payone/printParcelServicePreset', 'Payone\Controllers\ConfigController@printParcelServicePreset');

        $router->post('payone/status/', 'Payone\Controllers\StatusController@index');

        $router->post('payone/checkout/doAuth', 'Payone\Controllers\CheckoutController@doAuth');
        $router->post('payone/checkout/storeCCCheckResponse', 'Payone\Controllers\CheckoutController@storeCCCheckResponse');
    }
}
