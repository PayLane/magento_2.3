<?php

declare(strict_types=1);

/**
 * File: CreditCardAdapterTest.php
 *
 
 
 */

namespace PeP\PaymentGateway\Test\Unit\Model\Method;

use PeP\PaymentGateway\Model\Method\CreditCardAdapter;
use PeP\PaymentGateway\Test\Unit\Model\Config\Methods\CreditCardConfigProviderTestTrait;
use Magento\Framework\Event\ManagerInterface;
use Magento\Payment\Gateway\Config\ValueHandlerPoolInterface;
use Magento\Payment\Gateway\Data\PaymentDataObjectFactory;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

/**
 * Class CreditCardAdapterTest
 * @package PeP\PaymentGateway\Test\Unit\Model\Method
 */
class CreditCardAdapterTest extends TestCase
{
    use CreditCardConfigProviderTestTrait;

    /**
     * @var CreditCardAdapter|MockObject
     */
    private $creditCardAdapter;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpCreditCardConfigProvider();

        $this->creditCardAdapter = $this->getMockBuilder(CreditCardAdapter::class)
            ->setMethods(['callParentGetConfigPaymentAction', 'is3DSCheckEnabled', 'getConfigData'])
            ->setConstructorArgs(
                [
                    $this->creditCardConfigProviderMock,
                    $this->createMockForConstructionPurposeOnly(ManagerInterface::class),
                    $this->createMockForConstructionPurposeOnly(ValueHandlerPoolInterface::class),
                    $this->createMockForConstructionPurposeOnly(PaymentDataObjectFactory::class),
                    '',
                    '',
                    '',
                ]
            )
            ->getMock();
    }

    /**
     * @test
     *
     * @dataProvider provideDifferent3DSecureConfigurationValues
     *
     * @param bool $is3DSecureEnabled
     * @return void
     */
    public function testGetConfigPaymentActionCorrectlyResolvesAction(bool $is3DSecureEnabled): void
    {
        $action3DSecure = 'test_other';
        $action = 'test';

        $this->expectationsForGetting3DSecureConfigurationValue($is3DSecureEnabled);

        if ($is3DSecureEnabled) {
            $this->creditCardAdapter->expects($this->once())
                ->method('getConfigData')
                ->with('payment_3ds_action')
                ->willReturn($action3DSecure);
            $expected = $action3DSecure;
        } else {
            $this->creditCardAdapter->expects($this->once())
                ->method('callParentGetConfigPaymentAction')
                ->willReturn($action);
            $expected = $action;
        }

        $this->assertSame($expected, $this->creditCardAdapter->getConfigPaymentAction());
    }

    /**
     * @return array
     */
    public static function provideDifferent3DSecureConfigurationValues(): array
    {
        return [
            [true],
            [false]
        ];
    }

    /**
     * @param string $type
     * @return MockObject
     */
    private function createMockForConstructionPurposeOnly(string $type): MockObject
    {
        return $this->getMockBuilder($type)->disableOriginalConstructor()->getMock();
    }
}
