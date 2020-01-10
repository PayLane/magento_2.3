<?php

declare(strict_types=1);

/**
 * File: Mode.php
 *
 
 */

namespace PeP\PaymentGateway\Model\Source\Payment;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Mode
 * @package PeP\PaymentGateway\Model\Source\Payment
 */
class Mode implements OptionSourceInterface
{
    /**
     * @var string
     */
    public const API_MODE = 'API';

    /**
     * @var string
     */
    public const SECURE_FORM_MODE = 'SECURE_FORM';

    /**
     * @return array
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => self::API_MODE, 'label' => __('API')],
            ['value' => self::SECURE_FORM_MODE, 'label' => __('Secure Form')],
        ];
    }
}
