<?php

declare(strict_types=1);

/**
 * File: HandleSaleTransaction.php
 *
 
 
 */

namespace PeP\PaymentGateway\Controller\SecureForm;

use Magento\Sales\Model\Order;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Framework\App\RequestInterface;
use PeP\PaymentGateway\Model\Notification\Data;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use PeP\PaymentGateway\Api\Config\GeneralConfigProviderInterface;
use PeP\PaymentGateway\Api\Order\CaptureOperationWrapperInterface;
use PeP\PaymentGateway\Model\Request\BackRequestValidatorComposite;
use Psr\Log\LoggerInterface;

/**
 * Class HandleSaleTransaction
 * @package PeP\PaymentGateway\Controller\SecureForm
 */
class HandleSaleTransaction extends Action implements CsrfAwareActionInterface, HttpPostActionInterface
{
    /**
     * It's public as it is used in di.xml configuration
     * @var string
     */
    public const ID_SALE_PARAM = 'id_sale';

    /**
     * @var string
     */
    private const STATUS_PARAM = 'status';

    /**
     * @var string
     */
    private const ERROR_TEXT_PARAM = 'error_text';

    /**
     * @var GeneralConfigProviderInterface
     */
    private $generalConfigProvider;

    /**
     * @var CaptureOperationWrapperInterface
     */
    private $captureOperationWrapper;

    /**
     * @var BackRequestValidatorComposite
     */
    private $backRequestValidatorComposite;

    /**
     * @var Session
     */
    private $session;

     /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Handle constructor.
     * @param GeneralConfigProviderInterface $generalConfigProvider
     * @param CaptureOperationWrapperInterface $captureOperationWrapper
     * @param BackRequestValidatorComposite $backRequestValidatorComposite
     * @param Session $session
     * @param Context $context
     * @param LoggerInterface $logger
     * @SuppressWarnings(PHPMD.LongVariable)
     */
    public function __construct(
        GeneralConfigProviderInterface $generalConfigProvider,
        CaptureOperationWrapperInterface $captureOperationWrapper,
        BackRequestValidatorComposite $backRequestValidatorComposite,
        Session $session,
        Context $context,
        LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->generalConfigProvider = $generalConfigProvider;
        $this->captureOperationWrapper = $captureOperationWrapper;
        $this->backRequestValidatorComposite = $backRequestValidatorComposite;
        $this->session = $session;
        $this->logger = $logger;
    }

    /**
     * @return Redirect
     */
    public function execute(): Redirect
    {
        $redirect = $this->instantiateRedirect();

        $request = $this->getRequest();
        $status = $request->getParam(self::STATUS_PARAM, '');
        $lastOrder = $this->session->getLastRealOrder();
        /** @var $payment Order\Payment */
        $payment = $lastOrder->getPayment();

        if ($status === Data::STATUS_ERROR) {
            $this->noteError($lastOrder, $request);

            // $payment->setSkipTransactionCreation(true);
            // $state = 'pending_payment';
            // $status = 'pending_payment';
            // $payment->setState($state);
            // $payment->setStatus($status);
            // $payment->setIsTransactionPending(true);

            return $this->configureRedirectToFailure($redirect);

            //! todo - mozna dodac wznawianie kiedys? jak w woo

            // $lastOrder->cancel();
            // $lastOrder->save();
            // return $redirect->setPath('checkout/cart');
        }

        $this->noteProcessing($lastOrder, $request);
        $this->initializeTransactionId($lastOrder, $request);

        if ($status == Data::STATUS_PENDING) {
            $payment->setIsTransactionClosed(false);
            $payment->setIsTransactionPending(true);
            $this->processCapture($lastOrder, $redirect);
        } elseif (in_array($status, [Data::STATUS_PERFORMED, Data::STATUS_CLEARED])) {
            $payment->setIsTransactionPending(false);
            $this->processCapture($lastOrder, $redirect);
        }

        return $redirect;
    }

    /**
     * @param RequestInterface $request
     * @return InvalidRequestException|null
     * @codeCoverageIgnore
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function createCsrfValidationException(RequestInterface $request): ?InvalidRequestException
    {
        /** @var Redirect $redirect */
        $redirect = $this->resultRedirectFactory->create();
        $this->configureRedirectToFailure($redirect);

        return new InvalidRequestException(
            $redirect,
            [__('Something went wrong. Please try again later')]
        );
    }

    /**
     * @param RequestInterface $request
     * @return bool|null
     * @codeCoverageIgnore
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function validateForCsrf(RequestInterface $request): ?bool
    {
        return $this->backRequestValidatorComposite->validate($request);
    }

    /**
     * @return Redirect
     */
    private function instantiateRedirect(): Redirect
    {
        return $this->resultRedirectFactory->create();
    }

    /**
     * @param Redirect $redirect
     * @return Redirect
     */
    private function configureRedirectToFailure(Redirect $redirect): Redirect
    {
        return $redirect->setPath('checkout/onepage/failure');
    }

    /**
     * @param Order $order
     * @param RequestInterface $request
     * @return void
     */
    private function noteError(Order $order, RequestInterface $request): void
    {
        $errorMessage = $request->getParam(self::ERROR_TEXT_PARAM, '');
        $comment = __(
            'Payment handled via PayLane module | Error: %1',
            $errorMessage
        )->render();
        $this->logger->critical("SECURE FORM\n".$comment, []);
        $order->addCommentToStatusHistory($comment);
        $this->messageManager->addErrorMessage($errorMessage);
    }

    /**
     * @param Order $order
     * @param RequestInterface $request
     * @return void
     */
    private function noteProcessing(Order $order, RequestInterface $request): void
    {
        $comment = __(
            'Payment handled via PayLane module | Transaction ID: %1',
            $request->getParam(self::ID_SALE_PARAM, '')
        )->render();
        $this->logger->info("SECURE FORM\n".$comment,[]);
        $order->addCommentToStatusHistory($comment);
    }

    /**
     * @param Order $order
     * @param RequestInterface $request
     * @return void
     */
    private function initializeTransactionId(Order $order, RequestInterface $request): void
    {
        $payment = $order->getPayment();
        /** @var $payment Order\Payment */
        $payment->setTransactionId($request->getParam(self::ID_SALE_PARAM, ''));
    }

    /**
     * @param OrderInterface $order
     * @param Redirect $redirect
     * @return void
     */
    private function processCapture(OrderInterface $order, Redirect $redirect): void
    {
        try {
            $this->captureOperationWrapper->capture($order);
            $redirect->setPath('checkout/onepage/success');
        } catch (LocalizedException $exception) {
            $this->messageManager->addErrorMessage($exception->getMessage());
            $this->configureRedirectToFailure($redirect);
        }
    }
}
