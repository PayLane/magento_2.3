<?php

declare(strict_types=1);

/**
 * File: Blik0ConfigProviderInterface.php
 *
 */

namespace PeP\PaymentGateway\Api\Config\Methods;

/**
 * Interface Blik0ConfigProviderInterface
 * @package PeP\PaymentGateway\Api\Config\Methods
 */
interface Blik0ConfigProviderInterface extends
    ToggleableMethodConfigProviderInterface,
    SpecificMethodConfigProviderInterface
{
}
