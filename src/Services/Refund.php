<?php

namespace Payone\Services;

use Payone\Adapter\Logger;
use Payone\Adapter\PaymentHistory;
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
        CaptureDataProvider $captureDataProvider
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
            $this->logger->error('Refunding payment failed! The given order is invalid!');

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
            if (!$preAuth) {
                $text = 'No Auth reference found in payment.';
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
                    $payment,
                    $originalOrder,
                    $order,
                    $preAuth
                );
            } else {
                $refundPaymentResult = $this->refundOrder($payment, $order, $preAuth);
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

            $refundPayment = $this->createRefundPayment($payment->mopId, $payment, $originalOrder,
                $refundPaymentResult);

            if (!$refundPaymentResult->getSuccess()) {
                $this->logger->error('Api.doRefund',
                    [
                        'order' => $order->id,
                        'payment' => $payment,
                        'authReference' => $preAuth,
                        'errorMessage' => $refundPaymentResult->getErrorMessage(),
                    ]
                );
                $text = 'Refund von event procedure fehlgeschlagen. Meldung: ' . $refundPaymentResult->getErrorMessage();
                $this->paymentHistory->addPaymentHistoryEntry($payment, $text);
                continue;
            }

            $payment->status = $this->getNewPaymentStatus($payment, $refundPayment);
            $payment->updateOrderPaymentStatus = true;
            $this->paymentRepository->updatePayment($payment);
        }
    }

    /**
     * @param $mopId
     * @param $payment
     * @param $order
     * @param Response $transaction
     *
     * @return Payment
     */
    private function createRefundPayment(
        $mopId,
        $payment,
        Order $order,
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

        return $debitPayment;
    }

    /**
     * @param Payment $payment
     * @param Order $order
     * @param $preAuthUniqueId
     *
     * @return Response
     */
    private function refundOrder($payment, Order $order, $preAuthUniqueId)
    {
        $paymentCode = $this->paymentHelper->getPaymentCodeByMop($payment->mopId);

        $this->logger->setIdentifier(__METHOD__)->debug(
            'Api.doRefund',
            [
                'paymentCode' => $paymentCode,
                'order' => $order->toArray(),
                'authUniqueId' => $preAuthUniqueId,
            ]
        );

        if ($paymentCode == PayoneCCPaymentMethod::PAYMENT_CODE) {
            if (!$payment->amount) {// not captured yet?
                return $this->reverseAuth($order, $payment, $preAuthUniqueId);
            }
        }

        $requestData = $this->refundDataProvider->getDataFromOrder($paymentCode, $order, $preAuthUniqueId);

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
                return $this->reverseAuth($order, $payment, $preAuthUniqueId);
            }
        }

        $requestData = $this->refundDataProvider->getPartialRefundData($paymentCode, $order, $refund, $preAuthUniqueId);

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
     *
     * @throws \Exception
     *
     * @return Response
     */
    private function reverseAuth(Order $order, Payment $payment, $authTransactionId)
    {
        $amount = $order->amounts[0];
        $originalAmount = $amount->invoiceTotal;
        $amount->invoiceTotal = 0.;

        $paymentCode = $this->paymentHelper->getPaymentCodeByMop($payment->mopId);

        $requestData = $this->captureDataProvider->getDataFromOrder($paymentCode, $order, $authTransactionId);
        $amount->invoiceTotal = $originalAmount;

        return $this->api->doCapture($requestData);
    }
}
