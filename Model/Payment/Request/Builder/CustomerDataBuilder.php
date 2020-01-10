<?php

declare(strict_types=1);

/**
 * File: CustomerDataBuilder.php
 *
 
 */

/**
 * @see http://devzone.paylane.com/api-guide/cards/single-transaction/
 */

namespace PeP\PaymentGateway\Model\Payment\Request\Builder;

use Magento\Quote\Model\Quote;

/**
 * Class CustomerDataBuilder
 * @package PeP\PaymentGateway\Model\Payment\Request\Builder
 */
class CustomerDataBuilder implements BuilderInterface
{
    /**
     * @inheritdoc
     */
    public function build(Quote $quote): array
    {
        $billingAddress = $quote->getBillingAddress();

        $result['customer'] = [
            'name' => $billingAddress->getFirstname() . ' ' . $billingAddress->getLastname(),
            'email' => $billingAddress->getEmail(),
            'ip' => $quote->getRemoteIp(),
            'address' => [
                'street_house' => implode(',', $billingAddress->getStreet(true)),
                'city' => $billingAddress->getCity(),
                'state' => $billingAddress->getRegion(),
                'zip' => $billingAddress->getPostcode(),
                'country_code' => $billingAddress->getCountryId(),
            ],
        ];

        return $result;
    }
}
