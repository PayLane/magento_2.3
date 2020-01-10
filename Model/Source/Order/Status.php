<?php

declare(strict_types=1);

/**
 * File: Status.php
 *
 
 */

namespace PeP\PaymentGateway\Model\Source\Order;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Config;

/**
 * Class Status
 * @package PeP\PaymentGateway\Model\Source\Order
 */
class Status implements OptionSourceInterface
{
    /**
     * @var array
     */
    protected $stateStatuses = [
        Order::STATE_PROCESSING,
        // Order::STATE_CANCELED,
    ];

    /**
     * @var Config
     */
    protected $orderConfig;

    /**
     * Status constructor.
     * @param Config $orderConfig
     */
    public function __construct(Config $orderConfig)
    {
        $this->orderConfig = $orderConfig;
    }

    /**
     * @return array
     */
    public function toOptionArray(): array
    {
        $statuses = $this->orderConfig->getStateStatuses($this->stateStatuses);

        $options = [['value' => '', 'label' => __('-- Please Select --')]];

        foreach ($statuses as $code => $label) {
            $options[] = ['value' => $code, 'label' => $label];
        }

        return $options;
    }
}
