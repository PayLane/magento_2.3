<?php

declare(strict_types=1);

/**
 * File: HashComparerInterface.php
 *
 
 
 */

namespace PeP\PaymentGateway\Api;

/**
 * Interface HashComparerInterface
 * @package PeP\PaymentGateway\Api
 */
interface HashComparerInterface
{
    /**
     * @param string $hash
     * @param string $status
     * @param string $amount
     * @param string $incrementId
     * @param string $currency
     * @param string $idSale
     * @return bool
     */
    public function compareHashes(
        string $hash,
        string $status,
        string $amount,
        string $incrementId,
        string $currency,
        string $idSale
    ): bool;
}
