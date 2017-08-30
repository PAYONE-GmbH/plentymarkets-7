<?php

use ArvPayoneApi\Api\Client;
use ArvPayoneApi\Api\PostApi;
use ArvPayoneApi\Lib\Version;
use ArvPayoneApi\Request\Authorization\RequestFactory;
use ArvPayoneApi\Response\ClientErrorResponse;

try {
    if (class_exists('Payone\Tests\Integration\Mock\SdkRestApi')) {
        $sdkRestApi = Payone\Tests\Integration\Mock\SdkRestApi::class;
    } else {
        $sdkRestApi = \SdkRestApi::class;
    }
    $basket = $sdkRestApi::getParam('basket');
    $basketItems = $sdkRestApi::getParam('basketItems');
    $country = $sdkRestApi::getParam('country');
    $shippingAddress = $sdkRestApi::getParam('shippingAddress');
    $context = $sdkRestApi::getParam('context');
    $order = $sdkRestApi::getParam('order');
    $customer = $sdkRestApi::getParam('customer');
    $shippingProvider = $sdkRestApi::getParam('shippingProvider');

    $data['basket'] = $basket;
    $data['basketItems'] = $basketItems;
    $data['shippingAddress'] = $shippingAddress;
    $data['context'] = $context;
    $data['order'] = $order;
    $data['customer'] = $customer;
    $data['shippingProvider'] = $shippingProvider;

    $paymentMethod = $sdkRestApi::getParam('paymentMethod');
    $orderId = $sdkRestApi::getParam('orderId');

    $request = RequestFactory::create($paymentMethod, $orderId, $data);
    $client = new PostApi(new Client());
    $response = $client->doRequest($request);
} catch (Exception $e) {
    $errorResponse = new ClientErrorResponse(
        'SdkRestApi error: ' . $e->getMessage() . PHP_EOL .
        'Lib version: ' . Version::getVersion() . PHP_EOL .
        $e->getTraceAsString()
    );

    return $errorResponse->jsonSerialize();
}

if (!$response->getSuccess()) {
    $errorResponse = new ClientErrorResponse(
        'Request successful but response invalid. ' . PHP_EOL .
        'Lib version: ' . Version::getVersion() . PHP_EOL .
        'Message: ' . $response->getErrorMessage() . PHP_EOL .
        'Request was : ' . json_encode($request, JSON_PRETTY_PRINT) . PHP_EOL .
        'Response was: ' . json_encode($response, JSON_PRETTY_PRINT)
    );

    return $errorResponse->jsonSerialize();
}

return $response->jsonSerialize();
