<?php

declare(strict_types=1);

/**
 * File: CommandResolver.php
 *
 
 
 */

namespace PeP\PaymentGateway\Gateway\Command\CreditCard\CaptureStrategyCommand;

use Exception;
use PeP\PaymentGateway\Api\Adapter\PayLaneRestClientFactoryInterface;
use PeP\PaymentGateway\Api\Config\Methods\CreditCardConfigProviderInterface;
use PeP\PaymentGateway\Api\Order\Payment\TransactionProviderInterface;
use PeP\PaymentGateway\Gateway\Reader\SubjectReader;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Payment\Model\InfoInterface;
use Magento\Sales\Api\Data\OrderPaymentInterface;
use Magento\Sales\Api\Data\TransactionInterface;

/**
 * Class CommandResolver
 * @package PeP\PaymentGateway\Gateway\Command\CreditCard\CaptureStrategyCommand
 */
class CommandResolver
{
    /**
     * @var string
     */
    private const SALE = 'sale';

    /**
     * @var string
     */
    private const SALE_3DS = 'sale_3ds';

    /**
     * @var string
     */
    private const CAPTURE = 'settlement';

    /**
     * @var string
     */
    private const ID_AUTHORIZATION = 'id_authorization';

    /**
     * @var string
     */
    private const IS_CARD_ENROLLED = 'is_card_enrolled';

    /**
     * @var string
     */
    private const ACTIVE_AUTH_STATUS = 'ACTIVE';

    /**
     * @var PayLaneRestClientFactoryInterface
     */
    private $payLaneRestClientFactory;

    /**
     * @var CreditCardConfigProviderInterface
     */
    private $creditCardConfigProvider;

    /**
     * @var TransactionProviderInterface
     */
    private $transactionProvider;

    /**
     * @var SubjectReader
     */
    private $subjectReader;

    /**
     * CaptureStrategyCommand constructor.
     * @param PayLaneRestClientFactoryInterface $payLaneRestClientFactory
     * @param CreditCardConfigProviderInterface $creditCardConfigProvider
     * @param TransactionProviderInterface $transactionProvider
     * @param SubjectReader $subjectReader
     * @SuppressWarnings(PHPMD.LongVariable)
     */
    public function __construct(
        PayLaneRestClientFactoryInterface $payLaneRestClientFactory,
        CreditCardConfigProviderInterface $creditCardConfigProvider,
        TransactionProviderInterface $transactionProvider,
        SubjectReader $subjectReader
    ) {
        $this->payLaneRestClientFactory = $payLaneRestClientFactory;
        $this->creditCardConfigProvider = $creditCardConfigProvider;
        $this->transactionProvider = $transactionProvider;
        $this->subjectReader = $subjectReader;
    }

    /**
     * @param PaymentDataObjectInterface $paymentDO
     * @return string
     * @SuppressWarnings(PHPMD.LongVariable)
     */
    public function resolveCommandToBeUsed(PaymentDataObjectInterface $paymentDO): string
    {
        /** @var $payment OrderPaymentInterface|InfoInterface */
        $payment = $paymentDO->getPayment();

        $existsCapture = $this->isExistsCaptureTransaction($payment);

        //If there is already capture operation we do nothing
        if ($existsCapture) {
            return '';
        }

        $authorizationTransaction = $this->getAuthorizationTransaction($payment);

        // if auth transaction does not exist then execute authorize & capture (sale) command
        if (!$authorizationTransaction instanceof TransactionInterface) {
            return $this->getSaleCommand($payment);
        }

        // do capture for authorized transaction
        return $this->isExpiredAuthorization($payment)
            ? $this->getSaleCommand($payment)
            : self::CAPTURE;
    }

    /**
     * @param InfoInterface $paymentInfo
     * @return string
     */
    private function getSaleCommand(InfoInterface $paymentInfo): string
    {
        return $this->creditCardConfigProvider->is3DSCheckEnabled()
        && $paymentInfo->getAdditionalInformation(self::IS_CARD_ENROLLED)
            ? self::SALE_3DS
            : self::SALE;
    }

    /**
     * @param OrderPaymentInterface $payment
     * @return bool
     */
    private function isExistsCaptureTransaction(OrderPaymentInterface $payment): bool
    {
        $transactions = $this->transactionProvider->getByTxnType($payment, TransactionInterface::TYPE_CAPTURE);
        $count = $transactions->count();
        return (bool) $count;
    }

    /**
     * @param OrderPaymentInterface $payment
     * @return TransactionInterface|null
     */
    private function getAuthorizationTransaction(
        OrderPaymentInterface $payment
    ): ?TransactionInterface {
        $transactions = $this->transactionProvider->getByTxnType($payment, TransactionInterface::TYPE_AUTH);
        $transaction = $transactions->current();
        return $transaction instanceof TransactionInterface ? $transaction : null;
    }

    /**
     * @param InfoInterface $payment
     * @return bool
     */
    private function isExpiredAuthorization(InfoInterface $payment)
    {
        $client = $this->payLaneRestClientFactory->create();
        try {
            $idAuthorization = $payment->getAdditionalInformation(self::ID_AUTHORIZATION);
            //TODO: Getting info could be get by using fetch_transaction_info
            //TODO: (inside it should be resolve if we take auth, sale) if implemented
            $info = $client->getAuthorizationInfo([self::ID_AUTHORIZATION => $idAuthorization]);
            return !$this->isAuthActive($info);
        } catch (Exception $exception) {
            return true;
        }
    }

    /**
     * @param array $info
     * @return bool
     */
    private function isAuthActive(array $info): bool
    {
        return $this->subjectReader->wasRequestSuccessful($info)
            && $this->subjectReader->readField($info, 'status') === self::ACTIVE_AUTH_STATUS;
    }
}
