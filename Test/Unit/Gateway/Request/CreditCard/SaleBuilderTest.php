<?php

declare(strict_types=1);

/**
 * File: SaleBuilderTest.php
 *
 
 
 */

namespace PeP\PaymentGateway\Test\Unit\Gateway\Request\CreditCard;

use PeP\PaymentGateway\Gateway\Request\CreditCard\SaleBuilder;
use PeP\PaymentGateway\Test\Unit\Gateway\Request\BuilderTestCase;

/**
 * Class SaleBuilderTest
 * @package PeP\PaymentGateway\Test\Unit\Gateway\Request\CreditCard
 */
class SaleBuilderTest extends BuilderTestCase
{
    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->builder = new SaleBuilder($this->subjectReaderMock);
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
        $buildSubject = [$this->paymentDataObjectMock];

        $amount = 45.00;
        $currency = 'PLN';
        $description = '000434342';

        $this->expectationsForReadingPaymentDO($buildSubject);
        $this->expectationsForGettingOrder();

        $this->orderAdapterMock->expects($this->once())
            ->method('getGrandTotalAmount')
            ->willReturn($amount);
        $this->orderAdapterMock->expects($this->once())
            ->method('getCurrencyCode')
            ->willReturn($currency);
        $this->orderAdapterMock->expects($this->once())
            ->method('getOrderIncrementId')
            ->willReturn($description);

        $expected = [
            'sale' => [
                'amount' => sprintf('%01.2f', $amount),
                'currency' => $currency,
                'description' => $description
            ]
        ];

        $this->assertSame($expected, $this->builder->build($buildSubject));
    }
}
