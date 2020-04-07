<?php

namespace PeP\PaymentGateway\Plugin;

use Magento\Sales\Api\Data\OrderExtensionFactory;
use Magento\Sales\Api\Data\OrderExtensionInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderSearchResultInterface;
use Magento\Sales\Api\OrderRepositoryInterface;

/**
 * Class OrderRepositoryPlugin
 */
class OrderRepositoryPlugin
{
    const FIELD_TIMESTAMP = 'paylane_notification_timestamp';
    const FIELD_STATUS = 'paylane_notification_status';

    /**
     * Order Extension Attributes Factory
     *
     * @var OrderExtensionFactory
     */
    protected $extensionFactory;

    /**
     * OrderRepositoryPlugin constructor
     *
     * @param OrderExtensionFactory $extensionFactory
     */
    public function __construct(OrderExtensionFactory $extensionFactory)
    {
        $this->extensionFactory = $extensionFactory;
    }

    /**
     * @param OrderRepositoryInterface $subject
     * @param OrderInterface $order
     *
     * @return OrderInterface
     */
    public function afterGet(OrderRepositoryInterface $subject, OrderInterface $order)
    {
        $fieldTimestamp = $order->getData(self::FIELD_TIMESTAMP);
        $fieldStatus = $order->getData(self::FIELD_STATUS);
        $extensionAttributes = $order->getExtensionAttributes();
        $extensionAttributes = $extensionAttributes ? $extensionAttributes : $this->extensionFactory->create();
        $extensionAttributes->setPaylaneNotificationTimestamp($fieldTimestamp);
        $extensionAttributes->setPaylaneNotificationStatus($fieldStatus);
        $order->setExtensionAttributes($extensionAttributes);

        return $order;
    }

    /**
     * @param OrderRepositoryInterface $subject
     * @param OrderSearchResultInterface $searchResult
     *
     * @return OrderSearchResultInterface
     */
    public function afterGetList(OrderRepositoryInterface $subject, OrderSearchResultInterface $searchResult)
    {
        $orders = $searchResult->getItems();

        foreach ($orders as &$order) {
            $fieldTimestamp = $order->getData(self::FIELD_TIMESTAMP);
            $fieldStatus = $order->getData(self::FIELD_STATUS);
            $extensionAttributes = $order->getExtensionAttributes();
            $extensionAttributes = $extensionAttributes ? $extensionAttributes : $this->extensionFactory->create();
            $extensionAttributes->setPaylaneNotificationTimestamp($fieldTimestamp);
            $extensionAttributes->setPaylaneNotificationStatus($fieldStatus);
            $order->setExtensionAttributes($extensionAttributes);
        }

        return $searchResult;
    }
}