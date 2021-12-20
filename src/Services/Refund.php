<?php

namespace Payone\Services;

use Payone\Adapter\Logger;
use Payone\Adapter\PaymentHistory;
use Payone\Helpers\OrderHelper;
use Payone\Helpers\PaymentHelper;
use Payone\Methods\PayoneCCPaymentMethod;
use Payone\Models\Api\Response;
use Payone\Providers\Api\Request\CaptureDataProvider;
use Payone\Providers\Api\Request\DebitDataProvider;
use Plenty\Modules\Order\Contracts\OrderRepositoryContract;
use Plenty\Modules\Order\Models\Order;
use Plenty\Modules\Order\Models\OrderType;
use Plenty\Modules\Payment\Contracts\PaymentRepositoryContract;
use Plenty\Modules\Payment\Models\Payment;
use Plenty\Modules\Payment\Models\PaymentProperty;

class Refund
{
    /**
     * @var PaymentRepositoryContract
     */
    private $paymentRepository;
    /**
     * @var PaymentHelper
     */
    private $paymentHelper;
    /**
     * @var Logger
     */
    private $logger;
    /**
     * @var PaymentCreation
     */
    private $paymentCreation;

    /**
     * @var PaymentHistory
     */
    private $paymentHistory;
    /**
     * @var OrderRepositoryContract
     */
    private $orderRepo;

    /**
     * @var DebitDataProvider
     */
    private $refundDataProvider;
    /**
     * @var Api
     */
    private $api;
    /**
     * @var CaptureDataProvider
     */
    private $captureDataProvider;

    /**
     * @var OrderHelper
     */
    protected $orderHelper;

    /**
     * Refund constructor.
     *
     * @param PaymentRepositoryContract $paymentRepository
     * @param PaymentHelper $paymentHelper
     * @param Logger $logger
     * @param PaymentCreation $paymentCreation
     * @param PaymentHistory $paymentHistory
     * @param OrderRepositoryContract $orderRepo
     * @param DebitDataProvider $refundDataProvider
     * @param Api $api
     * @param CaptureDataProvider $captureDataProvider
     * @param OrderHelper $orderHelper
     */
    public function __construct(
        PaymentRepositoryContract $paymentRepository,
        PaymentHelper $paymentHelper,
        Logger $logger,
        PaymentCreation $paymentCreation,
        PaymentHistory $paymentHistory,
        OrderRepositoryContract $orderRepo,
        DebitDataProvider $refundDataProvider,
        Api $api,
        CaptureDataProvider $captureDataProvider,
        OrderHelper $orderHelper
    ) {
        $this->paymentRepository = $paymentRepository;
        $this->paymentHelper = $paymentHelper;
        $this->logger = $logger;
        $this->paymentCreation = $paymentCreation;
        $this->paymentHistory = $paymentHistory;
        $this->orderRepo = $orderRepo;
        $this->refundDataProvider = $refundDataProvider;
        $this->api = $api;
        $this->captureDataProvider = $captureDataProvider;
        $this->orderHelper = $orderHelper;
    }

