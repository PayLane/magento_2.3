<?php

declare(strict_types=1);

/**
 * File: AuthorizeOperationWrapperTest.php
 *
 
 
 */

namespace PeP\PaymentGateway\Test\Unit\Model\Order;

use PeP\PaymentGateway\Model\Order\AuthorizeOperationWrapper;
use Magento\Framework\Exception\LocalizedException;
use Magento\Payment\Gateway\Command\CommandException;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order\Payment;
use Magento\Sales\Model\Order\Payment\Processor;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

/**
 * Class AuthorizeOperationWrapperTest
 * @package PeP\PaymentGateway\Test\Unit\Model\Order
 */
class AuthorizeOperationWrapperTest extends TestCase
{
    /**
     * @var AuthorizeOperationWrapper
     */
    private $authorizeOperationWrapper;

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

        $this->authorizeOperationWrapper = new AuthorizeOperationWrapper(
            $this->orderRepositoryMock,
            $this->orderPaymentProcessorMock
        );
    }

    /**
     * @test
     *
     * @dataProvider provideDifferentValuesForIsOnlineArgument
     *
     * @param bool $isOnline
     * @return void
     * @throws CommandException
     * @throws LocalizedException
     */
    public function testAuthorizeWhenProcessorThrowsCommandException(bool $isOnline): void
    {
        $baseTotalDue = 28.00;
        $message = 'specific message';

        $this->expectationsForGettingPayment();
        $this->expectationsForTotalsOperations($baseTotalDue);
        $this->orderPaymentProcessorMock->expects($this->once())
            ->method('authorize')
            ->with($this->paymentMock, $isOnline, $baseTotalDue)
            ->willThrowException(new CommandException(__($message)));

        $this->expectException(CommandException::class);
        $this->expectExceptionMessage($message);

        $this->authorizeOperationWrapper->authorize($this->orderMock, $isOnline);
    }

    /**
     * @test
     *
     * @dataProvider provideDifferentValuesForIsOnlineArgument
     *
     * @param bool $isOnline
     * @return void
     * @throws CommandException
     * @throws LocalizedException
     */
    public function testAuthorizeWhenProcessorThrowsAnyOtherException(bool $isOnline): void
    {
        $baseTotalDue = 28.00;
        $otherMessage = 'specific message for other exception';

        $this->expectationsForGettingPayment();
        $this->expectationsForTotalsOperations($baseTotalDue);
        $this->orderPaymentProcessorMock->expects($this->once())
            ->method('authorize')
            ->with($this->paymentMock, $isOnline, $baseTotalDue)
            ->willReturn($this->paymentMock);
        $this->orderRepositoryMock->expects($this->once())
            ->method('save')
            ->with($this->orderMock)
            ->willThrowException(new LocalizedException(__($otherMessage)));

        $this->expectException(LocalizedException::class);
        $this->expectExceptionMessage('Transaction has been declined. Please try again later.');

        $this->authorizeOperationWrapper->authorize($this->orderMock, $isOnline);
    }

    /**
     * @test
     *
     * @dataProvider provideDifferentValuesForIsOnlineArgument
     *
     * @param bool $isOnline
     * @return void
     * @throws CommandException
     * @throws LocalizedException
     */
    public function testAuthorizeWhenSucceed(bool $isOnline): void
    {
        $baseTotalDue = 28.00;
        $this->expectationsForGettingPayment();

        $this->expectationsForTotalsOperations($baseTotalDue);
        $this->orderPaymentProcessorMock->expects($this->once())
            ->method('authorize')
            ->with($this->paymentMock, $isOnline, $baseTotalDue)
            ->willReturn($this->paymentMock);
        $this->orderRepositoryMock->expects($this->once())
            ->method('save')
            ->with($this->orderMock)
            ->willReturn($this->orderMock);

        $this->authorizeOperationWrapper->authorize($this->orderMock, $isOnline);
    }

    /**
     * @return array
     */
    public function provideDifferentValuesForIsOnlineArgument(): array
    {
        return [
            [true],
            [false]
        ];
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
     * @param float $baseTotalDue
     * @return void
     */
    private function expectationsForTotalsOperations(float $baseTotalDue): void
    {
        $totalDue = 25.00;

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
    }
}
