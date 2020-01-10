<?php

declare(strict_types=1);

/**
 * File: TokenBuilderTest.php
 *
 
 
 */

namespace PeP\PaymentGateway\Test\Unit\Gateway\Request\CreditCard;

use PeP\PaymentGateway\Gateway\Request\CreditCard\TokenBuilder;
use PeP\PaymentGateway\Test\Unit\Gateway\Request\BuilderTestCase;

/**
 * Class TokenBuilderTest
 * @package PeP\PaymentGateway\Test\Unit\Gateway\Request\CreditCard
 */
class TokenBuilderTest extends BuilderTestCase
{
    /**
     * @var string
     */
    private const TOKEN_PARAM = 'token';

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->builder = new TokenBuilder($this->subjectReaderMock);
    }

    /**
     * @test
     *
     * @return void
     */
    public function testBuildWhenSubjectReaderThrowsException(): void
    {
        $buildSubject = [];
        $this->expectationsForSubjectReaderThrowingException($buildSubject);
        $this->builder->build($buildSubject);
    }

    /**
     * @test
     *
     * @return void
     */
    public function testBuildCorrectlyBuildsRequestPayload(): void
    {
        $token = '332rfwefw423rre33333333333';

        $buildSubject = [$this->paymentDataObjectMock];
        $this->expectationsForReadingPaymentDO($buildSubject);
        $this->expectationsForGettingPaymentInfo();

        $this->paymentInfoMock->expects($this->once())
            ->method('getAdditionalInformation')
            ->with(self::TOKEN_PARAM)
            ->willReturn($token);

        $expected = [
            'card' => [
                self::TOKEN_PARAM => $token
            ]
        ];

        $this->assertSame($expected, $this->builder->build($buildSubject));
    }
}
