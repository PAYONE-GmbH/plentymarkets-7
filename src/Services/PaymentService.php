<?php //strict

namespace Payone\Services;

use Plenty\Modules\Payment\Contracts\PaymentRepositoryContract;
use Plenty\Modules\Payment\Method\Contracts\PaymentMethodRepositoryContract;
use Plenty\Modules\Basket\Models\Basket;
use Plenty\Modules\Plugin\Libs\Contracts\LibraryCallContract;
use Plenty\Plugin\ConfigRepository;
use Plenty\Modules\Account\Address\Contracts\AddressRepositoryContract;


class PaymentService
{

    /**
     * @var PaymentMethodRepositoryContract
     */
    private $paymentMethodRepository;

    /**
     * @var PaymentRepositoryContract
     */
    private $paymentRepository;

    /**
     * @var PaymentHelper
     */
    private $paymentHelper;

    /**
     * @var LibraryCallContract
     */
    private $libCall;

    /**
     * @var AddressRepositoryContract
     */
    private $addressRepo;

    /**
     * @var ConfigRepository
     */
    private $config;

    /**
     * @var SessionStorageService
     */
    private $sessionStorage;


    /**
     * PaymentService constructor.
     *
     * @param PaymentMethodRepositoryContract $paymentMethodRepository
     * @param PaymentRepositoryContract $paymentRepository
     * @param ConfigRepository $config
     * @param PaymentHelper $paymentHelper
     * @param LibraryCallContract $libCall
     * @param AddressRepositoryContract $addressRepo
     * @param SessionStorageService $sessionStorage
     */
    public function __construct(
        PaymentMethodRepositoryContract $paymentMethodRepository,
        PaymentRepositoryContract $paymentRepository,
        ConfigRepository $config,
        PaymentHelper $paymentHelper,
        LibraryCallContract $libCall,
        AddressRepositoryContract $addressRepo,
        SessionStorageService $sessionStorage
    ) {
        $this->paymentMethodRepository = $paymentMethodRepository;
        $this->paymentRepository = $paymentRepository;
        $this->paymentHelper = $paymentHelper;
        $this->libCall = $libCall;
        $this->addressRepo = $addressRepo;
        $this->config = $config;
        $this->sessionStorage = $sessionStorage;
    }

    /**
     * Get the type of payment from the content of the PayPal container
     *
     * @return string
     */
    public function getReturnType()
    {
        /*  'redirectUrl'|'externalContentUrl'|'errorCode'|'continue'  */
        return 'htmlContent';
    }

    /**
     * Get the PayPal payment content
     *
     * @param Basket $basket
     * @param string $mode
     * @return string
     */
    public function getPaymentContent(Basket $basket, $mode = ''): string
    {
        return 'html content';
    }


}
