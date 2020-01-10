<?php

declare(strict_types=1);

/**
 * File: Sale3DSecureClient.php
 *
 
 
 */

namespace PeP\PaymentGateway\Gateway\Http\Client\CreditCard;

use Exception;
use PeP\PaymentGateway\Gateway\Http\Client\AbstractClient;

/**
 * Class Sale3DSecureClient
 * @package PeP\PaymentGateway\Gateway\Http\Client\CreditCard
 */
class Sale3DSecureClient extends AbstractClient
{
    /**
     * @param array $data
     * @return array
     * @throws Exception
     */
    protected function process(array $data): array
    {
        return $this->payLaneRestClientFactory->create()->saleBy3DSecureAuthorization($data);
    }
}
