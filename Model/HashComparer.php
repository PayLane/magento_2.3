<?php

declare(strict_types=1);

/**
 * File: HashComparer.php
 *
 
 
 */

namespace PeP\PaymentGateway\Model;

use PeP\PaymentGateway\Api\Config\GeneralConfigProviderInterface;
use PeP\PaymentGateway\Api\HashComparerInterface;

/**
 * Class HashComparer
 * @package PeP\PaymentGateway\Model\HashComparer
 */
class HashComparer implements HashComparerInterface
{
    /**
     * @var GeneralConfigProviderInterface
     */
    private $generalConfigProvider;

    /**
     * HashValidator constructor.
     * @param GeneralConfigProviderInterface $generalConfigProvider
     * @SuppressWarnings(PHPMD.LongVariable)
     */
    public function __construct(GeneralConfigProviderInterface $generalConfigProvider)
    {
        $this->generalConfigProvider = $generalConfigProvider;
    }

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
    ): bool {
        $hashSalt = $this->generalConfigProvider->getHashSalt();

        $hashComputed = sha1(
            join(
                '|',
                [
                    $hashSalt,
                    $status,
                    $incrementId,
                    $amount,
                    $currency,
                    $idSale
                ]
            )
        );

        return $hash === $hashComputed;
    }
}
