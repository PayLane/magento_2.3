<?php

declare(strict_types=1);

/**
 * File: TransactionProviderTest.php
 *
 
 
 */

namespace PeP\PaymentGateway\Test\Unit\Model\Order\Payment;

use PeP\PaymentGateway\Model\Order\Payment\TransactionProvider;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Framework\Data\Collection;
use Magento\Sales\Api\Data\OrderPaymentInterface;
use Magento\Sales\Api\Data\TransactionInterface;
use Magento\Sales\Api\Data\TransactionSearchResultInterface;
use Magento\Sales\Api\TransactionRepositoryInterface;
use MSlwk\TypeSafeArray\ObjectArray;
use MSlwk\TypeSafeArray\ObjectArrayFactory;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

/**
 * Class TransactionProviderTest
 * @package PeP\PaymentGateway\Test\Unit\Model\Order\Payment
 */
class TransactionProviderTest extends TestCase
{
    /**
     * @var TransactionProvider
     */
    private $transactionProvider;

    /**
     * @var SearchCriteriaInterface|MockObject
     */
    private $searchCriteriaMock;

    /**
     * @var SortOrderBuilder|MockObject
     */
    private $sortOrderBuilderMock;

    /**
     * @var SortOrder|MockObject
     */
    private $sortOrderMock;

    /**
     * @var SearchCriteriaBuilder|MockObject
     */
    private $searchCriteriaBuilderMock;

    /**
     * @var OrderPaymentInterface|MockObject
     */
    private $orderPaymentMock;

    /**
     * @var TransactionInterface|MockObject
     */
    private $transactionMock;

    /**
     * @var TransactionSearchResultInterface|MockObject
     */
    private $searchResultMock;

    /**
     * @var TransactionRepositoryInterface|MockObject
     */
    private $transactionRepositoryMock;

    /**
     * @var ObjectArrayFactory|MockObject
     */
    private $objectArrayFactoryMock;

    /**
     * @var ObjectArray|MockObject
     */
    private $objectArrayMock;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        //Internal mocks
        $this->searchCriteriaMock = $this->getMockBuilder(SearchCriteriaInterface::class)->getMock();
        $this->sortOrderMock = $this->getMockBuilder(SortOrder::class)->getMock();
        $this->orderPaymentMock = $this->getMockBuilder(OrderPaymentInterface::class)
            ->getMock();
        $this->transactionMock = $this->getMockBuilder(TransactionInterface::class)->getMock();
        $this->searchResultMock = $this->getMockBuilder(TransactionSearchResultInterface::class)->getMock();
        $this->objectArrayMock = $this->getMockBuilder(ObjectArray::class)
            ->disableOriginalConstructor()
            ->getMock();

