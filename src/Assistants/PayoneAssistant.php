<?php

namespace Payone\Assistants;

use Payone\Assistants\DataSources\AssistantDataSource;
use Payone\Assistants\SettingsHandlers\AssistantSettingsHandler;
use Payone\Helpers\PaymentHelper;
use Payone\Methods\PayoneAmazonPayPaymentMethod;
use Payone\Methods\PayoneCCPaymentMethod;
use Payone\Methods\PayoneInvoiceSecurePaymentMethod;
use Payone\Methods\PayoneSofortPaymentMethod;
use Payone\Models\CreditcardTypes;
use Plenty\Modules\Order\Shipping\Countries\Contracts\CountryRepositoryContract;
use Plenty\Modules\Order\Shipping\Countries\Models\Country;
use Plenty\Modules\System\Contracts\WebstoreRepositoryContract;
use Plenty\Modules\System\Models\Webstore;
use Plenty\Modules\User\Contracts\UserRepositoryContract;
use Plenty\Modules\User\Models\User;
use Plenty\Modules\Wizard\Services\WizardProvider;
use Plenty\Plugin\Application;

class PayoneAssistant extends WizardProvider
{
    /**
     * @var PaymentHelper
     */
    protected $paymentHelper;

    /**
     * @var array
     */
    protected $activeCountries;

    /**
     * @var array
     */
    protected $activeCountries4AmazonPay;

    /**
     * PayoneAssistant constructor.
     * @param PaymentHelper $paymentHelper
     */
    public function __construct(PaymentHelper $paymentHelper)
    {
        $this->paymentHelper = $paymentHelper;
    }

    /**
     * @return array
     */
    protected function structure(): array
    {
        $config = [
            "title" => 'Assistant.assistantTitle',
            "shortDescription" => 'Assistant.assistantShortDescription',
            "iconPath" => $this->getIcon(),
            "settingsHandlerClass" => AssistantSettingsHandler::class,
            "dataSource" => AssistantDataSource::class,
            "translationNamespace" => "Payone",
            "key" => "payment-payone-assistant",
            "topics" => ["payment"],
            'priority' => 990,
            "options" => [
                "clientId" => [
                    "type" => 'select',
                    'defaultValue' => $this->getMainWebstore(),
                    "options" => [
                        "name" => 'Assistant.clientId',
                        'required' => true,
                        'listBoxValues' => $this->getWebstoreValues(),
                    ],
                ],
            ],
            "steps" => [
            ]
        ];

        $config = $this->createAccountStep($config);

        $config = $this->createProductsPageAndSteps($config);

        return $config;
    }

    /**
     * @param array $config
     * @return array
     */
    protected function createAccountStep(array $config): array
    {
        $backendUsers = $this->getBackendUsers();

        $config['steps']['payoneAccountStep'] = [
            'title' => 'Assistant.titlePayoneAccountStep',
            'description' => 'Assistant.descriptionPayoneAccountStep',
            'sections' => [
                [
                    'title' => 'Assistant.titlePayoneAccountStepAccount',
                    'description' => 'Assistant.descriptionPayoneAccountStepAccount',
                    'form' => [
                        'mid' => [
                            'type' => 'text',
                            'options' => [
                                'name' => 'Assistant.mid',
                                'required' => true
                            ]
                        ],
                        'portalId' => [
                            'type' => 'text',
                            'options' => [
                                'name' => 'Assistant.portalId',
                                'required' => true
                            ]
                        ],
                        'aid' => [
                            'type' => 'text',
                            'options' => [
                                'name' => 'Assistant.aid',
                                'required' => true
                            ]
                        ],
                        'key' => [
                            'type' => 'text',
                            'options' => [
                                'name' => 'Assistant.key',
                                'required' => true
                            ]
                        ],
                        'mode' => [
                            'type' => 'select',
                            "defaultValue" => 1,
                            'options' => [
                                'name' => 'Assistant.mode',
                                'listBoxValues' => [
                                    [
                                        "caption" => 'Assistant.modeProductiveOption',
                                        "value" => 1
                                    ],
                                    [
                                        "caption" => 'Assistant.modeTestingOption',
                                        "value" => 0
                                    ]
                                ]
                            ]
                        ],
                        'authType' => [
                            'type' => 'select',
                            "defaultValue" => 1,
                            'options' => [
                                'name' => 'Assistant.authType',
                                'listBoxValues' => [
                                    [
                                        "caption" => 'Assistant.authTypeAuthorization',
                                        "value" => 1
                                    ],
                                    [
                                        "caption" => 'Assistant.authTypePreAuthorization',
                                        "value" => 0
                                    ]
                                ]
                            ]
                        ],
                        "userId" => [
                            'type' => 'select',
                            'defaultValue' => $backendUsers[0]['value'],
                            'options' => [
                                'name' => 'Assistant.userId',
                                'required' => true,
                                'listBoxValues' => $backendUsers
                            ]
                        ]
                    ]
                ]
            ]
        ];

        return $config;
    }

