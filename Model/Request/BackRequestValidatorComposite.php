<?php

declare(strict_types=1);

/**
 * File: BackRequestValidatorComposite.php
 *
 
 
 */

namespace PeP\PaymentGateway\Model\Request;

use Magento\Framework\App\RequestInterface;
use MSlwk\TypeSafeArray\ObjectArray;
use MSlwk\TypeSafeArray\ObjectArrayFactory;

/**
 * Class BackRequestValidatorComposite
 * @package PeP\PaymentGateway\Model\Request
 */
class BackRequestValidatorComposite implements BackRequestValidatorInterface
{
    /**
     * @var ObjectArray
     */
    private $internalValidators;

    /**
     * BackRequestValidatorComposite constructor.
     * @param ObjectArrayFactory $objectArrayFactory
     * @param array $internalValidators
     */
    public function __construct(ObjectArrayFactory $objectArrayFactory, array $internalValidators = [])
    {
        $this->internalValidators = $objectArrayFactory->create(
            BackRequestValidatorInterface::class,
            $internalValidators
        );
    }

    /**
     * @param RequestInterface $request
     * @return bool
     */
    public function validate(RequestInterface $request): bool
    {
        foreach ($this->internalValidators as $internalValidator) {
            /** @var $internalValidator BackRequestValidatorInterface */
            if ($internalValidator->validate($request) === false) {
                return false;
            }
        }

        return true;
    }
}
