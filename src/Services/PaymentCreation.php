<?php

namespace Payone\Services;

use Payone\Adapter\Logger;
use Payone\Adapter\PaymentHistory;
use Payone\Helpers\PaymentHelper;
use Payone\Models\Api\Response;
use Payone\Models\ApiResponseCache;
use Payone\Models\PayonePaymentStatus;
use Payone\Providers\Api\Request\AuthDataProvider;
use Payone\Providers\Api\Request\BankAccount;
use Payone\Providers\Api\Request\CaptureDataProvider;
use Payone\Providers\Api\Request\PreAuthDataProvider;
use Payone\Providers\Api\Request\RefundDataProvider;
use Plenty\Modules\Account\Address\Contracts\AddressRepositoryContract;
use Plenty\Modules\Basket\Models\Basket;
use Plenty\Modules\Order\Contracts\OrderRepositoryContract;
use Plenty\Modules\Order\Models\Order;
use Plenty\Modules\Payment\Contracts\PaymentOrderRelationRepositoryContract;
use Plenty\Modules\Payment\Contracts\PaymentRepositoryContract;
use Plenty\Modules\Payment\Method\Contracts\PaymentMethodRepositoryContract;
use Plenty\Modules\Payment\Models\Payment;
use Plenty\Modules\Payment\Models\PaymentProperty;
use Plenty\Modules\Plugin\Libs\Contracts\LibraryCallContract;

/**
 * Class PaymentCreation
 */
class PaymentCreation
{
    /**
     * @var PaymentMethodRepositoryContract
     */
    private $paymentMethodRepository;

    /**
     * @var PaymentRepositoryContract
     */
    private $paymentRepository;

    /**
     * @var PaymentHelper
     */
    private $paymentHelper;

    /**
     * @var LibraryCallContract
     */
    private $libCall;

    /**
     * @var AddressRepositoryContract
     */
    private $addressRepo;

    /**
     * @var PaymentOrderRelationRepositoryContract
     */
    private $paymentOrderRelationRepo;
    /**
     * @var OrderRepositoryContract
     */
    private $orderRepo;

    /**
     * @var AuthDataProvider
     */
    private $preCheckDataProvider;

    /**
     * @var Api
     */
    private $api;
    /**
     * @var PreAuthDataProvider
     */
    private $preAuhtDataProvider;

    /**
     * @var CaptureDataProvider
     */
    private $captureDataProvider;

    /**
     * @var RefundDataProvider
     */
    private $refundDataProvider;
    /**
     * @var Logger
     */
    private $logger;
    /**
     * @var ApiResponseCache
     */
    private $apiResponseCache;
    /**
     * @var PaymentHistory
     */
    private $paymentHistory;

    /**
     * PaymentService constructor.
     *
     * @param PaymentMethodRepositoryContract $paymentMethodRepository
     * @param PaymentRepositoryContract $paymentRepository
     * @param PaymentHelper $paymentHelper
     * @param LibraryCallContract $libCall
     * @param AddressRepositoryContract $addressRepo
     * @param PaymentOrderRelationRepositoryContract $paymentOrderRelationRepo
     * @param OrderRepositoryContract $orderRepo
     * @param AuthDataProvider $preCheckDataProvider
     * @param PreAuthDataProvider $preAuhtDataProvider
     * @param ApiResponseCache $apiResponseCache
     * @param Api $api
     * @param CaptureDataProvider $captureDataProvide
     * @param RefundDataProvider $refundDataProvider
     * @param Logger $logger
     * @param PaymentHistory $paymentHistoryRepo
     */
    public function __construct(
        PaymentMethodRepositoryContract $paymentMethodRepository,
        PaymentRepositoryContract $paymentRepository,
        PaymentHelper $paymentHelper,
        LibraryCallContract $libCall,
        AddressRepositoryContract $addressRepo,
        PaymentOrderRelationRepositoryContract $paymentOrderRelationRepo,
        OrderRepositoryContract $orderRepo,
        AuthDataProvider $preCheckDataProvider,
        PreAuthDataProvider $preAuhtDataProvider,
        ApiResponseCache $apiResponseCache,
        Api $api,
        CaptureDataProvider $captureDataProvide,
        RefundDataProvider $refundDataProvider,
        Logger $logger,
        PaymentHistory $paymentHistoryRepo
    ) {
        $this->paymentMethodRepository = $paymentMethodRepository;
        $this->paymentRepository = $paymentRepository;
        $this->paymentHelper = $paymentHelper;
        $this->libCall = $libCall;
        $this->addressRepo = $addressRepo;
        $this->paymentOrderRelationRepo = $paymentOrderRelationRepo;
        $this->orderRepo = $orderRepo;
        $this->api = $api;
        $this->preCheckDataProvider = $preCheckDataProvider;
        $this->preAuhtDataProvider = $preAuhtDataProvider;
        $this->captureDataProvider = $captureDataProvide;
        $this->refundDataProvider = $refundDataProvider;
        $this->logger = $logger;
        $this->apiResponseCache = $apiResponseCache;
        $this->paymentHistory = $paymentHistoryRepo;
    }

