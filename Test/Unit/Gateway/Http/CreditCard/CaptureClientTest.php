<?php

declare(strict_types=1);

/**
 * File: CaptureClientTest.php
 *
 
 
 */

namespace PeP\PaymentGateway\Test\Unit\Gateway\Http\CreditCard;

use Exception;
use PeP\PaymentGateway\Gateway\Http\Client\CreditCard\CaptureClient;
use PeP\PaymentGateway\Test\Unit\Gateway\Http\ClientTestCase;

/**
 * Class CaptureClientTest
 * @package PeP\PaymentGateway\Test\Unit\Gateway\Http\CreditCard
 */
class CaptureClientTest extends ClientTestCase
{
    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->client = new CaptureClient(
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
            ->method('captureAuthorization')
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
            ->method('captureAuthorization')
            ->with($this->examplePayload)
            ->willReturn($response);
    }
}
