<?php

declare(strict_types=1);

/**
 * File: Blik0ConfigProvider.php
 *
 
 */

namespace PeP\PaymentGateway\Model\Config\Methods;

use PeP\PaymentGateway\Api\Config\Methods\Blik0ConfigProviderInterface;

/**
 * Class Blik0ConfigProvider
 * @package PeP\PaymentGateway\Model\Config\Methods
 */
class Blik0ConfigProvider extends AbstractToggleableMethodConfigProvider implements
    Blik0ConfigProviderInterface
{
    /**
     * @var string
     */
    public const CODE_BLIK0 = 'paylane_blik0';

    /**
     * @return string
     * @codeCoverageIgnore
     */
    protected function getMethodCode(): string
    {
        return self::CODE_BLIK0;
    }
}