    /**
     * @param array $config
     * @return array
     */
    protected function createProductsPageAndSteps(array $config): array
    {
        $config['steps']['payoneProductsStep'] = [
            'title' => 'Assistant.titlePayoneProductsStep',
            'description' => 'Assistant.descriptionPayoneProductsStep',
            'showFullDescription' => true,
            'sections' => []
        ];

        foreach ($this->paymentHelper->getPaymentCodes() as $paymentCode) {
            $config['steps']['payoneProductsStep']['sections'][] = [
                'title' => 'Assistant.titlePayoneProductsStep'.$paymentCode,
                'description' => 'Assistant.descriptionPayoneProductsStep'.$paymentCode,
                'showFullDescription' => true,
                'form' => [
                    $paymentCode.'Toggle' => [
                        'type' => 'toggle',
                        'defaultValue' => false,
                        'options' => [
                            'name' => 'Assistant.title'.$paymentCode.'_Toggle',
                            'required' => false
                        ]
                    ]
                ]
            ];

            if(in_array($paymentCode, [PayoneInvoiceSecurePaymentMethod::PAYMENT_CODE, PayoneCCPaymentMethod::PAYMENT_CODE, PayoneAmazonPayPaymentMethod::PAYMENT_CODE])) {
                // We need some special configurations for this methods.
                switch ($paymentCode) {
                    case PayoneInvoiceSecurePaymentMethod::PAYMENT_CODE:
                        $config = $this->createSecureInvoiceStep($config, $paymentCode);
                        break;
                    case PayoneCCPaymentMethod::PAYMENT_CODE:
                        $config = $this->createCreditCardStep($config, $paymentCode);
                        break;
                    case PayoneAmazonPayPaymentMethod::PAYMENT_CODE:
                        $config = $this->createAmazonPayStep($config, $paymentCode);
                        break;
                }
            } else {
                $config['steps']['payone'.$paymentCode.'Step'] = [
                    'title' => 'Assistant.titlePayoneProductsStep'.$paymentCode,
                    'description' => 'Assistant.descriptionPayoneProductsStep'.$paymentCode,
                    'showFullDescription' => true,
                    'condition' => $paymentCode.'Toggle',
                    'sections' => [
                        [
                            'title' => 'Assistant.titlePayonePaymentSection',
                            'description' => 'Assistant.descriptionPayonePaymentSection',
                            'form' =>
                                $this->getMinMaxAmountConfig($paymentCode)
                                    +
                                $this->getDeliveryCountriesConfig($paymentCode)
                                    +
                                $this->getAuthorizationConfig($paymentCode)
                        ]
                    ]
                ];
            }
        }

        return $config;
    }

