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
     * @return string
     */
    public function getClientId(): string
    {
        return $this->clientId;
    }

    /**
     * @return string
     */
    public function getSellerId(): string
    {
        return $this->sellerId;
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
