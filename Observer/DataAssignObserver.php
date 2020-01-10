<?php

declare(strict_types=1);

/**
 * File: DataAssignObserver.php
 *
 
 */

namespace PeP\PaymentGateway\Observer;

use Magento\Framework\Event\Observer;
use Magento\Payment\Observer\AbstractDataAssignObserver;
use Magento\Quote\Api\Data\PaymentInterface;

/**
 * Class DataAssignObserver
 * @package PeP\PaymentGateway\Observer
 */
class DataAssignObserver extends AbstractDataAssignObserver
{
    /**
     * @var array
     */
    private $additionalInformationList = [
        'token'
    ];

    /**
     * @param Observer $observer
     * @return void
     * @SuppressWarnings(PHPMD.LongVariable)
     */
    public function execute(Observer $observer): void
    {
        $data = $this->readDataArgument($observer);
        $additionalData = $data->getData(PaymentInterface::KEY_ADDITIONAL_DATA);

        if (!is_array($additionalData)) {
            return;
        }

        $paymentInfo = $this->readPaymentModelArgument($observer);

        foreach ($this->additionalInformationList as $additionalInformationKey) {
            if (isset($additionalData[$additionalInformationKey])) {
                $paymentInfo->setAdditionalInformation(
                    $additionalInformationKey,
                    $additionalData[$additionalInformationKey]
                );
            }
        }
    }
}
