<?php

declare(strict_types=1);

/**
 * File: GeneralAuthenticationConfigProviderInterface.php
 *
 */

namespace PeP\PaymentGateway\Api\Config;

use Magento\Store\Model\ScopeInterface;

/**
 * Interface GeneralAuthenticationConfigProviderInterface
 * @package PeP\PaymentGateway\Api\Config
 */
interface GeneralAuthenticationConfigProviderInterface
{
    /**
     * @param string $scopeType
     * @param null $scopeCode
     * @return string
     */
    public function getUsername(
        string $scopeType = ScopeInterface::SCOPE_WEBSITE,
        $scopeCode = null
    ): string;

    /**
     * @param string $scopeType
     * @param null $scopeCode
     * @return string
     */
    public function getPassword(
        string $scopeType = ScopeInterface::SCOPE_WEBSITE,
        $scopeCode = null
    ): string;
}
