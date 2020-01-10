<?php

declare(strict_types=1);

/**
 * File: Check3DSecureClientTest.php
 *
 
 
 */

namespace PeP\PaymentGateway\Test\Unit\Gateway\Http\CreditCard;

use Exception;
use PeP\PaymentGateway\Gateway\Http\Client\CreditCard\Check3DSecureClient;
use PeP\PaymentGateway\Test\Unit\Gateway\Http\ClientTestCase;

/**
 * Class Check3DSecureClientTest
 * @package PeP\PaymentGateway\Test\Unit\Gateway\Http\CreditCard
 */
class Check3DSecureClientTest extends ClientTestCase
{
    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->client = new Check3DSecureClient(
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
            ->method('checkCard3DSecureByToken')
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
            ->method('checkCard3DSecureByToken')
            ->with($this->examplePayload)
            ->willReturn($response);
    }
}
