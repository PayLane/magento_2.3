<?php

declare(strict_types=1);

/**
 * File: AbstractClient.php
 *
 
 
 */

namespace PeP\PaymentGateway\Gateway\Http\Client;

use Exception;
use PeP\PaymentGateway\Model\Adapter\PayLaneRestClientFactory;
use Magento\Payment\Gateway\Http\ClientException;
use Magento\Payment\Gateway\Http\ClientInterface;
use Magento\Payment\Gateway\Http\TransferInterface;
use Magento\Payment\Model\Method\Logger;
use Psr\Log\LoggerInterface;

/**
 * Class AbstractClient
 * @package PeP\PaymentGateway\Gateway\Http\Client
 */
abstract class AbstractClient implements ClientInterface
{
    /**
     * @var PayLaneRestClientFactory
     */
    protected $payLaneRestClientFactory;

    /**
     * @var Logger
     */
    protected $paymentLogger;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * TransactionSale constructor.
     * @param PayLaneRestClientFactory $payLaneRestClientFactory
     * @param Logger $paymentLogger
     * @param LoggerInterface $logger
     * @SuppressWarnings(PHPMD.LongVariable)
     */
    public function __construct(
        PayLaneRestClientFactory $payLaneRestClientFactory,
        Logger $paymentLogger,
        LoggerInterface $logger
    ) {
        $this->payLaneRestClientFactory = $payLaneRestClientFactory;
        $this->paymentLogger = $paymentLogger;
        $this->logger = $logger;
    }

    /**
     * @param TransferInterface $transferObject
     * @return array
     * @throws ClientException
     */
    public function placeRequest(TransferInterface $transferObject): array
    {
        $data = $transferObject->getBody();
        $log = $this->createLogPayload($data);

        $response = [];

        $this->logger->info(">>> ========== CARD PAYMENT START ========== <<<\n". json_encode($log));

        try {
            $response = $this->process($data);
        } catch (Exception $exception) {
            $message = __($exception->getMessage() ?: 'Sorry, but something went wrong');
            $this->logger->critical($message);
            throw new ClientException($message);
        } finally {
            $log['response'] = $response;
            $this->paymentLogger->debug($log);
        }

        return $response;
    }

    /**
     * Process http request
     * @param array $data
     * @return array
     */
    abstract protected function process(array $data): array;

    /**
     * @param array $requestData
     * @return array
     */
    protected function createLogPayload(array $requestData): array
    {
        return [
            'request' => $requestData,
            'client' => static::class
        ];
    }
}
