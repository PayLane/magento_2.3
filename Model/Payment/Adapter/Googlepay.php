<?php

declare (strict_types = 1);

/**
 * File: Googlepay.php
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
use PeP\PaymentGateway\Model\Payment\Request\Builder\GooglepayBackUrlBuilder;
use PeP\PaymentGateway\Model\Payment\Request\Builder\GooglePayCustomerDataBuilder;
use PeP\PaymentGateway\Model\Payment\Request\Builder\SaleDataBuilder;
use PeP\PaymentGateway\Model\Payment\Request\Builder\TokenBuilder;
use PeP\PaymentGateway\Model\TransactionHandler;
use Psr\Log\LoggerInterface;

/**
 * Class Googlepay
 * @package PeP\PaymentGateway\Model\Payment\Adapter
 */
class Googlepay extends AbstractAdapter
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
     * @var GooglePayCustomerDataBuilder
     */
    protected $customerDataBuilder;

    /**
     * @var GooglepayBackUrlBuilder
     */
    protected $backUrlBuilder;

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
     * Googlepay constructor.
     * @param GeneralConfigProvider $generalConfigProvider
     * @param PayLaneRestClientFactoryInterface $payLaneRestClientFactory
     * @param SaleDataBuilder $saleBuilder
     * @param RedirectInterface $redirect
     * @param OrderResource $orderResource
     * @param OrderFactory $orderFactory
     * @param CartManagementInterface $cartManagementInterface
     * @param TransactionHandler $transactionHandler
     * @param TokenBuilder $tokenBuilder
     * @param GooglePayCustomerDataBuilder $customerDataBuilder
     * @param GooglepayBackUrlBuilder $backUrlBuilder
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
        GooglePayCustomerDataBuilder $customerDataBuilder,
        GooglepayBackUrlBuilder $backUrlBuilder,
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
        $this->backUrlBuilder = $backUrlBuilder;
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
        $success = false;
        $orderStatus = $this->generalConfigProvider->getErrorOrderStatus();

        if (!empty($responseData['success']) && $responseData['success']) {
            if ($responseData['is_card_enrolled'] == false) {
                $responseData2 = $this->captureRequest($responseData['id_3dsecure_auth']);

                if (!empty($responseData2['success']) && $responseData2['success']) {
                    $orderStatus = $this->generalConfigProvider->getPendingOrderStatus();

                    $quote = $this->quote;
                    $orderId = $this->cartManagementInterface->placeOrder($quote->getId());

                    $order = $this->orderFactory->create();
                    $this->orderResource->load($order, $orderId);

                    $idSale = $responseData2['id_sale'];
                    $success = true;
                    $comment = __('Payment handled via PayLane module | Transaction ID: %1', $idSale);
                    $orderPayment = $order->getPayment();
                    $orderPayment->setTransactionId($idSale);

                    $orderPayment->setIsTransactionClosed(false);
                    $orderPayment->addTransaction('capture');

                    $this->transactionHandler->setOrderState($order, $orderStatus, $comment);
                    $this->orderResource->save($order);

                    $this->logger->info("PAYMENT OK [PENDING]\n" . (string) $comment);
                } else {
                    $quote = $this->quote;
                    $orderId = $this->cartManagementInterface->placeOrder($quote->getId());
                    $order = $this->orderFactory->create();
                    $this->orderResource->load($order, $orderId);
                    $error = $responseData2['error'];

                    $comment = __(
                        'Payment handled via PayLane module | Error (%1): %2',
                        $error['error_number'],
                        $error['error_description']
                    );

                    $this->logger->error((string) $comment);

                    $this->transactionHandler->setOrderState($order, $orderStatus, $comment);
                    $this->orderResource->save($order);
                }
            } else {
                header('Location: ' . $responseData['redirect_url']);
                die;
            }
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

            $this->logger->error((string) $comment);

            $this->transactionHandler->setOrderState($order, $orderStatus, $comment);
            $this->orderResource->save($order);
        }

        $this->handleRedirect($success, $response);

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
        $requestData = $this->buildRequest();
        $responseData = $this->makeRequest($requestData);

        return $responseData;
    }

    /**
     * @return array|mixed
     */
    protected function buildRequest(): array
    {
        $result = [];
        $result = array_merge_recursive($result, $this->customerDataBuilder->build($this->quote));
        $result = array_merge_recursive($result, $this->saleBuilder->build($this->quote));
        $result = array_merge_recursive($result, $this->backUrlBuilder->build($this->quote));

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
        return $client->googlePaySale($requestData);
    }

    /**
     * @param array $requestData
     * @return mixed
     * @throws Exception
     */
    protected function captureRequest($id3dsecureAuth)
    {
        $client = $this->payLaneRestClientFactory->create();
        return $client->saleBy3DSecureAuthorization(['id_3dsecure_auth' => $id3dsecureAuth]);
    }
}
