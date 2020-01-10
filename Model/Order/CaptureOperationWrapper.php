<?php

declare(strict_types=1);

/**
 * File: CaptureOperationWrapper.php
 *
 
 
 */

namespace PeP\PaymentGateway\Model\Order;

use Exception;
use PeP\PaymentGateway\Api\Order\CaptureOperationWrapperInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Payment\Gateway\Command\CommandException;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order\Payment\Processor;

/**
 * Class CaptureOperationWrapper
 * @package PeP\PaymentGateway\Model\Order
 */
class CaptureOperationWrapper implements CaptureOperationWrapperInterface
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
     * @return void
     * @throws LocalizedException
     */
    public function capture(OrderInterface $order): void
    {
        $payment = $order->getPayment();

        $totalDue = $order->getTotalDue();
        $baseTotalDue = $order->getBaseTotalDue();
        $payment->setAmountAuthorized($totalDue);
        $payment->setBaseAmountAuthorized($baseTotalDue);

        try {
            $this->orderPaymentProcessor->capture($payment, null);
            $this->orderRepository->save($order);
        } catch (CommandException $exception) {
            throw $exception;
        } catch (Exception $exception) {
            throw new LocalizedException(__('Transaction has been declined. Please try again later.'), $exception);
        }
    }
}
