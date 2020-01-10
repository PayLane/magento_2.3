<?php

declare(strict_types=1);

/**
 * File: CreditCardConfigProvider.php
 *
 
 
 */

namespace PeP\PaymentGateway\Model\Checkout;

use PeP\PaymentGateway\Api\Config\Methods\CreditCardConfigProviderInterface;
use Magento\Checkout\Model\ConfigProviderInterface;

/**
 * Class CreditCardConfigProvider
 * @package PeP\PaymentGateway\Model\Checkout
 */
class CreditCardConfigProvider implements ConfigProviderInterface
{
    /**
     * @var string
     */
    private const SHOW_IMAGE_PARAM = 'show_img';

    /**
     * @var string
     */
    private const IS_3DS_CHECK_ENABLED = 'is_3ds_check_enabled';

    /**
     * @var CreditCardConfigProviderInterface
     */
    private $creditCardConfigProvider;

    /**
     * CreditCardConfigProvider constructor.
     * @param CreditCardConfigProviderInterface $creditCardConfigProvider
     * @SuppressWarnings(PHPMD.LongVariable)
     */
    public function __construct(
        CreditCardConfigProviderInterface $creditCardConfigProvider
    ) {
        $this->creditCardConfigProvider = $creditCardConfigProvider;
    }

    /**
     * @return array
     */
    public function getConfig(): array
    {
        return [
            'payment' => [
                'paylane_creditcard' => [
                    self::SHOW_IMAGE_PARAM => $this->creditCardConfigProvider->isPaymentMethodImageShown(),
                    self::IS_3DS_CHECK_ENABLED => $this->creditCardConfigProvider->is3DSCheckEnabled()
                ],
            ]
        ];
    }
}
