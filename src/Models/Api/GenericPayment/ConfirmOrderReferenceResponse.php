<?php

namespace Payone\Models\Api\GenericPayment;

use Payone\Models\Api\ResponseAbstract;

class ConfirmOrderReferenceResponse extends ResponseAbstract implements \JsonSerializable
{
    private $workOrderId;

    /**
     * @param $success
     * @param $errorMessage
     * @param $workorderId
     *
     * @return $this
     */
    public function init($success, $errorMessage, $workorderId)
    {
        $this->success = $success;
        $this->errorMessage = $errorMessage;
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
                'workOrderId' => $this->workOrderId,
            ];
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
