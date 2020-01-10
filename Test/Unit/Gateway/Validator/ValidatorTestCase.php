<?php

declare(strict_types=1);

/**
 * File: ValidatorTestCase.php
 *
 
 
 */

namespace PeP\PaymentGateway\Test\Unit\Gateway\Validator;

use PeP\PaymentGateway\Test\Unit\Gateway\SubjectReaderTestTrait;
use Magento\Payment\Gateway\Command\CommandException;
use Magento\Payment\Gateway\Validator\ResultInterface;
use Magento\Payment\Gateway\Validator\ResultInterfaceFactory;
use Magento\Payment\Gateway\Validator\ValidatorInterface;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

/**
 * Class ValidatorTestCase
 * @package PeP\PaymentGateway\Test\Unit\Gateway\Validator
 */
abstract class ValidatorTestCase extends TestCase
{
    use SubjectReaderTestTrait;

    /**
     * @var ResultInterfaceFactory|MockObject
     */
    protected $resultFactoryMock;

    /**
     * @var ResultInterface|MockObject
     */
    protected $resultMock;

    /**
     * @var ValidatorInterface
     */
    protected $validator;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpSubjectReader();

        //Internal mocks
        $this->resultMock = $this->getMockBuilder(ResultInterface::class)
            ->getMock();

        //Dependencies mocks
        $this->resultFactoryMock = $this->getMockBuilder(ResultInterfaceFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @param array $validationSubject
     * @return void
     */
    protected function expectationsForSubjectReaderThrowingException(array $validationSubject): void
    {
        $this->expectationsForReadingPaymentDOAndThrowingException($validationSubject);
        $this->expectException(CommandException::class);
    }
}
