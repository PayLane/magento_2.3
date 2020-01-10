<?php

declare(strict_types=1);

/**
 * File: GeneralConfigProviderInterface.php
 *
 */

namespace PeP\PaymentGateway\Api\Config;

use Magento\Store\Model\ScopeInterface;

/**
 * Interface GeneralConfigProviderInterface
 * @package PeP\PaymentGateway\Api\Config
 */
interface GeneralConfigProviderInterface
{
    /**
     * @param string $scopeType
     * @param null $scopeCode
     * @return bool
     */
    public function isEnabled(
        string $scopeType = ScopeInterface::SCOPE_WEBSITE,
        $scopeCode = null
    ): bool;

    /**
     * @param string $scopeType
     * @param null $scopeCode
     * @return string
     */
    public function getTitle(
        string $scopeType = ScopeInterface::SCOPE_STORE,
        $scopeCode = null
    ): string;

    /**
     * @param string $scopeType
     * @param null $scopeCode
     * @return int
     */
    public function getSortOrder(
        string $scopeType = ScopeInterface::SCOPE_STORE,
        $scopeCode = null
    ): int;

    /**
     * @param string $scopeType
     * @param null $scopeCode
     * @return string
     */
    public function getPaymentMode(
        string $scopeType = ScopeInterface::SCOPE_WEBSITE,
        $scopeCode = null
    ): string;

    /**
     * @param string $scopeType
     * @param null $scopeCode
     * @return string
     */
    public function getHashSalt(
        string $scopeType = ScopeInterface::SCOPE_WEBSITE,
        $scopeCode = null
    ): string;

    /**
     * @param string $scopeType
     * @param null $scopeCode
     * @return string
     */
    public function getMerchantId(
        string $scopeType = ScopeInterface::SCOPE_WEBSITE,
        $scopeCode = null
    ): string;

    /**
     * @param string $scopeType
     * @param null $scopeCode
     * @return string
     */
    public function getApiKey(
        string $scopeType = ScopeInterface::SCOPE_WEBSITE,
        $scopeCode = null
    ): string;

    /**
     * @param string $scopeType
     * @param null $scopeCode
     * @return string
     */
    public function getPendingOrderStatus(
        string $scopeType = ScopeInterface::SCOPE_WEBSITE,
        $scopeCode = null
    ): string;

    /**
     * @param string $scopeType
     * @param null $scopeCode
     * @return string
     */
    public function getPerformedOrderStatus(
        string $scopeType = ScopeInterface::SCOPE_WEBSITE,
        $scopeCode = null
    ): string;

    /**
     * @param string $scopeType
     * @param null $scopeCode
     * @return string
     */
    public function getClearedOrderStatus(
        string $scopeType = ScopeInterface::SCOPE_WEBSITE,
        $scopeCode = null
    ): string;

    /**
     * @param string $scopeType
     * @param null $scopeCode
     * @return string
     */
    public function getErrorOrderStatus(
        string $scopeType = ScopeInterface::SCOPE_WEBSITE,
        $scopeCode = null
    ): string;

    /**
     * @param string $scopeType
     * @param null $scopeCode
     * @return string
     */
    public function getRedirectMethod(
        string $scopeType = ScopeInterface::SCOPE_WEBSITE,
        $scopeCode = null
    ): string;

    /**
     * @param string $scopeType
     * @param null $scopeCode
     * @return bool
     */
    public function isLoggingEnabled(
        string $scopeType = ScopeInterface::SCOPE_WEBSITE,
        $scopeCode = null
    ): bool;

    /**
     * @return array
     */
    public function getPaymentCodes(): array;
}
