<?php


use Payone\Models\CreditCardCheckRequestData;
use Payone\Models\PaymentConfig\ApiCredentials;

class CreditCardCheckRequestDataTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var CreditCardCheckRequestData
     */
    private $requestData;


    public function setUp()
    {
        $configRepo = self::createMock(ApiCredentials::class);
        $configRepo->expects($this->any())->method('getPortalid')->willReturn('2000001');
        $configRepo->expects($this->any())->method('getMid')->willReturn('10001');
        $configRepo->expects($this->any())->method('getAid')->willReturn('10002');
        $configRepo->expects($this->any())->method('getKey')->willReturn('secret');
        $this->requestData = new CreditCardCheckRequestData($configRepo);
    }

    public function testHashCorrect()
    {
        $this->assertSame(
            '002383167abd4693627b0b292ffd84c7f50c46b1adb30354b78ac83eb5fd5d6f1ec0244d94a14d26a4718a044e3e0d74',
            $this->requestData->jsonSerialize()['hash']
        );
    }
}