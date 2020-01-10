<?php

declare(strict_types=1);

/**
 * File: IdealConfigProviderInterface.php
 *
 
 */

namespace PeP\PaymentGateway\Api\Config\Methods;

/**
 * Interface IdealConfigProviderInterface
 * @package PeP\PaymentGateway\Api\Config\Methods
 */
interface IdealConfigProviderInterface extends
    ToggleableMethodConfigProviderInterface,
    SpecificMethodConfigProviderInterface
{
}
