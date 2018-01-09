<?php

namespace Payone\Tests\Unit\Api\Providers;

use Payone\Providers\Api\Request\AuthDataProvider;

class AuthDataProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AuthDataProvider
     */
    private $resource;

    public function setUp()
    {
        $this->resource = $this->getMockBuilder(AuthDataProvider::class)
            ->disableOriginalConstructor()
            ->setMethods(null)
            ->getMock();
    }

    public function testBasketIdDoesNotExceedMaxLength()
    {
        $uniqueBasketId = 'basket-' . $this->resource->getUniqueBasketId('1234567');
        self::assertTrue(
            strlen($uniqueBasketId) > 7,
            'Basket id too short: ' . $uniqueBasketId . ' "' . strlen($uniqueBasketId) . '" chars.'
        );
        self::assertTrue(
            strlen($uniqueBasketId) <= 20,
            'Basket id too long: ' . $uniqueBasketId . ' "' . strlen($uniqueBasketId) . '" chars.'
        );
    }
}
