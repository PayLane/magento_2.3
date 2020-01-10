<?php

declare(strict_types=1);

/**
 * File: SpecificMethodConfigProviderInterface.php
 *
 
 */

namespace PeP\PaymentGateway\Api\Config\Methods;

use Magento\Store\Model\ScopeInterface;

/**
 * Interface SpecificMethodConfigProviderInterface
 * @package PeP\PaymentGateway\Api\Config\Methods
 */
interface SpecificMethodConfigProviderInterface
{
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
     * @return bool
     */
    public function isPaymentMethodImageShown(
        string $scopeType = ScopeInterface::SCOPE_STORE,
        $scopeCode = null
    ): bool;
}
