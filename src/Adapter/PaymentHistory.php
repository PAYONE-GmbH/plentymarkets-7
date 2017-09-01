<?php

namespace Payone\Adapter;

use Plenty\Modules\Payment\History\Contracts\PaymentHistoryRepositoryContract;
use Plenty\Modules\Payment\History\Models\PaymentHistory as PaymentHistoryModel;
use Plenty\Modules\Payment\Models\Payment;

class PaymentHistory //implements PaymentHistoryRepositoryContract
{
    /**
     * @var PaymentHistoryRepositoryContract
     */
    private $paymentHistoryRepo;
    /**
     * @var Logger
     */
    private $logger;

    /**
     * PaymentHistory constructor.
     *
     * @param PaymentHistoryRepositoryContract $paymentHistoryRepo
     * @param Logger $logger
     */
    public function __construct(PaymentHistoryRepositoryContract $paymentHistoryRepo, Logger $logger)
    {
        $this->paymentHistoryRepo = $paymentHistoryRepo;
        $this->logger = $logger;
    }

    /**
     * @param Payment $payment
     * @param string $text
     *
     * @return PaymentHistoryModel
     */
    public function addPaymentHistoryEntry($payment, $text)
    {
        /** @var PaymentHistoryModel $paymentHistoryEntry */
        $paymentHistoryEntry = pluginApp(PaymentHistoryModel::class);
        $paymentHistoryEntry->typeId = PaymentHistoryModel::HISTORY_TYPE_STATUS_UPDATED;
        $paymentHistoryEntry->paymentId = $payment->id;
        $paymentHistoryEntry->value = $text;

        $this->logger->setIdentifier(__METHOD__)->debug('PaymentHistory.addEntry', $paymentHistoryEntry);

        return $this->paymentHistoryRepo->createHistory($paymentHistoryEntry);
    }

    /**
     * @param int $paymentId
     * @param int $typeId
     *
     * @return array
     */
    public function getByPaymentId(
        int $paymentId,
        int $typeId
    ): array {
        $historyEntry = $this->paymentHistoryRepo->getByPaymentId($paymentId, $typeId);
        $this->logger->setIdentifier(__METHOD__)->debug(
            'PaymentHistory.getByPaymentId',
            ['paymentId' => $paymentId, 'typeId' => $typeId, 'historyEntry' => $historyEntry]
        );

        return $historyEntry;
    }
}
