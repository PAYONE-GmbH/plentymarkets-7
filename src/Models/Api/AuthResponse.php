<?php

namespace Payone\Models\Api;

use Payone\Models\Api\Clearing\ClearingAbstract;

/**
 * Class Response
 */
class AuthResponse extends ResponseAbstract implements \JsonSerializable
{
    /**
     * @var ClearingAbstract
     */
    private $clearing;

    /**
     * @param $success
     * @param $errorMessage
     * @param $transactionID
     * @param ClearingAbstract $clearing
     *
     * @return $this
     */
    public function init($success, $errorMessage, $transactionID, ClearingAbstract $clearing)
    {
        $this->success = $success;
        $this->errorMessage = $errorMessage;
        $this->transactionID = $transactionID;
        $this->clearing = $clearing;

        return $this;
    }

    /**
     * Getter for Clearing
     *
     * @return ClearingAbstract
     */
    public function getClearing(): ClearingAbstract
    {
        return $this->clearing;
    }

    public function jsonSerialize(): array
    {
        return parent::jsonSerialize() + ['clearing' => $this->clearing->jsonSerialize()];
    }
}
