<?php

declare(strict_types=1);

/**
 * File: Mode.php
 *
 
 */

namespace PeP\PaymentGateway\Model\Source\Notification;

use PeP\PaymentGateway\Model\Notification\Data;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Mode
 * @package PeP\PaymentGateway\Model\Source\Notification
 */
class Mode implements OptionSourceInterface
{
    /**
     * @return array
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => Data::MODE_MANUAL, 'label' => __('Manual')],
            ['value' => Data::MODE_AUTO, 'label' => __('Automatic')]
        ];
    }
}
