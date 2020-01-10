<?php

declare(strict_types=1);

/**
 * File: BankCodeBuilder.php
 *
 
 */

/**
 * @see http://devzone.paylane.com/api-guide/cards/single-transaction/
 */
namespace PeP\PaymentGateway\Model\Payment\Request\Builder;

use Magento\Quote\Model\Quote;

/**
 * Class BankCodeBuilder
 * @package PeP\PaymentGateway\Model\Payment\Request\Builder
 */
class BankCodeBuilder implements BuilderInterface
{
    /**
     * @inheritdoc
     */
    public function build(Quote $quote): array
    {
        $payment = $quote->getPayment();
        $result = [
            'bank_code' => $payment->getAdditionalInformation('bank_code')
        ];

        return $result;
    }
}
