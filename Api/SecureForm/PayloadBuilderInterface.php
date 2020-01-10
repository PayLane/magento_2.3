<?php

declare(strict_types=1);

/**
 * File: PayloadBuilderInterface.php
 *
 
 
 */

namespace PeP\PaymentGateway\Api\SecureForm;

/**
 * Interface PayloadBuilderInterface
 * @package PeP\PaymentGateway\Api\SecureForm
 */
interface PayloadBuilderInterface
{
    /**
     * @return array
     */
    public function build(): array;
}
