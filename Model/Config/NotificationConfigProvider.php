<?php

declare(strict_types=1);

/**
 * File: NotificationConfigProvider.php
 *
 
 */

namespace PeP\PaymentGateway\Model\Config;

use PeP\PaymentGateway\Api\Config\NotificationConfigProviderInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * TODO: Unit test
 * Class NotificationConfigProvider
 * @package PeP\PaymentGateway\Model\Config
 */
class NotificationConfigProvider implements NotificationConfigProviderInterface
{
    /**
     * @var string
     */
    private const XML_PATH_PAYMENT_PAYLANE_NOTIFICATIONS_MODE = 'payment/paylane/paylane_notifications/mode';

    /**
     * @var string
     */
    private const XML_PATH_PAYMENT_PAYLANE_NOTIFICATIONS_TOKEN = 'payment/paylane/paylane_notifications/token';

    /**
     * @var string
     */
    private const XML_PATH_PAYMENT_PAYLANE_NOTIFICATIONS_ENABLE_LOG
        = 'payment/paylane/paylane_notifications/enable_log';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * GeneralConfigProvider constructor.
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param string $scopeType
     * @param null $scopeCode
     * @return string
     */
    public function getNotificationHandlingMode(
        string $scopeType = ScopeInterface::SCOPE_WEBSITE,
        $scopeCode = null
    ): string {
        return (string) $this->scopeConfig->getValue(
            self::XML_PATH_PAYMENT_PAYLANE_NOTIFICATIONS_MODE,
            $scopeType,
            $scopeCode
        );
    }

    /**
     * @param string $scopeType
     * @param null $scopeCode
     * @return string
     */
    public function getNotificationToken(
        string $scopeType = ScopeInterface::SCOPE_WEBSITE,
        $scopeCode = null
    ): string {
        return (string) $this->scopeConfig->getValue(
            self::XML_PATH_PAYMENT_PAYLANE_NOTIFICATIONS_TOKEN,
            $scopeType,
            $scopeCode
        );
    }

    /**
     * @param string $scopeType
     * @param null $scopeCode
     * @return bool
     */
    public function isLoggingEnabled(
        string $scopeType = ScopeInterface::SCOPE_WEBSITE,
        $scopeCode = null
    ): bool {
        return (bool) $this->scopeConfig->isSetFlag(
            self::XML_PATH_PAYMENT_PAYLANE_NOTIFICATIONS_ENABLE_LOG,
            $scopeType,
            $scopeCode
        );
    }
}
