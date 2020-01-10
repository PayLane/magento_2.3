<?php

declare(strict_types=1);

/**
 * File: BackRequestValidatorCompositeTest.php
 *
 
 
 */

namespace PeP\PaymentGateway\Test\Unit\Model\Request;

use PeP\PaymentGateway\Model\Request\BackRequestValidatorComposite;
use PeP\PaymentGateway\Model\Request\BackRequestValidatorInterface;
use Magento\Framework\App\RequestInterface;
use MSlwk\TypeSafeArray\ObjectArrayFactory;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

/**
 * Class BackRequestValidatorCompositeTest
 * @package PeP\PaymentGateway\Test\Unit\Model\Request
 */
class BackRequestValidatorCompositeTest extends TestCase
{
    /**
     * @var BackRequestValidatorInterface|MockObject
     */
    private $validatorMock;

    /**
     * @var BackRequestValidatorComposite
     */
    private $backRequestValidatorComposite;

    /**
     * @var RequestInterface|MockObject
     */
    private $requestMock;

    /**
     * @var ObjectArrayFactory
     */
    private $objectArrayFactory;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->validatorMock = $this->getMockBuilder(BackRequestValidatorInterface::class)->getMock();
        $this->requestMock = $this->getMockBuilder(RequestInterface::class)->getMock();
        $this->objectArrayFactory = new ObjectArrayFactory();

        $this->backRequestValidatorComposite = new BackRequestValidatorComposite(
            $this->objectArrayFactory,
            [$this->validatorMock, $this->validatorMock, $this->validatorMock]
        );
    }

    /**
     * @test
     *
     * @return void
     */
    public function testValidateWhenOneOfValidatorReturnsNegativeResult(): void
    {
        $this->validatorMock->expects($this->at(0))
            ->method('validate')
            ->with($this->requestMock)
            ->willReturn(true);
        $this->validatorMock->expects($this->at(1))
            ->method('validate')
            ->with($this->requestMock)
            ->willReturn(false);

        $this->assertFalse($this->backRequestValidatorComposite->validate($this->requestMock));
    }

    /**
     * @test
     *
     * @return void
     */
    public function testValidateWhenAllValidatorsReturnsPositiveResults(): void
    {
        $this->validatorMock->expects($this->exactly(3))
            ->method('validate')
            ->with($this->requestMock)
            ->willReturn(true);

        $this->assertTrue($this->backRequestValidatorComposite->validate($this->requestMock));
    }
}
