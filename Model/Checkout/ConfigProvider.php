<?php

declare(strict_types=1);

/**
 * File: ConfigProvider.php
 *
 
 */

namespace PeP\PaymentGateway\Model\Checkout;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use PeP\PaymentGateway\Api\Config\GeneralConfigProviderInterface;
use PeP\PaymentGateway\Api\Config\Methods\Blik0ConfigProviderInterface;
use PeP\PaymentGateway\Api\Config\Methods\IdealConfigProviderInterface;
use PeP\PaymentGateway\Api\Config\Methods\PayPalConfigProviderInterface;
use PeP\PaymentGateway\Api\Config\Methods\ApplePayConfigProviderInterface;
use PeP\PaymentGateway\Api\Config\Methods\CreditCardConfigProviderInterface;
use PeP\PaymentGateway\Api\Config\Methods\DirectDebitConfigProviderInterface;
use PeP\PaymentGateway\Api\Config\Methods\BankTransferConfigProviderInterface;
use PeP\PaymentGateway\Api\Config\Methods\SofortBankingConfigProviderInterface;

/**
 * Class ConfigProvider
 * @package PeP\PaymentGateway\Model\Checkout
 */
class ConfigProvider implements ConfigProviderInterface
{
    /**
     * @var ApplePayConfigProviderInterface
     */
    private $applePayConfigProvider;

    /**
     * @var BankTransferConfigProviderInterface
     */
    private $bankTransferConfigProvider;

    /**
     * @var CreditCardConfigProviderInterface
     */
    private $creditCardConfigProvider;

    /**
     * @var DirectDebitConfigProviderInterface
     */
    private $directDebitConfigProvider;

    /**
     * @var IdealConfigProviderInterface
     */
    private $idealConfigProvider;

    /**
     * @var PayPalConfigProviderInterface
     */
    private $payPalConfigProvider;

    /**
     * @var SofortBankingConfigProviderInterface
     */
    private $sofortBankingConfigProvider;

    /**
     * @var Blik0ConfigProviderInterface
     */
    private $blik0ConfigProvider;

    /**
     * @var GeneralConfigProviderInterface
     */
    private $generalConfigProvider;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * ConfigProvider constructor.
     * @param ApplePayConfigProviderInterface $applePayConfigProvider
     * @param BankTransferConfigProviderInterface $bankTransferConfigProvider
     * @param CreditCardConfigProviderInterface $creditCardConfigProvider
     * @param DirectDebitConfigProviderInterface $directDebitConfigProvider
     * @param IdealConfigProviderInterface $idealConfigProvider
     * @param PayPalConfigProviderInterface $payPalConfigProvider
     * @param SofortBankingConfigProviderInterface $sofortBankingConfigProvider
     * @param Blik0ConfigProviderInterface $blik0ConfigProvider
     * @param GeneralConfigProviderInterface $generalConfigProvider
     * @param ScopeConfigInterface $scopeConfig
     * @SuppressWarnings(PHPMD.LongVariable)
     */
    public function __construct(
        ApplePayConfigProviderInterface $applePayConfigProvider,
        BankTransferConfigProviderInterface $bankTransferConfigProvider,
        CreditCardConfigProviderInterface $creditCardConfigProvider,
        DirectDebitConfigProviderInterface $directDebitConfigProvider,
        IdealConfigProviderInterface $idealConfigProvider,
        PayPalConfigProviderInterface $payPalConfigProvider,
        SofortBankingConfigProviderInterface $sofortBankingConfigProvider,
        Blik0ConfigProviderInterface $blik0ConfigProvider,
        GeneralConfigProviderInterface $generalConfigProvider,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->applePayConfigProvider = $applePayConfigProvider;
        $this->bankTransferConfigProvider = $bankTransferConfigProvider;
        $this->creditCardConfigProvider = $creditCardConfigProvider;
        $this->directDebitConfigProvider = $directDebitConfigProvider;
        $this->idealConfigProvider = $idealConfigProvider;
        $this->payPalConfigProvider = $payPalConfigProvider;
        $this->sofortBankingConfigProvider = $sofortBankingConfigProvider;
        $this->generalConfigProvider = $generalConfigProvider;
        $this->scopeConfig = $scopeConfig;
        $this->blik0ConfigProvider = $blik0ConfigProvider;
    }

    /**
     * @return array
     */
    public function getConfig(): array
    {
        $result = [
            'store' => [
                'name' => $this->scopeConfig->getValue('general/store_information/name')
            ],
            'payment' => [
                'paylane' => [
                    'api_key' => $this->generalConfigProvider->getApiKey()
                ],
                'paylane_banktransfer' => [
                    'show_img' => $this->bankTransferConfigProvider->isPaymentMethodImageShown()
                ],
                'paylane_sofort' => [
                    'show_img' => $this->sofortBankingConfigProvider->isPaymentMethodImageShown()
                ],
                'paylane_directdebit' => [
                    'show_img' => $this->directDebitConfigProvider->isPaymentMethodImageShown()
                ],
                'paylane_paypal' => [
                    'show_img' => $this->payPalConfigProvider->isPaymentMethodImageShown()
                ],
                'paylane_ideal' => [
                    'show_img' => $this->idealConfigProvider->isPaymentMethodImageShown()
                ],
                'paylane_applepay' => [
                    'show_img' => $this->applePayConfigProvider->isPaymentMethodImageShown()
                ],
                'paylane_blik0' => [
                    'show_img' => $this->blik0ConfigProvider->isPaymentMethodImageShown()
                ],
            ]
        ];
        
        return $result;
    }
}
