<?php

namespace Payone\Models\Api\GenericPayment;

use Payone\Models\Api\ResponseAbstract;


class StartSessionResponse extends ResponseAbstract implements \JsonSerializable
{
    protected $klarnaClientToken;
    protected $klarnaSessionId;
    protected $klarnaWorkOrderId;

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
        string $klarnaClientToken = '',
        string $klarnaSessionId = '',
        string $klarnaWorkOrderId = ''
    )
    {
        $this->success = $success;
        $this->errorMessage = $errorMessage;
        $this->klarnaClientToken = $klarnaClientToken;
        $this->klarnaSessionId = $klarnaSessionId;
        $this->klarnaWorkOrderId = $klarnaWorkOrderId;


        return $this;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return parent::jsonSerialize() +
            [
                'klarnaClientToken' => $this->klarnaClientToken,
                'klarnaSessionId' => $this->klarnaSessionId,
                'klarnaWorkOrderId' => $this->klarnaWorkOrderId
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
    public function getKlarnaClientToken()
    {
        return $this->klarnaClientToken;
    }

    /**
     * @return mixed
     */
    public function getKlarnaSessionId()
    {
        return $this->klarnaSessionId;
    }

    /**
     * @return mixed
     */
    public function getKlarnaWorkOrderId()
    {
        return $this->klarnaWorkOrderId;
    }



}
