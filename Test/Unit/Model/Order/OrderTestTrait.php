<?php

declare(strict_types=1);

/**
 * File: OrderTestTrait.php
 *
 
 
 */

namespace PeP\PaymentGateway\Test\Unit\Model\Order;

use Magento\Sales\Model\Order;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

/**
 * Trait OrderTestTrait
 * @package PeP\PaymentGateway\Test\Unit\Model\Order
 */
trait OrderTestTrait
{
    /**
     * @var Order|MockObject
     */
    protected $orderMock;

    /**
     * @return void
     */
    protected function setUpOrderTrait(): void
    {
        $this->orderMock = TestCase::getMockBuilder(Order::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @param string $comment
     * @param string[] $params
     * @return void
     */
    protected function expectationsForAddingCommentToHistory(string $comment, string ... $params)
    {
        $this->orderMock->expects(TestCase::once())
            ->method('addCommentToStatusHistory')
            ->with(
                __(
                    $comment,
                    ... $params
                )->render()
            )->willReturnSelf();
    }
}
