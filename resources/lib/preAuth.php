<?php
use Payone\Api\Client;
use Payone\Api\PostApi;
use Payone\Request\RequestFactory;
use Payone\Request\Types;

$basket = SdkRestApi::getParam('basket');
$basketItems = SdkRestApi::getParam('basketItems');
$country = SdkRestApi::getParam('country');
$shippingAddress = SdkRestApi::getParam('shippingAddress');
$shippingAddress['country'] = $country['isoCode2'];
$context = SdkRestApi::getParam('context');
$order = SdkRestApi::getParam('order');
$customer = SdkRestApi::getParam('customer');
$shippingProvider = SdkRestApi::getParam('shippingProvider');

$data['basket'] = $basket;
$data['basketItems'] = $basketItems;
$data['shippingAddress'] = $shippingAddress;
$data['context'] = $context;
$data['order'] = $order;
$data['customer'] = $customer;
$data['shippingProvider'] = $shippingProvider;

$paymentMethod = SdkRestApi::getParam('paymentMethod');
$orderId = SdkRestApi::getParam('orderId');

$request = RequestFactory::create(Types::PREAUTHORIZATION, $paymentMethod, $orderId, $data);
$client = new PostApi(new Client());
$response = $client->doRequest($request->toArray());

return $response->toArray();
