<?php

namespace Payone\Models\Api\GenericPayment;

use Payone\Models\Api\ResponseAbstract;

/**
 * Class GetConfigurationResponse
 */
class GetConfigurationResponse extends ResponseAbstract implements \JsonSerializable
{
    /**
     * @var string
     */
    protected $clientId;

    /**
     * @var string
     */
    protected $sellerId;

    /**
     * @var string
     */
    protected $currency;

    /**
     * @var string
     */
    protected $workOrderId;

    /**
     * @param bool $success
     * @param string $errorMessage
     * @param string $clientId
     * @param string $sellerId
     * @param string $currency
     * @param string $workorderId
     *
     * @return $this
     */
    public function init(bool $success, string $errorMessage, string $clientId, string $sellerId, string $currency, string $workorderId)
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
     * @return string
     */
    public function getClientId()
    {
        return $this->clientId;
    }

    /**
     * @return string
     */
    public function getSellerId()
    {
        return $this->sellerId;
    }

    /**
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @return string
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
        return $this->getSuccess();
    }
}
