<?php

namespace PayoneApi\Request\Authorization;

use PayoneApi\Request\AuthorizationRequestAbstract;
use PayoneApi\Request\ClearingTypes;
use PayoneApi\Request\GenericAuthorizationRequest;
use PayoneApi\Request\Parts\RedirectUrls;

/**
 * Class Creditcard
 */
class Creditcard extends AuthorizationRequestAbstract
{
    /**
     * @var string
     */
    protected $clearingtype = ClearingTypes::CREDITCARD;

    /**
     * @var string
     */
    private $pseudocardpan;
    /**
     * @var RedirectUrls
     */
    private $urls;

    /**
     * Creditcard constructor.
     *
     * @param GenericAuthorizationRequest $authorizationRequest
     * @param RedirectUrls $urls
     * @param $pseudocardPan
     */
    public function __construct(
        GenericAuthorizationRequest $authorizationRequest,
        RedirectUrls $urls,
        $pseudocardPan
    ) {
        $this->authorizationRequest = $authorizationRequest;
        $this->pseudocardpan = $pseudocardPan;
        $this->urls = $urls;
    }

    /**
     * Getter for Pseudocardpan
     *
     * @return string
     */
    public function getPseudocardpan()
    {
        return $this->pseudocardpan;
    }

    /**
     * Getter for Urls
     *
     * @return RedirectUrls
     */
    public function getUrls()
    {
        return $this->urls;
    }
}
