<?php

namespace Payone\Services;

use Payone\Helpers\PaymentHelper;
use Payone\PluginConstants;
use Plenty\Modules\Plugin\Libs\Contracts\LibraryCallContract;
use Plenty\Plugin\Log\Loggable;

/**
 * Class Api
 */
class Api
{
    use Loggable;
    /**
     * @var LibraryCallContract
     */
    private $libCall;

    /**
     * @var PaymentHelper
     */
    private $paymentHelper;

    /**
     * Api constructor.
     *
     * @param LibraryCallContract $libCall
     * @param PaymentHelper $paymentHelper
     */
    public function __construct(LibraryCallContract $libCall, PaymentHelper $paymentHelper)
    {
        $this->libCall = $libCall;
        $this->paymentHelper = $paymentHelper;
    }

    /**
     * @param $paymentCode
     * @param $requestParams
     *
     * @return array
     */
    public function doPreCheck($paymentCode, $requestParams)
    {
        $requestParams['paymentCode'] = $paymentCode;
        $requestParams['systemInfo'] = [
            'vendor' => 'arvatis media GmbH',
            'version' => 7,
            'type' => 'Webshop',
            'url' => 'https://arvatis.plentymarkets-cloud01.com/', //Todo: get from WebstoreRepositoryContract
            'module' => 'plentymarkets 7 Payone plugin',
            'module_version' => 1,
        ];
        $requestParams['context'] = $this->paymentHelper->getApiContextParams($paymentCode);
        $response = $this->libCall->call(PluginConstants::NAME . 'doPreAuth', $requestParams);
        $this->getLogger(__CLASS__ . '::' . __METHOD__)->debug('doPreAuth', $response);

        return $response;
    }
}
