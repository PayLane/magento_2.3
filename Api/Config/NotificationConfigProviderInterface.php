<?php

declare(strict_types=1);

/**
 * File: NotificationConfigProviderInterface.php
 *
 
 */

namespace PeP\PaymentGateway\Api\Config;

use Magento\Store\Model\ScopeInterface;

/**
 * Interface NotificationConfigProviderInterface
 * @package PeP\PaymentGateway\Api\Config
 */
interface NotificationConfigProviderInterface
{
    /**
     * @param string $scopeType
     * @param null $scopeCode
     * @return string
     */
    public function getNotificationHandlingMode(
        string $scopeType = ScopeInterface::SCOPE_WEBSITE,
        $scopeCode = null
    ): string;

    /**
     * @param string $scopeType
     * @param null $scopeCode
     * @return string
     */
    public function getNotificationToken(
        string $scopeType = ScopeInterface::SCOPE_WEBSITE,
        $scopeCode = null
    ): string;

    /**
     * @param string $scopeType
     * @param null $scopeCode
     * @return bool
     */
    public function isLoggingEnabled(
        string $scopeType = ScopeInterface::SCOPE_WEBSITE,
        $scopeCode = null
    ): bool;
}