    /**
     * @param array $config
     * @param string $paymentCode
     * @return array
     */
    protected function createSecureInvoiceStep(array $config, string $paymentCode): array
    {
        $config['steps']['payone'.$paymentCode.'Step'] = [
            'title' => 'Assistant.titlePayoneProductsStep'.$paymentCode,
            'description' => 'Assistant.descriptionPayoneProductsStep'.$paymentCode,
            'showFullDescription' => true,
            'condition' => $paymentCode.'Toggle',
            'sections' => [
                [
                    'title' => 'Assistant.titlePayonePaymentSection',
                    'description' => 'Assistant.descriptionPayonePaymentSectionSecureInvoice',
                    'form' =>
                        $this->getMinMaxAmountConfig($paymentCode)
                            +
                        $this->getDeliveryCountriesConfig($paymentCode)
                            +
                        $this->getAuthorizationConfig($paymentCode)
                            + [
                        $paymentCode.'portalId' => [
                            'type' => 'text',
                            'options' => [
                                'name' => 'Assistant.portalId',
                                'required' => true
                            ]
                        ],
                        $paymentCode.'key' => [
                            'type' => 'text',
                            'options' => [
                                'name' => 'Assistant.key',
                                'required' => true
                            ]
                        ]
                    ]
                ]
            ]
        ];

        return $config;
    }

    /**
     * @param array $config
     * @param string $paymentCode
     * @return array
     */
    protected function createCreditCardStep(array $config, string $paymentCode): array
    {
        $config['steps']['payone'.$paymentCode.'Step'] = [
            'title' => 'Assistant.titlePayoneProductsStep'.$paymentCode,
            'description' => 'Assistant.descriptionPayoneProductsStep'.$paymentCode,
            'showFullDescription' => true,
            'condition' => $paymentCode.'Toggle',
            'sections' => [
                [
                    'title' => 'Assistant.titlePayonePaymentSection',
                    'description' => 'Assistant.descriptionPayonePaymentSection',
                    'form' =>
                        $this->getMinMaxAmountConfig($paymentCode)
                            +
                        $this->getDeliveryCountriesConfig($paymentCode)
                            +
                        $this->getAuthorizationConfig($paymentCode)
                            + [
                        $paymentCode.'minExpireTime' => [
                            'type' => 'text',
                            'defaultValue' => 30,
                            'options' => [
                                'name' => 'Assistant.minExpireTime',
                                'required' => true
                            ]
                        ],
                        $paymentCode.'defaultStyle' => [
                            'type' => 'text',
                            'defaultValue' => 'font-family: Helvetica; padding: 10.5px 21px; color: #7a7f7f; font-size: 17.5px; height:100%',
                            'options' => [
                                'name' => 'Assistant.defaultStyle',
                                'required' => true
                            ]
                        ],
                        $paymentCode.'defaultHeightInPx' => [
                            'type' => 'text',
                            'defaultValue' => '44',
                            'options' => [
                                'name' => 'Assistant.defaultHeightInPx',
                                'required' => true
                            ]
                        ],
                        $paymentCode.'defaultWidthInPx' => [
                            'type' => 'text',
                            'defaultValue' => '644',
                            'options' => [
                                'name' => 'Assistant.defaultWidthInPx',
                                'required' => true
                            ]
                        ],
                        $paymentCode.'AllowedCardTypes' => [
                            'type' => 'checkboxGroup',
                            'defaultValue' => ['V', 'M', 'A', 'O', 'U', 'D', 'B', 'C', 'J', 'P'],
                            'options' => [
                                'name' => 'Assistant.AllowedCardTypes',
                                'required' => true,
                                'checkboxValues' => $this->getAllowedCreditCardTypes()
                            ]
                        ]
                    ]
                ]
            ]
        ];

        return $config;
    }

