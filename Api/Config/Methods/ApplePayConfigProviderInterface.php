<?php

declare(strict_types=1);

/**
 * File: ApplePayConfigProviderInterface.php
 *
 */

namespace PeP\PaymentGateway\Api\Config\Methods;

/**
 * Interface ApplePayConfigProviderInterface
 * @package PeP\PaymentGateway\Api\Config\Methods
 */
interface ApplePayConfigProviderInterface extends
    ToggleableMethodConfigProviderInterface,
    SpecificMethodConfigProviderInterface
{

    /**
     * @param string $scopeType
     * @param null $scopeCode
     * @return string
     */
    public function getCertificate(
        string $scopeType = ScopeInterface::SCOPE_WEBSITE,
        $scopeCode = null
    ): string;
}
