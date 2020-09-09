<?php

namespace Payone\Models\Api\GenericPayment;

use Payone\Models\Api\ResponseAbstract;

/**
 * Class GetConfigurationResponse
 */
class GetOrderReferenceDetailsResponse extends ResponseAbstract implements \JsonSerializable
{
    private $shippingZip;
    private $shippingCity;
    private $shippingType;
    private $shippingCountry;
    private $shippingFirstname;
    private $shippingLastname;
    private $billingZip;
    private $billingCity;
    private $billingType;
    private $billingCountry;
    private $billingFirstname;
    private $billingLastname;
    private $storename;
    private $workOrderId;

    /**
     * @param $success
     * @param $errorMessage
     * @param $shippingZip
     * @param $shippingCity
     * @param $shippingType
     * @param $shippingCountry
     * @param $shippingFirstname
     * @param $shippingLastname
     * @param $billingZip
     * @param $billingCity
     * @param $billingType
     * @param $billingCountry
     * @param $billingFirstname
     * @param $billingLastname
     * @param $storename
     * @param $workOrderId
     * @return $this
     */
    public function init($success, $errorMessage, $shippingZip, $shippingCity, $shippingType, $shippingCountry,
                         $shippingFirstname, $shippingLastname, $billingZip, $billingCity, $billingType, $billingCountry,
                         $billingFirstname, $billingLastname, $storename, $workOrderId)
    {
        $this->success = $success;
        $this->errorMessage = $errorMessage;
        $this->shippingZip = $shippingZip;
        $this->shippingCity = $shippingCity;
        $this->shippingType = $shippingType;
        $this->shippingCountry = $shippingCountry;
        $this->shippingFirstname = $shippingFirstname;
        $this->shippingLastname = $shippingLastname;
        $this->billingZip = $billingZip;
        $this->billingCity = $billingCity;
        $this->billingType = $billingType;
        $this->billingCountry = $billingCountry;
        $this->billingFirstname = $billingFirstname;
        $this->billingLastname = $billingLastname;
        $this->storename = $storename;
        $this->workOrderId = $workOrderId;

        return $this;
    }

    public function jsonSerialize(): array
    {
        return parent::jsonSerialize() +
            [
                'shippingCity' => $this->shippingCity,
                'shippingType' => $this->shippingType,
                'shippingCountry' => $this->shippingCountry,
                'shippingFirstname' => $this->shippingFirstname,
                'shippingLastname' => $this->shippingLastname,
                'billingZip' => $this->billingZip,
                'billingCity' => $this->billingCity,
                'billingType' => $this->billingType,
                'billingCountry' => $this->billingCountry,
                'billingFirstname' => $this->billingFirstname,
                'billingLastname' => $this->billingLastname,
                'storename' => $this->storename,
                'workOrderId' => $this->workOrderId
            ];
    }

    /**
     * @return mixed
     */
    public function getShippingZip()
    {
        return $this->shippingZip;
    }

    /**
     * @return mixed
     */
    public function getShippingCity()
    {
        return $this->shippingCity;
    }

    /**
     * @return mixed
     */
    public function getShippingType()
    {
        return $this->shippingType;
    }

    /**
     * @return mixed
     */
    public function getShippingCountry()
    {
        return $this->shippingCountry;
    }

    /**
     * @return mixed
     */
    public function getShippingFirstname()
    {
        return $this->shippingFirstname;
    }

    /**
     * @return mixed
     */
    public function getShippingLastname()
    {
        return $this->shippingLastname;
    }

    /**
     * @return mixed
     */
    public function getBillingZip()
    {
        return $this->billingZip;
    }

    /**
     * @return mixed
     */
    public function getBillingCity()
    {
        return $this->billingCity;
    }

    /**
     * @return mixed
     */
    public function getBillingType()
    {
        return $this->billingType;
    }

    /**
     * @return mixed
     */
    public function getBillingCountry()
    {
        return $this->billingCountry;
    }

    /**
     * @return mixed
     */
    public function getBillingFirstname()
    {
        return $this->billingFirstname;
    }

    /**
     * @return mixed
     */
    public function getBillingLastname()
    {
        return $this->billingLastname;
    }

    /**
     * @return mixed
     */
    public function getStorename()
    {
        return $this->storename;
    }

    /**
     * @return mixed
     */
    public function getWorkOrderId()
    {
        return $this->workOrderId;
    }

    /**
     * @return bool
     */
    public function isSuccess(): bool
    {
        return $this->success;
    }

    /**
     * @return string
     */
    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }

    /**
     * @return string
     */
    public function getTransactionID(): string
    {
        return $this->transactionID;
    }


}
