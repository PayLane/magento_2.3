<?php

declare(strict_types=1);

/**
 * File: TokenBuilder.php
 *
 
 */

/**
 * @see http://devzone.paylane.com/api-guide/cards/single-transaction/
 */
namespace PeP\PaymentGateway\Model\Payment\Request\Builder;

use Magento\Quote\Model\Quote;

/**
 * Class TokenBuilder
 * @package PeP\PaymentGateway\Model\Payment\Request\Builder
 */
class TokenBuilder implements BuilderInterface
{
    /**
     * @inheritdoc
     */
    public function build(Quote $quote): array
    {
        $payment = $quote->getPayment();
        $result = [
            'card' => [
                'token' => $payment->getAdditionalInformation('token')
            ]
        ];

        return $result;
    }
}
