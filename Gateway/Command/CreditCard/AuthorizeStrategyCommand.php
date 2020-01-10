<?php

declare(strict_types=1);

/**
 * File: AuthorizeStrategyCommand.php
 *
 
 
 */

namespace PeP\PaymentGateway\Gateway\Command\CreditCard;

use PeP\PaymentGateway\Api\Config\Methods\CreditCardConfigProviderInterface;
use Magento\Framework\Exception\NotFoundException;
use Magento\Payment\Gateway\Command\CommandException;
use Magento\Payment\Gateway\Command\CommandPoolInterface;
use Magento\Payment\Gateway\CommandInterface;

/**
 * Class AuthorizeStrategyCommand
 * @package PeP\PaymentGateway\Gateway\Command\CreditCard
 */
class AuthorizeStrategyCommand implements CommandInterface
{
    /**
     * @var string
     */
    private const AUTHORIZE = 'real_authorize';

    /**
     * @var string
     */
    private const CHECK3DS = 'check3ds';

    /**
     * @var CreditCardConfigProviderInterface
     */
    private $creditCardConfigProvider;

    /**
     * @var CommandPoolInterface
     */
    private $commandPool;

    /**
     * CaptureStrategyCommand constructor.
     * @param CreditCardConfigProviderInterface $creditCardConfigProvider
     * @param CommandPoolInterface $commandPool
     * @SuppressWarnings(PHPMD.LongVariable)
     */
    public function __construct(
        CreditCardConfigProviderInterface $creditCardConfigProvider,
        CommandPoolInterface $commandPool
    ) {
        $this->creditCardConfigProvider = $creditCardConfigProvider;
        $this->commandPool = $commandPool;
    }

    /**
     * @param array $commandSubject
     * @return void
     * @throws CommandException
     */
    public function execute(array $commandSubject): void
    {
        $commandName = $this->resolveCommandToBeUsed();

        try {
            $command = $this->commandPool->get($commandName);
            $command->execute($commandSubject);
        } catch (NotFoundException $exception) {
            throw new CommandException(__('There was an error while trying to process the request.'), $exception);
        }
    }

    /**
     * @return string
     */
    private function resolveCommandToBeUsed(): string
    {
        return $this->creditCardConfigProvider->is3DSCheckEnabled()
            ? self::CHECK3DS
            : self::AUTHORIZE;
    }
}
