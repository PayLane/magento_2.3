<?php

declare(strict_types=1);

/**
 * File: CustomerBuilder.php
 *
 
 
 */

namespace PeP\PaymentGateway\Gateway\Request\CreditCard;

use PeP\PaymentGateway\Gateway\Reader\SubjectReader;
use Magento\Payment\Gateway\Command\CommandException;
use Magento\Payment\Gateway\Request\BuilderInterface;

/**
 * Class CustomerBuilder
 * @package PeP\PaymentGateway\Gateway\Request\CreditCard
 */
class CustomerBuilder implements BuilderInterface
{
    /**
     * @var SubjectReader
     */
    private $subjectReader;

    /**
     * Constructor
     *
     * @param SubjectReader $subjectReader
     */
    public function __construct(SubjectReader $subjectReader)
    {
        $this->subjectReader = $subjectReader;
    }

    /**
     * @param array $buildSubject
     * @return array
     * @throws CommandException
     */
    public function build(array $buildSubject): array
    {
        $orderDO = $this->subjectReader->readPayment($buildSubject)->getOrder();

        $billingAddress = $orderDO->getBillingAddress();

        $result['customer'] = [
            'name' => $billingAddress->getFirstname() . ' ' . $billingAddress->getLastname(),
            'email' => $billingAddress->getEmail(),
            'ip' => $orderDO->getRemoteIp(),
            'address' => [
                'street_house' => join(',', [$billingAddress->getStreetLine1(), $billingAddress->getStreetLine2()]),
                'city' => $billingAddress->getCity(),
                'state' => $billingAddress->getRegionCode(),
                'zip' => $billingAddress->getPostcode(),
                'country_code' => $billingAddress->getCountryId(),
            ]
        ];

        return $result;
    }
}
