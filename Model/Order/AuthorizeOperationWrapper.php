<?php

declare(strict_types=1);

/**
 * File: AuthorizeOperationWrapper.php
 *
 
 
 */

namespace PeP\PaymentGateway\Model\Order;

use Exception;
use PeP\PaymentGateway\Api\Order\AuthorizeOperationWrapperInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Payment\Gateway\Command\CommandException;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order\Payment\Processor;

/**
 * Class AuthorizeOperationWrapper
 * @package PeP\PaymentGateway\Model\Order
 */
class AuthorizeOperationWrapper implements AuthorizeOperationWrapperInterface
{
    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var Processor
     */
    private $orderPaymentProcessor;

    /**
     * CaptureOperationWrapper constructor.
     * @param OrderRepositoryInterface $orderRepository
     * @param Processor $processor
     */
    public function __construct(OrderRepositoryInterface $orderRepository, Processor $processor)
    {
        $this->orderRepository = $orderRepository;
        $this->orderPaymentProcessor = $processor;
    }

    /**
     * @param OrderInterface $order
     * @param bool $isOnline
     * @return void
     * @throws CommandException
     * @throws LocalizedException
     */
    public function authorize(OrderInterface $order, bool $isOnline = true): void
    {
        $payment = $order->getPayment();

        $totalDue = $order->getTotalDue();
        $baseTotalDue = $order->getBaseTotalDue();
        $payment->setAmountAuthorized($totalDue);

        try {
            $this->orderPaymentProcessor->authorize($payment, $isOnline, $baseTotalDue);
            $this->orderRepository->save($order);
        } catch (CommandException $exception) {
            throw $exception;
        } catch (Exception $exception) {
            throw new LocalizedException(__('Transaction has been declined. Please try again later.'), $exception);
        }
    }
}
