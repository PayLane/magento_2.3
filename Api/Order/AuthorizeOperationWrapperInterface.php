<?php

declare(strict_types=1);

/**
 * File: AuthorizeOperationWrapperInterface.php
 *
 
 
 */

namespace PeP\PaymentGateway\Api\Order;

use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\Data\OrderInterface;

/**
 * Interface AuthorizeOperationWrapperInterface
 * @package PeP\PaymentGateway\Api\Order
 */
interface AuthorizeOperationWrapperInterface
{
    /**
     * @param OrderInterface $order
     * @param bool $isOnline
     * @return void
     * @throws LocalizedException
     */
    public function authorize(OrderInterface $order, bool $isOnline = true): void;
}
