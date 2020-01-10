<?php

declare(strict_types=1);

/**
 * File: Level.php
 *
 
 */

namespace PeP\PaymentGateway\Model\Source\Avs\Check;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Level
 * @package PeP\PaymentGateway\Model\Source\Avs\Check
 */
class Level implements OptionSourceInterface
{
    /**
     * @return array
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => 0, 'label' => '0'],
            ['value' => 1, 'label' => '1'],
            ['value' => 2, 'label' => '2'],
            ['value' => 3, 'label' => '3'],
            ['value' => 4, 'label' => '4'],
        ];
    }
}
