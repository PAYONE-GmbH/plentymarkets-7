<?php

namespace Payone\Models\Api\GenericPayment;

use Payone\Models\Api\ResponseAbstract;

/**
 * Class GetConfigurationResponse
 */
class GetOrderReferenceDetailsResponse extends ResponseAbstract implements \JsonSerializable
{
    private $shippingZip;
    private $shippingStreet;
    private $shippingCompany;
    private $shippingCity;
    private $shippingType;
    private $shippingCountry;
    private $shippingDistrict;
    private $shippingTelephonenumber;
    private $shippingState;
    private $shippingFirstname;
    private $shippingLastname;
    private $billingZip;
    private $billingStreet;
    private $billingCompany;
    private $billingCity;
    private $billingType;
    private $billingCountry;
    private $billingFirstname;
    private $billingLastname;
    private $billingDistrict;
    private $billingTelephonenumber;
    private $billingState;
    private $storename;
    private $workOrderId;

    /**
     * @param $success
     * @param $errorMessage
     * @param $shippingZip
     * @param $shippingStreet
     * @param $shippingCompany
     * @param $shippingCity
     * @param $shippingType
     * @param $shippingCountry
     * @param $shippingDistrict
     * @param $shippingTelephonenumber
     * @param $shippingState
     * @param $shippingFirstname
     * @param $shippingLastname
     * @param $billingZip
     * @param $billingStreet
     * @param $billingCompany
     * @param $billingCity
     * @param $billingType
     * @param $billingCountry
     * @param $billingFirstname
     * @param $billingLastname
     * @param $billingDistrict
     * @param $billingTelephonenumber
     * @param $billingState
     * @param $storename
     * @param $workOrderId
     * @return $this
     */
    public function init($success, $errorMessage, $shippingZip, $shippingStreet, $shippingCompany, $shippingCity,
                         $shippingType, $shippingCountry, $shippingDistrict, $shippingTelephonenumber, $shippingState,
                         $shippingFirstname, $shippingLastname, $billingZip, $billingStreet, $billingCompany, $billingCity, $billingType,
                         $billingCountry, $billingFirstname, $billingLastname, $billingDistrict, $billingTelephonenumber,
                         $billingState, $storename, $workOrderId)
    {
        $this->success = $success;
        $this->errorMessage = $errorMessage;
        $this->shippingZip = $shippingZip;
        $this->shippingStreet = $shippingStreet;
        $this->shippingCompany = $shippingCompany;
        $this->shippingCity = $shippingCity;
        $this->shippingType = $shippingType;
        $this->shippingCountry = $shippingCountry;
        $this->shippingDistrict = $shippingDistrict;
        $this->shippingTelephonenumber = $shippingTelephonenumber;
        $this->shippingState = $shippingState;
        $this->shippingFirstname = $shippingFirstname;
        $this->shippingLastname = $shippingLastname;
        $this->billingZip = $billingZip;
        $this->billingStreet = $billingStreet;
        $this->billingCompany = $billingCompany;
        $this->billingCity = $billingCity;
        $this->billingType = $billingType;
        $this->billingCountry = $billingCountry;
        $this->billingFirstname = $billingFirstname;
        $this->billingLastname = $billingLastname;
        $this->billingDistrict = $billingDistrict;
        $this->billingTelephonenumber = $billingTelephonenumber;
        $this->billingState = $billingState;
        $this->storename = $storename;
        $this->workOrderId = $workOrderId;

        return $this;
    }

    public function jsonSerialize(): array
    {
        return parent::jsonSerialize() +
            [
                'shippingZip' => $this->shippingZip,
                'shippingStreet' => $this->shippingStreet,
                'shippingCompany' => $this->shippingCompany,
                'shippingCity' => $this->shippingCity,
                'shippingType' => $this->shippingType,
                'shippingCountry' => $this->shippingCountry,
                'shippingDistrict' => $this->shippingDistrict,
                'shippingTelephonenumber' => $this->shippingTelephonenumber,
                'shippingState' => $this->shippingState,
                'shippingFirstname' => $this->shippingFirstname,
                'shippingLastname' => $this->shippingLastname,
                'billingZip' => $this->billingZip,
                'billingStreet' => $this->billingStreet,
                'billingCompany' => $this->billingCompany,
                'billingCity' => $this->billingCity,
                'billingType' => $this->billingType,
                'billingCountry' => $this->billingCountry,
                'billingFirstname' => $this->billingFirstname,
                'billingLastname' => $this->billingLastname,
                'billingDistrict' => $this->billingDistrict,
                'billingTelephonenumber' => $this->billingTelephonenumber,
                'billingState' => $this->billingState,
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
    public function getShippingStreet()
    {
        return $this->shippingStreet;
    }

    /**
     * @return mixed
     */
    public function getShippingCompany()
    {
        return $this->shippingCompany;
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
    public function getShippingDistrict()
    {
        return $this->shippingDistrict;
    }

    /**
     * @return mixed
     */
    public function getShippingTelephonenumber()
    {
        return $this->shippingTelephonenumber;
    }

    /**
     * @return mixed
     */
    public function getShippingState()
    {
        return $this->shippingState;
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
    public function getBillingStreet()
    {
        return $this->billingStreet;
    }

    /**
     * @return mixed
     */
    public function getBillingCompany()
    {
        return $this->billingCompany;
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
    public function getBillingDistrict()
    {
        return $this->billingDistrict;
    }

    /**
     * @return mixed
     */
    public function getBillingTelephonenumber()
    {
        return $this->billingTelephonenumber;
    }

    /**
     * @return mixed
     */
    public function getBillingState()
    {
        return $this->billingState;
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
