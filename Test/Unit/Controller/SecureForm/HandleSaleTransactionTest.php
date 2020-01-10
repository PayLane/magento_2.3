<?php

declare(strict_types=1);

/**
 * File: HandleSaleTransactionTest.php
 *
 
 
 */

namespace PeP\PaymentGateway\Test\Unit\Controller\SecureForm;

use PeP\PaymentGateway\Controller\SecureForm\HandleSaleTransaction;
use PeP\PaymentGateway\Model\Notification\Data;
use PeP\PaymentGateway\Test\Unit\Controller\PaymentRedirectControllersTestCase;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\Data\OrderPaymentInterface;
use Magento\Sales\Model\Order\Payment;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

/**
 * Class HandleSaleTransactionTest
 * @package PeP\PaymentGateway\Test\Unit\Controller\SecureForm
 */
class HandleSaleTransactionTest extends PaymentRedirectControllersTestCase
{
    /**
     * It's public as it is used in di.xml configuration
     * @var string
     */
    public const ID_SALE_PARAM = 'id_sale';

    /**
     * @var string
     */
    private const ERROR_TEXT_PARAM = 'error_text';

    /**
     * @var HandleSaleTransaction
     */
    private $handleSaleTransaction;

    /**
     * @var Payment|MockObject
     */
    protected $paymentMock;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->expectationsForBuildingContext();

        $this->paymentMock = $this->getMockBuilder(Payment::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->handleSaleTransaction = new HandleSaleTransaction(
            $this->generalConfigProviderMock,
            $this->captureOperationWrapperMock,
            $this->backRequestValidatorCompositeMock,
            $this->sessionMock,
            $this->contextMock
        );
    }

    /**
     * @test
     *
     * @return void
     */
    public function testExecuteWhenRequestStatusIsError(): void
    {
        $errorText = 'error';

        $this->expectationsForCreatingRedirect();
        $this->expectationsForGettingLastOrder();
        $this->expectationsForGettingPayment();

        $this->requestMock->expects($this->exactly(2))
            ->method('getParam')
            ->withConsecutive([self::STATUS_PARAM, ''], [self::ERROR_TEXT_PARAM, ''])
            ->willReturnOnConsecutiveCalls(Data::STATUS_ERROR, $errorText);
        $this->expectationsForAddingCommentToHistory(
            'Payment handled via PayLane module | Error: %1',
            $errorText
        );
        $this->expectationsForAddingErrorMessage($errorText);

        $this->expectationsForConfiguringRedirectToFailure();
        $this->handleSaleTransaction->execute();
    }

    /**
     * @test
     *
     * @return void
     * @SuppressWarnings(PHPMD.LongVariable)
     */
    public function testExecuteWhenRequestStatusIsNotErrorAndCaptureOperationWrapperThrowsException(): void
    {
        $idSale = '455dsd';
        $errorMessage = 'fdsfgsd';

        $this->expectationsForCreatingRedirect();
        $this->requestMock->expects($this->exactly(3))
            ->method('getParam')
            ->withConsecutive([self::STATUS_PARAM, ''], [self::ID_SALE_PARAM, ''], [self::ID_SALE_PARAM, ''])
            ->willReturnOnConsecutiveCalls(Data::STATUS_PERFORMED, $idSale, $idSale);
        $this->expectationsForGettingLastOrder();
        $this->expectationsForGettingPayment(2);

        $this->expectationsForAddingCommentToHistory(
            'Payment handled via PayLane module | Transaction ID: %1',
            $idSale
        );
        $this->paymentMock->expects($this->once())
            ->method('setTransactionId')
            ->with($idSale);

        $this->captureOperationWrapperMock->expects($this->once())
            ->method('capture')
            ->with($this->orderMock)
            ->willThrowException(new LocalizedException(__($errorMessage)));
        $this->expectationsForAddingErrorMessage($errorMessage);
        $this->expectationsForConfiguringRedirectToFailure();

        $this->handleSaleTransaction->execute();
    }


    /**
     * @test
     *
     * @dataProvider provideDifferentStatuses
     *
     * @param string $status
     * @param bool $isTransactionPending
     * @return void
     * @SuppressWarnings(PHPMD.LongVariable)
     */
    public function testExecuteWhenRequestStatusIsNotError(string $status, bool $isTransactionPending): void
    {
        $idSale = '455dsd';

        $this->expectationsForCreatingRedirect();
        $this->requestMock->expects($this->exactly(3))
            ->method('getParam')
            ->withConsecutive([self::STATUS_PARAM, ''], [self::ID_SALE_PARAM, ''], [self::ID_SALE_PARAM, ''])
            ->willReturnOnConsecutiveCalls($status, $idSale, $idSale);
        $this->expectationsForGettingLastOrder();
        $this->expectationsForGettingPayment(2);

        $this->expectationsForAddingCommentToHistory(
            'Payment handled via PayLane module | Transaction ID: %1',
            $idSale
        );
        $this->paymentMock->expects($this->once())
            ->method('setTransactionId')
            ->with($idSale);

        if ($status === Data::STATUS_PENDING) {
            $this->paymentMock->expects($this->once())
                ->method('setIsTransactionClosed')
                ->with(false)
                ->willReturnSelf();
        }

        $this->paymentMock->expects($this->once())
            ->method('setIsTransactionPending')
            ->with($isTransactionPending)
            ->willReturnSelf();

        $this->captureOperationWrapperMock->expects($this->once())
            ->method('capture')
            ->with($this->orderMock);
        $this->expectationsForConfiguringRedirectToSuccess();

        $this->assertInstanceOf(Redirect::class, $this->handleSaleTransaction->execute());
    }

    /**
     * @return array
     */
    public function provideDifferentStatuses(): array
    {
        return [
            [Data::STATUS_PENDING, true],
            [Data::STATUS_PERFORMED, false],
            [Data::STATUS_CLEARED, false]
        ];
    }

    /**
     * @test
     * @return void
     */
    public function testCreateCsrfValidationExceptionReturnsCorrectlyConfiguredException(): void
    {
        $this->expectationsForCreatingRedirect();
        $this->expectationsForConfiguringRedirectToFailure();
        $result = $this->handleSaleTransaction->createCsrfValidationException($this->requestMock);
        $this->assertInstanceOf(
            InvalidRequestException::class,
            $result
        );
        $this->assertInstanceOf(Redirect::class, $result->getReplaceResult());
    }

    /**
     * @test
     * @return void
     */
    public function testValidateForCsrfCorrectlyValidatesRequest(): void
    {
        $isValid = true;
        $this->expectationsForRequestValidation($isValid);
        $this->assertSame($isValid, $this->handleSaleTransaction->validateForCsrf($this->requestMock));
    }
}
