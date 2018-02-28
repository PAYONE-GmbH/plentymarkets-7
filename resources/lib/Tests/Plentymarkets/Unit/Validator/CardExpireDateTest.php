<?php

namespace Payone\Tests\Unit\Validator;

use Payone\Models\CreditCardCheckResponse;
use Payone\Models\PaymentConfig\CreditCardExpiration;
use Payone\Validator\CardExpireDate;

class CardExpireDateTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CreditCardExpiration
     */
    private $expireDateRepo;
    /**
     * @var CardExpireDate
     */
    private $validator;

    /**
     * CardExpireDateTest constructor.
     */
    protected function setUp()
    {
        $this->expireDateRepo = self::createMock(CreditCardExpiration::class);
        $this->expireDateRepo->method('getMinExpireTimeInDays')->willReturn(30);
        $this->validator = new CardExpireDate(
            $this->expireDateRepo
        );
    }

    public function testDataValid()
    {
        self::assertTrue(
            $this->validator->validate(
                \DateTime::createFromFormat('Y-m-d', '2018-02-28'),
                \DateTime::createFromFormat('Y-m-d', '2018-01-18')
            )
        );
    }

    public function testDataInvalid()
    {
        self::expectException(\Exception::class);
        self::assertTrue($this->validator->validate(
            \DateTime::createFromFormat('Y-m-d', '2018-01-28'),
            \DateTime::createFromFormat('Y-m-d', '2018-01-18'))
        );
    }

    public function testDateFromThePast()
    {
        self::expectException(\Exception::class);
        $this->validator->validate(
            \DateTime::createFromFormat('Y-m-d', '2017-02-28'),
            \DateTime::createFromFormat('Y-m-d', '2018-01-18'));
    }

    public function testDataValidMin()
    {
        self::assertTrue(
            $this->validator->validate(
                \DateTime::createFromFormat('Y-m-d', '2018-01-31'),
                \DateTime::createFromFormat('Y-m-d', '2018-01-01')
            )
        );
    }

    public function testDataInValidMin()
    {
        self::expectException(\Exception::class);
        $this->validator->validate(
            \DateTime::createFromFormat('Y-m-d', '2018-01-30'),
            \DateTime::createFromFormat('Y-m-d', '2018-01-01')
        );
    }

    public function testDataValidDefaultToday()
    {
        self::assertTrue($this->validator->validate(
            \DateTime::createFromFormat('Y-m-d', '2999-01-30')
        ));
    }

    public function testDataInValidDefaultToday()
    {
        self::expectException(\Exception::class);
        $this->validator->validate(\DateTime::createFromFormat('Y-m-d', '2000-01-30'));
    }

    public function testLastDayOfMonthAndZeroDays()
    {
        $response = new CreditCardCheckResponse();
        $response->init('', '', '', '', '1802');
        $this->expireDateRepo = $this->expireDateRepo = self::createMock(CreditCardExpiration::class);
        $this->expireDateRepo->method('getMinExpireTimeInDays')->willReturn(0);
        $this->validator = new CardExpireDate(
            $this->expireDateRepo
        );
        self::assertTrue($this->validator->validate(
            \DateTime::createFromFormat('Y-m-d', $response->getCardexpiredate()),
            \DateTime::createFromFormat('Y-m-d', '2018-02-28')

        ));
    }
}
