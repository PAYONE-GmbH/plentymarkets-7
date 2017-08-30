<?php

namespace Payone\Tests\Integration\Unit;

use Payone\Tests\Integration\Mock\SdkRestApi;
use PHPUnit\Framework\TestCase;

/**
 * Class doRequestAbstract
 */
abstract class doRequestAbstract extends TestCase
{
    protected $payload;

    protected $payloadJson;

    protected function setUp()
    {
        $this->payload = $this->loadPayloadJson();
        $this->setPayLoad($this->payload);
    }

    /**
     * @param $payload
     */
    protected function setPayLoad($payload)
    {
        SdkRestApi::setPayload($payload);
    }

    /**
     * @throws \Exception
     *
     * @return mixed
     */
    private function loadPayloadJson()
    {
        $payload = \json_decode($this->payloadJson, true);
        if ($payload === false) {
            throw new \Exception('Invalid JSON payload provided.');
        }

        return $payload;
    }
}
