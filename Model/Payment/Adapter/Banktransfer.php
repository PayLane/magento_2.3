<?php

declare(strict_types=1);

/**
 * File: Banktransfer.php
 *
 
 */

namespace PeP\PaymentGateway\Model\Payment\Adapter;

use Exception;
use PeP\PaymentGateway\Api\Adapter\PayLaneRestClientFactoryInterface;
use PeP\PaymentGateway\Api\Config\GeneralConfigProviderInterface;
use PeP\PaymentGateway\Model\Payment\Request\Builder\BackUrlBuilder;
use PeP\PaymentGateway\Model\Payment\Request\Builder\CustomerDataBuilder;
use PeP\PaymentGateway\Model\Payment\Request\Builder\PaymentTypeBuilder;
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
 * Class Banktransfer
 * @package PeP\PaymentGateway\Model\Payment\Adapter
 */
class Banktransfer extends AbstractAdapter
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
     * @var PaymentTypeBuilder
     */
    protected $paymentTypeBuilder;

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
     * @param PaymentTypeBuilder $paymentTypeBuilder
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
        PaymentTypeBuilder $paymentTypeBuilder,
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
        $this->paymentTypeBuilder = $paymentTypeBuilder;
        $this->logger = $logger;
    }

    /**
     * @return array
     */
    public function getAdditionalFields(): array
    {
        return [
            'payment_type'
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
        $success = false;
        $orderStatus = $this->generalConfigProvider->getErrorOrderStatus();

        if (!empty($responseData['success']) && $responseData['success']) {
            header('Location: ' . $responseData['redirect_url']);
            die;
        } else {
            $quote = $this->quote;
            $orderId = $this->cartManagementInterface->placeOrder($quote->getId());
            $order = $this->orderFactory->create();
            $this->orderResource->load($order, $orderId);
            $error = $responseData['error'];

            $comment = __(
                'Payment handled via PayLane module | Error (%1): %2',
                $error['error_number'],
                $error['error_description']
            );

            $this->logger->error((string)$comment);

            $this->transactionHandler->setOrderState($order, $orderStatus, $comment);
            $this->orderResource->save($order);
        }

        $this->handleRedirect($success, $response);
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
        $result = array_merge_recursive($result, $this->paymentTypeBuilder->build($this->quote));

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
        return $client->bankTransferSale($requestData);
    }
}