    /**
     * @param $mopId
     * @param $response
     * @param Basket $basket
     * @param BankAccount|null $account
     *
     * @throws \Exception
     *
     * @return Payment
     */
    public function createPayment($mopId, Response $response, Basket $basket, BankAccount $account = null)
    {
        $this->logger->setIdentifier(__METHOD__)->debug(
            'PaymentCreation.createPayment',
            [
                'paymentId' => $mopId,
                'response' => $response,
                'basket' => $basket,
            ]
        );
        $transactionID = $response->getTransactionID();

        $paymentCode = $this->paymentHelper->getPaymentCodeByMop($mopId);

        $paymentData = $this->preAuhtDataProvider->getDataFromBasket($paymentCode, $basket, $transactionID);

        /** @var Payment $payment */
        $payment = pluginApp(Payment::class);

        $payment->mopId = (int) $mopId;
        $payment->transactionType = Payment::TRANSACTION_TYPE_BOOKED_POSTING;
        $payment->status = Payment::STATUS_APPROVED;
        $payment->currency = $paymentData['basket']['currency'];
        $payment->amount = 0; // zero till it is captured, so the order paid amount is not updated
        $payment->type = Payment::PAYMENT_TYPE_CREDIT;

        $paymentProperties = [];

        $paymentProperties[] = $this->createPaymentProperty(
            PaymentProperty::TYPE_TRANSACTION_ID,
            $transactionID
        );

        $paymentProperties[] = $this->createPaymentProperty(PaymentProperty::TYPE_ORIGIN, '' . Payment::ORIGIN_PLUGIN);
        $paymentProperties[] = $this->createPaymentProperty(
            PaymentProperty::TYPE_ACCOUNT_OF_RECEIVER,
            $basket->customerId);

        $paymentText = [
            'Request type' => 'PreAuth',
            'TransactionID' => $transactionID,
        ];

        if ($account) {
            $paymentText['accountHolder'] = $account->getHolder();
            $paymentText['iban'] = $account->getIban();
            $paymentText['bic'] = $account->getIban();
            $paymentText['referenceNumber'] = $response->getTransactionID();
        }

        $paymentProperties[] = $this->createPaymentProperty(
            PaymentProperty::TYPE_PAYMENT_TEXT,
            json_encode($paymentText)
        );

        $payment->properties = $paymentProperties;

        try {
            $payment = $this->paymentRepository->createPayment($payment);
        } catch (\Exception $e) {
            $storedPayment = $this->paymentRepository->getPaymentById($payment->id);
            if ($storedPayment) {
                return $storedPayment;
            }
            throw $e;
        }

        return $payment;
    }

    /**
     * @param Payment $payment
     * @param Order $order
     *
     * @return Payment
     */
    public function capturePayment(Payment $payment, Order $order)
    {
        $this->logger->setIdentifier(__METHOD__)->debug(
            'PaymentCreation.updatePayment',
            [
                'payment' => $payment,
                'order' => $order,
            ]
        );

        $payment->updateOrderPaymentStatus = true;
        $orderData = $order->toArray();
        $payment->currency = $orderData['amounts'][0]['currency'];
        $payment->amount = $orderData['amounts'][0]['grossTotal'];
        $payment->receivedAt = date('Y-m-d H:i:s');

        $payment = $this->paymentRepository->updatePayment($payment);

        return $payment;
    }

    /**
     * @param Payment $payment
     * @param Response $response
     * @param Order $order
     *
     * @return Payment
     */
    public function reAuthorizePayment(Payment $payment, Response $response, Order $order)
    {
        $this->logger->setIdentifier(__METHOD__)->debug(
            'PaymentCreation.updatePayment',
            [
                'payment' => $payment,
                'response' => $response,
                'order' => $order,
            ]
        );

        $transactionID = $response->getTransactionID();

        $paymentProperties = [];

        $paymentProperties[] = $this->createPaymentProperty(
            PaymentProperty::TYPE_TRANSACTION_ID,
            $transactionID
        );

        $paymentProperties[] = $this->createPaymentProperty(PaymentProperty::TYPE_ORIGIN, '' . Payment::ORIGIN_PLUGIN);

        $paymentText = [
            'Request type' => 'PreAuth',
            'TransactionID' => $transactionID,
        ];

        $paymentProperties[] = $this->createPaymentProperty(
            PaymentProperty::TYPE_PAYMENT_TEXT,
            json_encode($paymentText)
        );

        $payment->properties = $paymentProperties;

        $payment = $this->paymentRepository->updatePayment($payment);

        return $payment;
    }

