<?php

declare(strict_types=1);

/**
 * File: GeneralValidator.php
 *
 
 
 */

namespace PeP\PaymentGateway\Gateway\Validator;

use PeP\PaymentGateway\Gateway\Reader\SubjectReader;
use Magento\Payment\Gateway\Command\CommandException;
use Magento\Payment\Gateway\Validator\AbstractValidator;
use Magento\Payment\Gateway\Validator\ResultInterface;
use Magento\Payment\Gateway\Validator\ResultInterfaceFactory;

/**
 * Class GeneralValidator
 * @package PeP\PaymentGateway\Gateway\Validator
 */
class GeneralValidator extends AbstractValidator
{
    /**
     * @var SubjectReader
     */
    private $subjectReader;

    /**
     * @param SubjectReader $subjectReader
     * @param ResultInterfaceFactory $resultFactory
     */
    public function __construct(
        SubjectReader $subjectReader,
        ResultInterfaceFactory $resultFactory
    ) {
        parent::__construct($resultFactory);
        $this->subjectReader = $subjectReader;
    }

    /**
     * @param array $validationSubject
     * @return ResultInterface
     * @throws CommandException
     */
    public function validate(array $validationSubject): ResultInterface
    {
        $response = $this->subjectReader->readResponse($validationSubject);
        $errorMessages = [];
        $errorCodes = [];

        if (!$this->subjectReader->wasRequestSuccessful($response)) {
            $errorCodes[] = $this->subjectReader->readErrorNumber($response);
            $errorMessages[] = $this->subjectReader->readErrorDescription($response);
        }

        return $this->createResult($this->subjectReader->wasRequestSuccessful($response), $errorMessages, $errorCodes);
    }
}
