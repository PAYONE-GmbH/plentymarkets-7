<?php

namespace Payone\Procedures;

use Payone\Adapter\Logger;
use Payone\Services\ReAuth;
use Plenty\Modules\EventProcedures\Events\EventProceduresTriggered;

/**
 * Class ReAuthEventProcedure
 */
class ReAuthEventProcedure extends AbstractEventProcedure
{
    /**
     * @param EventProceduresTriggered $eventTriggered
     * @param ReAuth $reAuthService
     * @param Logger $logger
     */
    public function run(
        EventProceduresTriggered $eventTriggered,
        ReAuth $reAuthService,
        Logger $logger
    ) {
        $order = $eventTriggered->getOrder();
        $logger->setIdentifier(__METHOD__)->info('EventProcedure.triggerFunction', ['order' => $order]);
        $reAuthService->executeReAuth($order);
    }
}
