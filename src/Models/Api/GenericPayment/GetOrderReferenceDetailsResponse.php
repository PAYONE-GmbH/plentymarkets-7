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
     * @param string $shippingZip
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
     * @param string $billingZip
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
    public function init(
        bool $success = false,
        string $errorMessage = '',
        string $email = '',
        string $shippingZip = '',
        string $shippingStreet = '',
        string $shippingCompany = '',
        string $shippingCity = '',
        string $shippingType = '',
        string $shippingCountry = '',
        string $shippingDistrict = '',
        string $shippingTelephonenumber = '',
        string $shippingState = '',
        string $shippingFirstname = '',
        string $shippingLastname = '',
        string $billingZip = '',
        string $billingStreet = '',
        string $billingCompany = '',
        string $billingCity = '',
        string $billingType = '',
        string $billingCountry = '',
        string $billingFirstname = '',
        string $billingLastname = '',
        string $billingDistrict = '',
        string $billingTelephonenumber = '',
        string $billingState = '',
        string $storename = '',
        string $workOrderId = ''
    )
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
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getShippingZip(): string
    {
        return $this->shippingZip;
    }

    /**
     * @return string
     */
    public function getShippingStreet(): string
    {
        return $this->shippingStreet;
    }

    /**
     * @return string
     */
    public function getShippingCompany(): string
    {
        return $this->shippingCompany;
    }

    /**
     * @return string
     */
    public function getShippingCity(): string
    {
        return $this->shippingCity;
    }

    /**
     * @return string
     */
    public function getShippingType(): string
    {
        return $this->shippingType;
    }

    /**
     * @return string
     */
    public function getShippingCountry(): string
    {
        return $this->shippingCountry;
    }

    /**
     * @return string
     */
    public function getShippingDistrict(): string
    {
        return $this->shippingDistrict;
    }

    /**
     * @return string
     */
    public function getShippingTelephonenumber(): string
    {
        return $this->shippingTelephonenumber;
    }

    /**
     * @return string
     */
    public function getShippingState(): string
    {
        return $this->shippingState;
    }

    /**
     * @return string
     */
    public function getShippingFirstname(): string
    {
        return $this->shippingFirstname;
    }

    /**
     * @return string
     */
    public function getShippingLastname(): string
    {
        return $this->shippingLastname;
    }

    /**
     * @return string
     */
    public function getBillingZip(): string
    {
        return $this->billingZip;
    }

    /**
     * @return string
     */
    public function getBillingStreet(): string
    {
        return $this->billingStreet;
    }

    /**
     * @return string
     */
    public function getBillingCompany(): string
    {
        return $this->billingCompany;
    }

    /**
     * @return string
     */
    public function getBillingCity(): string
    {
        return $this->billingCity;
    }

    /**
     * @return string
     */
    public function getBillingType(): string
    {
        return $this->billingType;
    }

    /**
     * @return string
     */
    public function getBillingCountry(): string
    {
        return $this->billingCountry;
    }

    /**
     * @return string
     */
    public function getBillingFirstname(): string
    {
        return $this->billingFirstname;
    }

    /**
     * @return string
     */
    public function getBillingLastname(): string
    {
        return $this->billingLastname;
    }

    /**
     * @return string
     */
    public function getBillingDistrict(): string
    {
        return $this->billingDistrict;
    }

    /**
     * @return string
     */
    public function getBillingTelephonenumber(): string
    {
        return $this->billingTelephonenumber;
    }

    /**
     * @return string
     */
    public function getBillingState(): string
    {
        return $this->billingState;
    }

    /**
     * @return string
     */
    public function getStorename(): string
    {
        return $this->storename;
    }

    /**
     * @return string
     */
    public function getWorkOrderId(): string
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
