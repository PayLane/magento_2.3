<?php

declare(strict_types=1);

/**
 * File: SofortBankingConfigProvider.php
 *
 
 */

namespace PeP\PaymentGateway\Model\Config\Methods;

use PeP\PaymentGateway\Api\Config\Methods\SofortBankingConfigProviderInterface;

/**
 * Class SofortBankingConfigProvider
 * @package PeP\PaymentGateway\Model\Config\Methods
 */
class SofortBankingConfigProvider extends AbstractToggleableMethodConfigProvider implements
    SofortBankingConfigProviderInterface
{
    /**
     * @var string
     */
    public const CODE_SOFORT = 'paylane_sofort';

    /**
     * @return string
     * @codeCoverageIgnore
     */
    protected function getMethodCode(): string
    {
        return self::CODE_SOFORT;
    }
}
