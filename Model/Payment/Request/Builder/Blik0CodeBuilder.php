<?php

declare(strict_types=1);

/**
 * File: Blik0CodeBuilder.php
 *
 
 */

/**
 * @see http://devzone.paylane.com/api-guide/cards/single-transaction/
 */
namespace PeP\PaymentGateway\Model\Payment\Request\Builder;

use Magento\Quote\Model\Quote;

/**
 * Class Blik0CodeBuilder
 * @package PeP\PaymentGateway\Model\Payment\Request\Builder
 */
class Blik0CodeBuilder implements BuilderInterface
{
    /**
     * @inheritdoc
     */
    public function build(Quote $quote): array
    {
        $payment = $quote->getPayment();
        $result = [
            'code' => $payment->getAdditionalInformation('blik0_code')
        ];

        return $result;
    }
}
