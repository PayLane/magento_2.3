<?php

declare(strict_types=1);

/**
 * File: Check3DSecureClient.php
 *
 
 
 */

namespace PeP\PaymentGateway\Gateway\Http\Client\CreditCard;

use Exception;
use PeP\PaymentGateway\Gateway\Http\Client\AbstractClient;

/**
 * Class Check3DSecureClient
 * @package PeP\PaymentGateway\Gateway\Http\Client\CreditCard
 */
class Check3DSecureClient extends AbstractClient
{
    /**
     * @param array $data
     * @return array
     * @throws Exception
     */
    public function process(array $data): array
    {
        return $this->payLaneRestClientFactory->create()->checkCard3DSecureByToken($data);
    }
}
