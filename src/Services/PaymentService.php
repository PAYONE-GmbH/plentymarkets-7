<?php //strict

namespace Payone\Services;

use Plenty\Modules\Basket\Models\Basket;

/**
 * Class PaymentService
 */
class PaymentService
{

    /**
     * Get the type of payment from the content of the PayPal container
     *
     * @return string
     */
    public function getReturnType()
    {
        /*  'redirectUrl'|'externalContentUrl'|'errorCode'|'continue'  */
        return 'htmlContent';
    }

    /**
     * Get the PayPal payment content
     *
     * @param Basket $basket
     * @param string $mode
     * @return string
     */
    public function getPaymentContent(Basket $basket, $mode = ''): string
    {
        return 'html content';
    }


}
