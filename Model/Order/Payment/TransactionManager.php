<?php

declare(strict_types=1);

/**
 * File: TransactionManager.php
 *
 
 
 */

namespace PeP\PaymentGateway\Model\Order\Payment;

use PeP\PaymentGateway\Api\Order\Payment\TransactionManagerInterface;
use PeP\PaymentGateway\Api\Order\Payment\TransactionProviderInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\Data\OrderPaymentInterface;
use Magento\Sales\Api\Data\TransactionInterface;
use Magento\Sales\Api\TransactionRepositoryInterface;

/**
 * Class TransactionManager
 * @package PeP\PaymentGateway\Model\Order\Payment
 */
class TransactionManager implements TransactionManagerInterface
{
    /**
     * @var TransactionProviderInterface
     */
    private $transactionProvider;

    /**
     * @var TransactionRepositoryInterface
     */
    private $transactionRepository;

    /**
     * TransactionManager constructor.
     * @param TransactionProviderInterface $transactionProvider
     * @param TransactionRepositoryInterface $transactionRepository
     * @SuppressWarnings(PHPMD.LongVariable)
     */
    public function __construct(
        TransactionProviderInterface $transactionProvider,
        TransactionRepositoryInterface $transactionRepository
    ) {
        $this->transactionProvider = $transactionProvider;
        $this->transactionRepository = $transactionRepository;
    }

    /**
     * @param OrderPaymentInterface $payment
     * @return void
     */
    public function closeLastTxn(OrderPaymentInterface $payment): void
    {
        try {
            $transaction = $this->transactionProvider->getByTxnId($payment, $payment->getLastTransId());
            if ($transaction instanceof TransactionInterface) {
                $transaction->setIsClosed(1);
                $this->transactionRepository->save($transaction);
            }
        } catch (LocalizedException $exception) {
            //It is nothing to bo closed
        }
    }
}
