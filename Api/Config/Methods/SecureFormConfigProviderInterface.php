<?php

declare(strict_types=1);

/**
 * File: SecureFormConfigProviderInterface.php
 *
 
 */

namespace PeP\PaymentGateway\Api\Config\Methods;

use Magento\Store\Model\ScopeInterface;

/**
 * Interface SecureFormConfigProviderInterface
 * @package PeP\PaymentGateway\Api\Config\Methods
 */
interface SecureFormConfigProviderInterface extends SpecificMethodConfigProviderInterface
{
    /**
     * @param string $scopeType
     * @param null $scopeCode
     * @return bool
     */
    public function isSendCustomerData(
        string $scopeType = ScopeInterface::SCOPE_WEBSITE,
        $scopeCode = null
    ): bool;
}
