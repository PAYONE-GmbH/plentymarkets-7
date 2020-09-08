<?php

namespace Payone\Models\Api\GenericPayment;

use Payone\Models\Api\ResponseAbstract;

/**
 * Class GetConfigurationResponse
 */
class GetConfigurationResponse extends ResponseAbstract implements \JsonSerializable
{

    private $clientId = '';
    private $sellerId = '';
    private $workorderId = '';

    /**
     * @param $success
     * @param $errorMessage
     * @param $clientId
     * @param $sellerId
     * @param $workorderId
     *
     * @return $this
     */
    public function init($success, $errorMessage, $clientId, $sellerId, $workorderId)
    {
        $this->success = $success;
        $this->errorMessage = $errorMessage;
        $this->clientId = $clientId;
        $this->sellerId = $sellerId;
        $this->workorderId = $workorderId;
        return $this;
    }

    public function jsonSerialize(): array
    {
        return parent::jsonSerialize() +
            [
                'clientId' => $this->clientId,
                'sellerId' => $this->sellerId,
                'workorderId' => $this->workorderId,
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
    public function getWorkorderId(): string
    {
        return $this->workorderId;
    }

}
