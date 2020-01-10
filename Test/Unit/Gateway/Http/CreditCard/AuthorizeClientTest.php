<?php

declare(strict_types=1);

/**
 * File: AuthorizeClientTest.php
 *
 
 
 */

namespace PeP\PaymentGateway\Test\Unit\Gateway\Http\CreditCard;

use Exception;
use PeP\PaymentGateway\Gateway\Http\Client\CreditCard\AuthorizeClient;
use PeP\PaymentGateway\Test\Unit\Gateway\Http\ClientTestCase;
use Magento\Payment\Gateway\Http\ClientException;

/**
 * Class AuthorizeClientTest
 * @package PeP\PaymentGateway\Test\Unit\Gateway\Http\CreditCard
 */
class AuthorizeClientTest extends ClientTestCase
{
    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->client = new AuthorizeClient(
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
            ->method('cardAuthorizationByToken')
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
            ->method('cardAuthorizationByToken')
            ->with($this->examplePayload)
            ->willReturn($response);
    }
}
