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
     * @var string|null
     */
    private $successurl;

    /**
     * @var string|null
     */
    private $errorurl;

    /**
     * @var string|null
     */
    private $backurl;

    private $amount = 1000;
    private $currency = 'EUR';

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

        $this->backurl = $this->urls->getBackurl();
        $this->successurl = $this->urls->getSuccessurl();
        $this->errorurl = $this->urls->getErrorurl();
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

    /**
     * @return string|null
     */
    public function getSuccessurl(): ?string
    {
        return $this->successurl;
    }

    /**
     * @param string|null $successurl
     */
    public function setSuccessurl(?string $successurl): void
    {
        $this->successurl = $successurl;
    }

    /**
     * @return string|null
     */
    public function getErrorurl(): ?string
    {
        return $this->errorurl;
    }

    /**
     * @param string|null $errorurl
     */
    public function setErrorurl(?string $errorurl): void
    {
        $this->errorurl = $errorurl;
    }

    /**
     * @return string|null
     */
    public function getBackurl(): ?string
    {
        return $this->backurl;
    }

    /**
     * @param string|null $backurl
     */
    public function setBackurl(?string $backurl): void
    {
        $this->backurl = $backurl;
    }
}
