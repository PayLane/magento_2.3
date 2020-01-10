<?php

declare(strict_types=1);

/**
 * File: PaymentRedirectControllersTestCase.php
 *
 
 
 */

namespace PeP\PaymentGateway\Test\Unit\Controller;

use PeP\PaymentGateway\Api\Config\GeneralConfigProviderInterface;
use PeP\PaymentGateway\Api\Order\CaptureOperationWrapperInterface;
use PeP\PaymentGateway\Model\Request\BackRequestValidatorComposite;
use PeP\PaymentGateway\Test\Unit\Gateway\SubjectReaderTestTrait;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Message\ManagerInterface;
use Magento\Sales\Api\Data\OrderPaymentInterface;
use Magento\Sales\Model\Order;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

/**
 * Class PaymentRedirectControllersTestCase
 * @package PeP\PaymentGateway\Test\Unit\Controller
 * @SuppressWarnings(PHPMD.LongVariable)
 */
abstract class PaymentRedirectControllersTestCase extends TestCase
{
    use SubjectReaderTestTrait;

    /**
     * @var string
     */
    protected const STATUS_PARAM = 'status';

    /**
     * @var GeneralConfigProviderInterface|MockObject
     */
    protected $generalConfigProviderMock;

    /**
     * @var CaptureOperationWrapperInterface|MockObject
     */
    protected $captureOperationWrapperMock;

    /**
     * @var BackRequestValidatorComposite|MockObject
     */
    protected $backRequestValidatorCompositeMock;

    /**
     * @var Session|MockObject
     */
    protected $sessionMock;

    /**
     * @var RequestInterface|MockObject
     */
    protected $requestMock;

    /**
     * @var Context|MockObject
     */
    protected $contextMock;

    /**
     * @var Redirect|MockObject
     */
    protected $resultRedirectMock;

    /**
     * @var RedirectFactory|MockObject
     */
    protected $resultRedirectFactoryMock;

    /**
     * @var ManagerInterface|MockObject
     */
    protected $messageManagerMock;

    /**
     * @var Order|MockObject
     */
    protected $orderMock;

    /**
     * @var OrderPaymentInterface|MockObject
     */
    protected $paymentMock;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpSubjectReader();

        //Internal mocks
        $this->requestMock = $this->getMockBuilder(RequestInterface::class)->getMock();
        $this->resultRedirectMock = $this->getMockBuilder(Redirect::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->resultRedirectFactoryMock = $this->getMockBuilder(RedirectFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->messageManagerMock = $this->getMockBuilder(ManagerInterface::class)->getMock();
        $this->orderMock = $this->getMockBuilder(Order::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->paymentMock = $this->getMockBuilder(OrderPaymentInterface::class)->getMock();

        //Dependencies mocks
        $this->generalConfigProviderMock = $this->getMockBuilder(GeneralConfigProviderInterface::class)
            ->getMock();
        $this->captureOperationWrapperMock = $this->getMockBuilder(CaptureOperationWrapperInterface::class)->getMock();
        $this->backRequestValidatorCompositeMock = $this->getMockBuilder(BackRequestValidatorComposite::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->sessionMock = $this->getMockBuilder(Session::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->contextMock = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @return void
     */
    protected function expectationsForBuildingContext(): void
    {
        $this->contextMock->expects($this->once())
            ->method('getRequest')
            ->willReturn($this->requestMock);
        $this->contextMock->expects($this->once())
            ->method('getResultRedirectFactory')
            ->willReturn($this->resultRedirectFactoryMock);
        $this->contextMock->expects($this->once())
            ->method('getMessageManager')
            ->willReturn($this->messageManagerMock);
    }

    /**
     * @return void
     */
    protected function expectationsForCreatingRedirect(): void
    {
        $this->resultRedirectFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($this->resultRedirectMock);
    }

    /**
     * @return void
     */
    protected function expectationsForGettingLastOrder(): void
    {
        $this->sessionMock->expects($this->once())
            ->method('getLastRealOrder')
            ->willReturn($this->orderMock);
    }

    /**
     * @param bool $isValid
     * @return void
     */
    protected function expectationsForRequestValidation(bool $isValid): void
    {
        $this->backRequestValidatorCompositeMock->expects($this->once())
            ->method('validate')
            ->with($this->requestMock)
            ->willReturn($isValid);
    }

    /**
     * @param string $status
     * @return void
     */
    protected function expectationsForGettingStatusParam(string $status): void
    {
        $this->requestMock->expects($this->once())
            ->method('getParam')
            ->with(self::STATUS_PARAM, '')
            ->willReturn($status);
    }

    /**
     * @return void
     */
    protected function expectationsForConfiguringRedirectToFailure(): void
    {
        $this->resultRedirectMock->expects($this->once())
            ->method('setPath')
            ->with('checkout/onepage/failure')
            ->willReturnSelf();
    }

    /**
     * @return void
     */
    protected function expectationsForConfiguringRedirectToSuccess(): void
    {
        $this->resultRedirectMock->expects($this->once())
            ->method('setPath')
            ->with('checkout/onepage/success')
            ->willReturnSelf();
    }

    /**
     * @param int $count
     * @return void
     */
    protected function expectationsForGettingPayment(int $count = 1): void
    {
        $this->orderMock->expects($this->exactly($count))
            ->method('getPayment')
            ->willReturn($this->paymentMock);
    }

    /**
     * @param array $additionalInformation
     * @return void
     * @SuppressWarnings(PHPMD.LongVariable)
     */
    protected function expectationsForGettingAdditionalInformation(array $additionalInformation): void
    {
        $this->paymentMock->expects($this->once())
            ->method('getAdditionalInformation')
            ->willReturn($additionalInformation);
    }

    /**
     * @param string $comment
     * @param string ...$params
     * @return void
     */
    protected function expectationsForAddingCommentToHistory(string $comment, string ...$params)
    {
        $this->orderMock->expects($this->once())
            ->method('addCommentToStatusHistory')
            ->with(
                __(
                    $comment,
                    ... $params
                )->render()
            )->willReturnSelf();
    }

    /**
     * @param string $message
     * @return void
     */
    protected function expectationsForAddingErrorMessage(string $message): void
    {
        $this->messageManagerMock->expects($this->once())
            ->method('addErrorMessage')
            ->with($message);
    }
}
