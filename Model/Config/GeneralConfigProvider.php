<?php

declare(strict_types=1);

/**
 * File: GeneralConfigProvider.php
 *
 
 */

namespace PeP\PaymentGateway\Model\Config;

use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use PeP\PaymentGateway\Model\Config\Methods\Blik0ConfigProvider;
use PeP\PaymentGateway\Model\Config\Methods\IdealConfigProvider;
use PeP\PaymentGateway\Api\Config\GeneralConfigProviderInterface;
use PeP\PaymentGateway\Model\Config\Methods\PayPalConfigProvider;
use PeP\PaymentGateway\Model\Config\Methods\ApplePayConfigProvider;
use PeP\PaymentGateway\Model\Config\Methods\CreditCardConfigProvider;
use PeP\PaymentGateway\Model\Config\Methods\SecureFormConfigProvider;
use PeP\PaymentGateway\Model\Config\Methods\DirectDebitConfigProvider;
use PeP\PaymentGateway\Model\Config\Methods\BankTransferConfigProvider;
use PeP\PaymentGateway\Model\Config\Methods\SofortBankingConfigProvider;

/**
 * TODO: Unit test
 * Class GeneralConfigProvider
 * @package PeP\PaymentGateway\Model\Config
 */
class GeneralConfigProvider implements GeneralConfigProviderInterface
{
    /**
     * @var string
     */
    public const CODE_PAYLANE = 'paylane';

    /**
     * @var string
     */
    private const XML_PATH_PAYMENT_PAYLANE_ENABLE = 'payment/paylane/enable';

    /**
     * @var string
     */
    private const XML_PATH_PAYMENT_PAYLANE_TITLE = 'payment/paylane/title';

    /**
     * @var string
     */
    private const XML_PATH_PAYMENT_PAYLANE_SORT_ORDER = 'payment/paylane/sort_order';

    /**
     * @var string
     */
    private const XML_PATH_PAYMENT_PAYLANE_PAYMENT_MODE = 'payment/paylane/payment_mode';

    /**
     * @var string
     */
    private const XML_PATH_PAYMENT_PAYLANE_HASH_SALT = 'payment/paylane/hash_salt';

    /**
     * @var string
     */
    private const XML_PATH_PAYMENT_PAYLANE_MERCHANT_ID = 'payment/paylane/merchant_id';

    /**
     * @var string
     */
    private const XML_PATH_PAYMENT_PAYLANE_API_KEY = 'payment/paylane/api_key';

    /**
     * @var string
     */
    private const XML_PATH_PAYMENT_PAYLANE_PENDING_ORDER_STATUS = 'payment/paylane/pending_order_status';

    /**
     * @var string
     */
    private const XML_PATH_PAYMENT_PAYLANE_PERFORMED_ORDER_STATUS = 'payment/paylane/performed_order_status';

    /**
     * @var string
     */
    private const XML_PATH_PAYMENT_PAYLANE_CLEARED_ORDER_STATUS = 'payment/paylane/cleared_order_status';

    /**
     * @var string
     */
    private const XML_PATH_PAYMENT_PAYLANE_ERROR_ORDER_STATUS = 'payment/paylane/error_order_status';

    /**
     * @var string
     */
    private const XML_PATH_PAYMENT_PAYLANE_REDIRECT_METHOD = 'payment/paylane/redirect_method';

    /**
     * @var string
     */
    private const XML_PATH_PAYMENT_PAYLANE_ENABLE_LOG = 'payment/paylane/enable_log';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * GeneralConfigProvider constructor.
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param string $scopeType
     * @param null $scopeCode
     * @return bool
     */
    public function isEnabled(
        string $scopeType = ScopeInterface::SCOPE_WEBSITE,
        $scopeCode = null
    ): bool {
        return (bool) $this->scopeConfig->isSetFlag(self::XML_PATH_PAYMENT_PAYLANE_ENABLE, $scopeType, $scopeCode);
    }

    /**
     * @param string $scopeType
     * @param null $scopeCode
     * @return string
     */
    public function getTitle(
        string $scopeType = ScopeInterface::SCOPE_STORE,
        $scopeCode = null
    ): string {
        return (string) $this->scopeConfig->getValue(self::XML_PATH_PAYMENT_PAYLANE_TITLE, $scopeType, $scopeCode);
    }

    /**
     * @param string $scopeType
     * @param null $scopeCode
     * @return int
     */
    public function getSortOrder(
        string $scopeType = ScopeInterface::SCOPE_STORE,
        $scopeCode = null
    ): int {
        return (int) $this->scopeConfig->getValue(self::XML_PATH_PAYMENT_PAYLANE_SORT_ORDER, $scopeType, $scopeCode);
    }

