<?php

declare(strict_types=1);

/**
 * File: CreditCardConfigProviderTestTrait.php
 *
 
 
 */

namespace PeP\PaymentGateway\Test\Unit\Model\Config\Methods;

use PeP\PaymentGateway\Api\Config\Methods\CreditCardConfigProviderInterface;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

/**
 * Trait CreditCardConfigProviderTestTrait
 * @package PeP\PaymentGateway\Test\UnitConfig\Methods
 */
trait CreditCardConfigProviderTestTrait
{
    /**
     * @var CreditCardConfigProviderInterface|MockObject
     */
    protected $creditCardConfigProviderMock;

    /**
     * @return void
     */
    protected function setUpCreditCardConfigProvider(): void
    {
        $this->creditCardConfigProviderMock = TestCase::getMockBuilder(CreditCardConfigProviderInterface::class)
            ->getMock();
    }

    /**
     * @param bool $isEnabled
     * @return void
     */
    protected function expectationsForGetting3DSecureConfigurationValue(bool $isEnabled): void
    {
        $this->creditCardConfigProviderMock->expects(TestCase::once())
            ->method('is3DSCheckEnabled')
            ->willReturn($isEnabled);
    }
}
