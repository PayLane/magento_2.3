<?php

declare(strict_types=1);

/**
 * File: ThreeDSecureBuilderTest.php
 *
 
 
 */

namespace PeP\PaymentGateway\Test\Unit\Gateway\Request\CreditCard;

use PeP\PaymentGateway\Gateway\Request\CreditCard\ThreeDSecureBuilder;
use PeP\PaymentGateway\Test\Unit\Gateway\Request\BuilderTestCase;
use Magento\Framework\UrlInterface;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

/**
 * Class ThreeDSecureBuilderTest
 * @package PeP\PaymentGateway\Test\Unit\Gateway\Request\CreditCard
 */
class ThreeDSecureBuilderTest extends BuilderTestCase
{
    /**
     * @var string
     */
    private const BACK_URL_PARAM = 'back_url';

    /**
     * @var UrlInterface|MockObject
     */
    private $urlMock;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->urlMock = $this->getMockBuilder(UrlInterface::class)->getMock();

        $this->builder = new ThreeDSecureBuilder($this->urlMock);
    }

    /**
     * @test
     *
     * @return void
     */
    public function testBuildCorrectlyBuildsRequestPayload(): void
    {
        $buildSubject = [$this->paymentDataObjectMock];
        $url = 'http://test.com/paylane/test/test';

        $this->urlMock->expects($this->once())
            ->method('getUrl')
            ->with('paylane/creditcard/handle3dsecuretransaction')
            ->willReturn($url);

        $expected = [
            self::BACK_URL_PARAM => $url
        ];

        $this->assertSame($expected, $this->builder->build($buildSubject));
    }
}
