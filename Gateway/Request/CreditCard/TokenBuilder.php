<?php

declare(strict_types=1);

/**
 * File: TokenBuilder.php
 *
 
 
 */

namespace PeP\PaymentGateway\Gateway\Request\CreditCard;

use PeP\PaymentGateway\Gateway\Reader\SubjectReader;
use Magento\Payment\Gateway\Command\CommandException;
use Magento\Payment\Gateway\Request\BuilderInterface;

/**
 * Class TokenBuilder
 * @package PeP\PaymentGateway\Gateway\Request\CreditCard
 */
class TokenBuilder implements BuilderInterface
{
    /**
     * @var string
     */
    private const TOKEN_PARAM = 'token';

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
        $paymentInfo = $this->subjectReader->readPayment($buildSubject)->getPayment();

        $result = [
            'card' => [
                self::TOKEN_PARAM => $paymentInfo->getAdditionalInformation(self::TOKEN_PARAM)
            ]
        ];

        return $result;
    }
}
