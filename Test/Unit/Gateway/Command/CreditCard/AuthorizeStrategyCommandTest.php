<?php

declare(strict_types=1);

/**
 * File: AuthorizeStrategyCommandTest.php
 *
 
 
 */

namespace PeP\PaymentGateway\Test\Unit\Gateway\Command\CreditCard;

use PeP\PaymentGateway\Gateway\Command\CreditCard\AuthorizeStrategyCommand;
use PeP\PaymentGateway\Test\Unit\Model\Config\Methods\CreditCardConfigProviderTestTrait;
use Magento\Framework\Exception\NotFoundException;
use Magento\Payment\Gateway\Command\CommandException;
use Magento\Payment\Gateway\Command\CommandPoolInterface;
use Magento\Payment\Gateway\CommandInterface;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

/**
 * Class AuthorizeStrategyCommandTest
 * @package PeP\PaymentGateway\Test\Unit\Gateway\Command\CreditCard
 */
class AuthorizeStrategyCommandTest extends TestCase
{
    use CreditCardConfigProviderTestTrait;

    /**
     * @var string
     */
    private const AUTHORIZE = 'real_authorize';

    /**
     * @var string
     */
    private const CHECK3DS = 'check3ds';

    /**
     * @var AuthorizeStrategyCommand
     */
    private $authorizeStrategyCommand;

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
        $this->setUpCreditCardConfigProvider();

        $this->commandPoolMock = $this->getMockBuilder(CommandPoolInterface::class)->getMock();

        $this->authorizeStrategyCommand = new AuthorizeStrategyCommand(
            $this->creditCardConfigProviderMock,
            $this->commandPoolMock
        );
    }

    /**
     * @test
     *
     * @dataProvider provideDifferent3DSecureConfigurationValuesAndCommands
     *
     * @param bool $is3DSecureEnabled
     * @param string $command
     * @return void
     * @throws CommandException
     */
    public function testExecuteWhenCommandNotFound(bool $is3DSecureEnabled, string $command): void
    {
        $this->expectationsForGetting3DSecureConfigurationValue($is3DSecureEnabled);
        $this->commandPoolMock->expects($this->once())
            ->method('get')
            ->with($command)
            ->willThrowException(new NotFoundException(__()));

        $this->expectException(CommandException::class);
        $this->authorizeStrategyCommand->execute([]);
    }

    /**
     * @test
     *
     * @dataProvider provideDifferent3DSecureConfigurationValuesAndCommands
     *
     * @param bool $is3DSecureEnabled
     * @param string $command
     * @return void
     * @throws CommandException
     */
    public function testExecuteWhenCommandThrowsException(bool $is3DSecureEnabled, string $command): void
    {
        $commandSubject = [];
        $commandMock = $this->getMockBuilder(CommandInterface::class)->getMock();

        $this->expectationsForGetting3DSecureConfigurationValue($is3DSecureEnabled);

        $this->commandPoolMock->expects($this->once())
            ->method('get')
            ->with($command)
            ->willReturn($commandMock);
        $commandMock->expects($this->once())
            ->method('execute')
            ->with($commandSubject)
            ->willThrowException(new CommandException(__()));

        $this->expectException(CommandException::class);
        $this->authorizeStrategyCommand->execute($commandSubject);
    }

    /**
     * @test
     *
     * @dataProvider provideDifferent3DSecureConfigurationValuesAndCommands
     *
     * @param bool $is3DSecureEnabled
     * @param string $command
     * @return void
     * @throws CommandException
     */
    public function testExecuteWhenCommandExecutedWithSuccess(bool $is3DSecureEnabled, string $command): void
    {
        $commandSubject = [];
        $commandMock = $this->getMockBuilder(CommandInterface::class)->getMock();

        $this->expectationsForGetting3DSecureConfigurationValue($is3DSecureEnabled);
        $this->commandPoolMock->expects($this->once())
            ->method('get')
            ->with($command)
            ->willReturn($commandMock);
        $commandMock->expects($this->once())
            ->method('execute')
            ->with($commandSubject);

        $this->authorizeStrategyCommand->execute($commandSubject);
    }

    /**
     * @return array
     */
    public static function provideDifferent3DSecureConfigurationValuesAndCommands(): array
    {
        return [
            [true, self::CHECK3DS],
            [false, self::AUTHORIZE]
        ];
    }
}
