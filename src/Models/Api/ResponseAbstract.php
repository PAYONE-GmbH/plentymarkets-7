<?php

namespace Payone\Models\Api;

/**
 * Class Response
 */
abstract class ResponseAbstract implements \JsonSerializable
{
    /**
     * @var bool
     */
    protected $success;
    /**
     * @var string
     */
    protected $errorMessage;

    /**
     * @var string
     */
    protected $transactionID;

    /**
     * Request success
     *
     * @return bool
     */
    public function getSuccess(): bool
    {
        return (bool) $this->success;
    }

    /**
     * Get full error description from response
     *
     * @return string
     */
    public function getErrorMessage(): string
    {
        if ($this->getSuccess()) {
            return '';
        }

        return (string) $this->errorMessage;
    }

    /**
     * Get the transaction id passed in request
     *
     * @return string
     */
    public function getTransactionID(): string
    {
        return (string) $this->transactionID;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'success' => $this->success,
            'errorMessage' => $this->errorMessage,
            'transactionID' => $this->transactionID,
        ];
    }
}
