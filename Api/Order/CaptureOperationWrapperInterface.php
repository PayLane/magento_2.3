<?php

declare(strict_types=1);

/**
 * File: CaptureOperationWrapperInterface.php
 *
 
 
 */

namespace PeP\PaymentGateway\Api\Order;

use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\Data\OrderInterface;

/**
 * Interface CaptureOperationWrapperInterface
 * @package PeP\PaymentGateway\Api\Order
 */
interface CaptureOperationWrapperInterface
{
    /**
     * @param OrderInterface $order
     * @return void
     * @throws LocalizedException
     */
    public function capture(OrderInterface $order): void;
}
