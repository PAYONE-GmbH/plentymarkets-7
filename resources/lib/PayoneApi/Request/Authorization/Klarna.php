<?php

namespace PayoneApi\Request\Authorization;

use PayoneApi\Request\AuthorizationRequestAbstract;
use PayoneApi\Request\ClearingTypes;
use PayoneApi\Request\GenericAuthorizationRequest;
use PayoneApi\Request\Parts\Cart;
use PayoneApi\Request\Parts\RedirectUrls;

/**
 * Class Klarna
 */
class Klarna extends AuthorizationRequestAbstract
{
    /**
     * @var string
     */
    protected $clearingtype = ClearingTypes::FINANCING;

    /**
     * @var string
     */
    protected $financingtype;

    /**
     * @var string
     */
    protected $workorderid;

    /**
     * @var string
     */
    protected $authorisationToken;

    protected $amount;

    /**
     * @var string[]
     */
    protected $add_paydata = [
        'authorisation_token' => '',
    ];

    /**
     * @var RedirectUrls
     */
    protected $urls;
    /**
     * @var Cart
     */
    protected $cart;

    /**
     * @param GenericAuthorizationRequest $authorizationRequest
     * @param RedirectUrls $urls
     * @param string $workOrderId
     * @param string $authorisationToken
     * @param string $financingtype
     * @param Cart $cart
     */
    public function  __construct(
        GenericAuthorizationRequest $authorizationRequest,
        RedirectUrls $urls,
        string $workOrderId,
        string $authorisationToken,
        string $financingtype,
        Cart $cart

    )
    {
        $this->authorizationRequest = $authorizationRequest;
        $this->urls = $urls;
        $this->workorderid = $workOrderId;
        $this->add_paydata['authorisation_token'] = $authorisationToken;
        $this->financingtype = $financingtype;
        $this->cart = $cart;
    }

    /**
     * @return string
     */
    public function getFinancingtype(): string
    {
        return $this->financingtype;
    }

    /**
     * @return string
     */
    public function getWorkorderid(): string
    {
        return $this->workorderid;
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

    /**
     * @return Cart
     */
    public function getCart(): Cart
    {
        return $this->cart;
    }

}
