<?php

declare(strict_types=1);

/**
 * File: Handle3DSecureTransaction.php
 *
 
 
 */

namespace PeP\PaymentGateway\Controller\CreditCard;

use Magento\Sales\Model\Order;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use PeP\PaymentGateway\Model\Notification\Data;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\Exception\LocalizedException;
use PeP\PaymentGateway\Gateway\Reader\SubjectReader;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use PeP\PaymentGateway\Api\Config\GeneralConfigProviderInterface;
use PeP\PaymentGateway\Api\Order\CaptureOperationWrapperInterface;
use PeP\PaymentGateway\Model\Request\BackRequestValidatorComposite;
use PeP\PaymentGateway\Api\Order\Payment\TransactionManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * Class Handle3DSecureTransaction
 * @package PeP\PaymentGateway\Controller\CreditCard
 */
class Handle3DSecureTransaction extends Action implements CsrfAwareActionInterface, HttpPostActionInterface
{
    /**
     * It's public as it is used in di.xml configuration
     *
     * @var string
     */
    public const ID_3DSECURE_AUTH_PARAM = 'id_3dsecure_auth';

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
     * @var TransactionManagerInterface
     */
    private $transactionManager;

    /**
     * @var CaptureOperationWrapperInterface
     */
    private $captureOperationWrapper;

    /**
     * @var SubjectReader
     */
    private $subjectReader;

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
     * Handle3DSecureTransaction constructor.
     * @param GeneralConfigProviderInterface $generalConfigProvider
     * @param TransactionManagerInterface $transactionManager
     * @param CaptureOperationWrapperInterface $captureOperationWrapper
     * @param SubjectReader $subjectReader
     * @param BackRequestValidatorComposite $backRequestValidatorComposite
     * @param Session $session
     * @param Context $context
     * @SuppressWarnings(PHPMD.LongVariable)
     */
    public function __construct(
        GeneralConfigProviderInterface $generalConfigProvider,
        TransactionManagerInterface $transactionManager,
        CaptureOperationWrapperInterface $captureOperationWrapper,
        SubjectReader $subjectReader,
        BackRequestValidatorComposite $backRequestValidatorComposite,
        Session $session,
        Context $context,
        LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->generalConfigProvider = $generalConfigProvider;
        $this->transactionManager = $transactionManager;
        $this->captureOperationWrapper = $captureOperationWrapper;
        $this->subjectReader = $subjectReader;
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

        $this->logger->info("======== TRANSACTION CARD ========\n" . \json_encode($request->getParams()));

        if ($status === Data::STATUS_ERROR) {
            $params = $request->getParams();
            $this->closeAuthorizationTransaction(
                $lastOrder,
                $this->subjectReader->readErrorCode($params),
                $this->subjectReader->readErrorText($params)
            );

            $this->messageManager->addErrorMessage($params[self::ERROR_TEXT_PARAM]);
            return $this->configureRedirectToFailure($redirect);
        } else {
            $this->processCapture($lastOrder, $redirect);
            return $redirect;
        }
    }

    /**
     * @param RequestInterface $request
     * @return InvalidRequestException|null
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
     * @param string $errorNumber
     * @param string $errorDescription
     * @return void
     */
    private function closeAuthorizationTransaction(
        Order $order,
        string $errorNumber,
        string $errorDescription
    ): void {
        $comment = __(
            'Payment handled via PayLane module | Error (%1): %2',
            $errorNumber,
            $errorDescription
        )->render();

        $this->transactionManager->closeLastTxn($order->getPayment());
        $order->addCommentToStatusHistory($comment);

    }

    /**
     * @param Order $order
     * @param Redirect $redirect
     * @return void
     */
    private function processCapture(Order $order, Redirect $redirect): void
    {
        try {
            $payment = $order->getPayment();
            $additionalInfo = $payment->getAdditionalInformation();
            $idSecureAuth = $this->subjectReader->readField($additionalInfo, self::ID_3DSECURE_AUTH_PARAM);

            $comment = __(
                'Payment handled via PayLane module | Card Authorized, Authorization ID: %1',
                $idSecureAuth
            )->render();

            $order->addCommentToStatusHistory($comment);
            $this->captureOperationWrapper->capture($order);
            $redirect->setPath('checkout/onepage/success');
        } catch (LocalizedException $exception) {
            $this->messageManager->addErrorMessage($exception->getMessage());
            $this->configureRedirectToFailure($redirect);
        }
    }
}
