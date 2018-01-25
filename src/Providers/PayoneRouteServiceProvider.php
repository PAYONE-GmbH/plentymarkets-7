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
        $router->get('payone/printConfig/', 'Payone\Controllers\ConfigController@printConfig');
        $router->get('payone/migrate/', 'Payone\Controllers\ConfigController@migrate');
        $router->get('payone/printAllPaymentMethods/', 'Payone\Controllers\ConfigController@printAllPaymentMethods');
        $router->get('payone/testRequestData/', 'Payone\Controllers\ConfigController@testRequestData');
        $router->get('payone/doPreCheck', 'Payone\Controllers\ConfigController@doPreCheck');
        $router->get('payone/printShippingProfiles', 'Payone\Controllers\ConfigController@printShippingProfiles');
        $router->get('payone/printItemShippingProfiles', 'Payone\Controllers\ConfigController@printItemShippingProfiles');
        $router->get('payone/printParcelServicePreset', 'Payone\Controllers\ConfigController@printParcelServicePreset');


        $router->post('payone/status/', 'Payone\Controllers\StatusController@index');

        $router->post('payone/checkout/doAuth', 'Payone\Controllers\CheckoutController@doAuth');
        $router->post('payone/checkout/storeCCCheckResponse', 'Payone\Controllers\CheckoutController@storeCCCheckResponse');
        $router->get('payone/error', 'Payone\Controllers\ConfigController@redirectWithNotice');
    }
}
