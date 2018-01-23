<?php

namespace Payone\Tests\Unit\Models;

use Payone\Adapter\Config;
use Payone\Models\CreditcardTypes;

class CreditcardTypesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var  Config
     */
    private $configRepo;

    /**
     * @var CreditcardTypes
     */
    private $ccTyoes;

    public function setUp()
    {
        $this->configRepo = self::createMock(Config::class);
        $this->configRepo->expects($this->any())->method('get')
            ->willReturn('');
        $this->ccTyoes = new CreditcardTypes($this->configRepo);
    }

    public function testRetrievingTypesWithNoAllowedTypes()
    {
        self::assertSame(
            [],
            $this->ccTyoes->getAllowedTypes()
        );
    }

    public function testRetrievingAllowedWithAllAllowedTypes()
    {
        $this->configRepo = self::createMock(Config::class);
        $this->configRepo->expects($this->any())
            ->method('get')
            ->willReturn('all');
        $this->ccTyoes = new CreditcardTypes($this->configRepo);

        self::assertSame(
            $this->ccTyoes->getCreditCardTypes(),
            $this->ccTyoes->getAllowedTypes()
        );
    }

    public function testRetrievingAllowedWithTwoAllowedTypes()
    {
        $this->configRepo = self::createMock(Config::class);
        $this->configRepo->expects($this->any())
            ->method('get')
            ->willReturn('PAYONE_PAYONE_CREDIT_CARD.allowedCardTypes.V, PAYONE_PAYONE_CREDIT_CARD.allowedCardTypes.M');
        $this->ccTyoes = new CreditcardTypes($this->configRepo);

        self::assertSame(
            ['V', 'M'],
            $this->ccTyoes->getAllowedTypes()
        );
    }
}
