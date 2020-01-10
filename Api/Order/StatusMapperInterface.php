<?php

declare(strict_types=1);

/**
 * File: StatusMapperInterface.php
 *
 
 
 */

namespace PeP\PaymentGateway\Api\Order;

/**
 * Interface StatusMapperInterface
 * @package PeP\PaymentGateway\Api\Order
 */
interface StatusMapperInterface
{
    /**
     * @param string $notificationStatus
     * @return string
     */
    public function map(string $notificationStatus): string;
}
