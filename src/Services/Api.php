<?php

namespace Payone\Services;

use Payone\Adapter\Logger;
use Payone\Helpers\PaymentHelper;
use Payone\Models\Api\Response;
use Payone\Models\Api\ResponseAbstract;
use Payone\Models\Api\ResponseFactory;
use Payone\PluginConstants;
use Plenty\Modules\Plugin\Libs\Contracts\LibraryCallContract;
use Plenty\Plugin\ConfigRepository;

/**
 * Class Api
 */
class Api
{
    const REQUEST_MODE_TEST = 'test';
    const REQUEST_MODE_LIVE = 'live';

    const REQUEST_TYPE_AUTH = 'Auth';
    const REQUEST_TYPE_PRE_AUTH = 'PreAuth';
    const REQUEST_TYPE_RE_AUTH = 'ReAuth';
    const REQUEST_TYPE_CAPTURE = 'Capture';
    const REQUEST_TYPE_REVERSAL = 'Reversal';
    const REQUEST_TYPE_REFUND = 'Refund';
    const REQUEST_TYPE_CALCULATION = 'Calculation';

    /**
     * @var LibraryCallContract
     */
    private $libCall;

    /**
     * @var PaymentHelper
     */
    private $paymentHelper;

    /**
     * @var ConfigRepository
     */
    private $config;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * Api constructor.
     *
     * @param LibraryCallContract $libCall
     * @param PaymentHelper $paymentHelper
     * @param ConfigRepository $configRepository
     * @param Logger $logger
     */
    public function __construct(
        LibraryCallContract $libCall,
        PaymentHelper $paymentHelper,
        ConfigRepository $configRepository,
        Logger $logger
    ) {
        $this->libCall = $libCall;
        $this->paymentHelper = $paymentHelper;
        $this->config = $configRepository;
        $this->logger = $logger;
    }

    /**
     * @param $requestParams
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function doAuth($requestParams): Response
    {
        return $this->doLibCall((self::REQUEST_TYPE_AUTH), $requestParams);
    }

    /**
     * @param $requestParams
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function doPreAuth($requestParams): Response
    {
        return $this->doLibCall((self::REQUEST_TYPE_PRE_AUTH), $requestParams);
    }

    /**
     * @param $requestParams
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function doReversal($requestParams): Response
    {
        return $this->doLibCall((self::REQUEST_TYPE_REVERSAL), $requestParams);
    }

    /**
     * @param $requestParams
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function doCapture($requestParams): Response
    {
        return $this->doLibCall((self::REQUEST_TYPE_CAPTURE), $requestParams);
    }

    /**
     * @param $requestParams
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function doRefund($requestParams): Response
    {
        return $this->doLibCall((self::REQUEST_TYPE_REFUND), $requestParams);
    }

    /**
     * @param $requestParams
     *
     * @return Response
     */
    public function doReAuth($requestParams): Response
    {
        return $this->doLibCall((self::REQUEST_TYPE_RE_AUTH), $requestParams);
    }

    /**
     * @param string $call request type
     * @param $requestParams
     *
     * @return Response|ResponseAbstract
     */
    public function doLibCall($call, $requestParams): ResponseAbstract
    {
        try {
            $response = $this->libCall->call(
                PluginConstants::NAME . '::' . $this->getCallAction($call), $requestParams
            );
        } catch (\Exception $e) {
            // something unexpected happened
            $response = ['errorMessage' => $e->getMessage()];
        }
        if (isset($response['error'])) {
            //sdk error
            $response = ['errorMessage' => json_encode($response)];
        }
        $this->logger->setIdentifier(__METHOD__)->debug('Api.' . $this->getCallAction($call), $response);
        $success = $response['success'] ?? false;
        if (!$success) {// log all errors including successful but invalid requests
            $this->logger->setIdentifier(__METHOD__)->error('Api.' . $this->getCallAction($call), $response);
        }

        return ResponseFactory::create($call, $response);
    }

    /**
     * @param string $requestType
     *
     * @return string
     */
    private function getCallAction($requestType): string
    {
        return 'do' . $requestType;
    }
}
