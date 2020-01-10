<?php

declare(strict_types=1);

/**
 * File: BankTransferConfigProvider.php
 *
 
 */

namespace PeP\PaymentGateway\Model\Config\Methods;

use PeP\PaymentGateway\Api\Config\Methods\BankTransferConfigProviderInterface;

/**
 * TODO: Unit test
 * Class BankTransferConfigProvider
 * @package PeP\PaymentGateway\Model\Config\Methods
 */
class BankTransferConfigProvider extends AbstractToggleableMethodConfigProvider implements
    BankTransferConfigProviderInterface
{
    /**
     * @var string
     */
    public const CODE_BANK_TRANSFER = 'paylane_banktransfer';

    /**
     * @return string
     */
    protected function getMethodCode(): string
    {
        return self::CODE_BANK_TRANSFER;
    }
}