    /**
     * @param Order $refund
     */
    public function executeRefund(Order $refund)
    {
        $orderNote = '';

        $this->logger->setIdentifier(__METHOD__)->info('EventProcedure.triggerFunction', ['order' => $refund->id]);
        if (!in_array($refund->typeId, $this->getAllowedOrderTypes())) {
            $this->logger->error('Invalid order type ' . $refund->typeId . ' for order ' . $refund->id);

            return;
        }
        try {
            $originalOrder = $this->getOriginalOrder($refund);
        } catch (\Exception $e) {
            $this->logger->error('Error loading original order for order ' . $refund->id, $e->getMessage());

            return;
        }
        if (!$originalOrder) {
            $this->logger->error('Refunding payment failed! The given order is invalid!');
            $orderNote = 'Refunding payment failed! The given order is invalid!';
            $this->orderHelper->addOrderComment($refund->id, $orderNote);
            return;
        }
        try {
            $payments = $this->paymentRepository->getPaymentsByOrderId($originalOrder->id);
        } catch (\Exception $e) {
            $this->logger->error('Error loading payment', $e->getMessage());
            $orderNote = 'Error loading payment';
            $this->orderHelper->addOrderComment($refund->id, $orderNote);
            return;
        }
        $this->logger->debug(
            'General.getPaymentsByOrderId',
            ['orderId' => $originalOrder->id, 'payments' => $payments]
        );

        /* @var $payment Payment */
        foreach ($payments as $payment) {
            if (!$this->paymentHelper->isPayonePayment($payment->mopId)) {
                continue;
            }
            $preAuth = $this->paymentHelper->getPaymentPropertyValue($payment, PaymentProperty::TYPE_TRANSACTION_ID);
            if (!$preAuth) {
                $text = 'No Auth reference found in payment.';
                $this->logger->error('Api.doRefund',
                    [
                        'order' => $refund->id,
                        'payment' => $payment,
                        'errorMessage' => $text,
                    ]
                );
                $orderNote = $text . ' Order-ID: ' . $refund->id .' Payment-ID: '.$payment->id;
                $this->paymentHistory->addPaymentHistoryEntry($payment, $text);
                continue;
            }

            if ($refund->typeId != OrderType::TYPE_SALES_ORDER) {
                $refundPaymentResult = $this->refundCreditMemo(
                    $payment,
                    $originalOrder,
                    $refund,
                    $preAuth
                );
            } else {
                $refundPaymentResult = $this->refundOrder($payment, $refund, $preAuth);
            }

            $paymentCode = $this->paymentHelper->getPaymentCodeByMop($payment->mopId);
            if ($paymentCode == PayoneCCPaymentMethod::PAYMENT_CODE) {
                if (!$payment->amount) {// not captured yet?
                    $payment->status = Payment::STATUS_CANCELED;
                    $payment->updateOrderPaymentStatus = true;
                    $this->paymentRepository->updatePayment($payment);

                    return;
                }
            }

            if (!$refundPaymentResult->getSuccess()) {
                $this->logger->error('Api.doRefund',
                    [
                        'order' => $refund->id,
                        'payment' => $payment,
                        'authReference' => $preAuth,
                        'errorMessage' => $refundPaymentResult->getErrorMessage(),
                    ]
                );
                $orderNote = 'Refund fehlgeschlagen. Fehler im Log';
                continue;
            }

            $refundPayment = $this->createRefundPayment($payment->mopId, $payment, $refund,
                $refundPaymentResult);

            $payment->status = $this->getNewPaymentStatus($payment, $refundPayment);
            $payment = $this->paymentHelper->raiseSequenceNumber($payment);
            $orderNote ='Refund Successful Order-ID: ' . $refund->id .' Payment-ID: '.$payment->id;
            $this->paymentRepository->updatePayment($payment);
            $this->paymentHistory->addPaymentHistoryEntry($payment, $orderNote);
        }

        $this->orderHelper->addOrderComment($refund->id, $orderNote);
    }

    /**
     * @param $mopId
     * @param $payment
     * @param $refund
     * @param Response $transaction
     *
     * @return Payment
     */
    private function createRefundPayment(
        $mopId,
        $payment,
        Order $refund,
        $transaction
    ) {
        /* @var Payment $debitPayment */
        $debitPayment = $this->paymentCreation->createRefundPayment(
            $mopId,
            $transaction,
            $payment->currency,
            $this->getOrderAmount($refund, $payment),
            $payment->id,
            $refund->id
        );

        if (isset($debitPayment) && $debitPayment instanceof Payment) {
            $this->paymentCreation->assignPaymentToOrder($debitPayment, $refund);
        }

        $this->logger->debug(
            'General.createRefundPayment',
            ['orderId' => $refund->id, 'payment' => $debitPayment]
        );

        return $debitPayment;
    }

    /**
     * @param Payment $payment
     * @param Order $refund
     * @param $preAuthUniqueId
     *
     * @return Response
     */
    private function refundOrder($payment, Order $refund, $preAuthUniqueId)
    {
        $paymentCode = $this->paymentHelper->getPaymentCodeByMop($payment->mopId);

        $this->logger->setIdentifier(__METHOD__)->debug(
            'Api.doRefund',
            [
                'paymentCode' => $paymentCode,
                'order' => $refund->toArray(),
                'authUniqueId' => $preAuthUniqueId,
            ]
        );

        if ($paymentCode == PayoneCCPaymentMethod::PAYMENT_CODE) {
            if (!$payment->amount) {// not captured yet?
                return $this->reverseAuth($refund, $payment, $preAuthUniqueId, $refund->plentyId);
            }
        }

        $requestData = $this->refundDataProvider->getDataFromOrder($paymentCode, $refund, $preAuthUniqueId, $refund->plentyId);
        return $this->api->doDebit($requestData);
    }

