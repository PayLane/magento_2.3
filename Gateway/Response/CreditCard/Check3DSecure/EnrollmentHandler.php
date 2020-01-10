<?php

declare(strict_types=1);

/**
 * File: EnrollmentHandler.php
 *
 
 
 */

namespace PeP\PaymentGateway\Gateway\Response\CreditCard\Check3DSecure;

use PeP\PaymentGateway\Gateway\Reader\SubjectReader;
use Magento\Payment\Gateway\Command\CommandException;
use Magento\Payment\Gateway\Response\HandlerInterface;
use Magento\Sales\Model\Order\Payment;

/**
 * Class EnrollmentHandler
 * @package PeP\PaymentGateway\Gateway\Response\CreditCard\Check3DSecure
 */
class EnrollmentHandler implements HandlerInterface
{
    /**
     * @var string
     */
    private const IS_CARD_ENROLLED = 'is_card_enrolled';

    /**
     * @var SubjectReader
     */
    private $subjectReader;

    /**
     * Constructor
     *
     * @param SubjectReader $subjectReader
     * @SuppressWarnings(PHPMD.LongVariable)
     */
    public function __construct(
        SubjectReader $subjectReader
    ) {
        $this->subjectReader = $subjectReader;
    }

    /**
     * @param array $handlingSubject
     * @param array $response
     * @return void
     * @SuppressWarnings(PHPMD.LongVariable)
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @throws CommandException
     */
    public function handle(array $handlingSubject, array $response): void
    {
        $paymentDO = $this->subjectReader->readPayment($handlingSubject);
        $paymentInfo = $paymentDO->getPayment();
        $isCard3DSecureEnrolled = $paymentInfo->getAdditionalInformation(self::IS_CARD_ENROLLED);

        if (!$isCard3DSecureEnrolled) {
            if ($paymentInfo instanceof Payment) {
                $comment = __('Payment handled via PayLane module | Error: Card not enrolled to 3-D Secure program')
                    ->render();
                $paymentInfo->getOrder()->addCommentToStatusHistory($comment);
            }
        }
    }
}
