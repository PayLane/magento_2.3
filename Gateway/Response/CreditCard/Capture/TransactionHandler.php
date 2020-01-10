<?php

declare(strict_types=1);

/**
 * File: TransactionHandler.php
 *
 
 
 */

namespace PeP\PaymentGateway\Gateway\Response\CreditCard\Capture;

use PeP\PaymentGateway\Gateway\Reader\SubjectReader;
use Magento\Framework\Exception\LocalizedException;
use Magento\Payment\Gateway\Command\CommandException;
use Magento\Payment\Gateway\Response\HandlerInterface;
use Magento\Payment\Model\InfoInterface;
use Magento\Sales\Api\Data\OrderPaymentInterface;
use Magento\Sales\Model\Order\Payment;

/**
 * Class TransactionHandler
 * @package PeP\PaymentGateway\Gateway\Response\CreditCard
 */
class TransactionHandler implements HandlerInterface
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
     * Constructor
     *
     * @param SubjectReader $subjectReader
     */
    public function __construct(SubjectReader $subjectReader)
    {
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
        /** @var OrderPaymentInterface | InfoInterface $paymentInfo */
        $paymentInfo = $paymentDO->getPayment();

        $idSale = $this->subjectReader->readField($response, self::ID_SALE_PARAM);

        if ($paymentInfo instanceof Payment) {
            $paymentInfo->setTransactionId($idSale);
            $paymentInfo->setIsTransactionClosed($this->shouldCloseTransaction());
            $paymentInfo->setShouldCloseParentTransaction($this->shouldCloseParentTransaction());
        }

        $this->setSaleIdOnPayment($paymentInfo, (string) $idSale);
    }

    /**
     * @param InfoInterface $paymentInfo
     * @param string $idSale
     * @return void
     */
    private function setSaleIdOnPayment(InfoInterface $paymentInfo, string $idSale): void
    {
        $paymentInfo->setAdditionalInformation(self::ID_SALE_PARAM, $idSale);
    }

    /**
     * @return bool
     */
    private function shouldCloseTransaction(): bool
    {
        return true;
    }

    /**
     * @return bool
     */
    private function shouldCloseParentTransaction(): bool
    {
        return true;
    }
}
