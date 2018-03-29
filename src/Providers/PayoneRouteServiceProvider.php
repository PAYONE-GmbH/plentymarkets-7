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

        $router->get('payment/payone/migrate/', 'Payone\Controllers\ConfigController@migrate');

        $router->post('payment/payone/status/', 'Payone\Controllers\StatusController@index');

        $router->post('payment/payone/checkout/doAuth', 'Payone\Controllers\CheckoutController@doAuth');
        $router->post('payment/payone/checkout/storeCCCheckResponse', 'Payone\Controllers\CheckoutController@storeCCCheckResponse');
        $router->post('payment/payone/checkout/storeAccountData', 'Payone\Controllers\CheckoutController@storeAccountData');
        $router->get('payment/payone/error', 'Payone\Controllers\CheckoutController@redirectWithNotice');
        $router->get('payment/payone/checkoutSuccess', 'Payone\Controllers\CheckoutController@checkoutSuccess');
        $router->get('payment/payone/checkout/getSepaMandateStep', 'Payone\Controllers\CheckoutController@getSepaMandateStep');
    }
}
