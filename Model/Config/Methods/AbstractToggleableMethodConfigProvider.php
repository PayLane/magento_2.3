<?php

declare(strict_types=1);

/**
 * File: AbstractToggleableMethodConfigProvider.php
 *
 
 */

namespace PeP\PaymentGateway\Model\Config\Methods;

use PeP\PaymentGateway\Api\Config\Methods\ToggleableMethodConfigProviderInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * TODO: Unit test
 * Class AbstractToggleableMethodConfigProvider
 * @package PeP\PaymentGateway\Model\Config\Methods
 */
abstract class AbstractToggleableMethodConfigProvider extends AbstractMethodConfigProvider implements
    ToggleableMethodConfigProviderInterface
{
    /**
     * @var string
     */
    private const ACTIVE_FIELD = 'active';

    /**
     * @param string $scopeType
     * @param null $scopeCode
     * @return bool
     */
    public function isActive(
        string $scopeType = ScopeInterface::SCOPE_WEBSITE,
        $scopeCode = null
    ): bool {
        return (bool) $this->scopeConfig->isSetFlag(
            $this->buildPathForConfiguration(self::ACTIVE_FIELD),
            $scopeType,
            $scopeCode
        );
    }
}
