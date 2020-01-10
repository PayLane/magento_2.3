<?php

declare(strict_types=1);

/**
 * File: PayloadBuilderTest.php
 *
 
 
 */

namespace PeP\PaymentGateway\Test\Unit\Model\SecureForm;

use PeP\PaymentGateway\Api\Config\GeneralConfigProviderInterface;
use PeP\PaymentGateway\Model\SecureForm\PayloadBuilder;
use PeP\PaymentGateway\Model\TransactionHandler;
use PeP\PaymentGateway\Test\Unit\Model\Config\Methods\SecureFormConfigProviderTestTrait;
use Magento\Checkout\Model\Session;
use Magento\Framework\Locale\Resolver;
use Magento\Framework\UrlInterface;
use Magento\Sales\Api\Data\OrderAddressInterface;
use Magento\Sales\Model\Order;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class PayloadBuilderTest
 * @package PeP\PaymentGateway\Test\Unit\Model\SecureForm
 */
class PayloadBuilderTest extends TestCase
{
    use SecureFormConfigProviderTestTrait;

    /**
     * @var string
     */
    private const DEFAULT_SECURE_FORM_LANG = 'en';

    /**
     * @var GeneralConfigProviderInterface
     */
    private $generalConfigProviderMock;

    /**
     * @var PayloadBuilder
     */
    private $payloadBuilder;

    /**
     * @var Session
     */
    private $sessionMock;

    /**
     * @var Resolver
     */
    private $resolverMock;

    /**
     * @var UrlInterface
     */
    private $urlMock;

    /**
     * @var Order|MockObject
     */
    private $orderMock;

    /**
     * @var OrderAddressInterface|MockObject
     */
    private $orderAddressMock;

