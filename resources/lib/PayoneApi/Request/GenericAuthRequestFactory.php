<?php

namespace PayoneApi\Request;

use PayoneApi\Request\Parts\Customer;
use PayoneApi\Request\Parts\CustomerAddress;

class GenericAuthRequestFactory
{
    /**
     * @param $requestType
     * @param $data
     *
     * @return GenericAuthorizationRequest
     */
    public static function create($requestType, $data)
    {
        $customerAddressData = $data['shippingAddress'];
        $customerAddress = new CustomerAddress(
            $customerAddressData['street'] . ' ' . $customerAddressData['houseNumber'],
            $customerAddressData['addressaddition'],
            $customerAddressData['postalCode'],
            $customerAddressData['town'],
            $customerAddressData['country']
        );
        $customerData = $data['customer'];
        $customer = new Customer(
            $customerData['title'],
            $customerData['firstname'],
            $customerData['lastname'],
            $customerAddress,
            $customerData['email'],
            $customerData['telephonenumber'],
            $customerData['birthday'],
            $customerData['language'],
            $customerData['gender'],
            $customerData['ip']
        );
        $reference = isset($data['order']['orderId']) && $data['order']['orderId'] ?
            'order-' . $data['order']['orderId'] : 'basket-' . $data['basket']['id'];
        $genericRequest = GenericRequestFactory::create($requestType, $data);

        return new GenericAuthorizationRequest(
            $genericRequest,
            $reference,
            $customer
        );
    }
}
