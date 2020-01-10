<?php

declare(strict_types=1);

/**
 * File: ConfigProviderTrait.php
 *
 
 
 */

namespace PeP\PaymentGateway\Test\Unit\Model\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

/**
 * Trait ConfigProviderTrait
 * @package PeP\PaymentGateway\Test\Unit\Model\Config
 */
trait ConfigProviderTrait
{
    /**
     * @var ScopeConfigInterface|MockObject
     */
    protected $scopeConfigMock;

    /**
     * @return void
     */
    protected function setUpConfigProviderTrait(): void
    {
        $this->scopeConfigMock = TestCase::getMockBuilder(ScopeConfigInterface::class)
            ->getMock();
    }

    /**
     * @param string $path
     * @param string $value
     * @param string $scopeType
     * @param null $scopeCode
     * @return void
     */
    protected function expectationsForGettingValue(
        string $path,
        string $value,
        string $scopeType = ScopeInterface::SCOPE_WEBSITE,
        $scopeCode = null
    ) {
        $this->scopeConfigMock->expects(TestCase::once())
            ->method('getValue')
            ->with($path, $scopeType, $scopeCode)
            ->willReturn($value);
    }

    /**
     * @param string $path
     * @param bool $value
     * @param string $scopeType
     * @param null $scopeCode
     * @return void
     */
    protected function expectationsForGettingFlagValue(
        string $path,
        bool $value,
        string $scopeType = ScopeInterface::SCOPE_WEBSITE,
        $scopeCode = null
    ) {
        $this->scopeConfigMock->expects(TestCase::once())
            ->method('isSetFlag')
            ->with($path, $scopeType, $scopeCode)
            ->willReturn($value);
    }
}
