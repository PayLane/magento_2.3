<?php

declare(strict_types=1);

/**
 * File: CreditCardAdapter.php
 *
 
 
 */

namespace PeP\PaymentGateway\Model\Method;

use PeP\PaymentGateway\Api\Config\Methods\CreditCardConfigProviderInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Payment\Gateway\Command\CommandManagerInterface;
use Magento\Payment\Gateway\Command\CommandPoolInterface;
use Magento\Payment\Gateway\Config\ValueHandlerPoolInterface;
use Magento\Payment\Gateway\Data\PaymentDataObjectFactory;
use Magento\Payment\Gateway\Validator\ValidatorPoolInterface;
use Magento\Payment\Model\Method\Adapter;
use Psr\Log\LoggerInterface;

/**
 * Class CreditCardAdapter
 * @package PeP\PaymentGateway\Model\Method
 */
class CreditCardAdapter extends Adapter
{
    /**
     * @var CreditCardConfigProviderInterface
     */
    private $creditCardConfigProvider;

    /**
     * CreditCardAdapter constructor.
     * @param CreditCardConfigProviderInterface $creditCardConfigProvider
     * @param ManagerInterface $eventManager
     * @param ValueHandlerPoolInterface $valueHandlerPool
     * @param PaymentDataObjectFactory $paymentDataObjectFactory
     * @param $code
     * @param $formBlockType
     * @param $infoBlockType
     * @param CommandPoolInterface|null $commandPool
     * @param ValidatorPoolInterface|null $validatorPool
     * @param CommandManagerInterface|null $commandExecutor
     * @param LoggerInterface|null $logger
     * @SuppressWarnings(PHPMD.LongVariable)
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        CreditCardConfigProviderInterface $creditCardConfigProvider,
        ManagerInterface $eventManager,
        ValueHandlerPoolInterface $valueHandlerPool,
        PaymentDataObjectFactory $paymentDataObjectFactory,
        string $code,
        string $formBlockType,
        string $infoBlockType,
        CommandPoolInterface $commandPool = null,
        ValidatorPoolInterface $validatorPool = null,
        CommandManagerInterface $commandExecutor = null,
        LoggerInterface $logger = null
    ) {
        parent::__construct(
            $eventManager,
            $valueHandlerPool,
            $paymentDataObjectFactory,
            $code,
            $formBlockType,
            $infoBlockType,
            $commandPool,
            $validatorPool,
            $commandExecutor,
            $logger
        );

        $this->creditCardConfigProvider = $creditCardConfigProvider;
    }

    /**
     * @return string
     */
    public function getConfigPaymentAction(): string
    {
        return $this->creditCardConfigProvider->is3DSCheckEnabled()
            ? (string) $this->getConfigData('payment_3ds_action')
            : $this->callParentGetConfigPaymentAction();
    }

    /**
     * @return string
     * @codeCoverageIgnore
     */
    protected function callParentGetConfigPaymentAction(): string
    {
        return (string) parent::getConfigPaymentAction();
    }
}
