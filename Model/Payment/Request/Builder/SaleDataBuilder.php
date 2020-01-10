<?php

declare(strict_types=1);

/**
 * File: SaleDataBuilder.php
 *
 
 */

/**
 * @see http://devzone.paylane.com/api-guide/cards/single-transaction/
 */
namespace PeP\PaymentGateway\Model\Payment\Request\Builder;

use Magento\Quote\Model\Quote;

/**
 * Class SaleDataBuilder
 * @package PeP\PaymentGateway\Model\Payment\Request\Builder
 */
class SaleDataBuilder implements BuilderInterface
{
    /**
     * @inheritdoc
     */
    public function build(Quote $quote): array
    {
        $result = [
            'sale' => [
                'amount' => sprintf('%01.2f', $quote->getGrandTotal()),
                'currency' => $quote->getBaseCurrencyCode(),
                'description' => $quote->getReservedOrderId()
            ]
        ];

        return $result;
    }
}
