<?php

declare (strict_types = 1);

/**
 * File: GooglePayConfigProviderInterface.php
 *
 */

namespace PeP\PaymentGateway\Api\Config\Methods;

/**
 * Interface GooglePayConfigProviderInterface
 * @package PeP\PaymentGateway\Api\Config\Methods
 */
interface GooglePayConfigProviderInterface extends
ToggleableMethodConfigProviderInterface,
SpecificMethodConfigProviderInterface
{

    /**
     * @param string $scopeType
     * @param null $scopeCode
     * @return string
     */
    public function getGoogleMerchantId(string $scopeType = ScopeInterface::SCOPE_WEBSITE,
        $scopeCode = null
    ): string;

}
