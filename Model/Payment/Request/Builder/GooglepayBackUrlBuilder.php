<?php

declare(strict_types=1);

/**
 * File: BackUrlBuilder.php
 *
 
 */

/**
 * @see http://devzone.paylane.com/api-guide/cards/single-transaction/
 */
namespace PeP\PaymentGateway\Model\Payment\Request\Builder;

use Magento\Framework\UrlInterface;
use Magento\Quote\Model\Quote;

/**
 * Class BackUrlBuilder
 * @package PeP\PaymentGateway\Model\Payment\Request\Builder
 */
class GooglepayBackUrlBuilder implements BuilderInterface
{
    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @param UrlInterface $urlBuilder
     */
    public function __construct(UrlInterface $urlBuilder)
    {
        $this->urlBuilder = $urlBuilder;
    }


    public function build(Quote $quote): array
    {
        $result = [
            'back_url' => $this->urlBuilder->getUrl(
                'paylane/googlepay/handle/quote/' . $quote->getId()
            )
        ];

        return $result;
    }
}
