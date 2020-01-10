<?php

declare(strict_types=1);

/**
 * File: OrderExistenceValidator.php
 *
 
 
 */

namespace PeP\PaymentGateway\Model\Request;

use Magento\Checkout\Model\Session;
use Magento\Framework\App\RequestInterface;

/**
 * Class OrderExistenceValidator
 * @package PeP\PaymentGateway\Model\Request
 */
class OrderExistenceValidator implements BackRequestValidatorInterface
{
    /**
     * @var string
     */
    private const DESCRIPTION_PARAM = 'description';

    /**
     * @var Session
     */
    private $session;

    /**
     * OrderExistenceValidator constructor.
     * @param Session $session
     */
    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    /**
     * @param RequestInterface $request
     * @return bool
     */
    public function validate(RequestInterface $request): bool
    {
        $incrementId = $request->getParam(self::DESCRIPTION_PARAM, '');

        $lastOrder = $this->session->getLastRealOrder();
        $lastOrderIncrementId = $lastOrder->getIncrementId();

        return $lastOrderIncrementId === $incrementId;
    }
}
