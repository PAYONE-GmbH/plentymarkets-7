<?php

namespace PayoneApi\Response;

/**
 * Class ClientErrorResponse
 */
class ClientErrorResponse extends ResponseDataAbstract implements ResponseContract
{
    /** @var string */
    private $errorMessage;

    /**
     * ClientErrorResponse constructor.
     *
     * @param string $errorMessage
     */
    public function __construct($errorMessage)
    {
        $this->errorMessage = $errorMessage;
    }

    /**
     * @return bool
     */
    public function getSuccess()
    {
        return false;
    }

    /**
     * @return string
     */
    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return '';
    }

    /**
     * @return string
     */
    public function getTransactionID()
    {
        return '';
    }
}
