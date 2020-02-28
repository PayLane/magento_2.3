<?php

declare(strict_types=1);

/**
 * File: CreditCardConfigProvider.php
 *
 
 
 */

namespace PeP\PaymentGateway\Model\Checkout;

use PeP\PaymentGateway\Api\Config\Methods\GooglePayConfigProviderInterface;
use Magento\Checkout\Model\ConfigProviderInterface;

/**
 * Class CreditCardConfigProvider
 * @package PeP\PaymentGateway\Model\Checkout
 */
class GooglepayConfigProvider implements ConfigProviderInterface
{
    /**
     * @var string
     */
    private const SHOW_IMAGE_PARAM = 'show_img';

    /**
     * @var string
     */
    private const GOOGLE_MERCHANT_ID = 'google_merchant_id';

    /**
     * @var GooglePayConfigProviderInterface
     */
    private $googlePayConfigProvider;

    /**
     * GooglepayConfigProvider constructor.
     * @param GooglepayConfigProviderInterface $GooglepayConfigProvider
     * @SuppressWarnings(PHPMD.LongVariable)
     */
    public function __construct(
        GooglePayConfigProviderInterface $googlePayConfigProvider
    ) {
        $this->googlePayConfigProvider = $googlePayConfigProvider;
    }

    /**
     * @return array
     */
    public function getConfig(): array
    {
        return [
            'payment' => [
                'paylane_googlepay' => [
                    self::GOOGLE_MERCHANT_ID => $this->googlePayConfigProvider->getGoogleMerchantId()
                ],
            ]
        ];
    }
}
