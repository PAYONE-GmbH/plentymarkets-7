<?php

namespace Payone\Models\Api;

/**
 * Class Response
 */
class Response extends ResponseAbstract implements \JsonSerializable
{
    /**
     * @param bool $success
     * @param string $errorMessage
     * @param string $shortId
     * @param string $uniqueID
     * @param string $transactionID
     * @param $paymentReference
     *
     * @return $this
     */
    public function init($success, $errorMessage, $transactionID)
    {
        $this->success = $success;
        $this->errorMessage = $errorMessage;
        $this->transactionID = $transactionID;

        return $this;
    }
}
