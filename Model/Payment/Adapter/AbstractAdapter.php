<?php

declare(strict_types=1);

/**
 * File: AbstractAdapter.php
 *
 
 */

namespace PeP\PaymentGateway\Model\Payment\Adapter;

use Exception;
use PeP\PaymentGateway\Api\Adapter\PayLaneRestClientFactoryInterface;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Quote\Model\Quote;
use Magento\Sales\Model\Spi\OrderResourceInterface as OrderResource;

/**
 * Class AbstractAdapter
 * @package PeP\PaymentGateway\Model\Payment\Adapter
 */
abstract class AbstractAdapter
{
    /**
     * @var PayLaneRestClientFactoryInterface
     */
    protected $payLaneRestClientFactory;

    /**
     * @var RedirectInterface
     */
    protected $redirect;

    /**
     * @var Quote
     */
    protected $quote;

    /**
     * @var OrderResource
     */
    protected $orderResource;

    /**
     * Constructor
     *
     * @param PayLaneRestClientFactoryInterface $payLaneRestClientFactory
     * @param RedirectInterface $redirect
     * @param OrderResource $orderResource
     * @SuppressWarnings(PHPMD.LongVariable)
     */
    public function __construct(
        PayLaneRestClientFactoryInterface $payLaneRestClientFactory,
        RedirectInterface $redirect,
        OrderResource $orderResource
    ) {
        $this->payLaneRestClientFactory = $payLaneRestClientFactory;
        $this->redirect = $redirect;
        $this->orderResource = $orderResource;
    }

    /**
     * @param Quote $quote
     * @return void
     */
    public function setQuote(Quote $quote): void
    {
        $this->quote = $quote;
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
     * @return array
     */
    public function getAdditionalFields(): array
    {
        return [];
    }

    /**
     * @param bool $success
     * @param ResponseInterface $response
     * @return void
     */
    protected function handleRedirect(bool $success, ResponseInterface $response): void
    {
        if ($success) {
            $this->redirect->redirect($response, 'checkout/onepage/success', [
                '_nosid' => true,
                '_secure' => true
            ]);
        } else {
            $this->redirect->redirect($response, 'checkout/onepage/failure', [
                '_nosid' => true,
                '_secure' => true
            ]);
        }
    }

    /**
     * @return mixed
     */
    abstract protected function buildRequest();

    /**
     * @param array $requestData
     * @return mixed
     * @throws Exception
     */
    abstract protected function makeRequest(array $requestData);

    /**
     * @param array $responseData
     * @param ResponseInterface $response
     * @return mixed
     */
    abstract public function handleResponse(array $responseData, ResponseInterface $response);
}
