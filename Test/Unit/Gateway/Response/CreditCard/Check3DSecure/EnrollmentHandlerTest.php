<?php

declare(strict_types=1);

/**
 * File: EnrollmentHandlerTest.php
 *
 
 
 */

namespace PeP\PaymentGateway\Test\Unit\Gateway\Response\CreditCard\Check3DSecure;

use PeP\PaymentGateway\Gateway\Response\CreditCard\Check3DSecure\EnrollmentHandler;
use PeP\PaymentGateway\Test\Unit\Gateway\Response\ResponseHandlerTestCase;
use Magento\Payment\Gateway\Command\CommandException;
use Magento\Sales\Model\Order;

/**
 * Class EnrollmentHandlerTest
 * @package PeP\PaymentGateway\Test\Unit\Gateway\Response\CreditCard\Check3DSecure
 */
class EnrollmentHandlerTest extends ResponseHandlerTestCase
{
    /**
     * @var string
     */
    private const IS_CARD_ENROLLED = 'is_card_enrolled';

    /**
     * @var EnrollmentHandler
     */
    private $enrollmentHandler;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->enrollmentHandler = new EnrollmentHandler($this->subjectReaderMock);
    }

    /**
     * @test
     * @dataProvider boolean
     *
     * @param bool $isCardEnrolled
     * @return void
     * @throws CommandException
     */
    public function testHandleCorrectlyProcessEnrollment(bool $isCardEnrolled): void
    {
        $subject = [$this->paymentDataObjectMock];
        $response = [
            self::IS_CARD_ENROLLED => $isCardEnrolled,
            'success' => true
        ];

        $this->expectationsForReadingPaymentDO($subject);
        $this->expectationsForGettingPaymentInfo();

        $valuesReturned = [$isCardEnrolled];
        $keys = [
            [self::IS_CARD_ENROLLED]
        ];

        $this->expectationsForGettingAdditionalInformation($valuesReturned, ... $keys);

        if (!$isCardEnrolled) {
            $this->expectationsForGettingOrderModel();
            $this->expectationsForAddingCommentToHistory(
                'Payment handled via PayLane module | Error: Card not enrolled to 3-D Secure program'
            );
        }

        $this->enrollmentHandler->handle($subject, $response);
    }
}
