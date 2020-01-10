<?php

declare(strict_types=1);

/**
 * File: SaleClient.php
 *
 
 
 */

namespace PeP\PaymentGateway\Gateway\Http\Client\CreditCard;

use Exception;
use PeP\PaymentGateway\Gateway\Http\Client\AbstractClient;

/**
 * Class SaleClient
 * @package PeP\PaymentGateway\Gateway\Http\Client\CreditCard
 */
class AuthorizeClient extends AbstractClient
{
    /**
     * @param array $data
     * @return array
     * @throws Exception
     */
    protected function process(array $data): array
    {
        return $this->payLaneRestClientFactory->create()->cardAuthorizationByToken($data);
    }
}
