<?php

namespace Payone\Models\Api;

/**
 * Class Response
 */
class Response extends ResponseAbstract implements \JsonSerializable
{
    /**
     * @param $success
     * @param $errorMessage
     * @param $transactionID
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
