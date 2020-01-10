<?php

declare(strict_types=1);

/**
 * File: SubjectReader.php
 *
 
 
 */

namespace PeP\PaymentGateway\Gateway\Reader;

use InvalidArgumentException;
use Magento\Payment\Gateway\Command\CommandException;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Payment\Gateway\Helper\SubjectReader as MagentoSubjectReader;

/**
 * TODO: Needs dividing into a few separate readers, used in different situations. Too many public methods.
 * Class SubjectReader
 * @package PeP\PaymentGateway\Gateway\Helper
 */
class SubjectReader
{
    /**
     * @var MagentoSubjectReader
     */
    private $subjectReader;

    /**
     * SubjectReader constructor.
     * @param MagentoSubjectReader $subjectReader
     */
    public function __construct(MagentoSubjectReader $subjectReader)
    {
        $this->subjectReader = $subjectReader;
    }

    /**
     * @param array $subject
     * @return PaymentDataObjectInterface
     * @throws CommandException
     */
    public function readPayment(array $subject): PaymentDataObjectInterface
    {
        try {
            return $this->callStaticReadPayment($subject);
        } catch (InvalidArgumentException $exception) {
            $this->throwCommandException();
        }
    }

    /**
     * @param array $subject
     * @return array
     * @throws CommandException
     */
    public function readResponse(array $subject): array
    {
        try {
            return $this->callStaticReadResponse($subject);
        } catch (InvalidArgumentException $exception) {
            $this->throwCommandException();
        }
    }

    /**
     * @param array $response
     * @return bool
     */
    public function wasRequestSuccessful(array $response): bool
    {
        return isset($response['success']) && $response['success'] === true;
    }

    /**
     * @param array $subject
     * @return array
     */
    public function readErrorInfo(array $subject): array
    {
        return isset($subject['error'])
            ? (array) $subject['error']
            : [];
    }

    /**
     * @param array $subject
     * @return string
     */
    public function readErrorNumber(array $subject): string
    {
        $error = $this->readErrorInfo($subject);
        return isset($error['error_number'])
            ? (string) $error['error_number']
            : '';
    }

    /**
     * @param array $subject
     * @return string
     */
    public function readErrorDescription(array $subject): string
    {
        $error = $this->readErrorInfo($subject);
        return isset($error['error_description'])
            ? (string) $error['error_description']
            : '';
    }

    /**
     * Used when combing back to Magento from external store.
     *
     * @param array $subject
     * @return string
     */
    public function readErrorCode(array $subject): string
    {
        $error = $subject;
        return isset($error['error_code'])
            ? (string) $error['error_code']
            : '';
    }

    /**
     * Used when combing back to Magento from external store.
     *
     * @param array $subject
     * @return string
     */
    public function readErrorText(array $subject): string
    {
        $error = $subject;
        return isset($error['error_text'])
            ? (string) $error['error_text']
            : '';
    }

    /**
     * @param array $subject
     * @param string $key
     * @return bool
     */
    public function hasField(array $subject, string $key)
    {
        return isset($subject[$key]);
    }

    /**
     * @param array $subject
     * @param string $key
     * @return string|array
     */
    public function readField(array $subject, string $key)
    {
        if ($this->hasField($subject, $key)) {
            return $subject[$key];
        } else {
            return '';
        }
    }

    /**
     * @param array $subject
     * @return PaymentDataObjectInterface
     * @throws InvalidArgumentException
     * @codeCoverageIgnore
     */
    protected function callStaticReadPayment(array $subject): PaymentDataObjectInterface
    {
        return $this->subjectReader->readPayment($subject);
    }

    /**
     * @param array $subject
     * @return array $response
     * @throws InvalidArgumentException
     * @codeCoverageIgnore
     */
    protected function callStaticReadResponse(array $subject): array
    {
        return $this->subjectReader->readResponse($subject);
    }

    /**
     * @return void
     * @throws CommandException
     */
    private function throwCommandException(): void
    {
        throw new CommandException(__('Transaction has been declined. Please try again later.'));
    }
}
