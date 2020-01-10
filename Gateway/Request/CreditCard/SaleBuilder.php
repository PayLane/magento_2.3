<?php

declare(strict_types=1);

/**
 * File: SaleBuilder.php
 *
 
 
 */

namespace PeP\PaymentGateway\Gateway\Request\CreditCard;

use PeP\PaymentGateway\Gateway\Reader\SubjectReader;
use Magento\Payment\Gateway\Command\CommandException;
use Magento\Payment\Gateway\Request\BuilderInterface;

/**
 * Class SaleBuilder
 * @package PeP\PaymentGateway\Gateway\Request\CreditCard
 */
class SaleBuilder implements BuilderInterface
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

        $result = [
            'sale' => [
                'amount' => sprintf('%01.2f', $orderDO->getGrandTotalAmount()),
                'currency' => $orderDO->getCurrencyCode(),
                'description' => $orderDO->getOrderIncrementId()
            ]
        ];

        return $result;
    }
}
