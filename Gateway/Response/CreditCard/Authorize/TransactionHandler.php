<?php

declare(strict_types=1);

/**
 * File: TransactionHandler.php
 *
 
 
 */

namespace PeP\PaymentGateway\Gateway\Response\CreditCard\Authorize;

use PeP\PaymentGateway\Gateway\Reader\SubjectReader;
use Magento\Framework\Exception\LocalizedException;
use Magento\Payment\Gateway\Command\CommandException;
use Magento\Payment\Gateway\Response\HandlerInterface;
use Magento\Payment\Model\InfoInterface;
use Magento\Sales\Model\Order\Payment;

/**
 * Class TransactionIdHandler
 * @package Magento\Braintree\Gateway\Response
 */
class TransactionHandler implements HandlerInterface
{
    /**
     * @var string
     */
    private const ID_AUTHORIZATION_PARAM = 'id_authorization';

    /**
     * @var SubjectReader
     */
    private $subjectReader;

    /**
     * TransactionIdHandler constructor.
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
        $paymentInfo = $paymentDO->getPayment();

        $idAuthorization = $this->subjectReader->readField($response, self::ID_AUTHORIZATION_PARAM);

        if ($paymentInfo instanceof Payment) {
            $paymentInfo->setTransactionId($idAuthorization);
            $paymentInfo->setIsTransactionClosed($this->shouldCloseTransaction());
            $paymentInfo->setShouldCloseParentTransaction($this->shouldCloseParentTransaction());

            if (!$this->subjectReader->wasRequestSuccessful($response)) {
                $paymentInfo->setIsTransactionPending(true);
            }
        }

        $this->setAuthorizationIdOnPayment($paymentInfo, (string) $idAuthorization);
    }

    /**
     * @param InfoInterface $paymentInfo
     * @param string $idAuthorization
     * @return void
     */
    private function setAuthorizationIdOnPayment(InfoInterface $paymentInfo, string $idAuthorization): void
    {
        $paymentInfo->setAdditionalInformation(self::ID_AUTHORIZATION_PARAM, $idAuthorization);
    }

    /**
     * @return bool
     */
    private function shouldCloseTransaction(): bool
    {
        return false;
    }

    /**
     * @return bool
     */
    private function shouldCloseParentTransaction(): bool
    {
        return false;
    }
}
