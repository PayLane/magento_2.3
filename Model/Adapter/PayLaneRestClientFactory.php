<?php

declare(strict_types=1);

/**
 * File: PayLaneRestClientFactory.php
 *
 
 */

namespace PeP\PaymentGateway\Model\Adapter;

use PeP\PaymentGateway\Api\Adapter\PayLaneRestClientFactoryInterface;
use PeP\PaymentGateway\Api\Config\GeneralAuthenticationConfigProviderInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\ObjectManagerInterface;

/**
 * @TODO: Unit test
 * Class PayLaneRestClientFactory
 * @package PeP\PaymentGateway\Model\Adapter
 */
class PayLaneRestClientFactory implements PayLaneRestClientFactoryInterface
{
    /**
     * @var ScopeConfigInterface
     */
    private $generalAuthenticationConfigProvider;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * PayLaneRestClientFactory constructor.
     * @param GeneralAuthenticationConfigProviderInterface $generalAuthenticationConfigProvider
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(
        GeneralAuthenticationConfigProviderInterface $generalAuthenticationConfigProvider,
        ObjectManagerInterface $objectManager
    ) {
        $this->generalAuthenticationConfigProvider = $generalAuthenticationConfigProvider;
        $this->objectManager = $objectManager;
    }

    /**
     * @return PayLaneRestClient
     */
    public function create(): PayLaneRestClient
    {
        return $this->objectManager->create(
            PayLaneRestClient::class,
            [
                'username' => $this->generalAuthenticationConfigProvider->getUsername(),
                'password' => $this->generalAuthenticationConfigProvider->getPassword()
            ]
        );
    }
}
