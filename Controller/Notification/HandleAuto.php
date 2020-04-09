<?php

declare (strict_types = 1);

/**
 * File: HandleAuto.php
 *

 */

namespace PeP\PaymentGateway\Controller\Notification;

use Exception;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Data\Collection;
use Magento\Framework\DB\TransactionFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\CreditmemoFactory;
use Magento\Sales\Model\Order\Invoice;
use Magento\Sales\Model\Service\CreditmemoService;
use Magento\Sales\Model\Spi\OrderResourceInterface as OrderResource;
use PeP\PaymentGateway\Api\Config\GeneralConfigProviderInterface;
use PeP\PaymentGateway\Api\Config\NotificationAuthenticationConfigProviderInterface;
use PeP\PaymentGateway\Api\Config\NotificationConfigProviderInterface;
use PeP\PaymentGateway\Model\Notification\Data;
use PeP\PaymentGateway\Model\TransactionHandler;
use Psr\Log\LoggerInterface;

/**
 * Class HandleAuto
 * @package PeP\PaymentGateway\Controller\Notification
 */
class HandleAuto extends Action implements CsrfAwareActionInterface
{
    /**
     * @var GeneralConfigProviderInterface
     */
    private $generalConfigProvider;

    /**
     * @var NotificationAuthenticationConfigProviderInterface
     */
    private $notificationsAuthenticationConfigProvider;

    /**
     * @var NotificationConfigProviderInterface
     */
    private $notificationsConfigProvider;

    /**
     * @var TransactionHandler
     */
    private $transactionHandler;

    /**
     * @var Order
     */
    private $order;

    /**
     * @var OrderResource
     */
    private $orderResource;

    /**
     * @var TransactionFactory
     */
    private $transactionFactory;

    /**
     * @var CreditmemoFactory
     */
    private $creditmemoFactory;

    /**
     * @var CreditmemoService
     */
    private $creditmemoService;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var LoggerInterface
     */
    private $pplogger;

    /**
     * HandleAuto constructor.
     * @param GeneralConfigProviderInterface $generalConfigProvider
     * @param NotificationAuthenticationConfigProviderInterface $notificationAuthenticationConfigProvider
     * @param NotificationConfigProviderInterface $notificationConfigProvider
     * @param Context $context
     * @param TransactionHandler $transactionHandler
     * @param Order $order
     * @param OrderResource $orderResource
     * @param TransactionFactory $transactionFactory
     * @param CreditmemoFactory $creditmemoFactory
     * @param CreditmemoService $creditmemoService
     * @param LoggerInterface $logger
     * @param LoggerInterface $pplogger
     * @SuppressWarnings(PHPMD.LongVariable)
     */
    public function __construct(
        GeneralConfigProviderInterface $generalConfigProvider,
        NotificationAuthenticationConfigProviderInterface $notificationAuthenticationConfigProvider,
        NotificationConfigProviderInterface $notificationConfigProvider,
        Context $context,
        TransactionHandler $transactionHandler,
        Order $order,
        OrderResource $orderResource,
        TransactionFactory $transactionFactory,
        CreditmemoFactory $creditmemoFactory,
        CreditmemoService $creditmemoService,
        LoggerInterface $logger,
        LoggerInterface $pplogger
    ) {
        parent::__construct($context);
        $this->generalConfigProvider = $generalConfigProvider;
        $this->notificationsConfigProvider = $notificationConfigProvider;
        $this->notificationsAuthenticationConfigProvider = $notificationAuthenticationConfigProvider;
        $this->transactionHandler = $transactionHandler;
        $this->order = $order;
        $this->orderResource = $orderResource;
        $this->transactionFactory = $transactionFactory;
        $this->creditmemoFactory = $creditmemoFactory;
        $this->creditmemoService = $creditmemoService;
        $this->logger = $logger;
        $this->pplogger = $pplogger;
    }

    /**
     * @return ResponseInterface|ResultInterface|void
     * @throws Exception
     */
    public function execute()
    {
        $params = (array)$this->getRequest()->getParams(); 

        //prevent trigger notification before handle process ended
        sleep(5);

        if ($this->isAutoMode()) {

            if (!isset($params['content'])) {
                die(-1);
            }

            if (!is_array($params['content'])) { 
                die(-2);
            }

            $this->log("========== NOTIFICATION response ==========\n" . \json_encode($params));

            $username = $this->notificationsAuthenticationConfigProvider->getUsername();
            $password = $this->notificationsAuthenticationConfigProvider->getPassword();

            if (!empty($username) && !empty($password)) {
                if (!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW'])) {
                    $this->failAuthorization();
                }

                if ($username != $_SERVER['PHP_AUTH_USER'] || $password != $_SERVER['PHP_AUTH_PW']) {
                    $this->failAuthorization();
                }
            }

            if (empty($params['communication_id'])) {
                $this->log("========== empty comm id ==========\n");
                $message = __('Empty communication id')->getText();
                $this->log($message, [], 'error');
                die($message);
            }

            if (!empty($params['token'])
                && ($this->notificationsConfigProvider->getNotificationToken() !== $params['token'])) {
                    $this->log("========== wrong token ==========\n");
                $message = __('Wrong token')->getText();
                $this->log($message, [$params['token']]);
                die($message);
            }

            $messages = $params['content'];

            $this->handleAutoMessages($messages);

            $this->log('========== END NOTIFICATION response ==========');
            die($params['communication_id']);
        }
    }

