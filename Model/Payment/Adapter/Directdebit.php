<?php

declare(strict_types=1);

/**
 * File: Directdebit.php
 *
 
 */

namespace PeP\PaymentGateway\Model\Payment\Adapter;

use Exception;
use PeP\PaymentGateway\Api\Adapter\PayLaneRestClientFactoryInterface;
use PeP\PaymentGateway\Api\Config\GeneralConfigProviderInterface;
use PeP\PaymentGateway\Model\Payment\Request\Builder\AccountBuilder;
use PeP\PaymentGateway\Model\Payment\Request\Builder\CustomerDataBuilder;
use PeP\PaymentGateway\Model\Payment\Request\Builder\SaleDataBuilder;
use PeP\PaymentGateway\Model\TransactionHandler;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Api\CartManagementInterface;
use Magento\Sales\Model\OrderFactory;
use Magento\Sales\Model\Spi\OrderResourceInterface as OrderResource;
use Psr\Log\LoggerInterface;

/**
 * Class Directdebit
 * @package PeP\PaymentGateway\Model\Payment\Adapter
 */
class Directdebit extends AbstractAdapter
{
    /**
     * @var GeneralConfigProviderInterface
     */
    protected $generalConfigProvider;

    /**
     * @var SaleDataBuilder
     */
    protected $saleBuilder;

    /**
     * @var AccountBuilder
     */
    protected $accountBuilder;

    /**
     * @var CustomerDataBuilder
     */
    protected $customerDataBuilder;

    /**
     * @var OrderFactory
     */
    protected $orderFactory;

    /**
     * @var CartManagementInterface
     */
    protected $cartManagementInterface;

    /**
     * @var TransactionHandler
     */
    protected $transactionHandler;

      /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Directdebit constructor.
     *
     * @param GeneralConfigProviderInterface $generalConfigProvider
     * @param PayLaneRestClientFactoryInterface $payLaneRestClientFactory
     * @param SaleDataBuilder $saleBuilder
     * @param RedirectInterface $redirect
     * @param OrderResource $orderResource
     * @param OrderFactory $orderFactory
     * @param CartManagementInterface $cartManagementInterface
     * @param TransactionHandler $transactionHandler
     * @param AccountBuilder $accountBuilder
     * @param CustomerDataBuilder $customerDataBuilder
     * @SuppressWarnings(PHPMD.LongVariable)
     */
    public function __construct(
        GeneralConfigProviderInterface $generalConfigProvider,
        PayLaneRestClientFactoryInterface $payLaneRestClientFactory,
        SaleDataBuilder $saleBuilder,
        RedirectInterface $redirect,
        OrderResource $orderResource,
        OrderFactory $orderFactory,
        CartManagementInterface $cartManagementInterface,
        TransactionHandler $transactionHandler,
        AccountBuilder $accountBuilder,
        CustomerDataBuilder $customerDataBuilder,
        LoggerInterface $logger
    ) {
        parent::__construct($payLaneRestClientFactory, $redirect, $orderResource);
        $this->generalConfigProvider = $generalConfigProvider;
        $this->saleBuilder = $saleBuilder;
        $this->accountBuilder = $accountBuilder;
        $this->orderFactory = $orderFactory;
        $this->cartManagementInterface = $cartManagementInterface;
        $this->transactionHandler = $transactionHandler;
        $this->customerDataBuilder = $customerDataBuilder;
        $this->logger = $logger;
    }

    /**
     * @return array
     */
    public function getAdditionalFields(): array
    {
        return [
            'account_holder',
            'account_country',
            'iban',
            'bic'
        ];
    }

    /**
     * @param array $responseData
     * @param ResponseInterface $response
     * @return mixed|void
     * @throws Exception
     * @throws CouldNotSaveException
     * @throws LocalizedException
     */
    public function handleResponse(array $responseData, ResponseInterface $response): void
    {
        $success = false;
        $quote = $this->quote;
        $orderId = $this->cartManagementInterface->placeOrder($quote->getId());
        $order = $this->orderFactory->create();
        $this->orderResource->load($order, $orderId);
        $orderStatus = $this->generalConfigProvider->getErrorOrderStatus();

        if ($order->getId()) {
            if (!empty($responseData['success']) && $responseData['success']) {
                $orderStatus = $this->generalConfigProvider->getPendingOrderStatus();
                $success = true;
                $comment = __('Payment handled via PayLane module | Transaction ID: %1', $responseData['id_sale']);
                $this->logger->info((string)$comment);
                $orderPayment = $order->getPayment();
                $orderPayment->setTransactionId($responseData['id_sale']);
                $orderPayment->setIsTransactionClosed(false);
                $orderPayment->addTransaction('capture');
            } else {
                $error = $responseData['error'];
                $comment = __(
                    'Payment handled via PayLane module | Error (%1): %2',
                    $error['error_number'],
                    $error['error_description']
                );

                $this->logger->error((string)$comment);
            }

            $this->transactionHandler->setOrderState($order, $orderStatus, $comment);
            $this->orderResource->save($order);
        }

        $this->handleRedirect($success, $response);
    }

    /**
     * @return array
     */
    protected function buildRequest(): array
    {
        $result = [];
        $result = array_merge_recursive($result, $this->saleBuilder->build($this->quote));
        $result = array_merge_recursive($result, $this->accountBuilder->build($this->quote));
        $result = array_merge_recursive($result, $this->customerDataBuilder->build($this->quote));

        return $result;
    }

    /**
     * @param array $requestData
     * @return mixed
     * @throws Exception
     */
    protected function makeRequest(array $requestData)
    {
        $client = $this->payLaneRestClientFactory->create();
        return $client->directDebitSale($requestData);
    }
}
