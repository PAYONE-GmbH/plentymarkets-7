<?php

namespace Payone\Adapter;

use Payone\Helpers\ShopHelper;
use Payone\PluginConstants;
use Plenty\Log\Contracts\LoggerContract;
use Plenty\Plugin\Log\Loggable;

/**
 * Class Logger
 */
class Logger //implements LoggerContract
{
    use Loggable;

    /**
     * @var string
     */
    private $identifier;
    /**
     * @var ShopHelper
     */
    private $shopHelper;

    /**
     * Logger constructor.
     * @param ShopHelper $shopHelper
     */
    public function __construct(ShopHelper $shopHelper
    ) {
        $this->shopHelper = $shopHelper;
        $this->identifier = __CLASS__;
    }

    /**
     * @param string $identifier
     *
     * @return $this
     */
    public function setIdentifier(string $identifier)
    {
        $this->identifier = $identifier;

        return $this;
    }

    /**
     * @param string $code
     * @param array $additionalInfo
     *
     * @return mixed
     */
    public function debug(
        string $code,
        $additionalInfo = null
    ) {
        if ($this->shopHelper->isDebugModeActive()) {
            return $this->critical($code, $additionalInfo);
        }
        return $this->getLogger($this->identifier)->debug(PluginConstants::NAME . '::' . $code, $additionalInfo);
    }

    /**
     * @param string $code
     * @param array $additionalInfo
     *
     * @return mixed
     */
    public function info(
        string $code,
        $additionalInfo = null
    ) {
        if ($this->shopHelper->isDebugModeActive()) {
            return $this->critical($code, $additionalInfo);
        }
        return $this->getLogger($this->identifier)->info(PluginConstants::NAME . '::' . $code, $additionalInfo);
    }

    /**
     * @param string $code
     * @param array $additionalInfo
     *
     * @return mixed
     */
    public function notice(
        string $code,
        $additionalInfo = null
    ) {
        if ($this->shopHelper->isDebugModeActive()) {
            return $this->critical($code, $additionalInfo);
        }
        return $this->getLogger($this->identifier)->notice(PluginConstants::NAME . '::' . $code, $additionalInfo);
    }

    /**
     * @param string $code
     * @param array $additionalInfo
     *
     * @return mixed
     */
    public function warning(
        string $code,
        $additionalInfo = null
    ) {
        if ($this->shopHelper->isDebugModeActive()) {
            return $this->critical($code, $additionalInfo);
        }
        return $this->getLogger($this->identifier)->warning(PluginConstants::NAME . '::' . $code, $additionalInfo);
    }

    /**
     * @param string $code
     * @param array|null $additionalInfo
     *
     * @return mixed
     */
    public function error(
        string $code,
        $additionalInfo = null
    ) {
        return $this->getLogger($this->identifier)->error(PluginConstants::NAME . '::' . $code, $additionalInfo);
    }

    /**
     * @param string $code
     * @param array $additionalInfo
     *
     * @return mixed
     */
    public function critical(
        string $code,
        $additionalInfo = null
    ) {
        return $this->getLogger($this->identifier)->critical(PluginConstants::NAME . '::' . $code, $additionalInfo);
    }

    /**
     * @param string $code
     * @param array $additionalInfo
     *
     * @return mixed
     */
    public function alert(
        string $code,
        $additionalInfo = null
    ) {
        return $this->getLogger($this->identifier)->alert(PluginConstants::NAME . '::' . $code, $additionalInfo);
    }

    /**
     * @param $code
     * @param array $additionalInfo
     *
     * @return mixed
     */
    public function emergency(
        $code,
        $additionalInfo = null
    ) {
        return $this->getLogger($this->identifier)->emergency(PluginConstants::NAME . '::' . $code, $additionalInfo);
    }

    /**
     * @param \Exception $exception
     * @param int $traceDepth
     *
     * @return mixed
     */
    public function logException(
        \Exception $exception,
        int $traceDepth = 3
    ) {
        return $this->getLogger($this->identifier)->logException($exception, $traceDepth);
    }

    /**
     * @param string $referenceType
     *
     * @return LoggerContract
     */
    public function setReferenceType(
        string $referenceType
    ): LoggerContract {
        return $this->getLogger($this->identifier)->setReferenceValue($referenceType);
    }

    /**
     * @param $referenceValue
     *
     * @return LoggerContract
     */
    public function setReferenceValue(
        $referenceValue
    ): LoggerContract {
        return $this->getLogger($this->identifier)->setReferenceValue($referenceValue);
    }

    /**
     * @param string $referenceType
     * @param int $referenceValue
     *
     * @return LoggerContract
     */
    public function addReference(
        string $referenceType,
        int $referenceValue
    ): LoggerContract {
        return $this->getLogger($this->identifier)->addReference($referenceType, $referenceValue);
    }

    /**
     * @param string $code
     * @param null $additionalInfo
     * @return mixed
     */
    public function report(
        string $code,
        $additionalInfo = null
    ) {
        return $this->getLogger($this->identifier)->report($code, $additionalInfo);
    }
}
