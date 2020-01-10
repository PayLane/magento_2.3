<?php

/**
 * File: Handle.php
 *
 
 */

declare(strict_types=1);

namespace PeP\PaymentGateway\Controller\Applepay;

use Exception;
use PeP\PaymentGateway\Model\Payment\Adapter\Applepay;
use Magento\Checkout\Model\Type\Onepage;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\QuoteFactory;
use Magento\Quote\Model\ResourceModel\Quote as QuoteResource;
use Magento\Quote\Model\ResourceModel\Quote\Payment as PaymentResource;

/**
 * Class Handle
 * @package PeP\PaymentGateway\Controller\Applepay
 */
class Handle extends Action
{
    /**
     * @var Applepay
     */
    protected $adapter;

    /**
     * @var JsonFactory
     */
    protected $jsonFactory;

    /**
     * @var QuoteFactory
     */
    protected $quoteFactory;

    /**
     * @var QuoteResource
     */
    protected $quoteResource;

    /**
     * @var PaymentResource
     */
    private $paymentResource;

    /**
     * Handle constructor.
     *
     * @param Context $context
     * @param Applepay $adapter
     * @param JsonFactory $jsonFactory
     * @param QuoteFactory $quoteFactory
     * @param QuoteResource $quoteResource
     * @param PaymentResource $paymentResource
     */
    public function __construct(
        Context $context,
        Applepay $adapter,
        JsonFactory $jsonFactory,
        QuoteFactory $quoteFactory,
        QuoteResource $quoteResource,
        PaymentResource $paymentResource
    ) {
        parent::__construct($context);
        $this->adapter = $adapter;
        $this->jsonFactory = $jsonFactory;
        $this->quoteFactory = $quoteFactory;
        $this->quoteResource = $quoteResource;
        $this->paymentResource = $paymentResource;
    }

    /**
     * @return $this|ResponseInterface|ResultInterface
     * @throws NoSuchEntityException
     * @throws Exception
     * @throws CouldNotSaveException
     * @throws LocalizedException
     */
    public function execute()
    {
        $params = $this->parseRequestParams();
        $quote = $this->quoteFactory->create();
        $this->quoteResource->load($quote, $params['quote_id']);

        if ($quote->getId()) {
            $this->savePaymentData($quote, $params);

            $params['customer']['ip'] = $_SERVER['REMOTE_ADDR'];

            $this->adapter->setQuote($quote);
            $this->adapter->setParams($params);
            $responseData = $this->adapter->handleRequest();

            $order = $this->adapter->handleResponse($responseData, $this->getResponse());

            $additionalData = [];

            if (is_string($order)) {
                return $this->jsonFactory->create()->setData([
                    'message' => [
                        'result' => $order
                    ],
                    'error_description' => 'no checkout method',
                    'success' => false
                ]);
            }

            if (is_string($order) || !$order->getId()) {
                if (isset($responseData['error']) && isset($responseData['error']['error_description'])) {
                    $errorDescription = $responseData['error']['error_description'];
                } else {
                    $errorDescription = __(
                        'Error while processing order - please contact store administrator or try again'
                    );
                }
            } else {
                $errorDescription = null;
                $additionalData = [
                    'quote_id' => $quote->getId(),
                    'order_id' => $order->getId(),
                    'order_status' => $order->getStatus(),
                    'increment_id' => $order->getIncrementId()
                ];
            }

            return $this->jsonFactory->create()->setData(array_merge([
                'success' => $order->getId() ? true : false,
                'error_description' => $errorDescription
            ], $additionalData));
        } else {
            return $this->jsonFactory->create()->setData([
                'success' => false,
                'error_description' => __('Quote with provided ID doesn\'t exists')
            ]);
        }
    }

    /**
     * @return mixed
     */
    private function parseRequestParams()
    {
        $params = json_decode(file_get_contents('php://input'), true);
        return $params;
    }

    /**
     * @param Quote $quote
     * @param $params
     * @return void
     * @throws LocalizedException
     */
    private function savePaymentData(Quote $quote, $params)
    {
        $payment = $quote->getPayment();
        $payment->setMethod('paylane_applepay');
        $payment->setAdditionalInformation('token', $params['card']['token']);
        $this->paymentResource->save($payment);

        $quote->reserveOrderId();

        if (!$quote->getCheckoutMethod()) {
            if (!$quote->getCustomer()->getEmail()) {
                $quote->setCheckoutMethod(Onepage::METHOD_GUEST);
            } else {
                $quote->setCheckoutMethod(Onepage::METHOD_REGISTER);
            }
        }

        $this->quoteResource->save($quote);
    }
}
