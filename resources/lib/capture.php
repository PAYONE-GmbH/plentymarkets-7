<?php

use Payone\Api\Client;
use Payone\Api\PostApi;
use Payone\Request\RequestFactory;
use Payone\Request\Types;

$context = SdkRestApi::getParam('context');
$order = SdkRestApi::getParam('order');

$data['context'] = $context;
$data['order'] = $order;

$paymentMethod = SdkRestApi::getParam('paymentMethod');
$orderId = SdkRestApi::getParam('orderId');

$request = RequestFactory::create(Types::CAPTURE, $paymentMethod, $orderId, $data);
$client = new PostApi(new Client());
$response = $client->doRequest($request->toArray());

return $response->toArray();
