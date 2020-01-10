<?php

declare(strict_types=1);

/**
 * File: CaptureOperationWrapperTest.php
 *
 
 
 */

namespace PeP\PaymentGateway\Test\Unit\Model\Order;

use PeP\PaymentGateway\Model\Order\CaptureOperationWrapper;
use Magento\Framework\Exception\LocalizedException;
use Magento\Payment\Gateway\Command\CommandException;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order\Payment;
use Magento\Sales\Model\Order\Payment\Processor;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

/**
 * Class CaptureOperationWrapperTest
 * @package PeP\PaymentGateway\Test\Unit\Model\Order
 */
class CaptureOperationWrapperTest extends TestCase
{
    /**
     * @var CaptureOperationWrapper
     */
    private $captureOperationWrapper;

    /**
     * @var OrderInterface|MockObject
     */
    private $orderMock;

    /**
     * @var Payment|MockObject
     */
    private $paymentMock;

    /**
     * @var OrderRepositoryInterface|MockObject
     */
    private $orderRepositoryMock;

    /**
     * @var Processor|MockObject
     */
    private $orderPaymentProcessorMock;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        //Internal mocks
        $this->orderMock = $this->getMockBuilder(OrderInterface::class)->getMock();
        $this->paymentMock = $this->getMockBuilder(Payment::class)
            ->disableOriginalConstructor()
            ->getMock();

        //Dependencies mocks
        $this->orderRepositoryMock = $this->getMockBuilder(OrderRepositoryInterface::class)->getMock();
        $this->orderPaymentProcessorMock = $this->getMockBuilder(Processor::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->captureOperationWrapper = new CaptureOperationWrapper(
            $this->orderRepositoryMock,
            $this->orderPaymentProcessorMock
        );
    }

    /**
     * @test
     *
     * @return void
     * @throws LocalizedException
     */
    public function testCaptureWhenProcessorThrowsCommandException(): void
    {
        $message = 'specific message';
        $this->expectationsForGettingPayment();
        $this->expectationsForTotalsOperations();
        $this->orderPaymentProcessorMock->expects($this->once())
            ->method('capture')
            ->with($this->paymentMock, null)
            ->willThrowException(new CommandException(__($message)));

        $this->expectException(CommandException::class);
        $this->expectExceptionMessage($message);

        $this->captureOperationWrapper->capture($this->orderMock);
    }

    /**
     * @test
     *
     * @return void
     * @throws LocalizedException
     */
    public function testCaptureWhenProcessorThrowsAnyOtherException(): void
    {
        $otherMessage = 'specific message for other exception';
        $this->expectationsForGettingPayment();
        $this->expectationsForTotalsOperations();
        $this->orderPaymentProcessorMock->expects($this->once())
            ->method('capture')
            ->with($this->paymentMock, null)
            ->willReturn($this->paymentMock);
        $this->orderRepositoryMock->expects($this->once())
            ->method('save')
            ->with($this->orderMock)
            ->willThrowException(new LocalizedException(__($otherMessage)));

        $this->expectException(LocalizedException::class);
        $this->expectExceptionMessage('Transaction has been declined. Please try again later.');

        $this->captureOperationWrapper->capture($this->orderMock);
    }

    /**
     * @test
     *
     * @return void
     * @throws LocalizedException
     */
    public function testCaptureWhenSucceed(): void
    {
        $this->expectationsForGettingPayment();
        $this->expectationsForTotalsOperations();
        $this->orderPaymentProcessorMock->expects($this->once())
            ->method('capture')
            ->with($this->paymentMock, null)
            ->willReturn($this->paymentMock);
        $this->orderRepositoryMock->expects($this->once())
            ->method('save')
            ->with($this->orderMock)
            ->willReturn($this->orderMock);

        $this->captureOperationWrapper->capture($this->orderMock);
    }

    /**
     * @return void
     */
    private function expectationsForGettingPayment(): void
    {
        $this->orderMock->expects($this->once())
            ->method('getPayment')
            ->willReturn($this->paymentMock);
    }

    /**
     * @return void
     */
    private function expectationsForTotalsOperations(): void
    {
        $totalDue = 25.00;
        $baseTotalDue = 28.00;

        $this->orderMock->expects($this->once())
            ->method('getTotalDue')
            ->willReturn($totalDue);
        $this->orderMock->expects($this->once())
            ->method('getBaseTotalDue')
            ->willReturn($baseTotalDue);

        $this->paymentMock->expects($this->once())
            ->method('setAmountAuthorized')
            ->with($totalDue)
            ->willReturnSelf();
        $this->paymentMock->expects($this->once())
            ->method('setBaseAmountAuthorized')
            ->with($baseTotalDue)
            ->willReturnSelf();
    }
}
