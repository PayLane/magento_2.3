<?php

declare(strict_types=1);

/**
 * File: DirectDebitConfigProviderInterface.php
 *
 
 */

namespace PeP\PaymentGateway\Api\Config\Methods;

use Magento\Store\Model\ScopeInterface;

/**
 * Interface DirectDebitConfigProviderInterface
 * @package PeP\PaymentGateway\Api\Config\Methods
 */
interface DirectDebitConfigProviderInterface extends
    ToggleableMethodConfigProviderInterface,
    SpecificMethodConfigProviderInterface
{
    /**
     * @param string $scopeType
     * @param null $scopeCode
     * @return string
     */
    public function getMandateId(
        string $scopeType = ScopeInterface::SCOPE_WEBSITE,
        $scopeCode = null
    ): string;
}
