<?php

declare(strict_types=1);

/**
 * File: ThreeDSecureBuilder.php
 *
 
 
 */

namespace PeP\PaymentGateway\Gateway\Request\CreditCard;

use Magento\Framework\UrlInterface;
use Magento\Payment\Gateway\Request\BuilderInterface;

/**
 * Class ThreeDSecureBuilder
 * @package PeP\PaymentGateway\Gateway\Request\CreditCard
 */
class ThreeDSecureBuilder implements BuilderInterface
{
    /**
     * @var string
     */
    private const BACK_URL_PARAM = 'back_url';

    /**
     * @var UrlInterface
     */
    private $url;

    /**
     * ThreeDSecureBuilder constructor.
     * @param UrlInterface $url
     * @SuppressWarnings(PHPMD.LongVariable)
     */
    public function __construct(UrlInterface $url)
    {
        $this->url = $url;
    }

    /**
     * @param array $buildSubject
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function build(array $buildSubject): array
    {
        return [self::BACK_URL_PARAM => $this->url->getUrl('paylane/creditcard/handle3dsecuretransaction')];
    }
}
