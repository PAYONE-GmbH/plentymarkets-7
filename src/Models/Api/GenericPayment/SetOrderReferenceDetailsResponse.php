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

}
