<?php

declare(strict_types=1);

/**
 * File: PaymentMessageHandlerTest.php
 *
 
 
 */

namespace PeP\PaymentGateway\Test\Unit\Gateway\Response\CreditCard\Capture;

use PeP\PaymentGateway\Gateway\Response\CreditCard\Capture\PaymentMessageHandler;
use PeP\PaymentGateway\Test\Unit\Gateway\Response\ResponseHandlerTestCase;
use Magento\Payment\Gateway\Command\CommandException;

/**
 * Class PaymentMessageHandlerTest
 * @package PeP\PaymentGateway\Test\Unit\Gateway\Response\CreditCard\Capture
 */
class PaymentMessageHandlerTest extends ResponseHandlerTestCase
{
    /**
     * @var string
     */
    private const ID_SALE_PARAM = 'id_sale';

    /**
     * @var PaymentMessageHandler
     */
    private $paymentMessageHandler;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->paymentMessageHandler = new PaymentMessageHandler($this->subjectReaderMock);
    }

    /**
     * @test
     *
     * @return void
     * @throws CommandException
     */
    public function testHandleWhenRequestWasSuccessful(): void
    {
        $idSale = '3542d';

        $subject = [$this->paymentDataObjectMock];
        $response = [self::ID_SALE_PARAM => $idSale, 'success' => true];

        $this->expectationsForReadingPaymentDO($subject);
        $this->expectationsForGettingPaymentInfo();

        $this->expectationsForGettingAdditionalInformation([$idSale], [self::ID_SALE_PARAM]);
        $this->expectationsForGettingOrderModel();
        $this->expectationsForAddingCommentToHistory(
            'Payment handled via PayLane module | Transaction ID: %1',
            $idSale
        );

        $this->paymentMessageHandler->handle($subject, $response);
    }

    /**
     * @test
     *
     * @return void
     * @throws CommandException
     */
    public function testHandleWhenRequestWasNotSuccessful(): void
    {
        $idSale = '3542d';
        $errorCode = 454;
        $errorMsg = 'Some error';

        $subject = [$this->paymentDataObjectMock];
        $response = [
            self::ID_SALE_PARAM => $idSale,
            'success' => false,
            'error' => [
                'error_number' => $errorCode,
                'error_description' => $errorMsg
            ]
        ];

        $this->expectationsForReadingPaymentDO($subject);
        $this->expectationsForGettingPaymentInfo();

        $this->expectationsForGettingOrderModel();
        $this->expectationsForAddingCommentToHistory(
            'Payment handled via PayLane module | Error (%1): %2',
            (string) $errorCode,
            $errorMsg
        );

        $this->expectationsForSettingTransactionAsPending();

        $this->paymentMessageHandler->handle($subject, $response);
    }
}

