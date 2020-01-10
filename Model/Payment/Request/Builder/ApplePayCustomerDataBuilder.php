<?php

declare(strict_types=1);

/**
 * File: ApplePayCustomerDataBuilder.php
 *
 
 */

/**
 * @see http://devzone.paylane.com/api-guide/cards/single-transaction/
 */

namespace PeP\PaymentGateway\Model\Payment\Request\Builder;

use Magento\Quote\Model\Quote;

/**
 * Class ApplePayCustomerDataBuilder
 * @package PeP\PaymentGateway\Model\Payment\Request\Builder
 */
class ApplePayCustomerDataBuilder implements BuilderInterface
{
    /**
     * @inheritdoc
     */
    public function build(Quote $quote): array
    {
        $billingAddress = $quote->getBillingAddress();

        $result['customer'] = [
            'name' => $quote->getCustomerFirstname() . ' ' . $quote->getCustomerLastname(),
            'email' => $billingAddress->getEmail(),
            'ip' => $quote->getRemoteIp(),
            'country_code' => $billingAddress->getCountryId()
        ];

        return $result;
    }
}
