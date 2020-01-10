<?php

declare (strict_types = 1);

/**
 * File: ClientLoggerInterface.php
 *
 */

namespace PeP\PaymentGateway\Api;

/**
 * Interface ClientLoggerInterface
 * @package PeP\PaymentGateway\Api
 */
interface ClientLoggerInterface
{
    /**
     * @param string $message
     * @param array $params
     * @param string $type
     * @return void
     */
    public function log(string $message, array $params = [], string $type = 'info');
}
