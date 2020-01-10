<?php

declare(strict_types=1);

/**
 * File: DirectDebitConfigProvider.php
 *
 
 */

namespace PeP\PaymentGateway\Model\Config\Methods;

use PeP\PaymentGateway\Api\Config\Methods\DirectDebitConfigProviderInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * TODO: Unit test
 * Class DirectDebitConfigProvider
 * @package PeP\PaymentGateway\Model\Config\Methods
 */
class DirectDebitConfigProvider extends AbstractToggleableMethodConfigProvider implements
    DirectDebitConfigProviderInterface
{
    /**
     * @var string
     */
    public const CODE_DIRECT_DEBIT = 'paylane_directdebit';

    /**
     * @var string
     */
    private const XML_PATH_PAYMENT_DIRECTDEBIT_MANDATE_ID = 'payment/paylane_directdebit/mandate_id';

    /**
     * @return string
     * @codeCoverageIgnore
     */
    protected function getMethodCode(): string
    {
        return self::CODE_DIRECT_DEBIT;
    }

    /**
     * @param string $scopeType
     * @param null $scopeCode
     * @return string
     */
    public function getMandateId(
        string $scopeType = ScopeInterface::SCOPE_WEBSITE,
        $scopeCode = null
    ): string {
        return (string) $this->scopeConfig->getValue(
            self::XML_PATH_PAYMENT_DIRECTDEBIT_MANDATE_ID,
            $scopeType,
            $scopeCode
        );
    }
}
