<?php

declare(strict_types=1);

/**
 * File: PayPalConfigProvider.php
 *
 
 */

namespace PeP\PaymentGateway\Model\Config\Methods;

use PeP\PaymentGateway\Api\Config\Methods\PayPalConfigProviderInterface;

/**
 * Class PayPalConfigProvider
 * @package PeP\PaymentGateway\Model\Config\Methods
 */
class PayPalConfigProvider extends AbstractToggleableMethodConfigProvider implements
    PayPalConfigProviderInterface
{
    /**
     * @var string
     */
    public const CODE_PAYPAL = 'paylane_paypal';

    /**
     * @return string
     * @codeCoverageIgnore
     */
    protected function getMethodCode(): string
    {
        return self::CODE_PAYPAL;
    }
}