    /**
     * @param array $config
     * @param string $paymentCode
     * @return array
     */
    protected function createAmazonPayStep(array $config, string $paymentCode): array
    {
        $config['steps']['payone'.$paymentCode.'Step'] = [
            'title' => 'Assistant.titlePayoneProductsStep'.$paymentCode,
            'description' => 'Assistant.descriptionPayoneProductsStep'.$paymentCode,
            'showFullDescription' => true,
            'condition' => $paymentCode.'Toggle',
            'sections' => [
                [
                    'title' => 'Assistant.titlePayonePaymentSection',
                    'description' => 'Assistant.descriptionPayonePaymentSectionAmazonPay',
                    'form' =>
                        $this->getMinMaxAmountConfig($paymentCode)
                        + [
                            $paymentCode.'AllowedDeliveryCountries' => [
                                'type' => 'checkboxGroup',
                                'defaultValue' => $this->activeCountries4AmazonPay,
                                'options' => [
                                    'name' => 'Assistant.allowedDeliveryCountries',
                                    'required' => true,
                                    'checkboxValues' => $this->getDeliveryCountries4AmazonPay()
                                ]
                            ],
                        ]
                        + $this->getAuthorizationConfig($paymentCode) +
                        [
                            $paymentCode.'Sandbox' => [
                                'type' => 'select',
                                "defaultValue" => 0,
                                'options' => [
                                    'name' => 'Assistant.Sandbox',
                                    'listBoxValues' => [
                                        [
                                            "caption" => 'Assistant.sandboxProductiveOption',
                                            "value" => 0
                                        ],
                                        [
                                            "caption" => 'Assistant.sandboxTestingOption',
                                            "value" => 1
                                        ]
                                    ]
                                ]
                            ]
                        ]
                ]
            ]
        ];

        return $config;
    }


    /**
     * @param string $paymentCode
     * @return array
     */
    protected function getMinMaxAmountConfig(string $paymentCode): array
    {
        return [
            $paymentCode.'MinimumAmount' => [
                'type' => 'double',
                'defaultValue' => 0,
                'options' => [
                    'isPriceInput' => true,
                    'decimalCount' => 2,
                    'name' => 'Assistant.MinimumAmount'
                ]
            ],
            $paymentCode.'MaximumAmount' => [
                'type' => 'double',
                'defaultValue' => 0,
                'options' => [
                    'isPriceInput' => true,
                    'decimalCount' => 2,
                    'name' => 'Assistant.MaximumAmount'
                ]
            ]
        ];
    }

    /**
     * @param string $paymentCode
     * @return array
     */
    protected function getDeliveryCountriesConfig(string $paymentCode): array
    {
        return [
            $paymentCode.'AllowedDeliveryCountries' => [
                'type' => 'checkboxGroup',
                'defaultValue' => $this->activeCountries,
                'options' => [
                    'name' => 'Assistant.allowedDeliveryCountries',
                    'required' => true,
                    'checkboxValues' => $this->getDeliveryCountries()
                ]
            ]
        ];
    }

    /**
     * @param string $paymentCode
     * @return array
     */
    protected function getAuthorizationConfig(string $paymentCode): array
    {
        $listBoxValues = [
            [
                "caption" => 'Assistant.authTypeDefault',
                "value" => -1
            ],
            [
                "caption" => 'Assistant.authTypeAuthorization',
                "value" => 1
            ],
            [
                "caption" => 'Assistant.authTypePreAuthorization',
                "value" => 0
            ]
        ];

        if($paymentCode == PayoneSofortPaymentMethod::PAYMENT_CODE) {
            // Only this auth method available for SOFORT
            $listBoxValues = [
                [
                    "caption" => 'Assistant.authTypeAuthorization',
                    "value" => 1
                ]
            ];
        }

        return [
            $paymentCode.'AuthType' => [
                'type' => 'select',
                "defaultValue" => -1,
                'options' => [
                    'name' => 'Assistant.authType',
                    'listBoxValues' => $listBoxValues
                ]
            ]
        ];
    }

