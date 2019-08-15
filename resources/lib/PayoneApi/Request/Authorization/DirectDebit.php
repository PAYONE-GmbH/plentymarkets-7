<?php

namespace PayoneApi\Request\Authorization;

use PayoneApi\Request\AuthorizationRequestAbstract;
use PayoneApi\Request\ClearingTypes;
use PayoneApi\Request\GenericAuthorizationRequest;
use PayoneApi\Request\Parts\SepaMandate;

/**
 * Class DirectDebit
 */
class DirectDebit extends AuthorizationRequestAbstract
{
    /**
     * @var string
     */
    protected $clearingtype = ClearingTypes::DEBIT_PAYMENT;
    /**
     * @var SepaMandate
     */
    private $sepaMandate;

    /**
     * DirectDebit constructor.
     *
     * @param GenericAuthorizationRequest $authorizationRequest
     * @param SepaMandate $sepaMandate
     */
    public function __construct(
        GenericAuthorizationRequest $authorizationRequest,
        SepaMandate $sepaMandate
    ) {
        $this->authorizationRequest = $authorizationRequest;
        $this->sepaMandate = $sepaMandate;
    }

    /**
     * Getter for SepaMandate
     *
     * @return SepaMandate
     */
    public function getSepaMandate()
    {
        return $this->sepaMandate;
    }
}
