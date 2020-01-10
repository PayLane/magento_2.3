<?php

declare(strict_types=1);

/**
 * File: TransactionProvider.php
 *
 
 
 */

namespace PeP\PaymentGateway\Model\Order\Payment;

use PeP\PaymentGateway\Api\Order\Payment\TransactionProviderInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Framework\Data\Collection;
use Magento\Sales\Api\Data\OrderPaymentInterface;
use Magento\Sales\Api\Data\TransactionInterface;
use Magento\Sales\Api\TransactionRepositoryInterface;
use MSlwk\TypeSafeArray\ObjectArray;
use MSlwk\TypeSafeArray\ObjectArrayFactory;

/**
 * Class TransactionProvider
 * @package PeP\PaymentGateway\Model\Order\Payment
 */
class TransactionProvider implements TransactionProviderInterface
{
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var SortOrderBuilder
     */
    private $sortOrderBuilder;

    /**
     * @var TransactionRepositoryInterface
     */
    private $transactionRepository;

    /**
     * @var ObjectArrayFactory
     */
    private $objectArrayFactory;

    /**
     * TransactionProvider constructor.
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param SortOrderBuilder $sortOrderBuilder
     * @param TransactionRepositoryInterface $repository
     * @param ObjectArrayFactory $objectArrayFactory
     * @SuppressWarnings(PHPMD.LongVariable)
     */
    public function __construct(
        SearchCriteriaBuilder $searchCriteriaBuilder,
        SortOrderBuilder $sortOrderBuilder,
        TransactionRepositoryInterface $repository,
        ObjectArrayFactory $objectArrayFactory
    ) {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->sortOrderBuilder = $sortOrderBuilder;
        $this->transactionRepository = $repository;
        $this->objectArrayFactory = $objectArrayFactory;
    }

    /**
     * @param OrderPaymentInterface $payment
     * @param string $type
     * @return ObjectArray
     */
    public function getByTxnType(OrderPaymentInterface $payment, string $type): ObjectArray
    {
        $paymentId = $payment->getEntityId();

        if ($paymentId) {
            $this->searchCriteriaBuilder->addFilter(TransactionInterface::PAYMENT_ID, $paymentId);
            $this->searchCriteriaBuilder->addFilter(TransactionInterface::TXN_TYPE, $type);


            $transactionIdSort = $this->sortOrderBuilder
                ->setField(TransactionInterface::TRANSACTION_ID)
                ->setDirection(Collection::SORT_ORDER_DESC)
                ->create();
            $createdAtSort = $this->sortOrderBuilder
                ->setField(TransactionInterface::CREATED_AT)
                ->setDirection(Collection::SORT_ORDER_DESC)
                ->create();
            $this->searchCriteriaBuilder->addSortOrder($transactionIdSort);
            $this->searchCriteriaBuilder->addSortOrder($createdAtSort);

            $searchCriteria = $this->searchCriteriaBuilder->create();
            $searchResults = $this->transactionRepository->getList($searchCriteria);

            return $this->objectArrayFactory->create(TransactionInterface::class, $searchResults->getItems());
        }

        return $this->objectArrayFactory->create(TransactionInterface::class);
    }

    /**
     * @param OrderPaymentInterface $payment
     * @param string $txnId
     * @return TransactionInterface|null
     */
    public function getByTxnId(OrderPaymentInterface $payment, string $txnId): ?TransactionInterface
    {
        $paymentId = $payment->getEntityId();

        if (!$paymentId) {
            return null;
        }

        $this->searchCriteriaBuilder->addFilter(TransactionInterface::PAYMENT_ID, $paymentId);
        $this->searchCriteriaBuilder->addFilter(TransactionInterface::ORDER_ID, $payment->getParentId());
        $this->searchCriteriaBuilder->addFilter(TransactionInterface::TXN_ID, $txnId);

        $this->searchCriteriaBuilder->setPageSize(1);
        $searchCriteria = $this->searchCriteriaBuilder->create();
        $searchResults = $this->transactionRepository->getList($searchCriteria);
        $items = $searchResults->getItems();
        $transaction = current($items);
        return $transaction instanceof TransactionInterface
            ? $transaction
            : null;
    }
}
