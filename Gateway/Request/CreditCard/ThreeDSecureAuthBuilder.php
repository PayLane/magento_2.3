<?php

declare(strict_types=1);

/**
 * File: ThreeDSecureAuthBuilder.php
 *
 
 
 */

namespace PeP\PaymentGateway\Gateway\Request\CreditCard;

use PeP\PaymentGateway\Gateway\Reader\SubjectReader;
use Magento\Payment\Gateway\Command\CommandException;
use Magento\Payment\Gateway\Request\BuilderInterface;

/**
 * Class ThreeDSecureAuthBuilder
 * @package PeP\PaymentGateway\Gateway\Request\CreditCard
 */
class ThreeDSecureAuthBuilder implements BuilderInterface
{
    /**
     * @var string
     */
    private const ID_3DSECURE_AUTH_PARAM = 'id_3dsecure_auth';

    /**
     * @var SubjectReader
     */
    private $subjectReader;

    /**
     * ThreeDSecureAuthBuilder constructor.
     * @param SubjectReader $subjectReader
     * @SuppressWarnings(PHPMD.LongVariable)
     */
    public function __construct(
        SubjectReader $subjectReader
    ) {
        $this->subjectReader = $subjectReader;
    }

    /**
     * @param array $buildSubject
     * @return array
     * @throws CommandException
     */
    public function build(array $buildSubject): array
    {
        $paymentInfo = $this->subjectReader->readPayment($buildSubject)->getPayment();
        return [self::ID_3DSECURE_AUTH_PARAM => $paymentInfo->getAdditionalInformation(self::ID_3DSECURE_AUTH_PARAM)];
    }
}
