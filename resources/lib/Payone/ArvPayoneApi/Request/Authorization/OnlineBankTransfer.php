<?php

namespace Payone\ArvPayoneApi\Request\Authorization;

use Payone\ArvPayoneApi\Request\AuthorizationRequestAbstract;
use Payone\ArvPayoneApi\Request\ClearingTypes;
use Payone\ArvPayoneApi\Request\GenericAuthorizationRequest;
use Payone\ArvPayoneApi\Request\Parts\BankAccount;
use Payone\ArvPayoneApi\Request\Parts\RedirectUrls;

/**
 * Class OnlineBankTransfer
 */
class OnlineBankTransfer extends AuthorizationRequestAbstract
{
    const ONLINE_TRANSFER = 'EPS';

    protected $onlinebanktransfertype = self::ONLINE_TRANSFER;
    protected $clearingtype = ClearingTypes::ONLINE_BANK_TRANSFER;

    /**
     * @var RedirectUrls
     */
    private $urls;
    /**
     * @var BankAccount
     */
    private $bankAccount;

    /**
     * PayPal constructor.
     *
     * @param GenericAuthorizationRequest $authorizationRequest
     * @param RedirectUrls $urls
     * @param BankAccount $bankAccount
     */
    public function __construct(
        GenericAuthorizationRequest $authorizationRequest,
        RedirectUrls $urls,
        BankAccount $bankAccount
    ) {
        $this->authorizationRequest = $authorizationRequest;
        $this->urls = $urls;
        $this->bankAccount = $bankAccount;
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
     * Getter for Onlinebanktransfertype
     *
     * @return string
     */
    public function getOnlinebanktransfertype()
    {
        return $this->onlinebanktransfertype;
    }

    /**
     * Getter for BankAccount
     *
     * @return BankAccount
     */
    public function getBankAccount()
    {
        return $this->bankAccount;
    }
}
