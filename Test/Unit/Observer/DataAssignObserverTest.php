<?php

declare(strict_types=1);

/**
 * File: DataAssignObserverTest.php
 *
 
 
 */

namespace PeP\PaymentGateway\Test\Unit\Observer;

use PeP\PaymentGateway\Observer\DataAssignObserver;
use Magento\Framework\DataObject;
use Magento\Framework\Event\Observer;
use Magento\Payment\Model\InfoInterface;
use Magento\Quote\Api\Data\PaymentInterface;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

/**
 * Class DataAssignObserverTest
 * @package PeP\PaymentGateway\Test\Unit\Observer
 */
class DataAssignObserverTest extends TestCase
{
    /**
     * @var DataAssignObserver|MockObject
     */
    private $dataAssignObserver;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->dataAssignObserver = $this->getMockBuilder(DataAssignObserver::class)
            ->setMethods(
                [
                    'readDataArgument',
                    'readPaymentModelArgument'
                ]
            )->getMock();
    }

    /**
     * @test
     * @return void
     */
    public function testExecuteWhenAdditionalDataIsNotAnArray(): void
    {
        $data = new DataObject();

        $observerMock = $this->getMockBuilder(Observer::class)->getMock();

        $this->dataAssignObserver->expects($this->once())
            ->method('readDataArgument')
            ->with($observerMock)
            ->willReturn($data);

        $this->dataAssignObserver->execute($observerMock);
    }

    /**
     * @test
     * @return void
     */
    public function testExecuteCorrectlySetsDataonPaymentObject(): void
    {
        $data = new DataObject([PaymentInterface::KEY_ADDITIONAL_DATA => ['token' => 'test', 'not_existing' => 'sth']]);

        $observerMock = $this->getMockBuilder(Observer::class)->getMock();

        $this->dataAssignObserver->expects($this->once())
            ->method('readDataArgument')
            ->with($observerMock)
            ->willReturn($data);

        $paymentInfo = $this->getMockBuilder(InfoInterface::class)->getMock();

        $this->dataAssignObserver->expects($this->once())
            ->method('readPaymentModelArgument')
            ->willReturn($paymentInfo);

        $paymentInfo->expects($this->once())
            ->method('setAdditionalInformation')
            ->with('token', 'test')
            ->willReturnSelf();

        $this->dataAssignObserver->execute($observerMock);
    }
}
