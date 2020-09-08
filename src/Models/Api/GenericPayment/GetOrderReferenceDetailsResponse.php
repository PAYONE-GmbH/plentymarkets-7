<?php

namespace Payone\Models\Api\GenericPayment;

use Payone\Models\Api\ResponseAbstract;

/**
 * Class GetConfigurationResponse
 */
class GetOrderReferenceDetailsResponse extends ResponseAbstract implements \JsonSerializable
{
    /**
    status=OK
    add_paydata[shipping_zip]=80939
    add_paydata[shipping_city]=München
    add_paydata[shipping_type]=Physical
    add_paydata[shipping_country]=DE
    add_paydata[shipping_firstname]=Alfred
    add_paydata[shipping_lastname]=Amazing
    add_paydata[billing_zip]=80939
    add_paydata[billing_city]=München
    add_paydata[billing_type]=Physical
    add_paydata[billing_country]=DE
    add_paydata[billing_firstname]=Alfred
    add_paydata[billing_lastname]=Amazing
    add_paydata[storename]= Your Storename
    workorderid= WORKORDERID12345
     */
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
