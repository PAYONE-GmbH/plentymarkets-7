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
    /**
     * @var string
     */
    protected $clearingtype = ClearingTypes::WALLET;

    /**
     * @var string
     */
    protected $wallettype = WalletTypes::AMAZON_PAYMENTS;

    /**
     * @var string
     */
    protected $workorderid;

    /**
     * @var string
     */
    protected $reference;

    /**
     * @var string
     */
    protected $currency;

    /**
     * @var string
     */
    protected $amount;

    /**
     * @var string[]
     */
    protected $add_paydata = [
        'amazon_reference_id' => '',
    ];

    /**
     * @var RedirectUrls
     */
    protected $urls;

    /**
     * PayPal constructor.
     *
     * @param GenericAuthorizationRequest $authorizationRequest
     * @param RedirectUrls $urls
     * @param string $amount
     * @param string $workOrderId
     * @param string $reference
     * @param string $currency
     * @param string $amazonReferenceId
     */
    public function __construct(
        GenericAuthorizationRequest $authorizationRequest,
        RedirectUrls $urls,
        string $amount,
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
     * @return string
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
