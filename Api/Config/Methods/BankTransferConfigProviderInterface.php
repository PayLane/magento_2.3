<?php

declare(strict_types=1);

/**
 * File: BankTransferConfigProviderInterface.php
 *
 */

namespace PeP\PaymentGateway\Api\Config\Methods;

/**
 * Interface BankTransferConfigProviderInterface
 * @package PeP\PaymentGateway\Api\Config\Methods
 */
interface BankTransferConfigProviderInterface extends
    ToggleableMethodConfigProviderInterface,
    SpecificMethodConfigProviderInterface
{
}
