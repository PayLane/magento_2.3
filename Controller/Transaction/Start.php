<?php

declare(strict_types=1);

/**
 * File: Start.php
 *
 
 */

namespace PeP\PaymentGateway\Controller\Transaction;

use Exception;
use Magento\Checkout\Model\Session;
use Magento\Checkout\Model\Type\Onepage;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Model\ResourceModel\Quote as QuoteResource;
use Magento\Quote\Model\ResourceModel\Quote\Payment as PaymentResource;
use Psr\Log\LoggerInterface;

/**
 * Class Start
 * @package PeP\PaymentGateway\Controller\Transaction
 */
class Start extends Action
{
    /**
     * @var Session
     */
    protected $session;

    /**
     * @var QuoteResource
     */
    protected $quoteResource;

    /**
     * @var PaymentResource
     */
    private $paymentResource;

     /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Start constructor.
     * @param Context $context
     * @param Session $session
     * @param QuoteResource $quoteResource
     * @param PaymentResource $paymentResource
     * @param LoggerInterface $logger
     */
    public function __construct(
        Context $context,
        Session $session,
        QuoteResource $quoteResource,
        PaymentResource $paymentResource,
        LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->session = $session;
        $this->quoteResource = $quoteResource;
        $this->paymentResource = $paymentResource;
        $this->logger = $logger;
    }

    /**
     * @return ResponseInterface|ResultInterface|void
     * @throws Exception
     * @throws LocalizedException
     */
    public function execute()
    {
        $params = $this->getRequest()->getParams();

        $this->logger->info("PAYMENT START\n". \json_encode($params));

        if (isset($params['method_code'])) {
            $paymentMethodCode = $params['method_code'];

            $quote = $this->session->getQuote();

            if (!$quote->getCheckoutMethod()) {
                if (!$quote->getCustomer()->getEmail()) {
                    $quote->setCheckoutMethod(Onepage::METHOD_GUEST);
                } else {
                    $quote->setCheckoutMethod(Onepage::METHOD_REGISTER);
                }
            }

            if ($quote->getId()) {
                $payment = $quote->getPayment();
                $payment->setMethod($paymentMethodCode);

                $objectManager = ObjectManager::getInstance();
                $adapter = $objectManager->create(
                    'PeP\PaymentGateway\Model\Payment\Adapter\\' . ucwords(
                        str_replace(
                            'paylane_',
                            '',
                            $paymentMethodCode
                        )
                    )
                );

                $additionalFields = $adapter->getAdditionalFields();

                foreach ($additionalFields as $field) {
                    if (isset($params['additional_data'][$field])) {
                        $payment->setAdditionalInformation($field, $params['additional_data'][$field]);
                    }
                }

                $this->paymentResource->save($payment);

                $quote->reserveOrderId();
                $this->quoteResource->save($quote);

                $adapter->setQuote($quote);

                try {
                    $responseData = $adapter->handleRequest();
                    //print_r($responseData);exit;

                } catch (Exception $exception) {
                    $responseData['error'] = [
                        'error_description' => 'Unauthorized',
                        'error_number' => 505,
                    ];

                    $msg = __('Payment could not be fulfilled. Please contact out support.');

                    $this->messageManager->addErrorMessage(
                        $msg
                    );

                    $this->logger->error($msg);
                }

                $adapter->handleResponse($responseData, $this->getResponse());

            } else {
                $this->logger->error('Quote not found!');
                $this->_redirect('checkout/onepage/failure', ['_nosid' => true, '_secure' => true]);
            }
        } else {
            $this->logger->error('No payment method code!');
            $this->_redirect('checkout/onepage/failure', ['_nosid' => true, '_secure' => true]);
        }
    }
}
