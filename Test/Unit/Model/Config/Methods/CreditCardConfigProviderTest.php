<?php

declare(strict_types=1);

/**
 * File: CreditCardConfigProviderTest.php
 *
 
 
 */

namespace PeP\PaymentGateway\Test\Unit\Model\Config\Methods;

use PeP\PaymentGateway\Model\Config\Methods\CreditCardConfigProvider;
use PeP\PaymentGateway\Test\Unit\Model\Config\ConfigProviderTrait;
use PHPUnit\Framework\TestCase;

/**
 * Class CreditCardConfigProviderTest
 * @package PeP\PaymentGateway\Test\Unit\Model\Config\Methods
 */
class CreditCardConfigProviderTest extends TestCase
{
    use ConfigProviderTrait;

    /**
     * @var string
     */
    private const XML_PATH_PAYMENT_PAYLANE_CREDITCARD_PAYMENT_ACTION
        = 'payment/paylane_creditcard/payment_action';

    /**
     * @var string
     */
    private const XML_PATH_PAYMENT_PAYLANE_CREDITCARD_DS3_CHECK = 'payment/paylane_creditcard/ds3_check';

    /**
     * @var CreditCardConfigProvider
     */
    private $creditCardConfigProvider;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpConfigProviderTrait();

        $this->creditCardConfigProvider = new CreditCardConfigProvider($this->scopeConfigMock);
    }

    /**
     * @test
     *
     * @return void
     */
    public function testGetPaymentActionCorrectlyGetsValue(): void
    {
        $action = 'authorize';

        $this->expectationsForGettingValue(
            self::XML_PATH_PAYMENT_PAYLANE_CREDITCARD_PAYMENT_ACTION,
            $action
        );

        $this->assertSame($action, $this->creditCardConfigProvider->getPaymentAction());
    }

    /**
     * @test
     *
     * @return void
     */
    public function testIs3DSCheckEnabledCorrectlyGetsValue(): void
    {
        $isEnabled = true;

        $this->expectationsForGettingFlagValue(
            self::XML_PATH_PAYMENT_PAYLANE_CREDITCARD_DS3_CHECK,
            $isEnabled
        );

        $this->assertSame($isEnabled, $this->creditCardConfigProvider->is3DSCheckEnabled());
    }
}
