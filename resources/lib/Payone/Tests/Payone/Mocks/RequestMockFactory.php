<?php

namespace Tests\Payone\Mock;

use Payone\Request\Types;
use Tests\Payone\Mock\Data\DataAbstract;
use Tests\Payone\Mock\Request\RequestContract;

class RequestMockFactory
{
    private static $allowedPayments = [
        'Sofort',
    ];


    /**
     * @param string $paymentMethod
     * @param string $request PreCheck
     * @return \SimpleXMLElement
     */
    public static function getRequestData($paymentMethod, $request)
    {
        if (!in_array($paymentMethod, self::$allowedPayments)) {
            throw new \InvalidArgumentException('Unknown payment method "' . $paymentMethod . '"');
        }
        if (!in_array($request, Types::getRequestTypes())) {
            throw new \InvalidArgumentException('Unknown request type "' . $request . '""');
        }

        $className = 'Tests\Payone\Mock\Request\\' . $paymentMethod . '\\' . ucfirst($request) . 'Data';
        /** @var RequestContract $mockData */
        $mockData = new  $className();
        return array_merge($mockData->getConfig(), $mockData->getPersonalData(), $mockData->getRequestData());
    }

}