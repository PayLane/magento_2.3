<?php

declare(strict_types=1);

/**
 * File: OrderExistenceValidatorTest.php
 *
 
 
 */

namespace PeP\PaymentGateway\Test\Unit\Model\Request;

use PeP\PaymentGateway\Model\Request\OrderExistenceValidator;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\RequestInterface;
use Magento\Sales\Model\Order;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

/**
 * Class OrderExistenceValidatorTest
 * @package PeP\PaymentGateway\Test\Unit\Model\Request
 */
class OrderExistenceValidatorTest extends TestCase
{
    /**
     * @var string
     */
    private const DESCRIPTION_PARAM = 'description';

    /**
     * @var OrderExistenceValidator
     */
    private $orderExistenceValidator;

    /**
     * @var Session|MockObject
     */
    private $sessionMock;

    /**
     * @var RequestInterface|MockObject
     */
    private $requestMock;

    /**
     * @var Order|MockObject
     */
    private $orderMock;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        //Internal mocks
        $this->requestMock = $this->getMockBuilder(RequestInterface::class)->getMock();
        $this->orderMock = $this->getMockBuilder(Order::class)
            ->disableOriginalConstructor()
            ->getMock();

        //Dependencies mocks
        $this->sessionMock = $this->getMockBuilder(Session::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->orderExistenceValidator = new OrderExistenceValidator($this->sessionMock);
    }

    /**
     * @test
     * @dataProvider provideDifferentIncrementIdsPairs
     *
     * @param string $incrementIdFromRequest
     * @param string $incrementId
     * @param bool $isValid
     * @return void
     * @SuppressWarnings(PHPMD.LongVariable)
     */
    public function testValidateWhenNoLastOrderIncrementIdDoesntMatchIncrementIdFromRequest(
        string $incrementIdFromRequest,
        string $incrementId,
        bool $isValid
    ): void {
        $this->requestMock->expects($this->once())
            ->method('getParam')
            ->with(self::DESCRIPTION_PARAM, '')
            ->willReturn($incrementIdFromRequest);

        $this->sessionMock->expects($this->once())
            ->method('getLastRealOrder')
            ->willReturn($this->orderMock);

        $this->orderMock->expects($this->once())
            ->method('getIncrementId')
            ->willReturn($incrementId);

        $this->assertSame($isValid, $this->orderExistenceValidator->validate($this->requestMock));
    }

    /**
     * @return array
     */
    public function provideDifferentIncrementIdsPairs(): array
    {
        return [
            ['00003430', '00003410', false],
            ['00003430', '00003430', true]
        ];
    }
}
