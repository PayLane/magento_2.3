<?php

declare(strict_types=1);

/**
 * File: CaptureStrategyCommand.php
 *
 
 
 */

namespace PeP\PaymentGateway\Gateway\Command\CreditCard;

use PeP\PaymentGateway\Gateway\Command\CreditCard\CaptureStrategyCommand\CommandResolver;
use PeP\PaymentGateway\Gateway\Reader\SubjectReader;
use Magento\Framework\Exception\NotFoundException;
use Magento\Payment\Gateway\Command\CommandException;
use Magento\Payment\Gateway\Command\CommandPoolInterface;
use Magento\Payment\Gateway\CommandInterface;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;

/**
 * Class CaptureStrategyCommand
 * @package PeP\PaymentGateway\Gateway\Command
 */
class CaptureStrategyCommand implements CommandInterface
{
    /**
     * @var SubjectReader
     */
    private $subjectReader;

    /**
     * @var CommandResolver
     */
    private $commandResolver;

    /**
     * @var CommandPoolInterface
     */
    private $commandPool;

    /**
     * CaptureStrategyCommand constructor.
     * @param SubjectReader $subjectReader
     * @param CommandResolver $commandResolver
     * @param CommandPoolInterface $commandPool
     * @SuppressWarnings(PHPMD.LongVariable)
     */
    public function __construct(
        SubjectReader $subjectReader,
        CommandResolver $commandResolver,
        CommandPoolInterface $commandPool
    ) {
        $this->subjectReader = $subjectReader;
        $this->commandResolver = $commandResolver;
        $this->commandPool = $commandPool;
    }

    /**
     * @param array $commandSubject
     * @return void
     * @throws CommandException
     */
    public function execute(array $commandSubject): void
    {
        /** @var PaymentDataObjectInterface $paymentDO */
        $paymentDO = $this->subjectReader->readPayment($commandSubject);

        $commandName = $this->commandResolver->resolveCommandToBeUsed($paymentDO);

        if (empty($commandName)) {
            return;
        }

        try {
            $command = $this->commandPool->get($commandName);
            $command->execute($commandSubject);
        } catch (NotFoundException $exception) {
            throw new CommandException(__('There was an error while trying to process the request.'), $exception);
        }
    }
}
