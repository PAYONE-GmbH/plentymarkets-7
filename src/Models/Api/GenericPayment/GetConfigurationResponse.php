<?php

namespace Payone\Models\Api\GenericPayment;

use Payone\Models\Api\ResponseAbstract;

/**
 * Class GetConfigurationResponse
 */
class GetConfigurationResponse extends ResponseAbstract implements \JsonSerializable
{

    private $clientId;
    private $sellerId;
    private $currency;
    private $workOrderId;

    /**
     * @param $success
     * @param $errorMessage
     * @param $clientId
     * @param $sellerId
     * @param $currency
     * @param $workorderId
     *
     * @return $this
     */
    public function init($success, $errorMessage, $clientId, $sellerId, $currency, $workorderId)
    {
        $this->success = $success;
        $this->errorMessage = $errorMessage;
        $this->clientId = $clientId;
        $this->sellerId = $sellerId;
        $this->currency = $currency;
        $this->workOrderId = $workorderId;
        return $this;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return parent::jsonSerialize() +
            [
                'clientId' => $this->clientId,
                'sellerId' => $this->sellerId,
                'currency' => $this->currency,
                'workOrderId' => $this->workOrderId,
            ];
    }

    /**
     * @return mixed
     */
    public function getClientId()
    {
        return $this->clientId;
    }

    /**
     * @return mixed
     */
    public function getSellerId()
    {
        return $this->sellerId;
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
}