        //Dependencies mocks
        $this->searchCriteriaBuilderMock = $this->getMockBuilder(SearchCriteriaBuilder::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->sortOrderBuilderMock = $this->getMockBuilder(SortOrderBuilder::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->transactionRepositoryMock = $this->getMockBuilder(TransactionRepositoryInterface::class)
            ->getMock();
        $this->objectArrayFactoryMock = $this->getMockBuilder(ObjectArrayFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->transactionProvider = new TransactionProvider(
            $this->searchCriteriaBuilderMock,
            $this->sortOrderBuilderMock,
            $this->transactionRepositoryMock,
            $this->objectArrayFactoryMock
        );
    }

    /**
     * @test
     *
     * @return void
     */
    public function testGetByTxnTypeWhenPaymentHasNotBeenAlreadySaved(): void
    {
        $paymentId = null;
        $type = 'tetst';

        $this->orderPaymentMock->expects($this->once())
            ->method('getEntityId')
            ->willReturn($paymentId);
        $this->objectArrayFactoryMock->expects($this->once())
            ->method('create')
            ->with(TransactionInterface::class, []);

        $this->assertInstanceOf(
            ObjectArray::class,
            $this->transactionProvider->getByTxnType($this->orderPaymentMock, $type)
        );
    }

    /**
     * @test
     *
     * @return void
     */
    public function testGetByTxnTypeWhenPaymentHasBeenAlreadySaved(): void
    {
        $paymentId = '10';
        $type = 'tetst';

        $this->orderPaymentMock->expects($this->once())
            ->method('getEntityId')
            ->willReturn($paymentId);
        $this->searchCriteriaBuilderMock->expects($this->exactly(2))
            ->method('addFilter')
            ->withConsecutive([TransactionInterface::PAYMENT_ID, $paymentId], [TransactionInterface::TXN_TYPE, $type])
            ->willReturnSelf();

        $this->sortOrderBuilderMock->expects($this->exactly(2))
            ->method('setField')
            ->withConsecutive([TransactionInterface::TRANSACTION_ID], [TransactionInterface::CREATED_AT])
            ->willReturnSelf();
        $this->sortOrderBuilderMock->expects($this->exactly(2))
            ->method('setDirection')
            ->with(Collection::SORT_ORDER_DESC)
            ->willReturnSelf();
        $this->sortOrderBuilderMock->expects($this->exactly(2))
            ->method('create')
            ->willReturn($this->sortOrderMock);
        $this->searchCriteriaBuilderMock->expects($this->exactly(2))
            ->method('addSortOrder')
            ->with($this->sortOrderMock)
            ->willReturnSelf();

        $this->expectationsForSearchCriteriaBuilding();

        $this->transactionRepositoryMock->expects($this->once())
            ->method('getList')
            ->with($this->searchCriteriaMock)
            ->willReturn($this->searchResultMock);
        $this->searchResultMock->expects($this->once())
            ->method('getItems')
            ->willReturn([]);
        $this->objectArrayFactoryMock->expects($this->once())
            ->method('create')
            ->with(TransactionInterface::class, []);

        $this->assertInstanceOf(
            ObjectArray::class,
            $this->transactionProvider->getByTxnType($this->orderPaymentMock, $type)
        );
    }

    /**
     * @test
     *
     * @return void
     */
    public function testByTxnIdWhenPaymentHasNotBeenAlreadySaved(): void
    {
        $paymentId = null;
        $txnId = 'tetst435345';

        $this->orderPaymentMock->expects($this->once())
            ->method('getEntityId')
            ->willReturn($paymentId);

        $this->assertNull($this->transactionProvider->getByTxnId($this->orderPaymentMock, $txnId));
    }

    /**
     * @test
     *
     * @dataProvider provideDifferentItemsFoundResults
     *
     * @param bool $isAnyItemFound
     * @return void
     */
    public function testByTxnIdWhenPaymentHasBeenAlreadySaved(bool $isAnyItemFound): void
    {
        $paymentId = '10';
        $orderId = '34';
        $txnId = 'tetst435345';
        $items = $isAnyItemFound ? [$this->transactionMock]: [];
        $expected = $isAnyItemFound ? $this->transactionMock : null;

        $this->orderPaymentMock->expects($this->once())
            ->method('getEntityId')
            ->willReturn($paymentId);
        $this->orderPaymentMock->expects($this->once())
            ->method('getParentId')
            ->willReturn($orderId);
        $this->searchCriteriaBuilderMock->expects($this->exactly(3))
            ->method('addFilter')
            ->withConsecutive(
                [TransactionInterface::PAYMENT_ID, $paymentId],
                [TransactionInterface::ORDER_ID, $orderId],
                [TransactionInterface::TXN_ID, $txnId]
            )
            ->willReturnSelf();

        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('setPageSize')
            ->with(1)
            ->willReturnSelf();

        $this->expectationsForSearchCriteriaBuilding();

        $this->transactionRepositoryMock->expects($this->once())
            ->method('getList')
            ->with($this->searchCriteriaMock)
            ->willReturn($this->searchResultMock);
        $this->searchResultMock->expects($this->once())
            ->method('getItems')
            ->willReturn($items);

        $this->assertSame(
            $expected,
            $this->transactionProvider->getByTxnId($this->orderPaymentMock, $txnId)
        );
    }

    /**
     * @return array
     */
    public function provideDifferentItemsFoundResults(): array
    {
        return [
            [true],
            [false]
        ];
    }

    /**
     * @return void
     */
    private function expectationsForSearchCriteriaBuilding(): void
    {
        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('create')
            ->willReturn($this->searchCriteriaMock);
    }
}