    /**
     * @param $paymentCode
     * @param Order $order
     * @param Order $refund
     * @param $preAuthUniqueId
     *
     * @return Response
     */
    private function refundCreditMemo($payment, Order $order, Order $refund, $preAuthUniqueId)
    {
        $paymentCode = $this->paymentHelper->getPaymentCodeByMop($payment->mopId);

        $this->logger->setIdentifier(__METHOD__)->debug(
            'Api.doRefund',
            [
                'paymentCode' => $paymentCode,
                'order' => $order->toArray(),
                'refundOrder' => $refund->toArray(),
                'authUniqueId' => $preAuthUniqueId,
            ]
        );

        if ($paymentCode == PayoneCCPaymentMethod::PAYMENT_CODE) {
            if (!$payment->amount) {// already captured?
                return $this->reverseAuth($order, $payment, $preAuthUniqueId, $order->plentyId);
            }
        }

        $requestData = $this->refundDataProvider->getPartialRefundData($paymentCode, $order, $refund, $preAuthUniqueId, $order->plentyId);
        return $this->api->doDebit($requestData);
    }

    /**
     * @param Order $order
     *
     * @return int
     */
    private function getNewPaymentStatus(Payment $origPayment, Payment $refundPayment)
    {
        if ($origPayment->amount > $refundPayment->amount) {
            return Payment::STATUS_PARTIALLY_REFUNDED;
        }

        return Payment::STATUS_REFUNDED;
    }

    /**
     * @param Order $order
     * @param Payment $payment
     *
     * @return mixed
     */
    private function getOrderAmount($order, $payment)
    {
        switch ($order->typeId) {
            case OrderType::TYPE_SALES_ORDER:
                return $payment->amount;

            case OrderType::TYPE_CREDIT_NOTE:
            case OrderType::TYPE_RETURN:
            default:
                return $order->amounts[0]->invoiceTotal;
        }
    }

    /**
     * @param Order $order
     *
     * @return Order
     */
    private function getOriginalOrder(Order $order)
    {
        switch ($order->typeId) {
            case OrderType::TYPE_SALES_ORDER:
                return $order;

            case OrderType::TYPE_CREDIT_NOTE:
            case OrderType::TYPE_RETURN:
            default:
                $originOrders = $order->originOrders;
                if ($originOrders) {
                    $originOrder = $originOrders->first();

                    if ($originOrder instanceof Order) {
                        if ($originOrder->typeId == OrderType::TYPE_SALES_ORDER) {
                            $originOrders = $originOrder->id;
                        } else {
                            $originOriginOrders = $originOrder->originOrders;
                            if (is_array($originOriginOrders) && count($originOriginOrders) > 0) {
                                $originOriginOrder = $originOriginOrders->first();
                                if ($originOriginOrder instanceof Order) {
                                    $originOrders = $originOriginOrder->id;
                                }
                            }
                        }
                    }
                }

                return $this->orderRepo->findOrderById($originOrders);
        }
    }

    /**
     * @return array
     */
    private function getAllowedOrderTypes()
    {
        return [
            OrderType::TYPE_SALES_ORDER, // full refund
            OrderType::TYPE_CREDIT_NOTE, // partial refund / full refund
            OrderType::TYPE_RETURN, // partial return / full return
        ];
    }

    /**
     * @param Order $order
     * @param Payment $payment
     * @param $authTransactionId
     * @param int|null $clientId
     * @param int|null $pluginSetId
     * @return Response
     * @throws \Exception
     */
    private function reverseAuth(Order $order, Payment $payment, $authTransactionId, int $clientId = null, int $pluginSetId = null)
    {
        $amount = $order->amounts[0];
        $originalAmount = $amount->invoiceTotal;
        $amount->invoiceTotal = 0.;

        $paymentCode = $this->paymentHelper->getPaymentCodeByMop($payment->mopId);

        $requestData = $this->captureDataProvider->getDataFromOrder($paymentCode, $order, $authTransactionId, $clientId, $pluginSetId);
        $amount->invoiceTotal = $originalAmount;

        return $this->api->doCapture($requestData);
    }
}
