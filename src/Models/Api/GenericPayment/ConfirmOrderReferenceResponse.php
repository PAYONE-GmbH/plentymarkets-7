<?php

namespace Payone\Models\Api\GenericPayment;

use Payone\Models\Api\ResponseAbstract;

class ConfirmOrderReferenceResponse extends ResponseAbstract implements \JsonSerializable
{
    protected $workOrderId;

    /**
     * @param bool $success
     * @param string $errorMessage
     * @param string $workorderId
     *
     * @return $this
     */
    public function init(
        bool $success = false,
        string $errorMessage = '',
        string $workorderId = ''
    )
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
     * @return string
     */
    public function getWorkOrderId(): string
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
