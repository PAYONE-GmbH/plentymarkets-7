<?php

namespace Payone\Helpers;

use Plenty\Modules\Order\Models\Order;
use Plenty\Modules\Order\Property\Models\OrderProperty;
use Plenty\Modules\Order\Property\Models\OrderPropertyType;

class OrderHelper
{
    /**
     * @param Order $order
     *
     * @return string
     */
    public function getLang(Order $order)
    {
        /** @var OrderProperty $property */
        foreach ($order->properties as $property) {
            if ($property->typeId == OrderPropertyType::DOCUMENT_LANGUAGE) {
                return $property->value;
            }
        }

        return 'DE';
    }
}
