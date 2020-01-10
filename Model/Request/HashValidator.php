<?php

declare(strict_types=1);

/**
 * File: HashValidator.php
 *
 
 
 */

namespace PeP\PaymentGateway\Model\Request;

use PeP\PaymentGateway\Api\HashComparerInterface;
use Magento\Framework\App\RequestInterface;

/**
 * Class HashValidator
 * @package PeP\PaymentGateway\Model\Request\SecureForm
 */
class HashValidator implements BackRequestValidatorInterface
{
    /**
     * @var string
     */
    private const HASH_PARAM = 'hash';

    /**
     * @var string
     */
    private const STATUS_PARAM = 'status';

    /**
     * @var string
     */
    private const AMOUNT_PARAM = 'amount';

    /**
     * @var string
     */
    private const CURRENCY_PARAM = 'currency';

    /**
     * @var string
     */
    private const DESCRIPTION_PARAM = 'description';

    /**
     * @var HashComparerInterface
     */
    private $hashComparer;

    /**
     * @var string
     */
    private $transactionIdParamName;

    /**
     * HashValidator constructor.
     * @param HashComparerInterface $hashComparer
     * @param string $transactionIdParamName
     * @SuppressWarnings(PHPMD.LongVariable)
     */
    public function __construct(
        HashComparerInterface $hashComparer,
        string $transactionIdParamName
    ) {
        $this->hashComparer = $hashComparer;
        $this->transactionIdParamName = $transactionIdParamName;
    }

    /**
     * @param RequestInterface $request
     * @return bool
     */
    public function validate(RequestInterface $request): bool
    {
        $hash = $request->getParam(self::HASH_PARAM, '');
        $status = $request->getParam(self::STATUS_PARAM, '');
        $incrementId = $request->getParam(self::DESCRIPTION_PARAM, '');
        $amount = $request->getParam(self::AMOUNT_PARAM, '');
        $currency = $request->getParam(self::CURRENCY_PARAM, '');
        $transactionId = $request->getParam($this->transactionIdParamName, '');

        return $this->hashComparer->compareHashes(
            $hash,
            $status,
            $amount,
            $incrementId,
            $currency,
            $transactionId
        );
    }
}
