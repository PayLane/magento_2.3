<?php

declare(strict_types=1);

/**
 * File: PayLaneRestClientFactoryInterface.php
 *
 */

namespace PeP\PaymentGateway\Api\Adapter;

use PeP\PaymentGateway\Model\Adapter\PayLaneRestClient;

/**
 * Interface PayLaneRestClientFactoryInterface
 * @package PeP\PaymentGateway\Api
 */
interface PayLaneRestClientFactoryInterface
{
    /**
     * @return PayLaneRestClient
     */
    public function create(): PayLaneRestClient;
}
