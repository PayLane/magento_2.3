<?php

declare(strict_types=1);

/**
 * File: GeneralAuthenticationConfigProvider.php
 *
 
 */

namespace PeP\PaymentGateway\Model\Config;

use PeP\PaymentGateway\Api\Config\GeneralAuthenticationConfigProviderInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * TODO: Unit test
 * Class GeneralAuthenticationConfigProvider
 * @package PeP\PaymentGateway\Model\Config
 */
class GeneralAuthenticationConfigProvider implements GeneralAuthenticationConfigProviderInterface
{
    /**
     * @var string
     */
    private const XML_PATH_PAYMENT_PAYLANE_USERNAME = 'payment/paylane/username';

    /**
     * @var string
     */
    private const XML_PATH_PAYMENT_PAYLANE_PASSWORD = 'payment/paylane/password';

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
            self::XML_PATH_PAYMENT_PAYLANE_USERNAME,
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
            self::XML_PATH_PAYMENT_PAYLANE_PASSWORD,
            $scopeType,
            $scopeCode
        );
    }
}
