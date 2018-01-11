<?php

namespace Payone\Helpers;

use Plenty\Modules\Basket\Contracts\BasketRepositoryContract;
use Plenty\Modules\Basket\Models\Basket;

/**
 * Class SessionHelper
 */
class SessionHelper
{
    /**
     * @var Basket
     */
    private $basket;

    /**
     * SessionHelper constructor.
     *
     * @param BasketRepositoryContract $basketRepo
     */
    public function __construct(BasketRepositoryContract $basketRepo)
    {
        $this->basket = $basketRepo->load();
    }

    /**
     * @return bool
     */
    public function isLoggedIn()
    {
        //TODO: use account service when working
        return (bool) $this->basket->customerInvoiceAddressId;
    }
}
