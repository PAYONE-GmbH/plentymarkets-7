<?php

namespace Payone\Models;

use Payone\Adapter\Logger;
use Payone\Adapter\SessionStorage;
use Payone\Helpers\PaymentHelper;
use Payone\Models\Api\Response;
use Payone\Services\Api;

class ApiResponseCache
{
    /**
     * @var PaymentHelper
     */
    private $paymentHelper;
    /**
     * @var SessionStorage
     */
    private $sessionStorage;
    /**
     * @var Logger
     */
    private $logger;

    /**
     * ApiResponseCache constructor.
     *
     * @param PaymentHelper $paymentHelper
     * @param SessionStorage $sessionStorage
     * @param Logger $logger
     */
    public function __construct(
        PaymentHelper $paymentHelper,
        SessionStorage $sessionStorage,
        Logger $logger
    ) {
        $this->paymentHelper = $paymentHelper;
        $this->sessionStorage = $sessionStorage;
        $this->logger = $logger;
    }

    /**
     * @param $paymentCode
     * @param $lastUpdatedAt
     *
     * @return Response|void
     */
    public function loadPreCheck($paymentCode, $lastUpdatedAt = null)
    {
        $requestHash = $this->getRequestHash($paymentCode, Api::REQUEST_TYPE_AUTH);
        $sessionData = $this->sessionStorage->getSessionValue($requestHash);

        if ($lastUpdatedAt && (string) $lastUpdatedAt != (string) $sessionData['basketUpdatedAt']) {
            return null;
        }

        if (
            $sessionData
            && isset($sessionData['response'])
            && $sessionData['response'] instanceof Response
        ) {
            return $sessionData['response'];
        }
    }

    /**
     * @param $paymentCode
     * @param $response
     * @param $updatedAt
     */
    public function storePreCheckResponse($paymentCode, Response $response, $updatedAt)
    {
        $requestHash = $this->getRequestHash($paymentCode, Api::REQUEST_TYPE_AUTH);
        $this->sessionStorage->setSessionValue(
            $requestHash,
            [
                'basketUpdatedAt' => (string) $updatedAt,
                'response' => $response,
            ]
        );
    }

    /**
     * @param $paymentCode
     */
    public function deletePreCheckResponse($paymentCode)
    {
        $requestHash = $this->getRequestHash($paymentCode, Api::REQUEST_TYPE_AUTH);
        $this->sessionStorage->setSessionValue(
            $requestHash,
            null
        );
    }

    public function deletePreCheckResponses()
    {
        foreach ($this->paymentHelper->getPaymentCodes() as $paymentCode) {
            $requestHash = $this->getRequestHash($paymentCode, Api::REQUEST_TYPE_AUTH);
            $this->sessionStorage->setSessionValue(
                $requestHash,
                null
            );
        }
    }

    /**
     * @param string $paymentCode
     * @param string $requestType
     *
     * @return string
     *
     * @internal param array|string $requestData
     */
    private function getRequestHash(string $paymentCode, string $requestType)
    {
        //can't user last updated date; e.g. cart is already deleted when preauth is placed
        return $paymentCode . $requestType;

        return md5(json_encode([$paymentCode, $requestType]));
    }
}
