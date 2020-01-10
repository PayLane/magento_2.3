<?php

declare(strict_types=1);

/**
 * File: Action.php
 *
 
 
 */

namespace PeP\PaymentGateway\Model\Source\Payment;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Payment\Model\MethodInterface;

/**
 * Class Action
 * @package PeP\PaymentGateway\Model\Source\Payment
 */
class Action implements OptionSourceInterface
{
    /**
     * @return array
     */
    public function toOptionArray(): array
    {
        return [
            [
                'value' => MethodInterface::ACTION_AUTHORIZE,
                'label' => __('Authorize only'),
            ],
            [
                'value' => MethodInterface::ACTION_AUTHORIZE_CAPTURE,
                'label' => __('Authorize & Capture (Sale)')
            ]
        ];
    }
}
