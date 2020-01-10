<?php

declare(strict_types=1);

/**
 * File: ClientTestCase.php
 *
 
 
 */

namespace PeP\PaymentGateway\Test\Unit\Gateway\Http;

use Exception;
use PeP\PaymentGateway\Gateway\Http\Client\AbstractClient;
use PeP\PaymentGateway\Test\Unit\Gateway\PayLaneRestClientTestTrait;
use Magento\Payment\Gateway\Http\ClientException;
use Magento\Payment\Gateway\Http\TransferInterface;
use Magento\Payment\Model\Method\Logger;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Psr\Log\LoggerInterface;

/**
 * Class ClientTestCase
 * @package PeP\PaymentGateway\Test\Unit\Gateway\Http
 */
abstract class ClientTestCase extends TestCase
{
    use PayLaneRestClientTestTrait;

    /**
     * @var array
     */
    protected $examplePayload = ['some_request_data' => 34.00];

    /**
     * @var array
     */
    protected $exampleResponse = ['some_response_data' => true];

    /**
     * @var AbstractClient
     */
    protected $client;

    /**
     * @var TransferInterface|MockObject
     */
    protected $transferMock;

    /**
     * @var Logger|MockObject
     */
    protected $paymentLoggerMock;

    /**
     * @var LoggerInterface|MockObject
     */
    protected $loggerMock;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpPayLaneRestClient();

        //Internal mocks
        $this->transferMock = $this->getMockBuilder(TransferInterface::class)->getMock();

        //Dependencies mocks
        $this->paymentLoggerMock = $this->getMockBuilder(Logger::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->loggerMock = $this->getMockBuilder(LoggerInterface::class)->getMock();
    }

    /**
     * @test
     *
     * @return void
     * @throws ClientException
     */
    public function testPlaceRequestWhenClientThrowsException(): void
    {
        $this->expectationsForGettingRequestBody($this->examplePayload);
        $this->expectationsForCreatingPaylaneRestClient();

        $exception = new Exception('Test message');

        $this->expectationsForPlacingRequestWhenExceptionIsThrown($exception);
        $this->expectationsForExceptionHandling($exception);
        $this->expectationsForPaymentLogging($this->examplePayload, []);

        $this->client->placeRequest($this->transferMock);
    }

    /**
     * @test
     *
     * @return void
     * @throws ClientException
     */
    public function testPlaceRequestSuccessfullyPlaced(): void
    {
        $this->expectationsForGettingRequestBody($this->examplePayload);
        $this->expectationsForCreatingPaylaneRestClient();
        $this->expectationsForPlacingRequestWhenSucceed($this->exampleResponse);
        $this->expectationsForPaymentLogging($this->examplePayload, $this->exampleResponse);

        $this->assertSame($this->exampleResponse, $this->client->placeRequest($this->transferMock));
    }

    /**
     * @param Exception $exception
     * @return void
     */
    abstract protected function expectationsForPlacingRequestWhenExceptionIsThrown(Exception $exception): void;

    /**
     * @param array $response
     * @return void
     */
    abstract protected function expectationsForPlacingRequestWhenSucceed(array $response): void;

    /**
     * @param array $body
     * @return void
     */
    protected function expectationsForGettingRequestBody(array $body = []): void
    {
        $this->transferMock->expects($this->once())
            ->method('getBody')
            ->willReturn($body);
    }

    /**
     * @param Exception $exception
     * @return void
     */
    protected function expectationsForExceptionHandling(Exception $exception): void
    {
        $message = __($exception->getMessage() ?: 'Sorry, but something went wrong');
        $this->loggerMock->expects($this->once())
            ->method('critical')
            ->with($message);
        $this->expectException(ClientException::class);
        $this->expectExceptionMessage($message->render());
    }

    /**
     * @param array $requestData
     * @param array $response
     * @return void
     */
    protected function expectationsForPaymentLogging(array $requestData, array $response): void
    {
        $this->paymentLoggerMock->expects($this->once())
            ->method('debug')
            ->with([
                'request' => $requestData,
                'client' => get_class($this->client),
                'response' => $response
            ]);
    }
}
