<?php

declare(strict_types=1);

/**
 * File: ApplePayConfigProvider.php
 *
 
 */

namespace PeP\PaymentGateway\Model\Config\Methods;

use PeP\PaymentGateway\Api\Config\Methods\ApplePayConfigProviderInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Class ApplePayConfigProvider
 * @package PeP\PaymentGateway\Model\Config\Methods
 */
class ApplePayConfigProvider extends AbstractToggleableMethodConfigProvider implements ApplePayConfigProviderInterface
{
    /**
     * @var string
     */
    public const CODE_APPLE_PAY = 'paylane_applepay';

    /**
     * @var string
     */
    private const XML_PATH_PAYMENT_PAYLANE_APPLEPAY_CERTIFICATE
        = 'payment/paylane_applepay/certificate';

    /**
     * @return string
     * @codeCoverageIgnore
     */
    protected function getMethodCode(): string
    {
        return self::CODE_APPLE_PAY;
    }

     /**
     * @param string $scopeType
     * @param null $scopeCode
     * @return string
     */
    public function getCertificate(
        string $scopeType = ScopeInterface::SCOPE_WEBSITE,
        $scopeCode = null
    ): string {
        return (string) $this->scopeConfig->getValue(
            self::XML_PATH_PAYMENT_PAYLANE_APPLEPAY_CERTIFICATE,
            $scopeType,
            $scopeCode
        );
    }
}
