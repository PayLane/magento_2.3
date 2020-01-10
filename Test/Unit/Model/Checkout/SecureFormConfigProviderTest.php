<?php

declare(strict_types=1);

/**
 * File: SecureFormConfigProviderTest.php
 *
 
 
 */

namespace PeP\PaymentGateway\Test\Unit\Model\Checkout;

use PeP\PaymentGateway\Api\Config\Methods\SecureFormConfigProviderInterface;
use PeP\PaymentGateway\Model\Checkout\SecureFormConfigProvider;
use PeP\PaymentGateway\Test\Unit\Model\Config\Methods\SecureFormConfigProviderTestTrait;
use PHPUnit\Framework\TestCase;

/**
 * Class SecureFormConfigProviderTest
 * @package PeP\PaymentGateway\Test\Unit\Model\Checkout
 */
class SecureFormConfigProviderTest extends TestCase
{
    use SecureFormConfigProviderTestTrait;

    /**
     * @var SecureFormConfigProvider
     */
    private $secureFormConfigProvider;

    /**
     * @return void
     */
    protected function setUp()
    {
        parent::setUp();
        $this->setUpSecureFormConfigProvider();

        $this->secureFormConfigProvider = new SecureFormConfigProvider($this->secureFormConfigProviderMock);
    }

    /**
     * @test
     * @return void
     */
    public function testGetConfigCorrectlyModifiesConfig(): void
    {
        $isImageShown = false;

        $this->expectationsForCheckingIfPaymentImageIsShown($isImageShown);

        $expected = [
            'payment' => [
                'paylane_secureform' => [
                    'show_img' => $isImageShown
                ]
            ]
        ];

        $this->assertSame(
            $expected,
            $this->secureFormConfigProvider->getConfig()
        );
    }
}
