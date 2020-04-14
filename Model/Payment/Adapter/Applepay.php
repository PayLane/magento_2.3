<?php

declare (strict_types = 1);

/**
 * File: Applepay.php
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
use PeP\PaymentGateway\Model\Config\GeneralConfigProvider;
use PeP\PaymentGateway\Model\Payment\Request\Builder\CustomerDataBuilder;
use PeP\PaymentGateway\Model\Payment\Request\Builder\SaleDataBuilder;
use PeP\PaymentGateway\Model\Payment\Request\Builder\TokenBuilder;
use PeP\PaymentGateway\Model\TransactionHandler;
use Psr\Log\LoggerInterface;

/**
 * Class Applepay
 * @package PeP\PaymentGateway\Model\Payment\Adapter
 */
class Applepay extends AbstractAdapter
{
    /**
     * @var array
     */
    protected $params;

    /**
     * @var GeneralConfigProvider
     */
    private $generalConfigProvider;

    /**
     * @var SaleDataBuilder
     */
    protected $saleBuilder;

    /**
     * @var TokenBuilder
     */
    protected $tokenBuilder;

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
     * Applepay constructor.
     * @param GeneralConfigProvider $generalConfigProvider
     * @param PayLaneRestClientFactoryInterface $payLaneRestClientFactory
     * @param SaleDataBuilder $saleBuilder
     * @param RedirectInterface $redirect
     * @param OrderResource $orderResource
     * @param OrderFactory $orderFactory
     * @param CartManagementInterface $cartManagementInterface
     * @param TransactionHandler $transactionHandler
     * @param TokenBuilder $tokenBuilder
     * @param CustomerDataBuilder $customerDataBuilder
     * @param LoggerInterface $logger
     * @SuppressWarnings(PHPMD.LongVariable)
     */
    public function __construct(
        GeneralConfigProvider $generalConfigProvider,
        PayLaneRestClientFactoryInterface $payLaneRestClientFactory,
        SaleDataBuilder $saleBuilder,
        RedirectInterface $redirect,
        OrderResource $orderResource,
        OrderFactory $orderFactory,
        CartManagementInterface $cartManagementInterface,
        TransactionHandler $transactionHandler,
        TokenBuilder $tokenBuilder,
        CustomerDataBuilder $customerDataBuilder,
        LoggerInterface $logger
    ) {
        parent::__construct($payLaneRestClientFactory, $redirect, $orderResource);
        $this->generalConfigProvider = $generalConfigProvider;
        $this->saleBuilder = $saleBuilder;
        $this->tokenBuilder = $tokenBuilder;
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
            'token',
        ];
    }

    /**
     * @param array $responseData
     * @param ResponseInterface $response
     * @return $this|mixed
     * @throws Exception
     * @throws CouldNotSaveException
     * @throws LocalizedException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
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
                $orderStatus = $this->generalConfigProvider->getPendingOrderStatus();
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

                $this->logger->error((string) $comment);

                $this->transactionHandler->setOrderState($order, $orderStatus, $comment);
                $this->orderResource->save($order);

                return $error['error_description'];

            }

            $this->transactionHandler->setOrderState($order, $orderStatus, $comment);
            $this->orderResource->save($order);
        }

        $this->logger->info(">>> ========== APPLE PAY PAYMENT END ========== <<<\n");

        // $this->handleRedirect($success, $response);

        return $order;

    }

    /**
     * @param $params
     * @return void
     */
    public function setParams($params): void
    {
        $this->params = $params;
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public function handleRequest()
    {
        $responseData = $this->makeRequest($this->params);

        return $responseData;
    }

    /**
     * @return array|mixed
     */
    protected function buildRequest(): array
    {
        $result = [];
        return $result;
    }

    /**
     * @param array $requestData
     * @return mixed
     * @throws Exception
     */
    protected function makeRequest(array $requestData)
    {
        $requestData['sale']['description'] = $this->quote->getReservedOrderId();
        $this->logger->info(">>> ========== APPLE PAY PAYMENT START ========== <<<\n" . \json_encode($requestData['sale']));
        $client = $this->payLaneRestClientFactory->create();
        return $client->applePaySale($requestData);
    }
}
