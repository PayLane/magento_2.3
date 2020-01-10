<?php

declare(strict_types=1);

/**
 * File: RequiredValueValidatorTest.php
 *
 
 
 */

namespace PeP\PaymentGateway\Test\Unit\Gateway\Validator;

use PeP\PaymentGateway\Gateway\Validator\RequiredValueValidator;
use Magento\Payment\Gateway\Command\CommandException;
use Magento\Payment\Gateway\Validator\ResultInterface;

/**
 * Class RequiredValueValidatorTest
 * @package PeP\PaymentGateway\Test\Unit\Gateway\Validator
 */
class RequiredValueValidatorTest extends ValidatorTestCase
{
    /**
     * @var RequiredValueValidator
     */
    private $requiredValueValidator;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->requiredValueValidator = new RequiredValueValidator(
            $this->subjectReaderMock,
            $this->resultFactoryMock,
            ['test']
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
        $this->requiredValueValidator->validate($validationSubject);
    }

    /**
     * @test
     *
     * @dataProvider provideDifferentResponses
     *
     * @param array $response
     * @param bool $isValid
     * @return void
     * @throws CommandException
     */
    public function testValidateCorrectlyCheckRequiredParametersExistence(array $response, bool $isValid): void
    {
        $validationSubject = ['response' => $response];

        $this->expectationsForReadingResponse($validationSubject, $response);

        $this->resultFactoryMock->expects($this->once())
            ->method('create')
            ->with(
                [
                    'isValid' => $isValid,
                    'failsDescription' => $isValid
                        ? []
                        : ['Payment provider gateway response is not valid. Parameter test is required'],
                    'errorCodes' => []
                ]
            )->willReturn($this->resultMock);

        $this->assertInstanceOf(
            ResultInterface::class,
            $this->requiredValueValidator->validate($validationSubject)
        );
    }

    /**
     * @return array
     */
    public function provideDifferentResponses(): array
    {
        return [
            [
                [
                    'test' => 'sth',
                    'sth_other' => 56
                ],
                true
            ],
            [
                [
                    'sth_other' => 56
                ],
                false
            ]
        ];
    }
}