    /**
     * @param $paymentId
     * @param Response $response
     * @param $currency
     * @param $grandTotal
     * @param $parentPaymentId
     *
     * @throws \Exception
     *
     * @return Payment
     */
    public function createRefundPayment($paymentId, $response, $currency, $grandTotal, $parentPaymentId)
    {
        $this->logger->setIdentifier(__METHOD__)->debug(
            'PaymentCreation.createRefundPayment',
            [
                'paymentId' => $paymentId,
                'response' => $response,
                'currency' => $currency,
                'grandTotal' => $grandTotal,
                'parentPaymentId' => $parentPaymentId,
            ]
        );

        $transactionID = $response->getTransactionID();

        /** @var Payment $payment */
        $payment = pluginApp(Payment::class);
        $payment->updateOrderPaymentStatus = true;
        $payment->mopId = (int) $paymentId;
        $payment->transactionType = Payment::TRANSACTION_TYPE_BOOKED_POSTING;
        $payment->status = Payment::STATUS_CAPTURED;
        $payment->currency = $currency;
        $payment->amount = $grandTotal;
        $payment->receivedAt = date('Y-m-d H:i:s');
        $payment->type = Payment::PAYMENT_TYPE_DEBIT;
        $payment->parentId = $parentPaymentId;
        $paymentProperties = [];

        $paymentProperties[] = $this->createPaymentProperty(
            PaymentProperty::TYPE_TRANSACTION_ID,
            $transactionID
        );

        $paymentProperties[] = $this->createPaymentProperty(PaymentProperty::TYPE_ORIGIN, '' . Payment::ORIGIN_PLUGIN);

        $paymentText = [
            'Request type' => 'Capture',
            'TransactionID' => $transactionID,
        ];

        $paymentProperties[] = $this->createPaymentProperty(
            PaymentProperty::TYPE_PAYMENT_TEXT,
            json_encode($paymentText)
        );

        $payment->properties = $paymentProperties;

        try {
            $payment = $this->paymentRepository->createPayment($payment);
        } catch (\Exception $e) {
            $storedPayment = $this->paymentRepository->getPaymentById($payment->id);
            if ($storedPayment) {
                return $storedPayment;
            }

            throw $e;
        }

        return $payment;
    }

    /**
     * @param Payment $payment
     * @param Order $order
     */
    public function assignPaymentToOrder(Payment $payment, Order $order)
    {
        $this->logger->setIdentifier(__METHOD__)->debug(
            'PaymentCreation.assignPaymentToOrder',
            [
                'payment' => $payment,
                'orderId' => $order->id,
            ]
        );
        $this->paymentOrderRelationRepo->createOrderRelation($payment, $order);
    }

    /**
     * @param $ownerId
     * @param $selectedPaymentId
     *
     * @return null|Payment
     */
    public function findLastPayment($ownerId, $selectedPaymentId)
    {
        $payment = null;
        $page = 0;
        while (
        $payments = $this->paymentRepository->getPaymentsByPropertyTypeAndValue(
            PaymentProperty::TYPE_ACCOUNT_OF_RECEIVER,
            $ownerId,
            50,
            ++$page
        )
        ) {
            /** @var Payment $payment */
            foreach ($payments as $curPayment) {
                if (!$payment) {
                    $payment = $curPayment;
                }
                if ($curPayment->mopId == $selectedPaymentId && $payment->receivedAt < $curPayment->receivedAt) {
                    $payment = $curPayment;
                }
            }
        }

        return $payment;
    }

    /**
     * @param $orderId
     * @param $txid
     * @param string $txaction
     */
    public function updatePaymentStatus($orderId, $txid, $txaction)
    {
        $payments = $this->paymentRepository->getPaymentsByOrderId($orderId);

        /* @var $payment Payment */
        foreach ($payments as $payment) {
            /* @var $property PaymentProperty */
            foreach ($payment->properties as $property) {
                if (!($property instanceof PaymentProperty)) {
                    continue;
                }
                if ($property->typeId === 30 && $property->id === $txid) {
                    $payment->status = PayonePaymentStatus::getPlentyStatus($txaction);
                    $this->paymentRepository->updatePayment($payment);
                }
            }
        }
    }

    /**
     * Returns a PaymentProperty with the given params
     *
     * @param int $typeId
     * @param string $value
     *
     * @return PaymentProperty
     */
    private function createPaymentProperty($typeId, $value)
    {
        /** @var PaymentProperty $paymentProperty */
        $paymentProperty = pluginApp(PaymentProperty::class);

        $paymentProperty->typeId = $typeId;
        $paymentProperty->value = $value . '';

        return $paymentProperty;
    }
}
