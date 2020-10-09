<?php

namespace Payone\Models\Api\GenericPayment;

use Payone\Models\Api\ResponseAbstract;

/**
 * Class SetOrderReferenceDetailsResponse
 */
class SetOrderReferenceDetailsResponse extends ResponseAbstract implements \JsonSerializable
{
    protected $amazonAddressToken;
    protected $amazonReferenceId;
    protected $storename;
    protected $amount;
    protected $currency;
    protected $workOrderId;

    /**
     * @param bool $success
     * @param string $errorMessage
     * @param string $amazonAddressToken
     * @param string $amazonReferenceId
     * @param string $storename
     * @param string $amount
     * @param string $currency
     * @param string $workOrderId
     * @return $this
     */
    public function init(
        bool $success = false,
        string $errorMessage = '',
        string $amazonAddressToken = '',
        string $amazonReferenceId = '',
        string $storename = '',
        string $amount = '',
        string $currency = '',
        string $workOrderId = ''
    )
    {
        $this->success = $success;
        $this->errorMessage = $errorMessage;
        $this->amazonAddressToken = $amazonAddressToken;
        $this->amazonReferenceId = $amazonReferenceId;
        $this->storename = $storename;
        $this->amount = $amount;
        $this->currency = $currency;
        $this->workOrderId = $workOrderId;

        return $this;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return parent::jsonSerialize() +
            [
                'amazonAddressToken' => $this->amazonAddressToken,
                'amazonReferenceId' => $this->amazonReferenceId,
                'storename' => $this->storename,
                'amount' => $this->amount,
                'currency' => $this->currency,
                'workOrderId' => $this->workOrderId
            ];
    }

    /**
     * @return bool
     */
    public function isSuccess(): bool
    {
        return $this->success;
    }

    /**
     * @return string
     */
    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }

    /**
     * @return string
     */
    public function getTransactionID(): string
    {
        return $this->transactionID;
    }

    /**
     * @return string
     */
    public function getAmazonAddressToken(): string
    {
        return $this->amazonAddressToken;
    }

    /**
     * @return string
     */
    public function getAmazonReferenceId(): string
    {
        return $this->amazonReferenceId;
    }

    /**
     * @return string
     */
    public function getStorename(): string
    {
        return $this->storename;
    }

    /**
     * @return string
     */
    public function getAmount(): string
    {
        return $this->amount;
    }

    /**
     * @return string
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }

    /**
     * @return string
     */
    public function getWorkOrderId(): string
    {
        return $this->workOrderId;
    }


}
