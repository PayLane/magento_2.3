<?php

declare(strict_types=1);

/**
 * File: Handle3DSecure.php
 *
 
 
 */

namespace PeP\PaymentGateway\Controller\CreditCard;

use InvalidArgumentException;
use PeP\PaymentGateway\Api\Config\GeneralConfigProviderInterface;
use PeP\PaymentGateway\Api\Order\CaptureOperationWrapperInterface;
use PeP\PaymentGateway\Gateway\Reader\SubjectReader;
use Magento\Checkout\Model\DefaultConfigProvider;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderPaymentInterface;
use Psr\Log\LoggerInterface;

/**
 * Class Handle3DSecure
 * @package PeP\PaymentGateway\Controller\CreditCard
 */
class RedirectTo3DSecureProviderPage extends Action implements HttpGetActionInterface
{
    /**
     * @var string
     */
    private const IS_CARD_ENROLLED = 'is_card_enrolled';

    /**
     * @var string
     */
    private const REDIRECT_URL_PARAM = 'redirect_url';

    /**
     * @var GeneralConfigProviderInterface
     */
    private $generalConfigProvider;

    /**
     * @var CaptureOperationWrapperInterface
     */
    private $captureOperationWrapper;

    /**
     * @var SubjectReader
     */
    private $subjectReader;

    /**
     * @var DefaultConfigProvider
     */
    private $defaultConfigProvider;

    /**
     * @var Session
     */
    private $session;

      /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * RedirectTo3DSecureProviderPage constructor.
     * @param GeneralConfigProviderInterface $generalConfigProvider
     * @param CaptureOperationWrapperInterface $captureOperationWrapper
     * @param SubjectReader $subjectReader
     * @param DefaultConfigProvider $defaultConfigProvider
     * @param Session $session
     * @param Context $context
     * @SuppressWarnings(PHPMD.LongVariable)
     */
    public function __construct(
        GeneralConfigProviderInterface $generalConfigProvider,
        CaptureOperationWrapperInterface $captureOperationWrapper,
        SubjectReader $subjectReader,
        DefaultConfigProvider $defaultConfigProvider,
        Session $session,
        Context $context,
        LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->generalConfigProvider = $generalConfigProvider;
        $this->captureOperationWrapper = $captureOperationWrapper;
        $this->subjectReader = $subjectReader;
        $this->defaultConfigProvider = $defaultConfigProvider;
        $this->session = $session;
        $this->logger = $logger;
    }

    /**
     * @return Redirect
     */
    public function execute(): Redirect
    {
        $redirect = $this->instantiateRedirect();

        $lastOrder = $this->session->getLastRealOrder();
        $payment = $lastOrder->getPayment();
        $this->process3DSecureEnrollment($redirect, $lastOrder, $payment);

        return $redirect;
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
     * @param OrderInterface $order
     * @param OrderPaymentInterface $payment
     * @return void
     */
    private function process3DSecureEnrollment(
        Redirect $redirect,
        OrderInterface $order,
        OrderPaymentInterface $payment
    ): void {
        try {
            $additionalInfo = $payment->getAdditionalInformation();
            if ($this->isCardEnrolledIn3DSecure($additionalInfo)) {
                $redirectUrl = $this->getRedirectUrl($additionalInfo);
                $redirect->setUrl($redirectUrl);
            } else {
                //As card is not enrolled in 3DS, sale command should be performed, so we just redirect to success page
                $this->processCapture($order, $redirect);
            }
        } catch (InvalidArgumentException $exception) {
            $this->messageManager->addErrorMessage(__('Transaction has been declined. Please try again later.'));
            $redirect->setPath('checkout/onepage/failure');
        }
    }

    /**
     * @param array $additionalInformation
     * @return bool
     * @SuppressWarnings(PHPMD.LongVariable)
     */
    private function isCardEnrolledIn3DSecure(array $additionalInformation): bool
    {
        if (!$this->subjectReader->hasField($additionalInformation, self::IS_CARD_ENROLLED)) {
            throw new InvalidArgumentException(__('No enrollment information provided')->render());
        }

        return (bool) $this->subjectReader->readField($additionalInformation, self::IS_CARD_ENROLLED);
    }

    /**
     * @param array $additionalInformation
     * @return string
     * @SuppressWarnings(PHPMD.LongVariable)
     */
    private function getRedirectUrl(array $additionalInformation): string
    {
        if (!$this->subjectReader->hasField($additionalInformation, self::REDIRECT_URL_PARAM)) {
            throw new InvalidArgumentException(__('No redirect provided')->render());
        }

        return (string) $this->subjectReader->readField($additionalInformation, self::REDIRECT_URL_PARAM);
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
            $redirect->setUrl($this->defaultConfigProvider->getDefaultSuccessPageUrl());
        } catch (LocalizedException $exception) {
            $this->messageManager->addErrorMessage($exception->getMessage());
            $redirect->setPath('checkout/onepage/failure');
        }
    }
}
