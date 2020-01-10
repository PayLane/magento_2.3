<?php

declare(strict_types=1);

/**
 * File: ActionTest.php
 *
 
 
 */

namespace PeP\PaymentGateway\Test\Unit\Model\Source\Payment;

use PeP\PaymentGateway\Model\Source\Payment\Action;
use PHPUnit\Framework\TestCase;

/**
 * Class ActionTest
 * @package PeP\PaymentGateway\Test\Unit\Model\Source\Payment
 */
class ActionTest extends TestCase
{
    /**
     * @test
     *
     * @return void
     */
    public function testToOptionArrayReturnsOptionsInCorrectFormat(): void
    {
        $action = new Action();

        $options = $action->toOptionArray();
        $option = $options[0];

        $this->assertInternalType('array', $options);
        $this->assertArrayHasKey('label', $option);
        $this->assertArrayHasKey('value', $option);
    }
}
