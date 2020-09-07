<?php

namespace Payone\Models\Api;


/**
 * Class ManagemandateResponse
 */
class GetInvoiceResponse extends ResponseAbstract implements \JsonSerializable
{

    private $base64 = '';

    /**
     * @param $success
     * @param $errorMessage
     * @param $base64
     *
     * @return $this
     */
    public function init($success, $errorMessage, $base64)
    {
        $this->success = $success;
        $this->errorMessage = $errorMessage;
        $this->base64 = $base64;
        return $this;
    }

    public function jsonSerialize(): array
    {
        return parent::jsonSerialize() +
            [
                'document' => $this->base64
            ];
    }

    /**
     * Getter for Base64
     *
     * @return string
     */
    public function getBase64(): string
    {
        return $this->base64;
    }
}
