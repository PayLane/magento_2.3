<?php

declare(strict_types=1);

/**
 * File: PayPalConfigProviderInterface.php
 *
 
 */

namespace PeP\PaymentGateway\Api\Config\Methods;

/**
 * Interface PayPalConfigProviderInterface
 * @package PeP\PaymentGateway\Api\Config\Methods
 */
interface PayPalConfigProviderInterface extends
    ToggleableMethodConfigProviderInterface,
    SpecificMethodConfigProviderInterface
{
}
