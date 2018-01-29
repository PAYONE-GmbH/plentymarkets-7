<?php

namespace Payone\Models\Api;

use Payone\Models\Api\Clearing\ClearingAbstract;

/**
 * Class AuthResponse
 */
class AuthResponse extends ResponseAbstract implements \JsonSerializable
{
    /**
     * @var ClearingAbstract
     */
    private $clearing;

    /**
     * @var string
     */
    private $redirecturl;

    /**
     * @param $success
     * @param $errorMessage
     * @param $transactionID
     * @param ClearingAbstract $clearing
     * @param string $redirecturl
     *
     * @return $this
     */
    public function init($success, $errorMessage, $transactionID, ClearingAbstract $clearing = null, $redirecturl = '')
    {
        $this->success = $success;
        $this->errorMessage = $errorMessage;
        $this->transactionID = $transactionID;
        $this->clearing = $clearing;
        $this->redirecturl = $redirecturl;

        return $this;
    }

    /**
     * Getter for Redirecturl
     *
     * @return string
     */
    public function getRedirecturl(): string
    {
        return $this->redirecturl;
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
        return parent::jsonSerialize() +
            [
                'clearing' => $this->clearing->jsonSerialize(),
                'redirecturl' => $this->redirecturl,
            ];
    }
}
