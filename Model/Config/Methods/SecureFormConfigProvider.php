<?php

declare(strict_types=1);

/**
 * File: SecureFormConfigProvider.php
 *
 
 */

namespace PeP\PaymentGateway\Model\Config\Methods;

use PeP\PaymentGateway\Api\Config\Methods\SecureFormConfigProviderInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Class SecureFormConfigProvider
 * @package PeP\PaymentGateway\Model\Config\Methods
 */
class SecureFormConfigProvider extends AbstractMethodConfigProvider implements SecureFormConfigProviderInterface
{
    /**
     * @var string
     */
    public const CODE_SECURE_FORM = 'paylane_secureform';

    /**
     * @var string
     */
    private const XML_PATH_PAYMENT_PAYLANE_SECUREFORM_SEND_CUSTOMER_DATA
        = 'payment/paylane_secureform/send_customer_data';

    /**
     * @return string
     * @codeCoverageIgnore
     */
    protected function getMethodCode(): string
    {
        return self::CODE_SECURE_FORM;
    }

    /**
     * @param string $scopeType
     * @param null $scopeCode
     * @return bool
     */
    public function isSendCustomerData(
        string $scopeType = ScopeInterface::SCOPE_WEBSITE,
        $scopeCode = null
    ): bool {
        return (bool) $this->scopeConfig->isSetFlag(
            self::XML_PATH_PAYMENT_PAYLANE_SECUREFORM_SEND_CUSTOMER_DATA,
            $scopeType,
            $scopeCode
        );
    }
}
