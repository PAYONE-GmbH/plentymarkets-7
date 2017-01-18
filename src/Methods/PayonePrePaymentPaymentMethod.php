<?php // strict

namespace Payone\Methods;

use Plenty\Modules\Account\Contact\Contracts\ContactRepositoryContract;
use Plenty\Modules\Basket\Contracts\BasketRepositoryContract;
use Plenty\Modules\Payment\Method\Contracts\PaymentMethodService;
use Plenty\Plugin\ConfigRepository;

/**
 * Class PayonePrePaymentPaymentMethod
 *
 * @package Payone\Methods
 */
class PayonePrePaymentPaymentMethod extends PaymentContract
{
    const PAYMENT_CODE = 'PAYONE_PRE_PAYMENT';
}
