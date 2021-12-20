<?php

namespace PayoneApi\Request\GetInvoice;

use PayoneApi\Request\Parts\Config;
use PayoneApi\Request\Parts\SystemInfo;
use PayoneApi\Request\Types;

class GetInvoice
{
    /**
     * @var string
     */
    protected $invoiceTitle;

    /**
     * @var string
     */
    protected $request = Types::INVOICE;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var SystemInfo
     */
    protected $info;

    /**
     * ManageMandate constructor.
     *
     * @param Config $config
     * @param string $invoiceTitle
     * @param SystemInfo $info
     */
    public function __construct(
        Config $config,
        string $invoiceTitle,
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
     * Getter for InvoiceTitle
     *
     * @return string
     */
    public function getInvoiceTitle()
    {
        return $this->invoiceTitle;
    }
}
