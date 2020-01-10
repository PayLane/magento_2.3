<?php

declare(strict_types=1);

/**
 * File: TransactionHandlerTest.php
 *
 
 
 */

namespace PeP\PaymentGateway\Test\Unit\Gateway\Response\CreditCard\Check3DSecure;

use PeP\PaymentGateway\Gateway\Response\CreditCard\Check3DSecure\TransactionHandler;
use PeP\PaymentGateway\Test\Unit\Gateway\Response\ResponseHandlerTestCase;
use Magento\Payment\Gateway\Command\CommandException;

/**
 * Class TransactionHandlerTest
 * @package PeP\PaymentGateway\Test\Unit\Gateway\Response\CreditCard\Check3DSecure
 */
class TransactionHandlerTest extends ResponseHandlerTestCase
{
    /**
     * @var string
     */
    private const IS_CARD_ENROLLED = 'is_card_enrolled';

    /**
     * @var string
     */
    private const ID_3DSECURE_AUTH_PARAM = 'id_3dsecure_auth';

    /**
     * @var string
     */
    private const REDIRECT_URL_PARAM = 'redirect_url';

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
     * @param bool $hasRedirectUrl
     * @return void
     * @throws CommandException
     */
    public function testHandleCorrectlyProcessTransaction(bool $hasRedirectUrl): void
    {
        $id3DSecureAuth = '3542d';
        $redirectUrl = 'http://test.com';
        $isCardEnrolled = true;
        $params = [];

        $subject = [$this->paymentDataObjectMock];
        $response = [
            self::IS_CARD_ENROLLED => $isCardEnrolled,
            self::ID_3DSECURE_AUTH_PARAM => $id3DSecureAuth,
            'success' => true
        ];

        if ($hasRedirectUrl) {
            $response[self::REDIRECT_URL_PARAM] = $redirectUrl;
            $params[] = [self::REDIRECT_URL_PARAM, $redirectUrl];
        }

        $this->expectationsForReadingPaymentDO($subject);
        $this->expectationsForGettingPaymentInfo();

        $this->expectationsForSettingTransactionId($id3DSecureAuth);
        $this->expectationsForClosingTransaction(false);
        $this->expectationsForClosingParentTransaction(false);


        if (!$isCardEnrolled) {
            $this->expectationsForSettingTransactionAsPending();
        }

        $params[] = [self:: IS_CARD_ENROLLED, $isCardEnrolled];
        $params[] = [self::ID_3DSECURE_AUTH_PARAM, $id3DSecureAuth];

        $this->expectationsForSettingAdditionalInformation(... $params);

        $this->transactionHandler->handle($subject, $response);
    }
}
