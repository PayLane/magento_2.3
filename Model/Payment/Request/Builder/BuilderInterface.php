<?php

declare(strict_types=1);

/**
 * File: BuilderInterface.php
 *
 
 */

namespace PeP\PaymentGateway\Model\Payment\Request\Builder;

use Magento\Quote\Model\Quote;

/**
 * Interface BuilderInterface
 * @package PeP\PaymentGateway\Model\Payment\Request\Builder
 */
interface BuilderInterface
{
    /**
     * @param Quote $quote
     * @return array
     */
    public function build(Quote $quote): array;
}
