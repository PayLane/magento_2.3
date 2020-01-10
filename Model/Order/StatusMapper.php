<?php

declare(strict_types=1);

/**
 * File: StatusMapper.php
 *
 
 
 */

namespace PeP\PaymentGateway\Model\Order;

use PeP\PaymentGateway\Api\Config\GeneralConfigProviderInterface;
use PeP\PaymentGateway\Api\Order\StatusMapperInterface;
use PeP\PaymentGateway\Model\Notification\Data;

/**
 * Class StatusMapper
 * @package PeP\PaymentGateway\Model\Order
 */
class StatusMapper implements StatusMapperInterface
{
    /**
     * @var GeneralConfigProviderInterface
     */
    private $generalConfigProvider;

    /**
     * OrderStatusMapper constructor.
     * @param GeneralConfigProviderInterface $generalConfigProvider
     * @SuppressWarnings(PHPMD.LongVariable)
     */
    public function __construct(GeneralConfigProviderInterface $generalConfigProvider)
    {
        $this->generalConfigProvider = $generalConfigProvider;
    }

    /**
     * @param string $notificationStatus
     * @return string
     */
    public function map(string $notificationStatus): string
    {
        switch ($notificationStatus) {
            case Data::STATUS_PENDING:
                return $this->generalConfigProvider->getPendingOrderStatus();
            case Data::STATUS_PERFORMED:
                return $this->generalConfigProvider->getPerformedOrderStatus();
            case Data::STATUS_CLEARED:
                return $this->generalConfigProvider->getClearedOrderStatus();
            case Data::STATUS_ERROR:
            default:
                return $this->generalConfigProvider->getErrorOrderStatus();
        }
    }
}
