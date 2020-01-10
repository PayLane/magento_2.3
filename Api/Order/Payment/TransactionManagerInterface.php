<?php

declare(strict_types=1);

/**
 * File: TransactionManagerInterface.php
 *
 
 
 */

namespace PeP\PaymentGateway\Api\Order\Payment;

use Magento\Sales\Api\Data\OrderPaymentInterface;

/**
 * Interface TransactionManagerInterface
 * @package PeP\PaymentGateway\Api\Order\Payment
 */
interface TransactionManagerInterface
{
    /**
     * @param OrderPaymentInterface $payment
     * @return void
     */
    public function closeLastTxn(OrderPaymentInterface $payment): void;
}