    /**
     * @var array
     */
    private $allowedLanguages;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpSecureFormConfigProvider();
        //Internal mocks
        $this->orderMock = $this->getMockBuilder(Order::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->orderAddressMock = $this->getMockBuilder(OrderAddressInterface::class)->getMock();

        //Dependencies mocks
        $this->generalConfigProviderMock = $this->getMockBuilder(GeneralConfigProviderInterface::class)->getMock();
        $this->sessionMock = $this->getMockBuilder(Session::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->resolverMock = $this->getMockBuilder(Resolver::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->urlMock = $this->getMockBuilder(UrlInterface::class)->getMock();
        $this->allowedLanguages = ['en' => 'en', 'pl' => 'pl'];

        $this->payloadBuilder = new PayloadBuilder(
            $this->generalConfigProviderMock,
            $this->secureFormConfigProviderMock,
            $this->sessionMock,
            $this->resolverMock,
            $this->urlMock,
            $this->allowedLanguages
        );
    }

    /**
     * @test
     * @return void
     */
    public function testBuildWhenCustomerDataIsNotSent(): void
    {
        $incrementId = '000034';
        $grandTotal = 45.00;
        $currencyCode = 'PLN';
        $merchantId = 'fsdfsd';
        $firstName = 'test';
        $lastName = 'test2';
        $email = 'test@gmail.com';
        $backUrl = 'http://paylane/secureForm/handleSaleTransaction/';
        $locale = 'pl_PL';
        $hash = 'ab1a88a37b37d402186bcaa3b061b141de21d7cb';

        $this->expectationsForGettingOrderBasicInfo($incrementId, $grandTotal, $currencyCode);
        $this->expectationsForGettingConfig($merchantId);

        $this->orderMock->expects($this->once())
            ->method('getCustomerFirstname')
            ->willReturn($firstName);
        $this->orderMock->expects($this->once())
            ->method('getCustomerLastname')
            ->willReturn($lastName);
        $this->orderMock->expects($this->once())
            ->method('getCustomerEmail')
            ->willReturn($email);
        $this->expectationsForBuildingBackUrl($backUrl);
        $this->expectationsForResolvingLanguage($locale);
        $this->expectationsForCheckingIfCustomerDataIsSent(false);

        $expected = [
            'action' => 'https://secure.paylane.com/order/cart.html',
            'method' => 'POST',
            'fields' => [
                'amount' => sprintf('%01.2f', $grandTotal),
                'currency' => $currencyCode,
                'merchant_id' => $merchantId,
                'description' => $incrementId,
                'transaction_description' => sprintf(
                    'Order #%s, %s (%s)',
                    $incrementId,
                    $firstName . ' ' . $lastName,
                    $email
                ),
                'transaction_type' => TransactionHandler::TYPE_SALE,
                'back_url' => $backUrl,

                'language' => strstr($locale, '_', true),
                'hash' => $hash
            ]
        ];

        $this->assertSame(
            [$expected],
            $this->payloadBuilder->build()
        );
    }

    /**
     * @test
     * @return void
     */
    public function testBuildWhenCustomerDataIsSent(): void
    {
        $incrementId = '000034';
        $grandTotal = 45.00;
        $currencyCode = 'PLN';
        $merchantId = 'fsdfsd';
        $firstName = 'test';
        $lastName = 'test2';
        $email = 'test@gmail.com';
        $backUrl = 'http://paylane/secureForm/handleSaleTransaction/';
        $locale = 'pl_PL';
        $hash = 'ab1a88a37b37d402186bcaa3b061b141de21d7cb';
        $postCode = '45-456';
        $street = ['Main street 23', 'Downtown'];
        $city = 'Test';
        $region = 'Lesser Poland';
        $country = 'PL';

        $this->expectationsForGettingOrderBasicInfo($incrementId, $grandTotal, $currencyCode);
        $this->expectationsForGettingConfig($merchantId);

        $this->orderMock->expects($this->exactly(2))
            ->method('getCustomerFirstname')
            ->willReturn($firstName);
        $this->orderMock->expects($this->exactly(2))
            ->method('getCustomerLastname')
            ->willReturn($lastName);
        $this->orderMock->expects($this->exactly(2))
            ->method('getCustomerEmail')
            ->willReturn($email);
        $this->expectationsForBuildingBackUrl($backUrl);
        $this->expectationsForResolvingLanguage($locale);

        $this->orderMock->expects($this->once())
            ->method('getBillingAddress')
            ->willReturn($this->orderAddressMock);
        $this->orderAddressMock->expects($this->once())
            ->method('getStreet')
            ->willReturn($street);
        $this->orderAddressMock->expects($this->once())
            ->method('getPostCode')
            ->willReturn($postCode);
        $this->orderAddressMock->expects($this->once())
            ->method('getCity')
            ->willReturn($city);
        $this->orderAddressMock->expects($this->once())
            ->method('getRegion')
            ->willReturn($region);
        $this->orderAddressMock->expects($this->once())
            ->method('getCountryId')
            ->willReturn($country);

        $this->expectationsForCheckingIfCustomerDataIsSent(true);

        $expected = [
            'action' => 'https://secure.paylane.com/order/cart.html',
            'method' => 'POST',
            'fields' => [
                'amount' => sprintf('%01.2f', $grandTotal),
                'currency' => $currencyCode,
                'merchant_id' => $merchantId,
                'description' => $incrementId,
                'transaction_description' => sprintf(
                    'Order #%s, %s (%s)',
                    $incrementId,
                    $firstName . ' ' . $lastName,
                    $email
                ),
                'transaction_type' => TransactionHandler::TYPE_SALE,
                'back_url' => $backUrl,

                'language' => strstr($locale, '_', true),
                'hash' => $hash,

                'customer_name' => $firstName . ' ' . $lastName,
                'customer_email' => $email,
                'customer_address' => implode(',', $street),
                'customer_zip' => $postCode,
                'customer_city' => $city,
                'customer_state' => $region,
                'customer_country' => $country
            ]
        ];

        $this->assertSame(
            [$expected],
            $this->payloadBuilder->build()
        );
    }

    /**
     * @param string $incrementId
     * @param float $grandTotal
     * @param string $currencyCode
     * @return void
     */
    private function expectationsForGettingOrderBasicInfo(
        string $incrementId,
        float $grandTotal,
        string $currencyCode
    ): void {
        $this->sessionMock->expects($this->once())
            ->method('getLastRealOrder')
            ->willReturn($this->orderMock);
        $this->orderMock->expects($this->exactly(2))
            ->method('getIncrementId')
            ->willReturn($incrementId);
        $this->orderMock->expects($this->once())
            ->method('getGrandTotal')
            ->willReturn($grandTotal);
        $this->orderMock->expects($this->once())
            ->method('getOrderCurrencyCode')
            ->willReturn($currencyCode);
    }

    /**
     * @param string $merchantId
     * @return void
     */
    private function expectationsForGettingConfig(string $merchantId): void
    {
        $this->generalConfigProviderMock->expects($this->once())
            ->method('getMerchantId')
            ->willReturn($merchantId);
    }

    /**
     * @param string $backUrl
     * @return void
     */
    private function expectationsForBuildingBackUrl(string $backUrl): void
    {
        $this->urlMock->expects($this->once())
            ->method('getUrl')
            ->with('paylane/secureForm/handleSaleTransaction/')
            ->willReturn($backUrl);
    }

    /**
     * @param string $locale
     * @return void
     */
    private function expectationsForResolvingLanguage(string $locale): void
    {
        $this->resolverMock->expects($this->once())
            ->method('getLocale')
            ->willReturn($locale);
    }
}
