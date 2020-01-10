<?php

declare(strict_types=1);

/**
 * File: RequiredResponseValueValidator.php
 *
 
 
 */

namespace PeP\PaymentGateway\Gateway\Validator;

use PeP\PaymentGateway\Gateway\Reader\SubjectReader;
use Magento\Payment\Gateway\Command\CommandException;
use Magento\Payment\Gateway\Validator\AbstractValidator;
use Magento\Payment\Gateway\Validator\ResultInterface;
use Magento\Payment\Gateway\Validator\ResultInterfaceFactory;

/**
 * Class RequiredResponseValueValidator
 * @package PeP\PaymentGateway\Gateway\Validator
 */
class RequiredValueValidator extends AbstractValidator
{
    /**
     * @var SubjectReader
     */
    private $subjectReader;

    /**
     * @var ResultInterfaceFactory
     */
    private $resultFactory;

    /**
     * @var array
     */
    private $requiredValuesKeys;

    /**
     * @param SubjectReader $subjectReader
     * @param ResultInterfaceFactory $resultFactory
     * @param array $requiredValuesKeys
     */
    public function __construct(
        SubjectReader $subjectReader,
        ResultInterfaceFactory $resultFactory,
        array $requiredValuesKeys = []
    ) {
        parent::__construct($resultFactory);
        $this->subjectReader = $subjectReader;
        $this->resultFactory = $resultFactory;
        $this->requiredValuesKeys = $requiredValuesKeys;
    }

    /**
     * @param array $validationSubject
     * @return ResultInterface
     * @throws CommandException
     */
    public function validate(array $validationSubject): ResultInterface
    {
        $isSuccessful = true;
        $errorMessage = 'Payment provider gateway response is not valid. Parameter %s is required';
        $errorMessages = [];
        $response = $this->subjectReader->readResponse($validationSubject);

        foreach ($this->requiredValuesKeys as $requiredValuesKey) {
            if (!$this->subjectReader->hasField($response, $requiredValuesKey)) {
                $isSuccessful = false;
                $errorMessages[] = sprintf($errorMessage, $requiredValuesKey);
            }
        }

        return $this->createResult($isSuccessful, $errorMessages);
    }
}
