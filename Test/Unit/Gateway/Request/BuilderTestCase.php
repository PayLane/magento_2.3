<?php

declare(strict_types=1);

/**
 * File: BuilderTestCase.php
 *
 
 
 */

namespace PeP\PaymentGateway\Test\Unit\Gateway\Request;

use PeP\PaymentGateway\Test\Unit\Gateway\SubjectReaderTestTrait;
use Magento\Payment\Gateway\Command\CommandException;
use Magento\Payment\Gateway\Request\BuilderInterface;
use PHPUnit\Framework\TestCase;

/**
 * Class BuilderTestCase
 * @package PeP\PaymentGateway\Test\Unit\Gateway\Request
 */
abstract class BuilderTestCase extends TestCase
{
    use SubjectReaderTestTrait;

    /**
     * @var BuilderInterface
     */
    protected $builder;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpSubjectReader();
    }

    /**
     * @param array $buildSubject
     * @return void
     */
    protected function expectationsForSubjectReaderThrowingException(array $buildSubject): void
    {
        $this->expectationsForReadingPaymentDOAndThrowingException($buildSubject);
        $this->expectException(CommandException::class);
    }
}
