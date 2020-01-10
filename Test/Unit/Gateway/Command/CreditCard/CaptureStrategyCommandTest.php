<?php

declare(strict_types=1);

/**
 * File: CaptureStrategyCommandTest.php
 *
 
 
 */

namespace PeP\PaymentGateway\Test\Unit\Gateway\Command\CreditCard;

use PeP\PaymentGateway\Gateway\Command\CreditCard\CaptureStrategyCommand;
use PeP\PaymentGateway\Gateway\Command\CreditCard\CaptureStrategyCommand\CommandResolver;
use PeP\PaymentGateway\Test\Unit\Gateway\SubjectReaderTestTrait;
use Magento\Framework\Exception\NotFoundException;
use Magento\Payment\Gateway\Command\CommandException;
use Magento\Payment\Gateway\Command\CommandPoolInterface;
use Magento\Payment\Gateway\CommandInterface;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

/**
 * Class CaptureStrategyCommandTest
 * @package PeP\PaymentGateway\Test\Unit\Gateway\Command\CreditCard
 */
class CaptureStrategyCommandTest extends TestCase
{
    use SubjectReaderTestTrait;

    /**
     * @var string
     */
    private $exampleCommandToBeExecuted = '';

    /**
     * @var array
     */
    private $subject = [];

    /**
     * @var CommandResolver|MockObject
     */
    private $commandResolverMock;

    /**
     * @var CaptureStrategyCommand
     */
    private $captureStrategyCommand;

    /**
     * @var CommandInterface|MockObject
     */
    private $commandMock;

    /**
     * @var CommandPoolInterface|MockObject
     */
    private $commandPoolMock;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpSubjectReader();

        //Internal mocks
        $this->exampleCommandToBeExecuted = 'test';
        $this->subject = [$this->paymentDataObjectMock];
        $this->commandMock = $this->getMockBuilder(CommandInterface::class)->getMock();

        //Dependencies mocks
        $this->commandResolverMock = $this->getMockBuilder(CommandResolver::class)
        ->disableOriginalConstructor()
        ->getMock();
        $this->commandPoolMock = $this->getMockBuilder(CommandPoolInterface::class)->getMock();

        $this->captureStrategyCommand = new CaptureStrategyCommand(
            $this->subjectReaderMock,
            $this->commandResolverMock,
            $this->commandPoolMock
        );
    }

    /**
     * @test
     *
     * @return void
     * @throws CommandException
     */
    public function testExecuteWhenThereIsNoCommandToBeExecuted(): void
    {
        $this->expectationsForReadingPaymentDO($this->subject);
        $this->expectationsForResolvingCommandToBeExecuted('');

        $this->captureStrategyCommand->execute($this->subject);
    }

    /**
     * @test
     *
     * @return void
     * @throws CommandException
     */
    public function testExecuteWhenCommandNotFound(): void
    {
        $this->expectationsForReadingPaymentDO($this->subject);
        $this->expectationsForResolvingCommandToBeExecuted($this->exampleCommandToBeExecuted);

        $this->commandPoolMock->expects($this->once())
            ->method('get')
            ->with($this->exampleCommandToBeExecuted)
            ->willThrowException(new NotFoundException(__()));

        $this->expectException(CommandException::class);
        $this->captureStrategyCommand->execute($this->subject);
    }

    /**
     * @test
     *
     * @return void
     * @throws CommandException
     */
    public function testExecuteWhenCommandThrowsException(): void
    {
        $this->expectationsForReadingPaymentDO($this->subject);
        $this->expectationsForResolvingCommandToBeExecuted($this->exampleCommandToBeExecuted);

        $this->commandPoolMock->expects($this->once())
            ->method('get')
            ->with($this->exampleCommandToBeExecuted)
            ->willReturn($this->commandMock);
        $this->commandMock->expects($this->once())
            ->method('execute')
            ->with($this->subject)
            ->willThrowException(new CommandException(__()));

        $this->expectException(CommandException::class);
        $this->captureStrategyCommand->execute($this->subject);
    }

    /**
     * @test
     *
     * @return void
     * @throws CommandException
     */
    public function testExecuteWhenCommandExecutedWithSuccess(): void
    {
        $this->expectationsForReadingPaymentDO($this->subject);
        $this->expectationsForResolvingCommandToBeExecuted($this->exampleCommandToBeExecuted);

        $this->commandPoolMock->expects($this->once())
            ->method('get')
            ->with($this->exampleCommandToBeExecuted)
            ->willReturn($this->commandMock);
        $this->commandMock->expects($this->once())
            ->method('execute')
            ->with($this->subject);

        $this->captureStrategyCommand->execute($this->subject);
    }

    /**
     * @param string $commandToBeExecuted
     * @return void
     */
    private function expectationsForResolvingCommandToBeExecuted(string $commandToBeExecuted): void
    {
        $this->commandResolverMock->expects($this->once())
            ->method('resolveCommandToBeUsed')
            ->with($this->paymentDataObjectMock)
            ->willReturn($commandToBeExecuted);
    }
}
