<?php
namespace Payone\Methods;

use Plenty\Modules\Payment\Method\Contracts\PaymentMethodService;
use Plenty\Modules\Basket\Contracts\BasketRepositoryContract;
use Plenty\Plugin\ConfigRepository;

abstract class PaymentContract extends PaymentMethodService
{
    const PAYMENT_CODE = 'Payone';
    /**
     * @var BasketRepositoryContract
     */
    private $basketRepo;
    /**
     * @var ConfigRepository
     */
    private $configRepo;

    /**
     * PayonePaymentMethod constructor.
     *
     * @param BasketRepositoryContract $basketRepo
     * @param ConfigRepository $configRepo
     */
    public function __construct(
        BasketRepositoryContract $basketRepo,
        ConfigRepository $configRepo
    ) {
        $this->basketRepo = $basketRepo;
        $this->configRepo = $configRepo;
    }

    /**
     * Check whether Payone is active or not
     *
     * @return bool
     */
    public function isActive(): bool
    {
        return true; //TODO
        return (bool)$this->configRepo->get($this::PAYMENT_CODE . '.active');
    }

    /**
     * Get shown name
     *
     * @return string
     */
    public function getName(): string
    {
        $name = $this->configRepo->get($this::PAYMENT_CODE . '.name');
        return $name ? $name : '';
    }

    /**
     * Get Payone Fee
     *
     * @return float
     */
    public function getFee(): float
    {
        return 0.;
    }

    /**
     * Get Payone Icon
     *
     * @return string
     */
    public function getIcon(): string
    {
        return '';
    }

    /**
     * Get PayoneDescription
     *
     * @return string
     */
    public function getDescription(): string
    {
        $description = $this->configRepo->get($this::PAYMENT_CODE . '.description');
        return $description ? $description : '';
    }
}
