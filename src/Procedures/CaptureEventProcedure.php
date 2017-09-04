<?php

namespace Payone\Procedures;

use Payone\Adapter\Logger;
use Payone\Services\Capture;
use Payone\Services\PaymentService;
use Plenty\Modules\EventProcedures\Events\EventProceduresTriggered;
use Plenty\Modules\Order\Models\Order;

/**
 * Class CaptureEventProcedure
 */
class CaptureEventProcedure extends AbstractEventProcedure
{
    /**
     * @param EventProceduresTriggered $eventTriggered
     * @param PaymentService $paymentService
     * @param Logger $logger
     *
     * @throws \Exception
     */
    public function run(
        EventProceduresTriggered $eventTriggered,
        Logger $logger,
        Capture $captureService
    ) {
        /* @var $order Order */
        $order = $eventTriggered->getOrder();

        $logger->setIdentifier(__METHOD__)->info('EventProcedure.triggerFunction', ['order' => $order]);
        $captureService->doCapture($order);
    }
}
