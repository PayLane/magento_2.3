<?php

declare(strict_types=1);

/**
 * File: SubjectReaderTest.php
 *
 
 
 */

namespace PeP\PaymentGateway\Test\Unit\Gateway\Reader;

use InvalidArgumentException;
use PeP\PaymentGateway\Gateway\Reader\SubjectReader;
use PeP\PaymentGateway\Test\Unit\Gateway\SubjectReaderTestTrait;
use Magento\Payment\Gateway\Command\CommandException;
use Magento\Payment\Gateway\Helper\SubjectReader as MagentoSubjectReader;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

/**
 * Class SubjectReaderTest
 * @package PeP\PaymentGateway\Test\Unit\Gateway\Reader
 */
class SubjectReaderTest extends TestCase
{
    use SubjectReaderTestTrait;

    /**
     * @var MagentoSubjectReader|MockObject
     */
    private $magentoSubjectReader;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpSubjectReader();

        //Dependencies mocks
        $this->magentoSubjectReader = $this->getMockBuilder(MagentoSubjectReader::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->subjectReaderMock = TestCase::getMockBuilder(SubjectReader::class)
            ->setMethods(
                ['callStaticReadPayment', 'callStaticReadResponse']
            )
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @test
     *
     * @return void
     * @throws CommandException
     */
    public function testReadPaymentWhenExceptionIsThrown(): void
    {
        $subject = [];

        $this->subjectReaderMock->expects($this->once())
            ->method('callStaticReadPayment')
            ->with($subject)
            ->willThrowException(new InvalidArgumentException(('test message')));

        $this->expectException(CommandException::class);
        $this->subjectReaderMock->readPayment($subject);
    }

    /**
     * @test
     *
     * @return void
     * @throws CommandException
     */
    public function testReadPaymentWhenSucceed(): void
    {
        $subject = [$this->paymentDataObjectMock];

        $this->subjectReaderMock->expects($this->once())
            ->method('callStaticReadPayment')
            ->with($subject)
            ->willReturn($this->paymentDataObjectMock);

        $this->assertSame($this->paymentDataObjectMock, $this->subjectReaderMock->readPayment($subject));
    }

    /**
     * @test
     *
     * @return void
     * @throws CommandException
     */
    public function testReadResponseWhenExceptionIsThrown(): void
    {
        $subject = [];

        $this->subjectReaderMock->expects($this->once())
            ->method('callStaticReadResponse')
            ->with($subject)
            ->willThrowException(new InvalidArgumentException(('test message')));

        $this->expectException(CommandException::class);
        $this->subjectReaderMock->readResponse($subject);
    }

    /**
     * @test
     *
     * @return void
     * @throws CommandException
     */
    public function testReadResponseWhenSucceed(): void
    {
        $response = ['sth' => 0];
        $subject = ['response' => $response];

        $this->subjectReaderMock->expects($this->once())
            ->method('callStaticReadResponse')
            ->with($subject)
            ->willReturn($response);

        $this->assertSame($response, $this->subjectReaderMock->readResponse($subject));
    }

    /**
     * @test
     *
     * @dataProvider provideDifferentResponseWithSuccessInfo
     *
     * @param array $response
     * @param bool $isSuccessful
     * @return void
     */
    public function testWasRequestSuccessfulCorrectlyResolvesResult(array $response, bool $isSuccessful): void
    {
        $this->assertSame($isSuccessful, $this->subjectReaderMock->wasRequestSuccessful($response));
    }

    /**
     * @test
     *
     * @dataProvider provideDifferentResponseWithErrorInfo
     *
     * @param array $response
     * @param array $expectedErrorInfo
     * @return void
     */
    public function testErrorInfoCorrectlyReadsSubject(array $response, array $expectedErrorInfo): void
    {
        $this->assertSame($expectedErrorInfo, $this->subjectReaderMock->readErrorInfo($response));
    }

    /**
     * @test
     *
     * @dataProvider provideDifferentResponseWithErrorNumber
     *
     * @param array $response
     * @param string $expectedErrorCode
     * @return void
     */
    public function testErrorNumberCorrectlyReadsSubject(array $response, string $expectedErrorCode): void
    {
        $this->assertSame($expectedErrorCode, $this->subjectReaderMock->readErrorNumber($response));
    }

    /**
     * @test
     *
     * @dataProvider provideDifferentResponseWithErrorDescription
     *
     * @param array $response
     * @param string $expectedErrorDescription
     * @return void
     * @SuppressWarnings(PHPMD.LongVariable)
     */
    public function testErrorDescriptionCorrectlyReadsSubject(array $response, string $expectedErrorDescription): void
    {
        $this->assertSame($expectedErrorDescription, $this->subjectReaderMock->readErrorDescription($response));
    }

    /**
     * @test
     *
     * @dataProvider provideDifferentResponseWithErrorCode
     *
     * @param array $response
     * @param string $expectedErrorCode
     * @return void
     */
    public function testErrorCodeCorrectlyReadsSubject(array $response, string $expectedErrorCode): void
    {
        $this->assertSame($expectedErrorCode, $this->subjectReaderMock->readErrorCode($response));
    }

    /**
     * @test
     *
     * @dataProvider provideDifferentResponseWithErrorText
     *
     * @param array $response
     * @param string $expectedErrorText
     * @return void
     * @SuppressWarnings(PHPMD.LongVariable)
     */
    public function testErrorTextCorrectlyReadsSubject(array $response, string $expectedErrorText): void
    {
        $this->assertSame($expectedErrorText, $this->subjectReaderMock->readErrorText($response));
    }

    /**
     * @test
     *
     * @dataProvider provideDifferentResponseAndFieldExistenceCheckResults
     *
     * @param array $response
     * @param bool $fieldExists
     * @return void
     */
    public function testHasFieldCorrectlyReadsSubject(array $response, bool $fieldExists): void
    {
        $this->assertSame($fieldExists, $this->subjectReaderMock->hasField($response, 'test2'));
    }

    /**
     * @test
     *
     * @dataProvider provideDifferentResponseAndFieldRetrievingCheckResults
     *
     * @param array $response
     * @param $expectedValue
     * @return void
     */
    public function testReadFieldCorrectlyReadsSubject(array $response, $expectedValue): void
    {
        $this->assertSame($expectedValue, $this->subjectReaderMock->readField($response, 'test2'));
    }

    /**
     * @return array
     */
    public function provideDifferentResponseWithSuccessInfo(): array
    {
        return [
            'no_success_info' => [
                ['sth_else' => 5.00],
                false
            ],
            'failure' => [
                ['success' => false],
                false
            ],
            'success' => [
                ['success' => true],
                true
            ]
        ];
    }

    /**
     * @return array
     */
    public function provideDifferentResponseWithErrorInfo(): array
    {
        return [
            'no_error_info' => [
                ['sth_else' => 5.00],
                []
            ],
            'error_info_exists' => [
                ['error' => ['error_info']],
                ['error_info']
            ],
        ];
    }

    /**
     * @return array
     */
    public function provideDifferentResponseWithErrorNumber(): array
    {
        return [
            'no_error_info' => [
                ['sth_else' => 5.00],
                ''
            ],
            'no_error_number' => [
                ['error' => ['error_info']],
                ''
            ],
            'error_number_exits' => [
                ['error' => ['error_number' => 456]],
                '456'
            ],
        ];
    }

    /**
     * @return array
     */
    public function provideDifferentResponseWithErrorDescription(): array
    {
        return [
            'no_error_info' => [
                ['sth_else' => 5.00],
                ''
            ],
            'no_error_description' => [
                ['error' => ['error_info']],
                ''
            ],
            'error_description_exits' => [
                ['error' => ['error_description' => 'Some message']],
                'Some message'
            ],
        ];
    }

    /**
     * @return array
     */
    public function provideDifferentResponseWithErrorCode(): array
    {
        return [
            'no_error_code' => [
                [],
                ''
            ],
            'error_code_exits' => [
                ['error_code' => 456],
                '456'
            ],
        ];
    }

    /**
     * @return array
     */
    public function provideDifferentResponseWithErrorText(): array
    {
        return [
            'no_error_text' => [
                ['error' => ''],
                ''
            ],
            'error_description_exits' => [
                ['error_text' => 'Some message'],
                'Some message'
            ],
        ];
    }

    /**
     * @return array
     */
    public function provideDifferentResponseAndFieldExistenceCheckResults(): array
    {
        return [
            'field_does_not_exist' => [
                ['test' => 5.00],
                false
            ],
            'field_exists' => [
                ['test2' => 4.00],
                true
            ]
        ];
    }

    /**
     * @return array
     */
    public function provideDifferentResponseAndFieldRetrievingCheckResults(): array
    {
        return [
            'field_does_not_exist' => [
                ['test' => 5.00],
                ''
            ],
            'field_exists' => [
                ['test2' => 4.00],
                4.00
            ],
            'array_field_exists' => [
                ['test2' => ['sth' => 4.00]],
                ['sth' => 4.00]
            ]
        ];
    }
}
