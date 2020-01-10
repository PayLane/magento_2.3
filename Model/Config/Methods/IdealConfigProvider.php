<?php

declare(strict_types=1);

/**
 * File: IdealConfigProvider.php
 *
 
 */

namespace PeP\PaymentGateway\Model\Config\Methods;

use PeP\PaymentGateway\Api\Config\Methods\IdealConfigProviderInterface;

/**
 * Class IdealConfigProvider
 * @package PeP\PaymentGateway\Model\Config\Methods
 */
class IdealConfigProvider extends AbstractToggleableMethodConfigProvider implements
    IdealConfigProviderInterface
{
    /**
     * @var string
     */
    public const CODE_IDEAL = 'paylane_ideal';

    /**
     * @return string
     * @codeCoverageIgnore
     */
    protected function getMethodCode(): string
    {
        return self::CODE_IDEAL;
    }
}
