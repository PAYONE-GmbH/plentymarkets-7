<?php

namespace PayoneApi\Response;

/**
 * Class ClientErrorResponse
 */
class ClientErrorResponse extends ResponseDataAbstract implements ResponseContract
{
    /**
     * @var string
     */
    protected $errorMessage;

    /**
     * @var mixed
     */
    protected $requestData;

    /**
     * @var mixed
     */
    protected $responseData;

    /**
     * ClientErrorResponse constructor.
     *
     * @param string $errorMessage
     */
    public function __construct($errorMessage, $requestData = null, $responseData = null)
    {
        $this->errorMessage = $errorMessage;
        $this->requestData = $requestData;
        $this->responseData = $responseData;
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

    /**
     * @return mixed
     */
    public function getRequestData()
    {
        return $this->requestData;
    }

    /**
     * @return mixed
     */
    public function getResponseData()
    {
        return $this->responseData;
    }
}
