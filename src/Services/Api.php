<?php

namespace Payone\Services;

use Payone\Adapter\Logger;
use Payone\Models\Api\AuthResponse;
use Payone\Models\Api\Response;
use Payone\Models\Api\ResponseAbstract;
use Payone\Models\Api\ResponseFactory;
use Payone\PluginConstants;
use Plenty\Modules\Plugin\Libs\Contracts\LibraryCallContract;

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
    const REQUEST_TYPE_DEBIT = 'Debit';

    /**
     * @var LibraryCallContract
     */
    private $libCall;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * Api constructor.
     * @param LibraryCallContract $libCall
     * @param Logger $logger
     */
    public function __construct(
        LibraryCallContract $libCall,
        Logger $logger
    ) {
        $this->libCall = $libCall;
        $this->logger = $logger;
    }

    /**
     * @param $requestParams
     *
     * @throws \Exception
     *
     * @return AuthResponse
     */
    public function doAuth($requestParams): AuthResponse
    {
        return $this->doLibCall((self::REQUEST_TYPE_AUTH), $requestParams);
    }

    /**
     * @param $requestParams
     *
     * @throws \Exception
     *
     * @return AuthResponse
     */
    public function doPreAuth($requestParams): AuthResponse
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
     * @param $requestParams
     *
     * @return Response
     */
    public function doDebit($requestParams): Response
    {
        return $this->doLibCall((self::REQUEST_TYPE_DEBIT), $requestParams);
    }

    /**
     * @param string $call request type
     * @param $requestParams
     *
     * @return Response|ResponseAbstract|AuthResponse
     */
    public function doLibCall($call, $requestParams): ResponseAbstract
    {
        $this->logger->setIdentifier(__METHOD__)->debug('Api.' . $this->getCallAction($call), $requestParams);
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
