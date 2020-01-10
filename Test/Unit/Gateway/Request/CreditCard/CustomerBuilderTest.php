<?php

declare(strict_types=1);

/**
 * File: CustomerBuilderTest.php
 *
 
 
 */

namespace PeP\PaymentGateway\Test\Unit\Gateway\Request\CreditCard;

use PeP\PaymentGateway\Gateway\Request\CreditCard\CustomerBuilder;
use PeP\PaymentGateway\Test\Unit\Gateway\Request\BuilderTestCase;
use Magento\Payment\Gateway\Data\AddressAdapterInterface;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

/**
 * Class CustomerBuilderTest
 * @package PeP\PaymentGateway\Test\Unit\Gateway\Request\CreditCard
 */
class CustomerBuilderTest extends BuilderTestCase
{
    /**
     * @var AddressAdapterInterface|MockObject
     */
    protected $addressAdapterMock;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->addressAdapterMock = $this->getMockBuilder(AddressAdapterInterface::class)->getMock();

        $this->builder = new CustomerBuilder($this->subjectReaderMock);
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

        $firstName = 'sth';
        $lastName = 'other';
        $email = 'test@gmail.com';
        $ip = '127.0.0.1';
        $street1 = 'test2';
        $street2 = 'test3';
        $city = 'city';
        $state = 'state';
        $zip = '31-560';
        $countryCode = 'PL';


        $this->expectationsForReadingPaymentDO($buildSubject);
        $this->expectationsForGettingOrder();

        $this->orderAdapterMock->expects($this->once())
            ->method('getBillingAddress')
            ->willReturn($this->addressAdapterMock);

        $this->addressAdapterMock->expects($this->once())
            ->method('getFirstname')
            ->willReturn($firstName);
        $this->addressAdapterMock->expects($this->once())
            ->method('getLastname')
            ->willReturn($lastName);
        $this->addressAdapterMock->expects($this->once())
            ->method('getEmail')
            ->willReturn($email);
        $this->orderAdapterMock->expects($this->once())
            ->method('getRemoteIp')
            ->willReturn($ip);
        $this->addressAdapterMock->expects($this->once())
            ->method('getStreetLine1')
            ->willReturn($street1);
        $this->addressAdapterMock->expects($this->once())
            ->method('getStreetLine2')
            ->willReturn($street2);
        $this->addressAdapterMock->expects($this->once())
            ->method('getCity')
            ->willReturn($city);
        $this->addressAdapterMock->expects($this->once())
            ->method('getRegionCode')
            ->willReturn($state);
        $this->addressAdapterMock->expects($this->once())
            ->method('getPostcode')
            ->willReturn($zip);
        $this->addressAdapterMock->expects($this->once())
            ->method('getCountryId')
            ->willReturn($countryCode);

        $expected = [
            'customer' => [
                'name' => $firstName . ' ' . $lastName,
                'email' => $email,
                'ip' => $ip,
                'address' => [
                    'street_house' => join(',', [$street1, $street2]),
                    'city' => $city,
                    'state' => $state,
                    'zip' => $zip,
                    'country_code' => $countryCode,
                ]
            ]
        ];

        $this->assertSame($expected, $this->builder->build($buildSubject));
    }
}
