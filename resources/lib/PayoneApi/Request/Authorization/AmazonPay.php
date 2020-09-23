<?php

namespace PayoneApi\Request\Authorization;

use PayoneApi\Request\AuthorizationRequestAbstract;
use PayoneApi\Request\ClearingTypes;
use PayoneApi\Request\GenericAuthorizationRequest;
use PayoneApi\Request\Parts\RedirectUrls;
use PayoneApi\Request\WalletTypes;

/**
 * Class AmazonPay
 */
class AmazonPay extends AuthorizationRequestAbstract
{
    /** @var string */
    const WALLET_TYPE = WalletTypes::AMAZON_PAYMENTS;

    /** @var string */
    protected $clearingtype = ClearingTypes::WALLET;

    /** @var string */
    private $wallettype = self::WALLET_TYPE;

    /** @var string */
    private $workorderid;

    /** @var string */
    private $reference;

    /** @var string */
    private $currency;

    private $amount;

    private $add_paydata = [
        'amazon_reference_id' => '',
    ];

    /**
     * @var RedirectUrls
     */
    private $urls;

    /**
     * PayPal constructor.
     *
     * @param GenericAuthorizationRequest $authorizationRequest
     * @param RedirectUrls $urls
     * @param $amount
     * @param string $workOrderId
     * @param string $reference
     * @param string $currency
     * @param string $amazonReferenceId
     */
    public function __construct(
        GenericAuthorizationRequest $authorizationRequest,
        RedirectUrls $urls,
        $amount,
        string $workOrderId,
        string $reference,
        string $currency,
        string $amazonReferenceId
    )
    {
        $this->authorizationRequest = $authorizationRequest;
        $this->urls = $urls;

        $this->amount = $amount;
        $this->workorderid = $workOrderId;
        $this->reference = $reference;
        $this->currency = $currency;
        $this->add_paydata['amazon_reference_id'] = $amazonReferenceId;
    }

    /**
     * @return string
     */
    public function getClearingtype(): string
    {
        return $this->clearingtype;
    }

    /**
     * @return string
     */
    public function getWallettype(): string
    {
        return $this->wallettype;
    }

    /**
     * @return string
     */
    public function getWorkorderid(): string
    {
        return $this->workorderid;
    }

    /**
     * @return string
     */
    public function getReference(): string
    {
        return $this->reference;
    }

    /**
     * @return string
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }

    /**
     * @return mixed
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @return array
     */
    public function getAddPaydata(): array
    {
        return $this->add_paydata;
    }

    /**
     * @return RedirectUrls
     */
    public function getUrls(): RedirectUrls
    {
        return $this->urls;
    }

}
