<?php

namespace Payone\Models\Api;

use Payone\Models\SepaMandate;

/**
 * Class ManagemandateResponse
 */
class ManagemandateResponse extends ResponseAbstract implements \JsonSerializable
{
    /** @var SepaMandate */
    private $mandate;

    /**
     * @param $success
     * @param $errorMessage
     * @param $transactionID
     * @param SepaMandate $mandate
     *
     * @return $this
     */
    public function init($success, $errorMessage, $transactionID, SepaMandate $mandate)
    {
        $this->success = $success;
        $this->errorMessage = $errorMessage;
        $this->transactionID = $transactionID;
        $this->mandate = $mandate;

        return $this;
    }

    /**
     * Getter for Mandate
     *
     * @return SepaMandate
     */
    public function getMandate(): SepaMandate
    {
        return $this->mandate;
    }

    public function jsonSerialize(): array
    {
        return parent::jsonSerialize() +
            [
                'mandate' => $this->mandate->jsonSerialize(),
            ];
    }
}
