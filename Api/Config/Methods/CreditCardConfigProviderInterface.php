<?php

declare(strict_types=1);

/**
 * File: CreditCardConfigProviderInterface.php
 *
 
 */

namespace PeP\PaymentGateway\Api\Config\Methods;

use Magento\Store\Model\ScopeInterface;

/**
 * Interface CreditCardConfigProviderInterface
 * @package PeP\PaymentGateway\Api\Config\Methods
 */
interface CreditCardConfigProviderInterface extends
    ToggleableMethodConfigProviderInterface,
    SpecificMethodConfigProviderInterface
{
    /**
     * @param string $scopeType
     * @param null $scopeCode
     * @return string
     */
    public function getPaymentAction(
        string $scopeType = ScopeInterface::SCOPE_WEBSITE,
        $scopeCode = null
    ): string;

    /**
     * @param string $scopeType
     * @param null $scopeCode
     * @return bool
     */
    /*public function isFraudCheckOverwritten(
        string $scopeType = ScopeInterface::SCOPE_WEBSITE,
        $scopeCode = null
    ): bool;*/

    /**
     * @param string $scopeType
     * @param null $scopeCode
     * @return bool
     */
//    public function isFraudCheckEnabled(
//        string $scopeType = ScopeInterface::SCOPE_WEBSITE,
//        $scopeCode = null
//    ): bool;

    /**
     * @param string $scopeType
     * @param null $scopeCode
     * @return bool
     */
//    public function isAVSCheckOverwritten(
//        string $scopeType = ScopeInterface::SCOPE_WEBSITE,
//        $scopeCode = null
//    ): bool;

    /**
     * @param string $scopeType
     * @param null $scopeCode
     * @return string
     */
//    public function getAVSCheckLevel(
//        string $scopeType = ScopeInterface::SCOPE_WEBSITE,
//        $scopeCode = null
//    ): string;

    /**
     * @param string $scopeType
     * @param null $scopeCode
     * @return bool
     */
    public function is3DSCheckEnabled(
        string $scopeType = ScopeInterface::SCOPE_WEBSITE,
        $scopeCode = null
    ): bool;

    /**
     * @param string $scopeType
     * @param null $scopeCode
     * @return mixed
     */
//    public function getBlockedAmountInAuthorizationProcess(
//        string $scopeType = ScopeInterface::SCOPE_WEBSITE,
//        $scopeCode = null
//    ): float;

    /**
     * @param string $scopeType
     * @param null $scopeCode
     * @return bool
     */
//    public function isSingleClickPaymentEnabled(
//        string $scopeType = ScopeInterface::SCOPE_WEBSITE,,
//        $scopeCode = null
//    ): bool;
}
