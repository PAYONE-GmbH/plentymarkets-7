<?php

namespace Payone\Models\Api\GenericPayment;

use Payone\Models\Api\ResponseAbstract;

/**
 * Class GetConfigurationResponse
 */
class GetOrderReferenceDetailsResponse extends ResponseAbstract implements \JsonSerializable
{
    protected $email;
    protected $shippingZip;
    protected $shippingStreet;
    protected $shippingCompany;
    protected $shippingCity;
    protected $shippingType;
    protected $shippingCountry;
    protected $shippingDistrict;
    protected $shippingTelephonenumber;
    protected $shippingState;
    protected $shippingFirstname;
    protected $shippingLastname;
    protected $billingZip;
    protected $billingStreet;
    protected $billingCompany;
    protected $billingCity;
    protected $billingType;
    protected $billingCountry;
    protected $billingFirstname;
    protected $billingLastname;
    protected $billingDistrict;
    protected $billingTelephonenumber;
    protected $billingState;
    protected $storename;
    protected $workOrderId;

    /**
     * @param bool $success
     * @param string $errorMessage
     * @param string $email
     * @param int $shippingZip
     * @param string $shippingStreet
     * @param string $shippingCompany
     * @param string $shippingCity
     * @param string $shippingType
     * @param string $shippingCountry
     * @param string $shippingDistrict
     * @param string $shippingTelephonenumber
     * @param string $shippingState
     * @param string $shippingFirstname
     * @param string $shippingLastname
     * @param int $billingZip
     * @param string $billingStreet
     * @param string $billingCompany
     * @param string $billingCity
     * @param string $billingType
     * @param string $billingCountry
     * @param string $billingFirstname
     * @param string $billingLastname
     * @param string $billingDistrict
     * @param string $billingTelephonenumber
     * @param string $billingState
     * @param string $storename
     * @param string $workOrderId
     *
     * @return $this
     */
    public function init(bool $success, string $errorMessage, string $email, int $shippingZip, string $shippingStreet, string $shippingCompany, string $shippingCity,
                         string $shippingType, string $shippingCountry, string $shippingDistrict, string $shippingTelephonenumber, string $shippingState,
                         string $shippingFirstname, string $shippingLastname, int $billingZip, string $billingStreet, string $billingCompany, string $billingCity, string $billingType,
                         string $billingCountry, string $billingFirstname, string $billingLastname, string $billingDistrict, string $billingTelephonenumber,
                         string $billingState, string $storename, string $workOrderId)
    {
        $this->success = $success;
        $this->errorMessage = $errorMessage;
        $this->email = $email;
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

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return parent::jsonSerialize() +
            [
                'email' => $this->email,
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
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return int
     */
    public function getShippingZip()
    {
        return $this->shippingZip;
    }

    /**
     * @return string
     */
    public function getShippingStreet()
    {
        return $this->shippingStreet;
    }

    /**
     * @return string
     */
    public function getShippingCompany()
    {
        return $this->shippingCompany;
    }

    /**
     * @return string
     */
    public function getShippingCity()
    {
        return $this->shippingCity;
    }

    /**
     * @return string
     */
    public function getShippingType()
    {
        return $this->shippingType;
    }

    /**
     * @return string
     */
    public function getShippingCountry()
    {
        return $this->shippingCountry;
    }

    /**
     * @return string
     */
    public function getShippingDistrict()
    {
        return $this->shippingDistrict;
    }

    /**
     * @return string
     */
    public function getShippingTelephonenumber()
    {
        return $this->shippingTelephonenumber;
    }

    /**
     * @return string
     */
    public function getShippingState()
    {
        return $this->shippingState;
    }

    /**
     * @return string
     */
    public function getShippingFirstname()
    {
        return $this->shippingFirstname;
    }

    /**
     * @return string
     */
    public function getShippingLastname()
    {
        return $this->shippingLastname;
    }

    /**
     * @return int
     */
    public function getBillingZip()
    {
        return $this->billingZip;
    }

    /**
     * @return string
     */
    public function getBillingStreet()
    {
        return $this->billingStreet;
    }

    /**
     * @return string
     */
    public function getBillingCompany()
    {
        return $this->billingCompany;
    }

    /**
     * @return string
     */
    public function getBillingCity()
    {
        return $this->billingCity;
    }

    /**
     * @return string
     */
    public function getBillingType()
    {
        return $this->billingType;
    }

    /**
     * @return string
     */
    public function getBillingCountry()
    {
        return $this->billingCountry;
    }

    /**
     * @return string
     */
    public function getBillingFirstname()
    {
        return $this->billingFirstname;
    }

    /**
     * @return string
     */
    public function getBillingLastname()
    {
        return $this->billingLastname;
    }

    /**
     * @return string
     */
    public function getBillingDistrict()
    {
        return $this->billingDistrict;
    }

    /**
     * @return string
     */
    public function getBillingTelephonenumber()
    {
        return $this->billingTelephonenumber;
    }

    /**
     * @return string
     */
    public function getBillingState()
    {
        return $this->billingState;
    }

    /**
     * @return string
     */
    public function getStorename()
    {
        return $this->storename;
    }

    /**
     * @return string
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
        return $this->getSuccess();
    }
}
