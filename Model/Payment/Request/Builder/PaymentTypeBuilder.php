<?php

declare(strict_types=1);

/**
 * File: PaymentTypeBuilder.php
 *
 
 */

/**
 * @see http://devzone.paylane.com/api-guide/cards/single-transaction/
 */
namespace PeP\PaymentGateway\Model\Payment\Request\Builder;

use Magento\Quote\Model\Quote;

/**
 * Class PaymentTypeBuilder
 * @package PeP\PaymentGateway\Model\Payment\Request\Builder
 */
class PaymentTypeBuilder implements BuilderInterface
{
    /**
     * @inheritdoc
     */
    public function build(Quote $quote): array
    {
        $payment = $quote->getPayment();
        $result = [
            'payment_type' => $payment->getAdditionalInformation('payment_type')
        ];

        return $result;
    }
}
