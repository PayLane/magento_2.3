<?php

declare(strict_types=1);

/**
 * File: GooglePayConfigProvider.php
 *
 
 */

namespace PeP\PaymentGateway\Model\Config\Methods;

use PeP\PaymentGateway\Api\Config\Methods\GooglePayConfigProviderInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Class GooglePayConfigProvider
 * @package PeP\PaymentGateway\Model\Config\Methods
 */
class GooglePayConfigProvider extends AbstractToggleableMethodConfigProvider implements GooglePayConfigProviderInterface
{
    /**
     * @var string
     */
    public const CODE_GOOGLE_PAY = 'paylane_googlepay';

    /**
     * @var string
     */
    public const GOOGLE_MERCHANT_ID = 'google_merchant_id';

    /**
     * @return string
     * @codeCoverageIgnore
     */
    protected function getMethodCode(): string
    {
        return self::CODE_GOOGLE_PAY;
    }

     /**
     * @param string $scopeType
     * @param null $scopeCode
     * @return string
     */
    public function getGoogleMerchantId(
        string $scopeType = ScopeInterface::SCOPE_STORE,
        $scopeCode = null
    ): string {
        return (string) $this->scopeConfig->getValue(
            $this->buildPathForConfiguration(self::GOOGLE_MERCHANT_ID),
            $scopeType,
            $scopeCode
        );
    }

   

}
