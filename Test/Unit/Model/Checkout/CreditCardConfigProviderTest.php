<?php

declare(strict_types=1);

/**
 * File: CreditCardConfigProviderTest.php
 *
 
 
 */

namespace PeP\PaymentGateway\Test\Unit\Model\Checkout;

use PeP\PaymentGateway\Model\Checkout\CreditCardConfigProvider;
use PeP\PaymentGateway\Test\Unit\Model\Config\Methods\CreditCardConfigProviderTestTrait;
use PHPUnit\Framework\TestCase;

/**
 * Class CreditCardConfigProviderTest
 * @package PeP\PaymentGateway\Test\Unit\Model\Checkout
 */
class CreditCardConfigProviderTest extends TestCase
{
    use CreditCardConfigProviderTestTrait;

    /**
     * @var string
     */
    private const SHOW_IMAGE_PARAM = 'show_img';

    /**
     * @var string
     */
    private const IS_3DS_CHECK_ENABLED = 'is_3ds_check_enabled';

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

        $this->setUpCreditCardConfigProvider();
        $this->creditCardConfigProvider = new CreditCardConfigProvider($this->creditCardConfigProviderMock);
    }

    /**
     * @test
     *
     * @return void
     */
    public function testGetConfig(): void
    {
        $isImageShown = true;
        $threeDSecureEnabled = true;

        $this->creditCardConfigProviderMock->expects($this->once())
            ->method('isPaymentMethodImageShown')
            ->willReturn($isImageShown);
        $this->expectationsForGetting3DSecureConfigurationValue($threeDSecureEnabled);

        $expected = [
            'payment' => [
                'paylane_creditcard' => [
                    self::SHOW_IMAGE_PARAM => $isImageShown,
                    self::IS_3DS_CHECK_ENABLED => $threeDSecureEnabled
                ],
            ]
        ];

        $this->assertSame($expected, $this->creditCardConfigProvider->getConfig());
    }
}
