<?php

declare(strict_types=1);

/**
 * File: Handle3DSecureTransactionTest.php
 *
 
 
 */

namespace PeP\PaymentGateway\Test\Unit\Controller\CreditCard;

use PeP\PaymentGateway\Api\Order\Payment\TransactionManagerInterface;
use PeP\PaymentGateway\Controller\CreditCard\Handle3DSecureTransaction;
use PeP\PaymentGateway\Model\Notification\Data;
use PeP\PaymentGateway\Test\Unit\Controller\PaymentRedirectControllersTestCase;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Exception\LocalizedException;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

/**
 * Class Handle3DSecureTransactionTest
 * @package PeP\PaymentGateway\Test\Unit\Controller\CreditCard
 */
class Handle3DSecureTransactionTest extends PaymentRedirectControllersTestCase
{
    /**
     * @var string
     */
    private const ID_3DSECURE_AUTH_PARAM = 'id_3dsecure_auth';

    /**
     * @var string
     */
    private const ERROR_CODE_PARAM = 'error_code';

    /**
     * @var string
     */
    private const ERROR_TEXT_PARAM = 'error_text';

    /**
     * @var TransactionManagerInterface|MockObject
     */
    private $transactionManagerMock;

    /**
     * @var Handle3DSecureTransaction
     */
    private $handle3DSecureTransaction;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        //Dependencies mocks
        $this->transactionManagerMock = $this->getMockBuilder(TransactionManagerInterface::class)->getMock();

        $this->expectationsForBuildingContext();

        $this->handle3DSecureTransaction = new Handle3DSecureTransaction(
            $this->generalConfigProviderMock,
            $this->transactionManagerMock,
            $this->captureOperationWrapperMock,
            $this->subjectReaderMock,
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
        $errorCode = '456';
        $errorText = 'error';

        $this->expectationsForCreatingRedirect();
        $this->expectationsForGettingStatusParam(Data::STATUS_ERROR);
        $this->expectationsForGettingLastOrder();
        $this->expectationsForGettingPayment();

        $this->requestMock->expects($this->once())
            ->method('getParams')
            ->willReturn(
                [
                    self::ERROR_CODE_PARAM => $errorCode,
                    self::ERROR_TEXT_PARAM => $errorText
                ]
            );
        $this->transactionManagerMock->expects($this->once())
            ->method('closeLastTxn')
            ->with(
                $this->paymentMock
            );
        $this->expectationsForAddingCommentToHistory(
            'Payment handled via PayLane module | Error (%1): %2',
            $errorCode,
            $errorText
        );
        $this->expectationsForAddingErrorMessage($errorText);

        $this->expectationsForConfiguringRedirectToFailure();
        $this->handle3DSecureTransaction->execute();
    }

    /**
     * @test
     *
     * @return void
     * @SuppressWarnings(PHPMD.LongVariable)
     */
    public function testExecuteWhenRequestStatusIsNotErrorAndCaptureOperationWrapperThrowsException(): void
    {
        $idSecureAuth = '455dsd';
        $errorMessage = 'fdsfgsd';

        $this->expectationsForCreatingRedirect();
        $this->expectationsForGettingStatusParam(Data::STATUS_CLEARED);
        $this->expectationsForGettingLastOrder();
        $this->expectationsForGettingPayment();

        $additionalInformation = [
            self::ID_3DSECURE_AUTH_PARAM => $idSecureAuth
        ];

        $this->expectationsForGettingAdditionalInformation($additionalInformation);
        $this->expectationsForAddingCommentToHistory(
            'Payment handled via PayLane module | Card Authorized, Authorization ID: %1',
            $idSecureAuth
        );
        $this->captureOperationWrapperMock->expects($this->once())
            ->method('capture')
            ->with($this->orderMock)
            ->willThrowException(new LocalizedException(__($errorMessage)));
        $this->expectationsForAddingErrorMessage($errorMessage);
        $this->expectationsForConfiguringRedirectToFailure();

        $this->handle3DSecureTransaction->execute();
    }


    /**
     * @test
     *
     * @return void
     * @SuppressWarnings(PHPMD.LongVariable)
     */
    public function testExecuteWhenRequestStatusIsNotError(): void
    {
        $idSecureAuth = '455dsd';

        $this->expectationsForCreatingRedirect();
        $this->expectationsForGettingStatusParam(Data::STATUS_CLEARED);
        $this->expectationsForGettingLastOrder();
        $this->expectationsForGettingPayment();

        $additionalInformation = [
            self::ID_3DSECURE_AUTH_PARAM => $idSecureAuth
        ];

        $this->expectationsForGettingAdditionalInformation($additionalInformation);
        $this->expectationsForAddingCommentToHistory(
            'Payment handled via PayLane module | Card Authorized, Authorization ID: %1',
            $idSecureAuth
        );
        $this->captureOperationWrapperMock->expects($this->once())
            ->method('capture')
            ->with($this->orderMock);
        $this->expectationsForConfiguringRedirectToSuccess();

        $this->handle3DSecureTransaction->execute();
    }

    /**
     * @test
     *
     * @return void
     */
    public function testCreateCsrfValidationExceptionReturnsCorrectlyConfiguredException(): void
    {
        $this->expectationsForCreatingRedirect();
        $this->expectationsForConfiguringRedirectToFailure();
        $result = $this->handle3DSecureTransaction->createCsrfValidationException($this->requestMock);
        $this->assertInstanceOf(
            InvalidRequestException::class,
            $result
        );
        $this->assertInstanceOf(Redirect::class, $result->getReplaceResult());
    }

    /**
     * @test
     *
     * @return void
     */
    public function testValidateForCsrfCorrectlyValidatesRequest(): void
    {
        $isValid = true;
        $this->expectationsForRequestValidation($isValid);
        $this->assertSame($isValid, $this->handle3DSecureTransaction->validateForCsrf($this->requestMock));
    }
}
