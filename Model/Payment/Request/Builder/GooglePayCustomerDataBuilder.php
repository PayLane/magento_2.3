<?php

declare(strict_types=1);

/**
 * File: GooglePayCustomerDataBuilder.php
 *
 
 */

/**
 * @see http://devzone.paylane.com/api-guide/cards/single-transaction/
 */

namespace PeP\PaymentGateway\Model\Payment\Request\Builder;

use Magento\Quote\Model\Quote;

/**
 * Class GooglePayCustomerDataBuilder
 * @package PeP\PaymentGateway\Model\Payment\Request\Builder
 */
class GooglePayCustomerDataBuilder implements BuilderInterface
{
    /**
     * @inheritdoc
     */
    public function build(Quote $quote): array
    {
        $billingAddress = $quote->getBillingAddress();
        $payment = $quote->getPayment();

        $result['customer'] = [
            'name' => $quote->getCustomerFirstname() . ' ' . $quote->getCustomerLastname(),
            'email' => $billingAddress->getEmail(),
            'ip' => $quote->getRemoteIp(),
            'country_code' => $billingAddress->getCountryId()
        ];
        $result['card']['token'] = $payment->getAdditionalInformation('token');


        return $result;
    }
}
