<?php

declare(strict_types=1);

/**
 * File: RedirectTo3DSecureProviderPageTest.php
 *
 
 
 */

namespace PeP\PaymentGateway\Test\Unit\Controller\CreditCard;

use InvalidArgumentException;
use PeP\PaymentGateway\Api\Config\GeneralConfigProviderInterface;
use PeP\PaymentGateway\Controller\CreditCard\RedirectTo3DSecureProviderPage;
use PeP\PaymentGateway\Model\Order\CaptureOperationWrapper;
use PeP\PaymentGateway\Test\Unit\Controller\PaymentRedirectControllersTestCase;
use Magento\Checkout\Model\DefaultConfigProvider;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\Data\OrderPaymentInterface;
use Magento\Sales\Model\Order\Payment;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

/**
 * Class RedirectTo3DSecureProviderPageTest
 * @package PeP\PaymentGateway\Test\Unit\Controller\CreditCard
 */
class RedirectTo3DSecureProviderPageTest extends PaymentRedirectControllersTestCase
{
    /**
     * @var string
     */
    private const IS_CARD_ENROLLED = 'is_card_enrolled';

    /**
     * @var string
     */
    private const REDIRECT_URL_PARAM = 'redirect_url';

    /**
     * @var RedirectTo3DSecureProviderPage
     */
    private $redirectTo3DSecureProviderPage;

    /**
     * @var DefaultConfigProvider|MockObject
     */
    private $defaultConfigProviderMock;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->defaultConfigProviderMock = $this->getMockBuilder(DefaultConfigProvider::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->expectationsForBuildingContext();

        $this->redirectTo3DSecureProviderPage = new RedirectTo3DSecureProviderPage(
            $this->generalConfigProviderMock,
            $this->captureOperationWrapperMock,
            $this->subjectReaderMock,
            $this->defaultConfigProviderMock,
            $this->sessionMock,
            $this->contextMock
        );
    }

    /**
     * @test
     *
     * @return void
     */
    public function testExecuteWhenThereIsNoInfoAboutEnrollmentIn3DSecure(): void
    {
        $this->preliminaryExpectations();
        $additionalInfo = [];
        $this->expectationsForGettingAdditionalInformation($additionalInfo);

        $this->expectationsForAddingErrorMessage('Transaction has been declined. Please try again later.');
        $this->expectationsForConfiguringRedirectToFailure();

        $this->assertInstanceOf(Redirect::class, $this->redirectTo3DSecureProviderPage->execute());
    }

    /**
     * @test
     *
     * @return void
     */
    public function testExecuteWhenCardIsEnrolledIn3DSecureButThereIsNoRedirectUrl(): void
    {
        $this->preliminaryExpectations();
        $additionalInfo = [self::IS_CARD_ENROLLED => true];
        $this->expectationsForGettingAdditionalInformation($additionalInfo);
        $this->expectationsForAddingErrorMessage('Transaction has been declined. Please try again later.');
        $this->expectationsForConfiguringRedirectToFailure();

        $this->assertInstanceOf(Redirect::class, $this->redirectTo3DSecureProviderPage->execute());
    }

    /**
     * @test
     *
     * @return void
     */
    public function testExecuteWhenCardIsEnrolledIn3DSecure(): void
    {
        $redirectUrl = 'http://test.com';

        $this->preliminaryExpectations();
        $additionalInfo = [self::IS_CARD_ENROLLED => true, self::REDIRECT_URL_PARAM => $redirectUrl];
        $this->expectationsForGettingAdditionalInformation($additionalInfo);
        $this->resultRedirectMock->expects($this->once())
            ->method('setUrl')
            ->with($redirectUrl);
        $this->assertInstanceOf(Redirect::class, $this->redirectTo3DSecureProviderPage->execute());
    }

    /**
     * @test
     *
     * @return void
     */
    public function testExecuteWhenCardIsNotEnrolledIn3DSecureButCaptureOperationWrapperThrowsException(): void
    {
        $errorMessage = 'Transaction has been declined. Please try again later.';

        $this->preliminaryExpectations();
        $additionalInfo = [self::IS_CARD_ENROLLED => false];
        $this->expectationsForGettingAdditionalInformation($additionalInfo);
        $this->captureOperationWrapperMock->expects($this->once())
            ->method('capture')
            ->with($this->orderMock)
            ->willThrowException(new LocalizedException(__($errorMessage)));
        $this->expectationsForAddingErrorMessage($errorMessage);
        $this->expectationsForConfiguringRedirectToFailure();

        $this->assertInstanceOf(Redirect::class, $this->redirectTo3DSecureProviderPage->execute());
    }

    /**
     * @test
     *
     * @return void
     */
    public function testExecuteWhenCardIsNotEnrolledIn3DSecureAndPaymentIsSuccessfullyCaptured(): void
    {
        $successUrl = 'checkout/sth/sth';

        $this->preliminaryExpectations();
        $additionalInfo = [self::IS_CARD_ENROLLED => false];
        $this->expectationsForGettingAdditionalInformation($additionalInfo);
        $this->captureOperationWrapperMock->expects($this->once())
            ->method('capture')
            ->with($this->orderMock);
        $this->defaultConfigProviderMock->expects($this->once())
            ->method('getDefaultSuccessPageUrl')
            ->willReturn($successUrl);
        $this->resultRedirectMock->expects($this->once())
            ->method('setUrl')
            ->with($successUrl);
        $this->assertInstanceOf(Redirect::class, $this->redirectTo3DSecureProviderPage->execute());
    }

    /**
     * @return void
     */
    private function preliminaryExpectations(): void
    {
        $this->expectationsForCreatingRedirect();
        $this->expectationsForGettingLastOrder();
        $this->expectationsForGettingPayment();
    }
}
