<?php

declare(strict_types=1);

/**
 * File: SofortBankingConfigProviderInterface.php
 *
 
 */

namespace PeP\PaymentGateway\Api\Config\Methods;

/**
 * Interface SofortBankingConfigProviderInterface
 * @package PeP\PaymentGateway\Api\Config\Methods
 */
interface SofortBankingConfigProviderInterface extends
    ToggleableMethodConfigProviderInterface,
    SpecificMethodConfigProviderInterface
{
}
