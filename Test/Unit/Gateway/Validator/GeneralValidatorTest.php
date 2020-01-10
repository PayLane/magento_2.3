<?php

declare(strict_types=1);

/**
 * File: GeneralValidatorTest.php
 *
 
 
 */

namespace PeP\PaymentGateway\Test\Unit\Gateway\Validator;

use PeP\PaymentGateway\Gateway\Validator\GeneralValidator;
use Magento\Payment\Gateway\Command\CommandException;
use Magento\Payment\Gateway\Validator\ResultInterface;

/**
 * Class GeneralValidatorTest
 * @package PeP\PaymentGateway\Test\Unit\Gateway\Validator
 */
class GeneralValidatorTest extends ValidatorTestCase
{
    /**
     * @var GeneralValidator
     */
    private $generalValidator;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->generalValidator = new GeneralValidator(
            $this->subjectReaderMock,
            $this->resultFactoryMock
        );
    }

    /**
     * @test
     *
     * @return void
     * @throws CommandException
     */
    public function testValidateWhenSubjectReaderThrowsException(): void
    {
        $validationSubject = [];
        $this->expectationsForReadingResponseAndThrowingException($validationSubject);
        $this->generalValidator->validate($validationSubject);
    }

    /**
     * @test
     *
     * @return void
     * @throws CommandException
     */
    public function testValidateWhenRequestWasSuccessful(): void
    {
        $validationSubject = ['response' => ['success' => true]];
        $response = $validationSubject['response'];

        $this->expectationsForReadingResponse($validationSubject, $response);

        $this->resultFactoryMock->expects($this->once())
            ->method('create')
            ->with(
                [
                    'isValid' => true,
                    'failsDescription' => [],
                    'errorCodes' => []
                ]
            )->willReturn($this->resultMock);

        $this->assertInstanceOf(
            ResultInterface::class,
            $this->generalValidator->validate($validationSubject)
        );
    }

    /**
     * @test
     *
     * @return void
     * @throws CommandException
     */
    public function testValidateWhenRequestWasNotSuccessful(): void
    {
        $validationSubject = [
            'response' => [
                'success' => false,
                'error' => ['error_description' => 'sth', 'error_number' => '456']
            ]
        ];
        $response = $validationSubject['response'];

        $this->expectationsForReadingResponse($validationSubject, $response);

        $this->resultFactoryMock->expects($this->once())
            ->method('create')
            ->with(
                [
                    'isValid' => false,
                    'failsDescription' => ['sth'],
                    'errorCodes' => ['456']
                ]
            )->willReturn($this->resultMock);

        $this->assertInstanceOf(
            ResultInterface::class,
            $this->generalValidator->validate($validationSubject)
        );
    }
}
