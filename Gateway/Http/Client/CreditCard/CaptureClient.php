<?php

declare(strict_types=1);

/**
 * File: CaptureClient.php
 *
 
 
 */

namespace PeP\PaymentGateway\Gateway\Http\Client\CreditCard;

use Exception;
use PeP\PaymentGateway\Gateway\Http\Client\AbstractClient;

/**
 * Class CaptureClient
 * @package PeP\PaymentGateway\Gateway\Http\Client\CreditCard
 */
class CaptureClient extends AbstractClient
{
    /**
     * @param array $data
     * @return array
     * @throws Exception
     */
    protected function process(array $data): array
    {
        return $this->payLaneRestClientFactory->create()->captureAuthorization($data);
    }
}
