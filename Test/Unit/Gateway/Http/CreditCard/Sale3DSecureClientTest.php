<?php

declare(strict_types=1);

/**
 * File: Sale3DSecureClientTest.php
 *
 
 
 */

namespace PeP\PaymentGateway\Test\Unit\Gateway\Http\CreditCard;

use Exception;
use PeP\PaymentGateway\Gateway\Http\Client\CreditCard\Sale3DSecureClient;
use PeP\PaymentGateway\Test\Unit\Gateway\Http\ClientTestCase;

/**
 * Class Sale3DSecureClientTest
 * @package PeP\PaymentGateway\Test\Unit\Gateway\Http\CreditCard
 */
class Sale3DSecureClientTest extends ClientTestCase
{
    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->client = new Sale3DSecureClient(
            $this->payLaneRestClientFactoryMock,
            $this->paymentLoggerMock,
            $this->loggerMock
        );
    }

    /**
     * @param Exception $exception
     * @return void
     */
    protected function expectationsForPlacingRequestWhenExceptionIsThrown(Exception $exception): void
    {
        $this->payLaneRestClientMock->expects($this->once())
            ->method('saleBy3DSecureAuthorization')
            ->with($this->examplePayload)
            ->willThrowException($exception);
    }

    /**
     * @param array $response
     * @return void
     */
    protected function expectationsForPlacingRequestWhenSucceed(array $response): void
    {
        $this->payLaneRestClientMock->expects($this->once())
            ->method('saleBy3DSecureAuthorization')
            ->with($this->examplePayload)
            ->willReturn($response);
    }
}
