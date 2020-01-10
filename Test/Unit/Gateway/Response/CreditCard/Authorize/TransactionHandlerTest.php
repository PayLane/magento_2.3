<?php

declare(strict_types=1);

/**
 * File: TransactionHandlerTest.php
 *
 
 
 */

namespace PeP\PaymentGateway\Test\Unit\Gateway\Response\CreditCard\Authorize;

use PeP\PaymentGateway\Gateway\Response\CreditCard\Authorize\TransactionHandler;
use PeP\PaymentGateway\Test\Unit\Gateway\Response\ResponseHandlerTestCase;
use Magento\Payment\Gateway\Command\CommandException;

/**
 * Class TransactionHandlerTest
 * @package PeP\PaymentGateway\Test\Unit\Gateway\Response\CreditCard\Authorize
 */
class TransactionHandlerTest extends ResponseHandlerTestCase
{
    /**
     * @var string
     */
    private const ID_AUTHORIZATION_PARAM = 'id_authorization';

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
     * @dataProvider boolean
     *
     * @param bool $wasRequestSuccessful
     * @return void
     * @throws CommandException
     */
    public function testHandleCorrectlyProcessTransaction(bool $wasRequestSuccessful): void
    {
        $idAuthorization = '3542d';

        $subject = [$this->paymentDataObjectMock];
        $response = [self::ID_AUTHORIZATION_PARAM => $idAuthorization, 'success' => $wasRequestSuccessful];

        $this->expectationsForReadingPaymentDO($subject);
        $this->expectationsForGettingPaymentInfo();

        $this->expectationsForSettingTransactionId($idAuthorization);
        $this->expectationsForClosingTransaction(false);
        $this->expectationsForClosingParentTransaction(false);


        if (!$wasRequestSuccessful) {
            $this->expectationsForSettingTransactionAsPending();
        }

        $this->expectationsForSettingAdditionalInformation([self::ID_AUTHORIZATION_PARAM, $idAuthorization]);

        $this->transactionHandler->handle($subject, $response);
    }
}
