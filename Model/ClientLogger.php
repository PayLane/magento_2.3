<?php

declare(strict_types=1);

/**
 * File: ClientLogger.php
 *
 */

namespace PeP\PaymentGateway\Model;

use PeP\PaymentGateway\Api\ClientLoggerInterface;
use PeP\PaymentGateway\Api\Config\GeneralConfigProviderInterface;
use Psr\Log\LoggerInterface;

/**
 * Class ClientLogger
 * @package PeP\PaymentGateway\Model\ClientLogger
 */
class ClientLogger implements ClientLoggerInterface
{
    /**
     * @var GeneralConfigProviderInterface
     */
    private $generalConfigProvider;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * ClientLogger constructor.
     * @param GeneralConfigProviderInterface $generalConfigProvider
     * @param LoggerInterface $logger
     * @SuppressWarnings(PHPMD.LongVariable)
     */
    public function __construct(
        GeneralConfigProviderInterface $generalConfigProvider,
        LoggerInterface $logger
    ) {
        $this->generalConfigProvider = $generalConfigProvider;
        $this->logger = $logger;
    }

     /**
     * @param string $message
     * @param array $params
     * @param string $type
     * @return void
     */
    public function log(string $message, array $params = [], string $type = 'info')
    {
        if ($this->generalConfigProvider->isLoggingEnabled()) {
            switch ($type) {
                case 'error':
                    $this->logger->critical($message, $params);
                    break;
                default:
                    $this->logger->info($message, $params);
            }
        }
    }
}
