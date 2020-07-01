<?php

namespace PayoneApi\Request\GetInvoice;

use PayoneApi\Request\Parts\Config;
use PayoneApi\Request\Parts\SystemInfo;
use PayoneApi\Request\Types;

class GetInvoice
{
    private $invoiceTitle;

    private $request = Types::INVOICE;
    /**
     * @var Config
     */
    private $config;
    /**
     * @var SystemInfo
     */
    private $info;

    /**
     * ManageMandate constructor.
     *
     * @param Config $config
     * @param string $invoiceTitle
     * @param SystemInfo $info
     */
    public function __construct(
        Config $config,
        $invoiceTitle,
        SystemInfo $info
    ) {
        $this->config = $config;
        $this->invoiceTitle = $invoiceTitle;
        $this->info = $info;
    }

    /**
     * Getter for Request
     *
     * @return string
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Getter for Config
     *
     * @return Config
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Getter for Info
     *
     * @return SystemInfo
     */
    public function getInfo()
    {
        return $this->info;
    }

    /**
     * Getter for Sequencenumber
     */
    public function getSequencenumber()
    {
        return null;
    }

    /**
     * Getter for Amount
     *
     * @return int
     */
    public function getAmount()
    {
        return null;
    }

    /**
     * Getter for InvoiceTitle
     *
     * @return string
     */
    public function getInvoiceTitle()
    {
        return $this->invoiceTitle;
    }
}
