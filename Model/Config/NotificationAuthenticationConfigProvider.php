<?php

declare(strict_types=1);

/**
 * File: NotificationAuthenticationConfigProvider.php
 *
 
 */

namespace PeP\PaymentGateway\Model\Config;

use PeP\PaymentGateway\Api\Config\NotificationAuthenticationConfigProviderInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * TODO: Unit test
 * Class NotificationAuthenticationConfigProvider
 * @package PeP\PaymentGateway\Model\Config
 */
class NotificationAuthenticationConfigProvider implements NotificationAuthenticationConfigProviderInterface
{
    /**
     * @var string
     */
    private const XML_PATH_PAYMENT_PAYLANE_NOTIFICATIONS_USERNAME = 'payment/paylane/paylane_notifications/username';

    /**
     * @var string
     */
    private const XML_PATH_PAYMENT_PAYLANE_NOTIFICATIONS_PASSWORD = 'payment/paylane/paylane_notifications/password';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * NotificationAuthenticationConfigProvider constructor.
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
    public function getUsername(
        string $scopeType = ScopeInterface::SCOPE_WEBSITE,
        $scopeCode = null
    ): string {
        return (string) $this->scopeConfig->getValue(
            self::XML_PATH_PAYMENT_PAYLANE_NOTIFICATIONS_USERNAME,
            $scopeType,
            $scopeCode
        );
    }

    /**
     * @param string $scopeType
     * @param null $scopeCode
     * @return string
     */
    public function getPassword(
        string $scopeType = ScopeInterface::SCOPE_WEBSITE,
        $scopeCode = null
    ): string {
        return (string) $this->scopeConfig->getValue(
            self::XML_PATH_PAYMENT_PAYLANE_NOTIFICATIONS_PASSWORD,
            $scopeType,
            $scopeCode
        );
    }
}
