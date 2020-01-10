<?php

declare(strict_types=1);

/**
 * File: TransactionHandlerTest.php
 *
 
 
 */

namespace PeP\PaymentGateway\Test\Unit\Gateway\Response\CreditCard\Capture;

use PeP\PaymentGateway\Gateway\Response\CreditCard\Capture\TransactionHandler;
use PeP\PaymentGateway\Test\Unit\Gateway\Response\ResponseHandlerTestCase;
use Magento\Payment\Gateway\Command\CommandException;

/**
 * Class TransactionHandlerTest
 * @package PeP\PaymentGateway\Test\Unit\Gateway\Response\CreditCard\Capture
 */
class TransactionHandlerTest extends ResponseHandlerTestCase
{
    /**
     * @var string
     */
    private const ID_SALE_PARAM = 'id_sale';

    /**
     * @var TransactionHandler
     */
    private $transactionHandler;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->transactionHandler = new TransactionHandler($this->subjectReaderMock);
    }

    /**
     * @test
     *
     * @return void
     * @throws CommandException
     */
    public function testHandleCorrectlyProcessTransaction(): void
    {
        $idSale = '3542d';

        $subject = [$this->paymentDataObjectMock];
        $response = [self::ID_SALE_PARAM => $idSale, 'success' => true];

        $this->expectationsForReadingPaymentDO($subject);
        $this->expectationsForGettingPaymentInfo();

        $this->expectationsForSettingTransactionId($idSale);
        $this->expectationsForClosingTransaction(true);
        $this->expectationsForClosingParentTransaction(true);

        $this->expectationsForSettingAdditionalInformation([self::ID_SALE_PARAM, $idSale]);

        $this->transactionHandler->handle($subject, $response);
    }
}
