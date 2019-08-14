<?php

namespace PayoneApi\Api;

/**
 * Interface ClientContract
 */
interface ClientContract
{
    /**
     * @param array $data
     *
     * @return string
     */
    public function doRequest($data);

    /**
     * @param string $method
     */
    public function setMethod($method);

    /**
     * @param string $url
     */
    public function setEndpointUrl($url);

    /**
     * @param string $key
     * @param string $value
     */
    public function addHeader($key, $value);
}
