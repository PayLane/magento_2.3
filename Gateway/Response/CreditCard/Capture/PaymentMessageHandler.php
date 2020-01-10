<?php

declare(strict_types=1);

/**
 * File: PaymentMessageHandler.php
 *
 
 
 */

namespace PeP\PaymentGateway\Gateway\Response\CreditCard\Capture;

use PeP\PaymentGateway\Gateway\Reader\SubjectReader;
use Magento\Payment\Gateway\Command\CommandException;
use Magento\Payment\Gateway\Response\HandlerInterface;
use Magento\Payment\Model\InfoInterface;
use Magento\Sales\Model\Order\Payment;

/**
 * Class PaymentMessageHandler
 * @package PeP\PaymentGateway\Gateway\Response\CreditCard\Capture
 */
class PaymentMessageHandler implements HandlerInterface
{
    /**
     * @var string
     */
    private const ID_SALE_PARAM = 'id_sale';

    /**
     * @var SubjectReader
     */
    private $subjectReader;

    /**
     * CommentsHistoryUpdater constructor.
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
     * @throws CommandException
     */
    public function handle(array $handlingSubject, array $response): void
    {
        $paymentDO = $this->subjectReader->readPayment($handlingSubject);

        /** @var InfoInterface $paymentInfo */
        $paymentInfo = $paymentDO->getPayment();

        if ($this->subjectReader->wasRequestSuccessful($response)) {
            $saleId = (string) $paymentInfo->getAdditionalInformation(self::ID_SALE_PARAM);
            $comment = __('Payment handled via PayLane module | Transaction ID: %1', $saleId)->render();
            if ($paymentInfo instanceof Payment) {
                $paymentInfo->getOrder()->addCommentToStatusHistory($comment);
            }
        } else {
            if ($paymentInfo instanceof Payment) {
                $comment = __(
                    'Payment handled via PayLane module | Error (%1): %2',
                    $this->subjectReader->readErrorNumber($response),
                    $this->subjectReader->readErrorDescription($response)
                )->render();
                $paymentInfo->getOrder()->addCommentToStatusHistory($comment);
                $paymentInfo->setIsTransactionPending(true);
            }
        }
    }
}
