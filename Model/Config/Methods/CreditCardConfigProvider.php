<?php

declare(strict_types=1);

/**
 * File: CreditCardConfigProvider.php
 *
 
 */

namespace PeP\PaymentGateway\Model\Config\Methods;

use PeP\PaymentGateway\Api\Config\Methods\CreditCardConfigProviderInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Class CreditCardConfigProvider
 * @package PeP\PaymentGateway\Model\Config\Methods
 */
class CreditCardConfigProvider extends AbstractToggleableMethodConfigProvider implements
    CreditCardConfigProviderInterface
{
    /**
     * @var string
     */
    public const CODE_CREDIT_CARD = 'paylane_creditcard';

    /**
     * @var string
     */
    private const XML_PATH_PAYMENT_PAYLANE_CREDITCARD_PAYMENT_ACTION
        = 'payment/paylane_creditcard/payment_action';

    /**
     * @var string
     */
//    private const XML_PATH_PAYMENT_PAYLANE_CREDITCARD_FRAUD_CHECK_OVERWRITE
//        = 'payment/paylane_creditcard/fraud_check_overwrite';

    /**
     * @var string
     */
    //private const XML_PATH_PAYMENT_PAYLANE_CREDITCARD_FRAUD_CHECK_ENABLED = 'payment/paylane_creditcard/fraud_check';

    /**
     * @var string
     */
    /*private const XML_PATH_PAYMENT_PAYLANE_CREDITCARD_AWS_CHECK_OVERWRITE
        = 'payment/paylane_creditcard/avs_check_overwrite';*/

    /**
     * @var string
     */
    //private const XML_PATH_PAYMENT_PAYLANE_CREDITCARD_AWS_CHECK_LEVEL = 'payment/paylane_creditcard/avs_check_level';

    /**
     * @var string
     */
    /*private const XML_PATH_PAYMENT_PAYLANE_CREDITCARD_AUTHORIZATION_AMOUNT
        = 'payment/paylane_creditcard/authorization_amount';*/

    /**
     * @var string
     */
    /*private const XML_PATH_PAYMENT_PAYLANE_CREDITCARD_SINGLE_CLICK_ACTIVE
        = 'payment/paylane_creditcard/single_click_active';*/

    /**
     * @var string
     */
    private const XML_PATH_PAYMENT_PAYLANE_CREDITCARD_DS3_CHECK = 'payment/paylane_creditcard/ds3_check';

    /**
     * @return string
     * @codeCoverageIgnore
     */
    protected function getMethodCode(): string
    {
        return self::CODE_CREDIT_CARD;
    }

    /**
     * @param string $scopeType
     * @param null $scopeCode
     * @return string
     */
    public function getPaymentAction(
        string $scopeType = ScopeInterface::SCOPE_WEBSITE,
        $scopeCode = null
    ): string {
        return 'authorize_capture';
        // return (string) $this->scopeConfig->getValue(
        //     self::XML_PATH_PAYMENT_PAYLANE_CREDITCARD_PAYMENT_ACTION,
        //     $scopeType,
        //     $scopeCode
        // );
    }

    /**
     * @param string $scopeType
     * @param null $scopeCode
     * @return bool
     */
    /*public function isFraudCheckOverwritten(
        string $scopeType = ScopeInterface::SCOPE_WEBSITE,
        $scopeCode = null
    ): bool {
        return (bool) $this->scopeConfig->isSetFlag(
            self::XML_PATH_PAYMENT_PAYLANE_CREDITCARD_FRAUD_CHECK_OVERWRITE,
            $scopeType,
            $scopeCode
        );
    }*/

    /**
     * @param string $scopeType
     * @param null $scopeCode
     * @return bool
     */
//    public function isFraudCheckEnabled(
//        string $scopeType = ScopeInterface::SCOPE_WEBSITE,
//        $scopeCode = null
//    ): bool {
//        return (bool) $this->scopeConfig->isSetFlag(
//            self::XML_PATH_PAYMENT_PAYLANE_CREDITCARD_FRAUD_CHECK_ENABLED,
//            $scopeType,
//            $scopeCode
//        );
//    }

    /**
     * @param string $scopeType
     * @param null $scopeCode
     * @return bool
     */
//    public function isAVSCheckOverwritten(
//        string $scopeType = ScopeInterface::SCOPE_WEBSITE,
//        $scopeCode = null
//    ): bool {
//        return (bool) $this->scopeConfig->isSetFlag(
//            self::XML_PATH_PAYMENT_PAYLANE_CREDITCARD_AWS_CHECK_OVERWRITE,
//            $scopeType,
//            $scopeCode
//        );
//    }

    /**
     * @param string $scopeType
     * @param null $scopeCode
     * @return string
     */
//    public function getAVSCheckLevel(
//        string $scopeType = ScopeInterface::SCOPE_WEBSITE,
//        $scopeCode = null
//    ): string {
//        return (string) $this->scopeConfig->getValue(
//            self::XML_PATH_PAYMENT_PAYLANE_CREDITCARD_AWS_CHECK_LEVEL,
//            $scopeType,
//            $scopeCode
//        );
//    }

    /**
     * @param string $scopeType
     * @param null $scopeCode
     * @return bool
     */
    public function is3DSCheckEnabled(
        string $scopeType = ScopeInterface::SCOPE_WEBSITE,
        $scopeCode = null
    ): bool {
        return true;
        // return (bool) $this->scopeConfig->isSetFlag(
        //     self::XML_PATH_PAYMENT_PAYLANE_CREDITCARD_DS3_CHECK,
        //     $scopeType,
        //     $scopeCode
        // );
    }

    /**
     * @param string $scopeType
     * @param null $scopeCode
     * @return float
     */
//    public function getBlockedAmountInAuthorizationProcess(
//        string $scopeType = ScopeInterface::SCOPE_WEBSITE,
//        $scopeCode = null
//    ): float {
//        return (float) $this->scopeConfig->getValue(
//            self::XML_PATH_PAYMENT_PAYLANE_CREDITCARD_AUTHORIZATION_AMOUNT,
//            $scopeType,
//            $scopeCode
//        );
//    }

    /**
     * @param string $scopeType
     * @param null $scopeCode
     * @return bool
     */
    /*public function isSingleClickPaymentEnabled(
        string $scopeType = ScopeInterface::SCOPE_WEBSITE,
        $scopeCode = null
    ): bool {
        return (bool) $this->scopeConfig->isSetFlag(
            self::XML_PATH_PAYMENT_PAYLANE_CREDITCARD_SINGLE_CLICK_ACTIVE,
            $scopeType,
            $scopeCode
        );
    }*/
}