    /**
     * @param string $scopeType
     * @param null $scopeCode
     * @return string
     */
    public function getPaymentMode(
        string $scopeType = ScopeInterface::SCOPE_WEBSITE,
        $scopeCode = null
    ): string {
        return (string) $this->scopeConfig->getValue(
            self::XML_PATH_PAYMENT_PAYLANE_PAYMENT_MODE,
            $scopeType,
            $scopeCode
        );
    }

    /**
     * @param string $scopeType
     * @param null $scopeCode
     * @return string
     */
    public function getHashSalt(
        string $scopeType = ScopeInterface::SCOPE_WEBSITE,
        $scopeCode = null
    ): string {
        return (string) $this->scopeConfig->getValue(self::XML_PATH_PAYMENT_PAYLANE_HASH_SALT, $scopeType, $scopeCode);
    }

    /**
     * @param string $scopeType
     * @param null $scopeCode
     * @return string
     */
    public function getMerchantId(
        string $scopeType = ScopeInterface::SCOPE_WEBSITE,
        $scopeCode = null
    ): string {
        return (string) $this->scopeConfig->getValue(
            self::XML_PATH_PAYMENT_PAYLANE_MERCHANT_ID,
            $scopeType,
            $scopeCode
        );
    }

    /**
     * @param string $scopeType
     * @param null $scopeCode
     * @return string
     */
    public function getApiKey(
        string $scopeType = ScopeInterface::SCOPE_WEBSITE,
        $scopeCode = null
    ): string {
        return (string) $this->scopeConfig->getValue(self::XML_PATH_PAYMENT_PAYLANE_API_KEY, $scopeType, $scopeCode);
    }

    /**
     * @param string $scopeType
     * @param null $scopeCode
     * @return string
     */
    public function getPendingOrderStatus(
        string $scopeType = ScopeInterface::SCOPE_WEBSITE,
        $scopeCode = null
    ): string {
        return (string) $this->scopeConfig->getValue(
            self::XML_PATH_PAYMENT_PAYLANE_PENDING_ORDER_STATUS,
            $scopeType,
            $scopeCode
        );
    }

    /**
     * @param string $scopeType
     * @param null $scopeCode
     * @return string
     */
    public function getPerformedOrderStatus(
        string $scopeType = ScopeInterface::SCOPE_WEBSITE,
        $scopeCode = null
    ): string {
        return (string) $this->scopeConfig->getValue(
            self::XML_PATH_PAYMENT_PAYLANE_PERFORMED_ORDER_STATUS,
            $scopeType,
            $scopeCode
        );
    }

    /**
     * @param string $scopeType
     * @param null $scopeCode
     * @return string
     */
    public function getClearedOrderStatus(
        string $scopeType = ScopeInterface::SCOPE_WEBSITE,
        $scopeCode = null
    ): string {
        return (string) $this->scopeConfig->getValue(
            self::XML_PATH_PAYMENT_PAYLANE_CLEARED_ORDER_STATUS,
            $scopeType,
            $scopeCode
        );
    }

    /**
     * @param string $scopeType
     * @param null $scopeCode
     * @return string
     */
    public function getErrorOrderStatus(
        string $scopeType = ScopeInterface::SCOPE_WEBSITE,
        $scopeCode = null
    ): string {
        return (string) $this->scopeConfig->getValue(
            self::XML_PATH_PAYMENT_PAYLANE_ERROR_ORDER_STATUS,
            $scopeType,
            $scopeCode
        );
    }

    /**
     * @param string $scopeType
     * @param string $scopeCode
     * @return string
     */
    public function getRedirectMethod(
        string $scopeType = ScopeInterface::SCOPE_WEBSITE,
        $scopeCode = null
    ): string {
        return (string) $this->scopeConfig->getValue(
            self::XML_PATH_PAYMENT_PAYLANE_REDIRECT_METHOD,
            $scopeType,
            $scopeCode
        );
    }

    /**
     * @param string $scopeType
     * @param string $scopeCode
     * @return bool
     */
    public function isLoggingEnabled(
        string $scopeType = ScopeInterface::SCOPE_WEBSITE,
        $scopeCode = null
    ): bool {
        return (bool) $this->scopeConfig->isSetFlag(self::XML_PATH_PAYMENT_PAYLANE_ENABLE_LOG, $scopeType, $scopeCode);
    }

    /**
     * TODO: Move to di.xml, to be able to add new via DI
     * @return array
     */
    public function getPaymentCodes(): array
    {
        return [
            BankTransferConfigProvider::CODE_BANK_TRANSFER,
            CreditCardConfigProvider::CODE_CREDIT_CARD,
            DirectDebitConfigProvider::CODE_DIRECT_DEBIT,
            IdealConfigProvider::CODE_IDEAL,
            PayPalConfigProvider::CODE_PAYPAL,
            SecureFormConfigProvider::CODE_SECURE_FORM,
            SofortBankingConfigProvider::CODE_SOFORT,
            ApplePayConfigProvider::CODE_APPLE_PAY,
            Blik0ConfigProvider::CODE_BLIK0
        ];
    }
}
