<?php

declare(strict_types=1);

/**
 * File: TransactionManagerTest.php
 *
 
 
 */

namespace PeP\PaymentGateway\Test\Unit\Model\Order\Payment;

use PeP\PaymentGateway\Api\Order\Payment\TransactionProviderInterface;
use PeP\PaymentGateway\Model\Order\Payment\TransactionManager;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\Data\OrderPaymentInterface;
use Magento\Sales\Api\Data\TransactionInterface;
use Magento\Sales\Api\TransactionRepositoryInterface;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

/**
 * Class TransactionManagerTest
 * @package PeP\PaymentGateway\Test\Unit\Model\Order\Payment
 */
class TransactionManagerTest extends TestCase
{
    /**
     * @var TransactionProviderInterface|MockObject
     */
    private $transactionProviderMock;

    /**
     * @var TransactionManager
     */
    private $transactionManager;

    /**
     * @var OrderPaymentInterface|MockObject
     */
    private $orderPaymentMock;

    /**
     * @var TransactionInterface|MockObject
     */
    private $transactionMock;

    /**
     * @var TransactionRepositoryInterface|MockObject
     */
    private $transactionRepositoryMock;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        //Internal mocks
        $this->orderPaymentMock = $this->getMockBuilder(OrderPaymentInterface::class)->getMock();
        $this->transactionMock = $this->getMockBuilder(TransactionInterface::class)->getMock();

        //Dependencies mocks
        $this->transactionProviderMock = $this->getMockBuilder(TransactionProviderInterface::class)
            ->getMock();
        $this->transactionRepositoryMock = $this->getMockBuilder(TransactionRepositoryInterface::class)
            ->getMock();

        $this->transactionManager = new TransactionManager(
            $this->transactionProviderMock,
            $this->transactionRepositoryMock
        );
    }

    /**
     * @test
     *
     * @return void
     */
    public function testCloseLastTxnWhenNoTransactionFound(): void
    {
        $lastTxnId = 'FSA$@';

        $this->orderPaymentMock->expects($this->once())
            ->method('getLastTransId')
            ->willReturn($lastTxnId);
        $this->transactionProviderMock->expects($this->once())
            ->method('getByTxnId')
            ->with($this->orderPaymentMock, $lastTxnId)
            ->willReturn(null);

        $this->transactionManager->closeLastTxn($this->orderPaymentMock);
    }

    /**
     * @return void
     */
    public function testCloseLastTxnWhenTransactionRepositoryThrowsException(): void
    {
        $this->expectationsForGettingTransaction();
        $this->expectationsForClosingTransaction();
        $this->transactionRepositoryMock->expects($this->once())
            ->method('save')
            ->with($this->transactionMock)
            ->willThrowException(new LocalizedException(__()));

        $this->transactionManager->closeLastTxn($this->orderPaymentMock);
    }

    /**
     * @return void
     */
    public function testCloseLastTxnWhenSucceeded(): void
    {
        $this->expectationsForGettingTransaction();
        $this->expectationsForClosingTransaction();
        $this->transactionRepositoryMock->expects($this->once())
            ->method('save')
            ->with($this->transactionMock)
            ->willReturn($this->transactionMock);

        $this->transactionManager->closeLastTxn($this->orderPaymentMock);
    }

    /**
     * @return void
     */
    private function expectationsForGettingTransaction(): void
    {
        $lastTxnId = 'FSA$@';

        $this->orderPaymentMock->expects($this->once())
            ->method('getLastTransId')
            ->willReturn($lastTxnId);
        $this->transactionProviderMock->expects($this->once())
            ->method('getByTxnId')
            ->with($this->orderPaymentMock, $lastTxnId)
            ->willReturn($this->transactionMock);
    }

    /**
     * @return void
     */
    private function expectationsForClosingTransaction(): void
    {
        $this->transactionMock->expects($this->once())
            ->method('setIsClosed')
            ->with(1)
            ->willReturnSelf();
    }
}
