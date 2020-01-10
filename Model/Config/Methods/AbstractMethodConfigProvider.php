<?php

declare(strict_types=1);

/**
 * File: AbstractMethodConfigProvider.php
 *
 
 */

namespace PeP\PaymentGateway\Model\Config\Methods;

use PeP\PaymentGateway\Api\Config\Methods\SpecificMethodConfigProviderInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * TODO: Unit test
 * Class AbstractMethodConfigProvider
 * @package PeP\PaymentGateway\Model\Config\Methods
 */
abstract class AbstractMethodConfigProvider implements SpecificMethodConfigProviderInterface
{
    /**
     * @var string
     */
    private const PAYMENT_CONFIG_PATTERN = 'payment/%s/%s';

    /**
     * @var string
     */
    private const TITLE_FIELD = 'title';

    /**
     * @var string
     */
    private const SORT_ORDER = 'sort_order';

    /**
     * @var string
     */
    private const SHOW_IMG = 'show_img';

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * AbstractMethodConfigProvider constructor.
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param string $scopeType
     * @param null $scopeCode
     * @return string
     */
    public function getTitle(
        string $scopeType = ScopeInterface::SCOPE_STORE,
        $scopeCode = null
    ): string {
        return (string) $this->scopeConfig->getValue(
            $this->buildPathForConfiguration(self::TITLE_FIELD),
            $scopeType,
            $scopeCode
        );
    }

    /**
     * @param string $scopeType
     * @param null $scopeCode
     * @return int
     */
    public function getSortOrder(
        string $scopeType = ScopeInterface::SCOPE_STORE,
        $scopeCode = null
    ): int {
        return (int) $this->scopeConfig->getValue(
            $this->buildPathForConfiguration(self::SORT_ORDER),
            $scopeType,
            $scopeCode
        );
    }

    /**
     * @param string $scopeType
     * @param null $scopeCode
     * @return bool
     */
    public function isPaymentMethodImageShown(
        string $scopeType = ScopeInterface::SCOPE_STORE,
        $scopeCode = null
    ): bool {
        return (bool) $this->scopeConfig->getValue(
            $this->buildPathForConfiguration(self::SHOW_IMG),
            $scopeType,
            $scopeCode
        );
    }

    /**
     * @param string $fieldPathPart
     * @return string
     */
    protected function buildPathForConfiguration(string $fieldPathPart): string
    {
        return sprintf(self::PAYMENT_CONFIG_PATTERN, $this->getMethodCode(), $fieldPathPart);
    }

    /**
     * @return string
     */
    abstract protected function getMethodCode(): string;
}
