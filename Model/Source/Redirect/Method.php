<?php

declare(strict_types=1);

/**
 * File: Method.php
 *
 
 */

namespace PeP\PaymentGateway\Model\Source\Redirect;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Method
 * @package PeP\PaymentGateway\Model\Source\Redirect
 */
class Method implements OptionSourceInterface
{
    /**
     * @return array
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => 'GET', 'label' => __('GET')],
            ['value' => 'POST', 'label' => __('POST')],
        ];
    }
}
