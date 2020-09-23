<?php

namespace Payone\Models\Api\GenericPayment;

use Payone\Models\Api\ResponseAbstract;

/**
 * Class SetOrderReferenceDetailsResponse
 */
class SetOrderReferenceDetailsResponse extends ResponseAbstract implements \JsonSerializable
{
    private $amazonAddressToken;
    private $amazonReferenceId;
    private $storename;
    private $amount;
    private $currency;
    private $workOrderId;

    /**
     * @param $success
     * @param $errorMessage
     * @param $amazonAddressToken
     * @param $amazonReferenceId
     * @param $storename
     * @param $amount
     * @param $currency
     * @param $workOrderId
     * @return $this
     */
    public function init($success, $errorMessage, $amazonAddressToken, $amazonReferenceId,
                         $storename, $amount, $currency, $workOrderId)
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
     * @return mixed
     */
    public function getAmazonAddressToken()
    {
        return $this->amazonAddressToken;
    }

    /**
     * @return mixed
     */
    public function getAmazonReferenceId()
    {
        return $this->amazonReferenceId;
    }

    /**
     * @return mixed
     */
    public function getStorename()
    {
        return $this->storename;
    }

    /**
     * @return mixed
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @return mixed
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @return mixed
     */
    public function getWorkOrderId()
    {
        return $this->workOrderId;
    }


}
