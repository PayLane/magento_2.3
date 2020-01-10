<?php

declare(strict_types=1);

/**
 * File: SecureFormConfigProvider.php
 *
 
 
 */

namespace PeP\PaymentGateway\Model\Checkout;

use PeP\PaymentGateway\Api\Config\Methods\SecureFormConfigProviderInterface;
use Magento\Checkout\Model\ConfigProviderInterface;

/**
 * Class SecureFormConfigProvider
 * @package PeP\PaymentGateway\Model\Checkout
 */
class SecureFormConfigProvider implements ConfigProviderInterface
{
    /**
     * @var SecureFormConfigProviderInterface
     */
    private $secureFormConfigProvider;

    /**
     * SecureFormConfigProvider constructor.
     * @param SecureFormConfigProviderInterface $secureFormConfigProvider
     * @SuppressWarnings(PHPMD.LongVariable)
     */
    public function __construct(SecureFormConfigProviderInterface $secureFormConfigProvider)
    {
        $this->secureFormConfigProvider = $secureFormConfigProvider;
    }

    /**
     * @return array
     */
    public function getConfig(): array
    {
        $result = [
            'payment' => [
                'paylane_secureform' => [
                    'show_img' => (bool) $this->secureFormConfigProvider->isPaymentMethodImageShown()
                ]
            ]
        ];

        return $result;
    }
}