    /**
     * @TODO: Handle responses more gently, get rid of exit(), die()
     * @param $messages
     * @return void
     * @throws Exception
     */
    protected function handleAutoMessages($messages)
    {
        foreach ($messages as $message) {
            if (empty($message['text'])) {
                continue;
            }

            // $_txt = json_decode(stripslashes($message['text']), true);
            // if (is_array($_txt)) {
            //     $order_id = ($_txt['description']);
            // } else {
            //     $order_id = ($_txt['text']);
            // }

            $order_id = $message['text'];

            $this->log(
                (string) __(
                    'Handling message with PayLane Sale ID %1 for order #%2',
                    $message['id_sale'],
                    $order_id
                )
            );

            $order = $this->order->loadByIncrementId($order_id);

            $now = time();

            if ($order->getId()) {
                // if ($notificationType === false || ($notificationType !== false && $this->canUpdateStatus($notificationType, $notification['type']))) {
                switch ($message['type']) {
                    case TransactionHandler::TYPE_SALE:
                        $orderStatus = $this->generalConfigProvider->getClearedOrderStatus();
                        $comment = __('TRANSACTION CONFIRMED! Order status changed via PayLane module');
                        $order->setPaylaneNotificationTimestamp($now);
                        $order->setPaylaneNotificationStatus($message['type']);
                        $orderPayment = $order->getPayment();
                        $orderPayment->setIsTransactionClosed(true);
                        $this->transactionHandler->setOrderState($order, $orderStatus, $comment);
                        $this->orderResource->save($order);
                        $this->log((string) __('Changed order status to: %1', $orderStatus).' | '.$order->getId().' | '.$order_id);
                        break;

                    case TransactionHandler::TYPE_REFUND:
                        try {
                            $this->handleRefund($order);
                            $order->addCommentToStatusHistory((string) __(
                                'Notification: Refund was handled via PayLane module | Refund ID: %1',
                                $message['id']
                            ));

                            $order->setPaylaneNotificationTimestamp($now);
                            $order->setPaylaneNotificationStatus($message['type']);

                            $this->orderResource->save($order);

                            $this->log(
                                (string) __(
                                    'Order #%1 was refunded to amount %2',
                                    $order->getIncrementId(),
                                    $message['amount']
                                )
                            );
                        } catch (Exception $exception) {
                            $this->log((string) __('There was an error in refunding.'), [$exception->getMessage()]);
                        }

                        break;

                    default:
                        $errorMessage = (string) __('Unrecognized message type.');
                        $this->log($errorMessage, [$message['type']]);
                        die($errorMessage);
                        break;
                }
                // }
            } else {
                $this->log('Order not found!', [], 'error');
                die('Order not found');
            }
        }
    }

    /**
     * @TODO: Move logic to separate class
     * @param Order $order
     * @return bool
     * @throws LocalizedException
     */
    protected function handleRefund(Order $order)
    {
        if ($order) {
            $invoice = $this->initInvoice($order);

            if (!$order->canCreditmemo($order)) {
                return false;
            }

            $creditmemo = $this->creditmemoFactory->createByOrder($order);
            $creditmemo->setInvoice($invoice);
            //do offline refund because it is already done on the PayLane side
            $this->creditmemoService->refund($creditmemo, true);
        }
    }

    /**
     * @TODO: Move logic to separate class
     * @param Invoice | bool $order
     * @return Invoice
     */
    protected function initInvoice($order)
    {
        return $order->getInvoiceCollection()->setOrder('updated_at', Collection::SORT_ORDER_DESC)->getFirstItem();
    }

    /**
     * @return bool
     */
    protected function isAutoMode(): bool
    {
        return $this->notificationsConfigProvider->getNotificationHandlingMode() === Data::MODE_AUTO;
    }

    
    private function canUpdateStatus($currentNotifType, $newNotifType)
    {
        if ($currentNotifType == TransactionHandler::TYPE_SALE && $newNotifType == TransactionHandler::TYPE_REFUND) {
            return false;
        } elseif ($currentNotifType == TransactionHandler::TYPE_REFUND && $newNotifType == TransactionHandler::TYPE_SALE) {
            return true;
        } elseif (in_array($currentNotifType, [TransactionHandler::TYPE_SALE, TransactionHandler::TYPE_REFUND])) {
            return false;
        }

        return true;
    }

    /**
     * @TODO: Move to separate class responsible for authorization
     * @return void
     */
    protected function failAuthorization()
    {
        // authentication failed
        header("WWW-Authenticate: Basic realm=\"Secure Area\"");
        header("HTTP/1.0 401 Unauthorized");
        exit();
    }

    public function createCsrfValidationException(RequestInterface $request): ?InvalidRequestException
    {
        return null;
    }

    public function validateForCsrf(RequestInterface $request): ?bool
    {
        return true;
    }

    /**
     * @param string $message
     * @param array $params
     * @return void
     */
    protected function log(string $message, array $params = [], string $type = 'info')
    {
        if ($this->notificationsConfigProvider->isLoggingEnabled()) {
            switch ($type) {
                case 'error':
                    $this->logger->critical($message, $params);
                    $this->pplogger->critical($message, $params);
                    break;
                default:
                    $this->logger->info($message, $params);
                    $this->pplogger->info($message, $params);
            }
        }
    }
}
