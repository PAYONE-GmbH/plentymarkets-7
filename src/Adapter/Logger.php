<?php

namespace Payone\Adapter;

use Payone\Helpers\ShopHelper;
use Payone\PluginConstants;
use Plenty\Log\Contracts\LoggerContract;
use Plenty\Plugin\Log\Loggable;

/**
 * Class Logger
 */
class Logger implements LoggerContract
{
    use Loggable {
        getLogger as private getPlentyLogger;
    }

    const PAYONE_REQUEST_REFERENCE = 'payone_txid';
    /**
     * @var string
     */
    private $identifier;
    /**
     * @var ShopHelper
     */
    private $shopHelper;


    /**
     * @var LoggerContract
     */
    private $logger;
    private $referenceType;
    private $referenceValue;

    /**
     * Logger constructor.
     *
     * @param ShopHelper $shopHelper
     */
    public function __construct(ShopHelper $shopHelper
    ) {
        $this->shopHelper = $shopHelper;
        $this->identifier = __CLASS__;
        $this->logger = $this->getPlentyLogger($this->identifier);
    }

    /**
     * @return LoggerContract
     */
    private function getLogger()
    {
        return $this->logger;
    }

    /**
     * @param string $identifier
     *
     * @return Logger
     */
    public function setIdentifier(string $identifier)
    {
        $this->logger = $this->getPlentyLogger($identifier);
        $this->logger->setReferenceType($this->referenceType);
        $this->logger->setReferenceValue($this->referenceValue);

        return $this;
    }

    /**
     * @param string $code
     * @param array $additionalInfo
     *
     * @return Logger
     */
    public function debug(
        string $code,
        $additionalInfo = null
    ) {
        if ($this->shopHelper->isDebugModeActive()) {
            return $this->critical($code, $additionalInfo);
        }

        $this->getLogger()->debug(PluginConstants::NAME . '::' . $code, $additionalInfo);

        return $this;
    }

    /**
     * @param string $code
     * @param array $additionalInfo
     *
     * @return Logger
     */
    public function info(
        string $code,
        $additionalInfo = null
    ) {
        if ($this->shopHelper->isDebugModeActive()) {
            return $this->critical($code, $additionalInfo);
        }

        $this->getLogger()->info(PluginConstants::NAME . '::' . $code, $additionalInfo);

        return $this;
    }

    /**
     * @param string $code
     * @param array $additionalInfo
     *
     * @return Logger
     */
    public function notice(
        string $code,
        $additionalInfo = null
    ) {
        if ($this->shopHelper->isDebugModeActive()) {
            return $this->critical($code, $additionalInfo);
        }

        $this->getLogger()->notice(PluginConstants::NAME . '::' . $code, $additionalInfo);

        return $this;
    }

    /**
     * @param string $code
     * @param array $additionalInfo
     *
     * @return Logger
     */
    public function warning(
        string $code,
        $additionalInfo = null
    ) {
        if ($this->shopHelper->isDebugModeActive()) {
            return $this->critical($code, $additionalInfo);
        }

        $this->getLogger()->warning(PluginConstants::NAME . '::' . $code, $additionalInfo);

        return $this;
    }

    /**
     * @param string $code
     * @param array|null $additionalInfo
     *
     * @return Logger
     */
    public function error(
        string $code,
        $additionalInfo = null
    ) {
        $this->getLogger()->error(PluginConstants::NAME . '::' . $code, $additionalInfo);

        return $this;
    }

    /**
     * @param string $code
     * @param array $additionalInfo
     *
     * @return Logger
     */
    public function critical(
        string $code,
        $additionalInfo = null
    ) {
        $this->getLogger()->critical(PluginConstants::NAME . '::' . $code, $additionalInfo);

        return $this;
    }

    /**
     * @param string $code
     * @param array $additionalInfo
     *
     * @return Logger
     */
    public function alert(
        string $code,
        $additionalInfo = null
    ) {
        $this->getLogger()->alert(PluginConstants::NAME . '::' . $code, $additionalInfo);

        return $this;
    }

    /**
     * @param $code
     * @param array $additionalInfo
     *
     * @return Logger
     */
    public function emergency(
        $code,
        $additionalInfo = null
    ) {
        $this->getLogger()->emergency(PluginConstants::NAME . '::' . $code, $additionalInfo);

        return $this;
    }

    /**
     * @param \Exception $exception
     * @param int $traceDepth
     *
     * @return Logger
     */
    public function logException(
        \Exception $exception
    ) {
        $this->getLogger()->logException($exception);

        return $this;
    }

    /**
     * @param string $referenceType
     * @return Logger
     */
    public function setReferenceType(
        string $referenceType
    ): LoggerContract {

        $this->referenceType = $referenceType;

        return $this;
    }

    /**
     * @param $referenceValue
     * @return Logger
     */
    public function setReferenceValue(
        $referenceValue
    ): LoggerContract {

        $this->referenceValue = $referenceValue;
        return $this;
    }
}
