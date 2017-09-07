<?php

namespace Payone\Procedures;

use Payone\Adapter\Logger;
use Payone\Services\Reversal;
use Plenty\Modules\EventProcedures\Events\EventProceduresTriggered;

/**
 * Class CancelEventProcedure
 */
class CancelEventProcedure extends AbstractEventProcedure
{
    /**
     * @param EventProceduresTriggered $eventTriggered
     * @param Reversal $reversalService
     * @param Logger $logger
     */
    public function run(
        EventProceduresTriggered $eventTriggered,
        Reversal $reversalService,
        Logger $logger
    ) {
        $order = $eventTriggered->getOrder();
        $logger->setIdentifier(__METHOD__)->info('EventProcedure.triggerFunction', ['order' => $order]);
        $reversalService->executeReversal($order);
    }
}
