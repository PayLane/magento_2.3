<?php

declare(strict_types=1);

/**
 * File: BackRequestValidatorInterface.php
 *
 
 
 */

namespace PeP\PaymentGateway\Model\Request;

use Magento\Framework\App\RequestInterface;

/**
 * Interface BackRequestValidatorInterface
 * @package PeP\PaymentGateway\Model\Request
 */
interface BackRequestValidatorInterface
{
    /**
     * @param RequestInterface $request
     * @return bool
     */
    public function validate(RequestInterface $request): bool;
}
