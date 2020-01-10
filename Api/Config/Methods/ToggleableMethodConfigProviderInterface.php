<?php

declare(strict_types=1);

/**
 * File: ToggleableMethodConfigProviderInterface.php
 *
 
 */

namespace PeP\PaymentGateway\Api\Config\Methods;

use Magento\Store\Model\ScopeInterface;

/**
 * Interface ToggleableMethodConfigProviderInterface
 * @package PeP\PaymentGateway\Api\Config\Methods
 */
interface ToggleableMethodConfigProviderInterface
{
    /**
     * @param string $scopeType
     * @param null $scopeCode
     * @return bool
     */
    public function isActive(
        string $scopeType = ScopeInterface::SCOPE_WEBSITE,
        $scopeCode = null
    ): bool;
}
