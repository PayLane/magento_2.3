<?php

declare(strict_types=1);

/**
 * File: ResponseHandlerTestCase.php
 *
 
 
 */

namespace PeP\PaymentGateway\Test\Unit\Gateway\Response;

use PeP\PaymentGateway\Test\Unit\Gateway\SubjectReaderTestTrait;
use PeP\PaymentGateway\Test\Unit\Model\Order\OrderTestTrait;
use Magento\Sales\Model\Order\Payment;
use PHPUnit\Framework\TestCase;

/**
 * Class ResponseHandlerTestCase
 * @package PeP\PaymentGateway\Test\Unit\Gateway\Response
 */
abstract class ResponseHandlerTestCase extends TestCase
{
    use OrderTestTrait, SubjectReaderTestTrait;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpOrderTrait();
        $this->setUpSubjectReader();
        $this->paymentInfoMock = $this->getMockBuilder(Payment::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @return array
     */
    public function boolean(): array
    {
        return [
            [true],
            [false]
        ];
    }

    /**
     * @param string $id
     * @return void
     * @SuppressWarnings(PHPMD.ShortVariable)
     */
    protected function expectationsForSettingTransactionId(string $id): void
    {
        $this->paymentInfoMock->expects($this->once())
            ->method('setTransactionId')
            ->with($id)
            ->willReturnSelf();
    }

    /**
     * @param bool $shouldBeClosed
     * @return void
     */
    protected function expectationsForClosingTransaction(bool $shouldBeClosed): void
    {
        $this->paymentInfoMock->expects($this->once())
            ->method('setIsTransactionClosed')
            ->with($shouldBeClosed)
            ->willReturnSelf();
    }

    /**
     * @param bool $shouldBeClosed
     * @return void
     */
    protected function expectationsForClosingParentTransaction(bool $shouldBeClosed): void
    {
        $this->paymentInfoMock->expects($this->once())
            ->method('setShouldCloseParentTransaction')
            ->with($shouldBeClosed)
            ->willReturnSelf();
    }

    /**
     * @return void
     */
    protected function expectationsForSettingTransactionAsPending(): void
    {
        $this->paymentInfoMock->expects($this->once())
            ->method('setIsTransactionPending')
            ->with(true)
            ->willReturnSelf();
    }

    /**
     * @param array[] $params
     * @return void
     */
    protected function expectationsForSettingAdditionalInformation(array ... $params): void
    {
        $count = count($params);
        $this->paymentInfoMock->expects($this->exactly($count))
            ->method('setAdditionalInformation')
            ->withConsecutive(... $params)
            ->willReturnSelf();
    }

    /**
     * @param array $valuesReturned
     * @param array[] $keys
     * @return void
     */
    protected function expectationsForGettingAdditionalInformation(array $valuesReturned, array ... $keys): void
    {
        $count = count($keys);
        $this->paymentInfoMock->expects($this->exactly($count))
            ->method('getAdditionalInformation')
            ->withConsecutive(... $keys)
            ->willReturnOnConsecutiveCalls(... $valuesReturned);
    }

    /**
     * @return void
     */
    protected function expectationsForGettingOrderModel(): void
    {
        $this->paymentInfoMock->expects($this->once())
            ->method('getOrder')
            ->willReturn($this->orderMock);
    }
}
