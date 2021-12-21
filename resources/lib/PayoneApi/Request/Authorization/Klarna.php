<?php

namespace PayoneApi\Request\Authorization;

use PayoneApi\Request\AuthorizationRequestAbstract;
use PayoneApi\Request\ClearingTypes;
use PayoneApi\Request\GenericAuthorizationRequest;
use PayoneApi\Request\Parts\RedirectUrls;
use PayoneApi\Request\WalletTypes;

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
     * @param GenericAuthorizationRequest $authorizationRequest
     * @param RedirectUrls $urls
     * @param string $workOrderId
     * @param string $authorisationToken
     */
    public function __construct(
        GenericAuthorizationRequest $authorizationRequest,
        RedirectUrls $urls,
        string $workOrderId,
        string $authorisationToken
    )
    {
        $this->authorizationRequest = $authorizationRequest;
        $this->urls = $urls;
        $this->workorderid = $workOrderId;
        $this->add_paydata['authorisation_token'] = $authorisationToken;
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

}
