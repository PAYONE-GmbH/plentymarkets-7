<?php

namespace Payone\Services;

use Payone\Adapter\Logger;
use Payone\Adapter\PaymentHistory;
use Payone\Helpers\PaymentHelper;
use Payone\Models\Api\Response;
use Payone\Providers\Api\Request\RefundDataProvider;
use Plenty\Modules\Order\Contracts\OrderRepositoryContract;
use Plenty\Modules\Order\Models\Order;
use Plenty\Modules\Order\Models\OrderAmount;
use Plenty\Modules\Order\Models\OrderItem;
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
     * @var RefundDataProvider
     */
    private $refundDataProvider;
    /**
     * @var Api
     */
    private $api;

    /**
     * Refund constructor.
     *
     * @param PaymentRepositoryContract $paymentRepository
     * @param PaymentHelper $paymentHelper
     * @param Logger $logger
     * @param PaymentCreation $paymentCreation
     * @param PaymentHistory $paymentHistory
     * @param OrderRepositoryContract $orderRepo
     * @param $refundDataProvider
     * @param Api $api
     */
    public function __construct(
        PaymentRepositoryContract $paymentRepository,
        PaymentHelper $paymentHelper,
        Logger $logger,
        PaymentCreation $paymentCreation,
        PaymentHistory $paymentHistory,
        OrderRepositoryContract $orderRepo,
        RefundDataProvider $refundDataProvider,
        Api $api
    ) {
        $this->paymentRepository = $paymentRepository;
        $this->paymentHelper = $paymentHelper;
        $this->logger = $logger;
        $this->paymentCreation = $paymentCreation;
        $this->paymentHistory = $paymentHistory;
        $this->orderRepo = $orderRepo;
        $this->refundDataProvider = $refundDataProvider;
        $this->api = $api;
    }

    /**
     * @param Order $order
     */
    public function executeRefund(Order $order)
    {
        $this->logger->setIdentifier(__METHOD__)->info('EventProcedure.triggerFunction', ['order' => $order->id]);
        if (!in_array($order->typeId, $this->getAllowedOrderTypes())) {
            $this->logger->error('Invalid order type ' . $order->typeId . ' for order ' . $order->id);

            return;
        }
        try {
            $originalOrder = $this->getOriginalOrder($order);
        } catch (\Exception $e) {
            $this->logger->error('Error loading original order for order ' . $order->id, $e->getMessage());

            return;
        }
        if (!$originalOrder) {
            $this->logger->error('Refunding Payolution payment failed! The given order is invalid!');

            return;
        }
        try {
            $payments = $this->paymentRepository->getPaymentsByOrderId($originalOrder->id);
        } catch (\Exception $e) {
            $this->logger->error('Error loading payment', $e->getMessage());

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
            $paymentCode = $this->paymentHelper->getPaymentCodeByMop($payment->mopId);
            if (!$preAuth) {
                $text = 'No PreAuth reference found in payment.';
                $this->logger->error('Api.doRefund',
                    [
                        'order' => $order->id,
                        'payment' => $payment,
                        'errorMessage' => $text,
                    ]
                );
                $this->paymentHistory->addPaymentHistoryEntry($payment, $text);
                continue;
            }

            if ($order->typeId != OrderType::TYPE_SALES_ORDER) {
                $refundPaymentResult = $this->refundCreditMemo(
                    $paymentCode,
                    $originalOrder,
                    $order,
                    $preAuth
                );
            } else {
                $refundPaymentResult = $this->refundOrder($paymentCode, $order, $preAuth);
            }
            $this->createRefundPayment($payment->mopId, $payment, $originalOrder, $refundPaymentResult);

            if (!$refundPaymentResult->getSuccess()) {
                $this->logger->error('Api.doRefund',
                    [
                        'order' => $order->id,
                        'payment' => $payment,
                        'preAuthReference' => $preAuth,
                        'errorMessage' => $refundPaymentResult->getErrorMessage(),
                    ]
                );
                $text = 'Refund von event procedure fehlgeschlagen. Meldung: ' . $refundPaymentResult->getErrorMessage();
                $this->paymentHistory->addPaymentHistoryEntry($payment, $text);
                continue;
            }

            $payment->status = $this->getNewPaymentStatus($order);
            $payment->updateOrderPaymentStatus = false;
            $this->paymentRepository->updatePayment($payment);
        }
    }

    /**
     * @param $mopId
     * @param $payment
     * @param $order
     * @param Response $transaction
     */
    public function createRefundPayment(
        $mopId,
        $payment,
        $order,
        $transaction
    ) {
        /* @var Payment $debitPayment */
        $debitPayment = $this->paymentCreation->createRefundPayment(
            $mopId,
            $transaction,
            $payment->currency,
            $this->getOrderAmount($order, $payment),
            $payment->id
        );

        if (isset($debitPayment) && $debitPayment instanceof Payment) {
            $this->paymentCreation->assignPaymentToOrder($debitPayment, $order);
        }

        $this->logger->debug(
            'General.createRefundPayment',
            ['orderId' => $order->id, 'payment' => $debitPayment]
        );
    }

    /**
     * @param $paymentCode
     * @param Order $order
     * @param $preAuthUniqueId
     *
     * @return Response
     */
    public function refundOrder($paymentCode, Order $order, $preAuthUniqueId)
    {
        $this->logger->setIdentifier(__METHOD__)->debug(
            'Api.doRefund',
            [
                'paymentCode' => $paymentCode,
                'order' => $order->toArray(),
                'preAuthUniqueId' => $preAuthUniqueId,
            ]
        );

        $requestData = $this->refundDataProvider->getDataFromOrder($paymentCode, $order, $preAuthUniqueId);

        $response = $this->api->doDebit(
            $requestData
        );

        return $response;
    }

    /**
     * @param $paymentCode
     * @param Order $order
     * @param Order $refund
     * @param $preAuthUniqueId
     *
     * @return Response
     */
    public function refundCreditMemo($paymentCode, Order $order, Order $refund, $preAuthUniqueId)
    {
        $this->logger->setIdentifier(__METHOD__)->debug(
            'Api.doRefund',
            [
                'paymentCode' => $paymentCode,
                'order' => $order->toArray(),
                'refundOrder' => $refund->toArray(),
                'preAuthUniqueId' => $preAuthUniqueId,
            ]
        );

        $requestData = $this->refundDataProvider->getPartialRefundData($paymentCode, $order, $refund, $preAuthUniqueId);

        $response = $this->api->doDebit(
            $requestData
        );

        return $response;
    }

    /**
     * @param Order $order
     *
     * @return int
     */
    private function getNewPaymentStatus(Order $order)
    {
        //TODO: handle last partial refund for order
        switch ($order->typeId) {
            case OrderType::TYPE_SALES_ORDER:

                return Payment::STATUS_REFUNDED;

            case OrderType::TYPE_CREDIT_NOTE:
            case OrderType::TYPE_RETURN:
            default:

                /* @var $orderAmount OrderAmount */
                $orderAmount = $order->amounts[0];

                /* @var $parentOrder Order */
                $parentOrder = $this->getOriginalOrder($order);
                /* @var $debitAmount OrderAmount */
                $debitAmount = $parentOrder->amounts[0];

                if ($orderAmount->invoiceTotal == $debitAmount->invoiceTotal) {
                    return Payment::STATUS_REFUNDED;
                }
                $parentItems = [];

                /* @var $parentOrderItem OrderItem */
                foreach ($parentOrder->orderItems as $parentOrderItem) {
                    $parentItems[] = $parentOrderItem->itemVariationId;
                }

                /* @var $orderItem OrderItem */
                foreach ($order->orderItems as $orderItem) {
                    $variationId = $orderItem->itemVariationId;
                    if (!in_array($variationId, $parentItems)) {
                        // good will refund
                        return Payment::STATUS_PARTIALLY_REFUNDED;
                    }
                }

                return Payment::STATUS_PARTIALLY_REFUNDED;
        }
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
}
