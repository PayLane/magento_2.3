<?php

declare (strict_types = 1);

/**
 * File: Blik0.php
 *

 */

namespace PeP\PaymentGateway\Model\Payment\Adapter;

use Exception;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Api\CartManagementInterface;
use Magento\Sales\Model\OrderFactory;
use Magento\Sales\Model\Spi\OrderResourceInterface as OrderResource;
use PeP\PaymentGateway\Api\Adapter\PayLaneRestClientFactoryInterface;
use PeP\PaymentGateway\Api\Config\GeneralConfigProviderInterface;
use PeP\PaymentGateway\Model\Payment\Request\Builder\BackUrlBuilder;
use PeP\PaymentGateway\Model\Payment\Request\Builder\Blik0CodeBuilder;
use PeP\PaymentGateway\Model\Payment\Request\Builder\CustomerDataBuilder;
use PeP\PaymentGateway\Model\Payment\Request\Builder\SaleDataBuilder;
use PeP\PaymentGateway\Model\TransactionHandler;
use Psr\Log\LoggerInterface;

/**
 * Class Banktransfer
 * @package PeP\PaymentGateway\Model\Payment\Adapter
 */
class Blik0 extends AbstractAdapter
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
     * @var BackUrlBuilder
     */
    protected $backUrlBuilder;

    /**
     * @var CustomerDataBuilder
     */
    protected $customerDataBuilder;

    /**
     * @var Blik0CodeBuilder
     */
    protected $blik0CodeBuilder;

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
     * Banktransfer constructor.
     * @param GeneralConfigProviderInterface $generalConfigProvider
     * @param PayLaneRestClientFactoryInterface $payLaneRestClientFactory
     * @param SaleDataBuilder $saleBuilder
     * @param RedirectInterface $redirect
     * @param OrderResource $orderResource
     * @param OrderFactory $orderFactory
     * @param CartManagementInterface $cartManagementInterface
     * @param TransactionHandler $transactionHandler
     * @param BackUrlBuilder $backUrlBuilder
     * @param CustomerDataBuilder $customerDataBuilder
     * @param Blik0CodeBuilder $blik0CodeBuilder
     * @param LoggerInterface $logger
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
        BackUrlBuilder $backUrlBuilder,
        CustomerDataBuilder $customerDataBuilder,
        Blik0CodeBuilder $blik0CodeBuilder,
        LoggerInterface $logger
    ) {
        parent::__construct($payLaneRestClientFactory, $redirect, $orderResource);
        $this->generalConfigProvider = $generalConfigProvider;
        $this->payLaneRestClientFactory = $payLaneRestClientFactory;
        $this->saleBuilder = $saleBuilder;
        $this->backUrlBuilder = $backUrlBuilder;
        $this->orderFactory = $orderFactory;
        $this->cartManagementInterface = $cartManagementInterface;
        $this->transactionHandler = $transactionHandler;
        $this->customerDataBuilder = $customerDataBuilder;
        $this->blik0CodeBuilder = $blik0CodeBuilder;
        $this->logger = $logger;
    }

    /**
     * @return array
     */
    public function getAdditionalFields(): array
    {
        return [
            'blik0_code',
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
    public function handleResponse(array $responseData, ResponseInterface $response)
    {
        $quote = $this->quote;
        $orderId = $this->cartManagementInterface->placeOrder($quote->getId());
        $order = $this->orderFactory->create();
        $this->orderResource->load($order, $orderId);
        $orderStatus = $this->generalConfigProvider->getErrorOrderStatus();
        $success = false;

        if ($order->getId()) {
            if ($responseData['success']) {
                $success = true;
                $orderStatus = $this->generalConfigProvider->getClearedOrderStatus();
                $comment = __('Payment handled via PayLane module | Transaction ID: %1', $responseData['id_sale']);
                $orderPayment = $order->getPayment();
                $orderPayment->setTransactionId($responseData['id_sale']);
                $orderPayment->setIsTransactionClosed(true);
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

        return $order;
    }

    /**
     * @return array|mixed
     */
    protected function buildRequest()
    {
        $result = [];
        $result = array_merge_recursive($result, $this->saleBuilder->build($this->quote));
        $result = array_merge_recursive($result, $this->backUrlBuilder->build($this->quote));
        $result = array_merge_recursive($result, $this->customerDataBuilder->build($this->quote));
        $result = array_merge_recursive($result, $this->blik0CodeBuilder->build($this->quote));

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

        return $client->blikSale($requestData);
    }
}
