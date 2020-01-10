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
class BackUrlBuilder implements BuilderInterface
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

    /**
     * @inheritdoc
     */
    public function build(Quote $quote): array
    {
        $result = [
            'back_url' => $this->urlBuilder->getUrl(
                'paylane/transaction/handle/quote/' . $quote->getId(),
                ['_nosid' => true]
            )
        ];

        return $result;
    }
}
