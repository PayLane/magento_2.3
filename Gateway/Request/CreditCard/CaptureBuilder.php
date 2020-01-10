<?php

declare(strict_types=1);

/**
 * File: CaptureBuilder.php
 *
 
 
 */

namespace PeP\PaymentGateway\Gateway\Request\CreditCard;

use PeP\PaymentGateway\Gateway\Reader\SubjectReader;
use Magento\Payment\Gateway\Command\CommandException;
use Magento\Payment\Gateway\Request\BuilderInterface;

/**
 * Class CaptureBuilder
 * @package PeP\PaymentGateway\Gateway\Request\CreditCard
 */
class CaptureBuilder implements BuilderInterface
{
    /**
     * @var string
     */
    private const ID_AUTHORIZATION_PARAM = 'id_authorization';

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
        $paymentDO = $this->subjectReader->readPayment($buildSubject);
        $paymentInfo = $paymentDO->getPayment();
        $order  = $paymentDO->getOrder();

        $result = [
            'id_authorization' => $paymentInfo->getAdditionalInformation(self::ID_AUTHORIZATION_PARAM),
            'amount' => sprintf('%01.2f', $order->getGrandTotalAmount()),
            'description' => $order->getOrderIncrementId()
        ];

        return $result;
    }
}
