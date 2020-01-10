<?php

declare(strict_types=1);

/**
 * File: SecureFormConfigProviderTest.php
 *
 
 
 */

namespace PeP\PaymentGateway\Test\Unit\Model\Config\Methods;

use PeP\PaymentGateway\Model\Config\Methods\SecureFormConfigProvider;
use PeP\PaymentGateway\Test\Unit\Model\Config\ConfigProviderTrait;
use PHPUnit\Framework\TestCase;

/**
 * Class SecureFormConfigProviderTest
 * @package PeP\PaymentGateway\Test\Unit\Model\Config\Methods
 */
class SecureFormConfigProviderTest extends TestCase
{
    use ConfigProviderTrait;

    /**
     * @var string
     */
    private const XML_PATH_PAYMENT_PAYLANE_SECUREFORM_SEND_CUSTOMER_DATA
        = 'payment/paylane_secureform/send_customer_data';

    /**
     * @var SecureFormConfigProvider
     */
    private $secureFormConfigProvider;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpConfigProviderTrait();

        $this->secureFormConfigProvider = new SecureFormConfigProvider($this->scopeConfigMock);
    }

    /**
     * @test
     *
     * @return void
     */
    public function testIsCustomerDataSendCorrectlyGetsValue(): void
    {
        $isSent = true;

        $this->expectationsForGettingFlagValue(
            self::XML_PATH_PAYMENT_PAYLANE_SECUREFORM_SEND_CUSTOMER_DATA,
            $isSent
        );

        $this->assertSame($isSent, $this->secureFormConfigProvider->isSendCustomerData());
    }
}