    /**
     * @return int
     */
    protected function getMainWebstore(): int
    {
        /** @var WebstoreRepositoryContract $webstoreRepository */
        $webstoreRepository = pluginApp(WebstoreRepositoryContract::class);
        $webstore = $webstoreRepository->findById(0);
        return $webstore->storeIdentifier;
    }

    /**
     * @return array
     */
    protected function getWebstoreValues(): array
    {
        /** @var WebstoreRepositoryContract $webstoreRepository */
        $webstoreRepository = pluginApp(WebstoreRepositoryContract::class);
        $webstores = $webstoreRepository->loadAll();

        $values = [];

        /** @var Webstore $webstore */
        foreach ($webstores as $webstore) {
            $values[] = [
                'caption' => $webstore->name,
                'value' => $webstore->storeIdentifier
            ];
        }
        return $values;
    }

    /**
     * @return string
     */
    protected function getIcon(): string
    {
        $app = pluginApp(Application::class);
        $icon = $app->getUrlPath('Payone').'/images/logos/PAYONE_PAYONE_CREDIT_CARD.png';

        return $icon;
    }

    /**
     * @return array
     */
    protected function getBackendUsers(): array
    {
        /** @var UserRepositoryContract $app */
        $userRepo = pluginApp(UserRepositoryContract::class);

        $allBackendUsers = $userRepo->getAll();
        $users = [];
        /** @var User $backendUser */
        foreach ($allBackendUsers as $backendUser) {
            $users[] = [
                'caption' => $backendUser->realName,
                'value' => $backendUser->id
            ];
        }

        return $users;
    }

    /**
     * @return array
     */
    protected function getDeliveryCountries(): array
    {
        $deliveryCountries = [];
        $countries = ['DE', 'AT', 'CH'];
        foreach($countries as $country) {
            $deliveryCountries[] = [
                'caption' => 'Assistant.deliveryCountry'.$country,
                'value' => $country
            ];
        }

        return $deliveryCountries;
    }

    /**
     * @return array
     */
    protected function getDeliveryCountries4AmazonPay(): array
    {
        $deliveryCountries = [];
        $countries = ['DE', 'FR', 'IT', 'ES', 'LU', 'NL', 'SE', 'PT', 'HU', 'DK'];
        foreach($countries as $country) {
            $deliveryCountries[] = [
                'caption' => 'Assistant.deliveryCountry'.$country,
                'value' => $country
            ];
        }

        return $deliveryCountries;
    }

    /**
     * Load the active country values
     */
    protected function loadActiveCountriesValues()
    {
        if ($this->activeCountries === null || $this->activeCountries4AmazonPay === null) {
            /** @var CountryRepositoryContract $countryRepository */
            $countryRepository = pluginApp(CountryRepositoryContract::class);
            $activeCountries = $countryRepository->getActiveCountriesList();
            /** @var Country $country */
            foreach($activeCountries as $country){
                // All Payone payment methods
                if(in_array($country->isoCode2, ['DE', 'AT', 'CH'])) {
                    $this->activeCountries[] = $country->isoCode2;
                }

                // Amazon Pay over Payone
                if(in_array($country->isoCode2, ['DE', 'FR', 'IT', 'ES', 'LU', 'NL', 'SE', 'PT', 'HU', 'DK'])) {
                    $this->activeCountries4AmazonPay[] = $country->isoCode2;
                }
            }
        }
    }

    /**
     * @return array
     */
    protected function getAllowedCreditCardTypes(): array
    {
        /** @var CreditcardTypes $creditCardTypes */
        $creditCardTypes = pluginApp(CreditcardTypes::class);

        $allowedCreditCards = [];
        $cards = $creditCardTypes->getCreditCardTypes();
        foreach($cards as $card) {
            $allowedCreditCards[] = [
                'caption' => 'Assistant.creditCardType'.$card,
                'value' => $card
            ];
        }

        return $allowedCreditCards;
    }
}
