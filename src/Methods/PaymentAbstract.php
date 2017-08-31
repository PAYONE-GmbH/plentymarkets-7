<?php

namespace Payone\Methods;

use Payone\Adapter\Config as ConfigAdapter;
use Payone\PluginConstants;
use Plenty\Modules\Basket\Contracts\BasketRepositoryContract;
use Plenty\Modules\Payment\Method\Contracts\PaymentMethodService;
use Plenty\Plugin\Application;

abstract class PaymentAbstract extends PaymentMethodService
{
    const PAYMENT_CODE = 'Payone';

    /**
     * @var ConfigAdapter
     */
    private $configRepo;
    /**
     * @var PaymentValidator
     */
    private $paymentValidator;

    /**
     * @var Application
     */
    private $app;
    /**
     * PayonePaymentMethod constructor.
     *
     * @param BasketRepositoryContract $basketRepo
     * @param PaymentValidator $paymentValidator
     * @param ConfigAdapter $configRepo
     */
    public function __construct(
        Application $application,
        PaymentValidator $paymentValidator,
        ConfigAdapter $configRepo
    ) {
        $this->paymentValidator = $paymentValidator;
        $this->configRepo = $configRepo;
        $this->app = $app = $application;
    }


    /**
     * Check whether Payolution is active or not
     *
     * @return bool
     */
    public function isActive()
    {
        return (bool) $this->configRepo->get( $this::PAYMENT_CODE . '.active')
            && $this->paymentValidator->validate($this);
    }

    /**
     * Get shown name
     *
     * @return string
     */
    public function getName(): string
    {
        $name = $this->configRepo->get( $this::PAYMENT_CODE . '.name');

        return $name ? (string) $name : '';
    }

    /**
     * Get Payolution Fee
     *
     * @return float
     */
    public function getFee(): float
    {
        return 0.;
    }

    /**
     * Get Payolution Icon
     *
     * @return string
     */
    public function getIcon(): string
    {
        $pluginPath = $this->app->getUrlPath(PluginConstants::NAME);

        return $pluginPath . '/images/logos/' . $this::PAYMENT_CODE . '.png';
    }

    /**
     * Get PayolutionDescription
     *
     * @return string
     */
    public function getDescription(): string
    {
        $description = $this->configRepo->get( $this::PAYMENT_CODE . '.description');

        return $description ? $description : '';
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this::PAYMENT_CODE;
    }

    /**
     * @return float
     */
    public function getMaxCartAmount()
    {
        $amount = $this->configRepo->get( $this::PAYMENT_CODE . '.maxCartAmount');

        return $amount ? (float) $amount : 0.;
    }

    /**
     * @return float
     */
    public function getMinCartAmount()
    {
        $amount = $this->configRepo->get( $this::PAYMENT_CODE . '.minCartAmount');

        return $amount ? (float) $amount : 0.;
    }


    /**
     * @return array
     */
    public function getAllowedCountries()
    {
        $countries = explode(
            ',',
            $this->configRepo->get( $this::PAYMENT_CODE . '.allowedCountries')
        );

        return $countries;
    }

}
