<?php

namespace PayoneApi\Request\Authorization;

use PayoneApi\Request\AuthorizationRequestAbstract;
use PayoneApi\Request\ClearingTypes;
use PayoneApi\Request\GenericAuthorizationRequest;
use PayoneApi\Request\Parts\RedirectUrls;

/**
 * Class PayPal
 */
class AmazonPay extends AuthorizationRequestAbstract
{
    const WALLET_TYPE = 'AMZ';

    protected $clearingtype = ClearingTypes::WALLET;
    /**
     * @var string
     */
    private $wallettype = self::WALLET_TYPE;

    private $amount;

    private $workorderid;

    private $reference;

    private $currency;

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
     * @param $workOrderId
     * @param $reference
     * @param $currency
     * @param $amazonReferenceId
     */
    public function __construct(
        GenericAuthorizationRequest $authorizationRequest,
        RedirectUrls $urls,
        $amount,
        $workOrderId,
        $reference,
        $currency,
        $amazonReferenceId
    ) {
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
     * @return mixed
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @return mixed
     */
    public function getWorkorderid()
    {
        return $this->workorderid;
    }

    /**
     * @return mixed
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * @return mixed
     */
    public function getCurrency()
    {
        return $this->currency;
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
