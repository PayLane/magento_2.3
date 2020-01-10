<?php

declare(strict_types=1);

/**
 * File: SecureFormConfigProviderTestTrait.php
 *
 
 
 */

namespace PeP\PaymentGateway\Test\Unit\Model\Config\Methods;

use PeP\PaymentGateway\Api\Config\Methods\SecureFormConfigProviderInterface;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

/**
 * Trait SecureFormConfigProviderTestTrait
 * @package PeP\PaymentGateway\Test\Unit\Model\Config\Methods
 */
trait SecureFormConfigProviderTestTrait
{
    /**
     * @var SecureFormConfigProviderInterface|MockObject
     */
    private $secureFormConfigProviderMock;

    /**
     * @return void
     */
    protected function setUpSecureFormConfigProvider(): void
    {
        $this->secureFormConfigProviderMock = TestCase::getMockBuilder(SecureFormConfigProviderInterface::class)
            ->getMock();
    }

    /**
     * @param bool $isSent
     * @return void
     */
    protected function expectationsForCheckingIfCustomerDataIsSent(bool $isSent): void
    {
        $this->secureFormConfigProviderMock->expects(TestCase::once())
            ->method('isSendCustomerData')
            ->willReturn($isSent);
    }

    /**
     * @param bool $isShown
     * @return void
     */
    protected function expectationsForCheckingIfPaymentImageIsShown(bool $isShown): void
    {
        $this->secureFormConfigProviderMock->expects(TestCase::once())
            ->method('isPaymentMethodImageShown')
            ->willReturn($isShown);
    }
}
