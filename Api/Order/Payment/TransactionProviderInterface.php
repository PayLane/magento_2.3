<?php

declare(strict_types=1);

/**
 * File: TransactionProviderInterface.php
 *
 
 
 */

namespace PeP\PaymentGateway\Api\Order\Payment;

use Magento\Sales\Api\Data\OrderPaymentInterface;
use Magento\Sales\Api\Data\TransactionInterface;
use MSlwk\TypeSafeArray\ObjectArray;

/**
 * Interface TransactionProviderInterface
 * @package PeP\PaymentGateway\Api\Order\Payment
 */
interface TransactionProviderInterface
{
    /**
     * @param OrderPaymentInterface $payment
     * @param string $type
     * @return ObjectArray
     */
    public function getByTxnType(OrderPaymentInterface $payment, string $type): ObjectArray;

    /**
     * @param OrderPaymentInterface $payment
     * @param string $txnId
     * @return TransactionInterface|null
     */
    public function getByTxnId(OrderPaymentInterface $payment, string $txnId): ?TransactionInterface;
}
