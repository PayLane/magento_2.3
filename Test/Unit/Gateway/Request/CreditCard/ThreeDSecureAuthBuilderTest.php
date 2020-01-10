<?php

declare(strict_types=1);

/**
 * File: ThreeDSecureAuthBuilderTest.php
 *
 
 
 */

namespace PeP\PaymentGateway\Test\Unit\Gateway\Request\CreditCard;

use PeP\PaymentGateway\Gateway\Request\CreditCard\ThreeDSecureAuthBuilder;
use PeP\PaymentGateway\Test\Unit\Gateway\Request\BuilderTestCase;

/**
 * Class ThreeDSecureAuthBuilderTest
 * @package PeP\PaymentGateway\Test\Unit\Gateway\Request\CreditCard
 */
class ThreeDSecureAuthBuilderTest extends BuilderTestCase
{
    /**
     * @var string
     */
    private const ID_3DSECURE_AUTH_PARAM = 'id_3dsecure_auth';

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->builder = new ThreeDSecureAuthBuilder($this->subjectReaderMock);
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
        $id3DSecureAuth = '334d33';
        $buildSubject = [$this->paymentDataObjectMock];
        $this->expectationsForReadingPaymentDO($buildSubject);
        $this->expectationsForGettingPaymentInfo();

        $this->paymentInfoMock->expects($this->once())
            ->method('getAdditionalInformation')
            ->with(self::ID_3DSECURE_AUTH_PARAM)
            ->willReturn($id3DSecureAuth);

        $expected = [
            self::ID_3DSECURE_AUTH_PARAM => $id3DSecureAuth
        ];

        $this->assertSame($expected, $this->builder->build($buildSubject));
    }
}
