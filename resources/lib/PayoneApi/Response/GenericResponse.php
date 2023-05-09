<?php

namespace PayoneApi\Response;

use PayoneApi\Lib\Version;

/**
 * Class XmlApiResponse
 */
class GenericResponse extends ResponseDataAbstract implements ResponseContract
{
    /**
     * @var array
     */
    protected $responseData = [];

    /** @var mixed */
    protected $requestdata;

    /**
     * XmlApiResponse constructor.
     *
     * @param array $responseData
     */
    public function __construct(array $responseData)
    {
        $this->responseData = $responseData;
    }

    /**
     * Request success
     *
     * @return bool
     */
    public function getSuccess()
    {
        if (!$this->responseData || $this->getStatus() == 'ERROR') {
            return false;
        }

        return true;
    }

    /**
     * Get full error description from response
     *
     * @return string
     */
    public function getErrorMessage()
    {
        if ($this->getSuccess()) {
            return '';
        }

        $response = 'empty response';
        if ($this->responseData) {
            $response = print_r($this->responseData, true);
        }

        return 'Payone returned an error: ' . $response;
    }

    /**
     * Get the transaction id
     *
     * @return string
     */
    public function getTransactionID()
    {
        if (!isset($this->responseData['txid'])) {
            return '';
        }

        return (string) $this->responseData['txid'];
    }

    /**
     * Getter for ResponseData
     *
     * @return array
     */
    public function getResponseData()
    {
        return $this->responseData;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        if (!isset($this->responseData['status'])) {
            return '';
        }

        return (string) $this->responseData['status'];
    }

    /**
     * @return string
     */
    public function getLibVersion()
    {
        return Version::getVersion();
    }

    /**
     * @return string|null
     */
    public function getRequestdata()
    {
        return $this->requestdata;
    }

    /**
     * @param string|null $requestdata
     */
    public function setRequestdata($requestdata)
    {
        $this->requestdata = $requestdata;
    }
}
