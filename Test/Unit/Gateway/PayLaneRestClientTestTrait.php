<?php

declare(strict_types=1);

/**
 * File: PayLaneRestClientTestTrait.php
 *
 
 
 */

namespace PeP\PaymentGateway\Test\Unit\Gateway;

use PeP\PaymentGateway\Model\Adapter\PayLaneRestClient;
use PeP\PaymentGateway\Model\Adapter\PayLaneRestClientFactory;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

/**
 * Class PayLaneRestClientTestTrait
 * @package PeP\PaymentGateway\Test\Unit\Gateway
 */
trait PayLaneRestClientTestTrait
{
    /**
     * @var PayLaneRestClient|MockObject
     */
    protected $payLaneRestClientMock;

    /**
     * @var PayLaneRestClientFactory|MockObject
     */
    protected $payLaneRestClientFactoryMock;

    /**
     * @return void
     */
    protected function setUpPayLaneRestClient(): void
    {
        //Internal mocks
        $this->payLaneRestClientMock = TestCase::getMockBuilder(PayLaneRestClient::class)
            ->disableOriginalConstructor()
            ->getMock();

        //Dependencies mocks
        $this->payLaneRestClientFactoryMock = TestCase::getMockBuilder(PayLaneRestClientFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @return void
     */
    protected function expectationsForCreatingPaylaneRestClient(): void
    {
        $this->payLaneRestClientFactoryMock->expects(TestCase::once())
            ->method('create')
            ->willReturn($this->payLaneRestClientMock);
    }
}
