<?php

namespace Payolution\Procedures;

use Plenty\Modules\Order\Models\Order;
use Plenty\Modules\Order\Models\OrderReference;

/**
 * Class AbstractEventProcedure
 */
abstract class AbstractEventProcedure
{
    /**
     * Get the parent order from references.
     *
     * @param array $references
     *
     * @return Order|null
     */
    protected function getParentOrder($references)
    {
        $referenceOrder = null;

        /* @var $reference OrderReference */
        foreach ($references as $reference) {
            if ($reference->referenceType === OrderReference::REFERENCE_TYPE_PARENT) {
                $referenceOrder = $reference->referenceOrder;

                return $referenceOrder;
            }
        }
    }
}
