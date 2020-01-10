<?php

declare(strict_types=1);

/**
 * File: CaptureBuilderTest.php
 *
 
 
 */

namespace PeP\PaymentGateway\Test\Unit\Gateway\Request\CreditCard;

use PeP\PaymentGateway\Gateway\Request\CreditCard\CaptureBuilder;
use PeP\PaymentGateway\Test\Unit\Gateway\Request\BuilderTestCase;

/**
 * Class CaptureBuilderTest
 * @package PeP\PaymentGateway\Test\Unit\Gateway\Request\CreditCard
 */
class CaptureBuilderTest extends BuilderTestCase
{
    /**
     * @var string
     */
    private const ID_AUTHORIZATION_PARAM = 'id_authorization';

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->builder = new CaptureBuilder($this->subjectReaderMock);
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
        $idAuthorization = '2334244';
        $grandTotal = 35.00;
        $incrementId = '0045674';

        $buildSubject = [$this->paymentDataObjectMock];
        $this->expectationsForReadingPaymentDO($buildSubject);
        $this->expectationsForGettingPaymentInfo();
        $this->expectationsForGettingOrder();

        $this->paymentInfoMock->expects($this->once())
            ->method('getAdditionalInformation')
            ->with(self::ID_AUTHORIZATION_PARAM)
            ->willReturn($idAuthorization);
        $this->orderAdapterMock->expects($this->once())
            ->method('getGrandTotalAmount')
            ->willReturn($grandTotal);
        $this->orderAdapterMock->expects($this->once())
            ->method('getOrderIncrementId')
            ->willReturn($incrementId);

        $expected = [
            'id_authorization' => $idAuthorization,
            'amount' => sprintf('%01.2f', $grandTotal),
            'description' => $incrementId
        ];

        $this->assertSame($expected, $this->builder->build($buildSubject));
    }
}
