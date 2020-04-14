<?php

declare (strict_types = 1);

/**
 * File: Handle.php
 *

 */

namespace PeP\PaymentGateway\Controller\Googlepay;

use Exception;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Quote\Api\CartManagementInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\OrderFactory;
use Magento\Sales\Model\Spi\OrderResourceInterface as OrderResource;
use PeP\PaymentGateway\Api\Config\GeneralConfigProviderInterface;
use PeP\PaymentGateway\Model\Notification\Data;
use PeP\PaymentGateway\Model\TransactionHandler;
use Psr\Log\LoggerInterface;
use PeP\PaymentGateway\Api\Adapter\PayLaneRestClientFactoryInterface;

/**
 * Class Handle
 * @package PeP\PaymentGateway\Controller\Transaction
 */
class Handle extends Action implements CsrfAwareActionInterface
{

     /**
     * @var PayLaneRestClientFactoryInterface
     */
    private $payLaneRestClientFactory;

    
    /**
     * @var GeneralConfigProviderInterface
     */
    private $generalConfigProvider;

    /**
     * @var OrderFactory
     */
    private $orderFactory;

    /**
     * @var TransactionHandler
     */
    private $transactionHandler;

    /**
     * @var CartManagementInterface
     */
    private $cartManagementInterface;

    /**
     * @var OrderResource
     */
    private $orderResource;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Handle constructor.
     * @param GeneralConfigProviderInterface $generalConfigProvider
     * @param Context $context
     * @param OrderFactory $orderFactory
     * @param TransactionHandler $transactionHandler
     * @param CartManagementInterface $cartManagementInterface
     * @param OrderResource $orderResource
     * @param PayLaneRestClientFactoryInterface $payLaneRestClientFactory
     * @SuppressWarnings(PHPMD.LongVariable)
     */
    public function __construct(
        GeneralConfigProviderInterface $generalConfigProvider,
        Context $context,
        OrderFactory $orderFactory,
        TransactionHandler $transactionHandler,
        CartManagementInterface $cartManagementInterface,
        OrderResource $orderResource,
        PayLaneRestClientFactoryInterface $payLaneRestClientFactory,
        LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->generalConfigProvider = $generalConfigProvider;
        $this->orderFactory = $orderFactory;
        $this->transactionHandler = $transactionHandler;
        $this->cartManagementInterface = $cartManagementInterface;
        $this->orderResource = $orderResource;
        $this->payLaneRestClientFactory = $payLaneRestClientFactory;
        $this->logger = $logger;
    }

    /**
     * @return void
     * @throws Exception
     * @throws CouldNotSaveException
     */
    public function execute(): void
    {
        $params = $this->getRequest()->getParams();

        $this->logger->info("======== TRANSACTION ========\n" . \json_encode($params));

        $success = false;

        $amount = $params['amount'];
        // $currency = $params['currency'];
        $incrementId = $params['description'];
        $hash = $params['hash'];
        $idSale = isset($params['id_sale']) ? $params['id_sale'] : null;
        $hashSalt = $this->generalConfigProvider->getHashSalt();

        $hcurrency = null;
        if (!isset($params['status']) && isset($params['correct']) && $params['correct'] == '1') {
            $status = "";
            if (!isset($params['currency'])) {
                $hcurrency = $params['currency_code'];
            } else {
                $hcurrency = $params['currency'];
            }
        } else {
            $hcurrency = $params['currency'];
            $status = $params['status'];
        }

        $hashComputed = sha1($hashSalt . '|' .
            $status . '|' .
            $incrementId . '|' .
            $amount . '|' .
            $hcurrency . '|' .
            $idSale);

        $orderStatus = $this->generalConfigProvider->getErrorOrderStatus();

        if ($hash === $hashComputed && $status !== Data::STATUS_ERROR) {
            $orderStatus = $this->getOrderStatus((string) $status);
        }

        $orderId = $this->cartManagementInterface->placeOrder($params['quote']);

        /** @var $order Order */
        $order = $this->orderFactory->create();
        $this->orderResource->load($order, $orderId);

        if ($order->getId()) {
            $isError = false;
            if ($status != Data::STATUS_ERROR) {
                $responseData2 = $this->captureRequest($params['id_3dsecure_auth']);

                if (!empty($responseData2['success']) && $responseData2['success']) {
                    $orderStatus = $this->generalConfigProvider->getPendingOrderStatus();

                    $idSale = $responseData2['id_sale'];
                    $comment = __('Payment handled via PayLane module | Transaction ID: %1', $idSale);
                    $orderPayment = $order->getPayment();
                    $orderPayment->setTransactionId($idSale);
                    $orderPayment->setIsTransactionClosed(true);
                    $orderPayment->addTransaction('capture');

                    $this->logger->info("PAYMENT OK [PENDING]\n" . (string) $comment);
                    $success = true;
                } else {
                    $isError = true;
                    $params = $responseData2;
                }
            }

            if ($status == Data::STATUS_ERROR || $isError) {
                if (isset($params['error'])) {
                    $errorNumber = $params['error']['error_number'];
                    $errorDescription = $params['error']['error_description'];
                } elseif (isset($params['id_error'])) {
                    $errorNumber = $params['error_code'];
                    $errorDescription = $params['error_text'];
                } else {
                    $errorNumber = 'Undefined';
                    $errorDescription = 'Undefined';
                }

                $comment = __('Payment handled via PayLane module | Error (%1): %2', $errorNumber, $errorDescription);

                $this->logger->error("PAYMENT ERROR\n" . (string) $comment);
            }

            $this->transactionHandler->setOrderState($order, $orderStatus, $comment);
            $this->orderResource->save($order);
        } else {
            $this->logger->error('No order!');
        }

        $this->logger->info("======== END TRANSACTION ========");

        if ($success) {
            $this->_redirect('checkout/onepage/success', ['_nosid' => true, '_secure' => true]);
        } else {
            $this->_redirect('checkout/onepage/failure', ['_nosid' => true, '_secure' => true]);
        }
    }

    /**
     * @param RequestInterface $request
     * @return InvalidRequestException|null
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function createCsrfValidationException(RequestInterface $request): ?InvalidRequestException
    {
        return null;
    }

    /**
     * @param RequestInterface $request
     * @return bool|null
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function validateForCsrf(RequestInterface $request): ?bool
    {
        return true;
    }

    /**
     * @param string $notificationStatus
     * @return string
     */
    private function getOrderStatus(string $notificationStatus): string
    {
        switch ($notificationStatus) {
            case Data::STATUS_PENDING:
                return $this->generalConfigProvider->getPendingOrderStatus();
            case Data::STATUS_PERFORMED:
                return $this->generalConfigProvider->getPerformedOrderStatus();
            case Data::STATUS_CLEARED:
                return $this->generalConfigProvider->getClearedOrderStatus();
            case Data::STATUS_ERROR:
            default:
                return $this->generalConfigProvider->getErrorOrderStatus();
        }
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
