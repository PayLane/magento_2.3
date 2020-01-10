<?php

declare(strict_types=1);

/**
 * File: TransactionHandler.php
 *
 
 
 */

namespace PeP\PaymentGateway\Gateway\Response\CreditCard\Check3DSecure;

use PeP\PaymentGateway\Gateway\Reader\SubjectReader;
use Magento\Framework\Exception\LocalizedException;
use Magento\Payment\Gateway\Command\CommandException;
use Magento\Payment\Gateway\Response\HandlerInterface;
use Magento\Payment\Model\InfoInterface;
use Magento\Sales\Api\Data\OrderPaymentInterface;
use Magento\Sales\Model\Order\Payment;

/**
 * Class TransactionHandler
 * @package PeP\PaymentGateway\Gateway\Response\CreditCard\Check3DSecure
 */
class TransactionHandler implements HandlerInterface
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

        $isCardEnrolled = (bool) $this->subjectReader->readField($response, self::IS_CARD_ENROLLED);
        $id3DSecureAuth = $this->subjectReader->readField($response, self:: ID_3DSECURE_AUTH_PARAM);

        if ($this->subjectReader->hasField($response, self::REDIRECT_URL_PARAM)) {
            $redirectUrl = $this->subjectReader->readField($response, self::REDIRECT_URL_PARAM);
            $this->setRedirectUrlOnPayment($paymentInfo, (string) $redirectUrl);
        }

        if ($paymentInfo instanceof Payment) {
            $paymentInfo->setTransactionId($id3DSecureAuth);
            $paymentInfo->setIsTransactionClosed(false);
            $paymentInfo->setShouldCloseParentTransaction(false);
        }

        $this->setIsCardEnrolledOnPayment($paymentInfo, (bool) $isCardEnrolled);
        $this->setId3DSecureAuthOnPayment($paymentInfo, (string) $id3DSecureAuth);
    }

    /**
     * @param InfoInterface $paymentInfo
     * @param bool $isCardEnrolled
     * @return void
     */
    private function setIsCardEnrolledOnPayment(InfoInterface $paymentInfo, bool $isCardEnrolled): void
    {
        $paymentInfo->setAdditionalInformation(self::IS_CARD_ENROLLED, $isCardEnrolled);
    }

    /**
     * @param InfoInterface $paymentInfo
     * @param string $idSale
     * @return void
     */
    private function setId3DSecureAuthOnPayment(InfoInterface $paymentInfo, string $idSale): void
    {
        $paymentInfo->setAdditionalInformation(self::ID_3DSECURE_AUTH_PARAM, $idSale);
    }

    /**
     * @param InfoInterface $paymentInfo
     * @param string $redirectUrl
     * @return void
     */
    private function setRedirectUrlOnPayment(InfoInterface $paymentInfo, string $redirectUrl): void
    {
        $paymentInfo->setAdditionalInformation(self::REDIRECT_URL_PARAM, $redirectUrl);
    }
}
