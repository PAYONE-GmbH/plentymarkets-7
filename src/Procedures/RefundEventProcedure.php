<?php

namespace Payone\Procedures;

use Payone\Adapter\Logger;
use Payone\Services\Refund;
use Plenty\Modules\EventProcedures\Events\EventProceduresTriggered;

/**
 * Class RefundEventProcedure
 */
class RefundEventProcedure extends AbstractEventProcedure
{
    /**
     * @param EventProceduresTriggered $eventTriggered
     * @param Refund $refundService
     * @param Logger $logger
     */
    public function run(
        EventProceduresTriggered $eventTriggered,
        Refund $refundService,
        Logger $logger
    ) {
        $order = $eventTriggered->getOrder();
        $logger->setIdentifier(__METHOD__)->info('EventProcedure.triggerFunction', ['order' => $order->id]);
        $refundService->executeRefund($order);
        $logger->setIdentifier(__METHOD__)->info('Even.Procedure.triggerFunction', 'done');
    }
}
