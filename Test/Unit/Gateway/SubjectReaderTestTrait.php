<?php

declare(strict_types=1);

/**
 * File: BuilderTestCase.php
 *
 
 
 */

namespace PeP\PaymentGateway\Test\Unit\Gateway;

use PeP\PaymentGateway\Gateway\Reader\SubjectReader;
use Magento\Payment\Gateway\Command\CommandException;
use Magento\Payment\Gateway\Data\OrderAdapterInterface;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Payment\Gateway\Helper\SubjectReader as MagentoSubjectReader;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Payment\Model\InfoInterface;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

/**
 * Class BuilderTestCase
 * @package PeP\PaymentGateway\Test\Unit\Gateway\Request
 */
trait SubjectReaderTestTrait
{
    /**
     * @var SubjectReader|MockObject
     */
    protected $subjectReaderMock;

    /**
     * @var BuilderInterface
     */
    protected $builder;

    /**
     * @var OrderAdapterInterface|MockObject
     */
    protected $orderAdapterMock;

    /**
     * @var PaymentDataObjectInterface|MockObject
     */
    protected $paymentDataObjectMock;

    /**
     * @var InfoInterface|MockObject
     */
    protected $paymentInfoMock;

    /**
     * @return void
     */
    protected function setUpSubjectReader(): void
    {
        $this->subjectReaderMock = TestCase::getMockBuilder(SubjectReader::class)
            ->setMethodsExcept(
                [
                    'readErrorInfo',
                    'readErrorNumber',
                    'readErrorDescription',
                    'readErrorCode',
                    'readErrorText',
                    'hasField',
                    'wasRequestSuccessful',
                    'readField'
                ]
            )
            ->setConstructorArgs(
                [
                    'subjectReader' => new MagentoSubjectReader()
                ]
            )
            ->getMock();
        $this->orderAdapterMock = TestCase::getMockBuilder(OrderAdapterInterface::class)->getMock();
        $this->paymentDataObjectMock = TestCase::getMockBuilder(PaymentDataObjectInterface::class)
            ->getMock();
        $this->paymentInfoMock = TestCase::getMockBuilder(InfoInterface::class)->getMock();
    }

    /**
     * @param array $subject
     * @return void
     */
    protected function expectationsForReadingPaymentDO(array $subject): void
    {
        $this->subjectReaderMock->expects(TestCase::once())
            ->method('readPayment')
            ->with($subject)
            ->willReturn($this->paymentDataObjectMock);
    }

    /**
     * @param array $subject
     * @return void
     */
    protected function expectationsForReadingPaymentDOAndThrowingException(array $subject): void
    {
        $this->subjectReaderMock->expects(TestCase::once())
            ->method('readPayment')
            ->with($subject)
            ->willThrowException(new CommandException(__('test message')));
    }

    /**
     * @return void
     */
    protected function expectationsForGettingPaymentInfo(): void
    {
        $this->paymentDataObjectMock->expects(TestCase::once())
            ->method('getPayment')
            ->willReturn($this->paymentInfoMock);
    }

    /**
     * @return void
     */
    protected function expectationsForGettingOrder(): void
    {
        $this->paymentDataObjectMock->expects(TestCase::once())
            ->method('getOrder')
            ->willReturn($this->orderAdapterMock);
    }

    /**
     * @param array $subject
     * @param array $response
     * @return void
     */
    protected function expectationsForReadingResponse(array $subject, array $response): void
    {
        $this->subjectReaderMock->expects(TestCase::once())
            ->method('readResponse')
            ->with($subject)
            ->willReturn($response);
    }

    /**
     * @param array $subject
     * @return void
     */
    protected function expectationsForReadingResponseAndThrowingException(array $subject): void
    {
        $this->subjectReaderMock->expects(TestCase::once())
            ->method('readResponse')
            ->with($subject)
            ->willThrowException(new CommandException(__('test message')));

        $this->expectException(CommandException::class);
    }

    /**
     * @param array $response
     * @param bool $isSuccessful
     * @return void
     */
    protected function expectationsForCheckingIfResponseWasSuccessful(array $response, bool $isSuccessful): void
    {
        $this->subjectReaderMock->expects(TestCase::once())
            ->method('wasRequestSuccessful')
            ->with($response)
            ->willReturn($isSuccessful);
    }
}
