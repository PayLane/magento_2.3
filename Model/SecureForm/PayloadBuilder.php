<?php

declare(strict_types=1);

/**
 * File: PayloadBuilder.php
 *
 
 
 */

namespace PeP\PaymentGateway\Model\SecureForm;

use PeP\PaymentGateway\Api\Config\GeneralConfigProviderInterface;
use PeP\PaymentGateway\Api\Config\Methods\SecureFormConfigProviderInterface;
use PeP\PaymentGateway\Api\SecureForm\PayloadBuilderInterface;
use PeP\PaymentGateway\Model\TransactionHandler;
use Magento\Checkout\Model\Session;
use Magento\Framework\Locale\Resolver;
use Magento\Framework\UrlInterface;
use Magento\Sales\Api\Data\OrderInterface;

/**
 * Class PayloadBuilder
 * @package PeP\PaymentGateway\Model\SecureForm
 */
class PayloadBuilder implements PayloadBuilderInterface
{
    /**
     * @var string
     */
    private const DEFAULT_SECURE_FORM_LANG = 'en';

    /**
     * @var GeneralConfigProviderInterface
     */
    private $generalConfigProvider;

    /**
     * @var SecureFormConfigProviderInterface
     */
    private $secureFormConfigProvider;

    /**
     * @var Session
     */
    private $session;

    /**
     * @var Resolver
     */
    private $resolver;

    /**
     * @var UrlInterface
     */
    private $url;

    /**
     * @var array
     */
    private $allowedLanguages;

    /**
     * PayloadBuilder constructor.
     * @param GeneralConfigProviderInterface $generalConfigProvider
     * @param SecureFormConfigProviderInterface $secureFormConfigProvider
     * @param Session $session
     * @param Resolver $resolver
     * @param UrlInterface $url
     * @param array $allowedLanguages
     * @SuppressWarnings(PHPMD.LongVariable)
     */
    public function __construct(
        GeneralConfigProviderInterface $generalConfigProvider,
        SecureFormConfigProviderInterface $secureFormConfigProvider,
        Session $session,
        Resolver $resolver,
        UrlInterface $url,
        array $allowedLanguages = []
    ) {
        $this->generalConfigProvider = $generalConfigProvider;
        $this->secureFormConfigProvider = $secureFormConfigProvider;
        $this->session = $session;
        $this->resolver = $resolver;
        $this->url = $url;
        $this->allowedLanguages = $allowedLanguages;
    }

    /**
     * Out of the box this module supports handling
     * only sale-type operation for secure form.
     *
     * @return array
     */
    public function build(): array
    {
        $data = [
            'action' => 'https://secure.paylane.com/order/cart.html',
            'method' => 'POST',
            'fields' => []
        ];

        $order = $this->session->getLastRealOrder();
        $incrementId = $order->getIncrementId();
        $grandTotal = $order->getGrandTotal();
        $currencyCode = $order->getOrderCurrencyCode();
        $grandTotalFormatted = sprintf('%01.2f', $grandTotal);

        $result = [
            'amount' => $grandTotalFormatted,
            'currency' => $currencyCode,
            'merchant_id' => $this->generalConfigProvider->getMerchantId(),
            'description' => $incrementId,
            'transaction_description' => $this->buildTransactionDescription($order),
            'transaction_type' => TransactionHandler::TYPE_SALE,
            'back_url' => $this->url->getUrl(
                'paylane/secureForm/handleSaleTransaction/'
            ),

            'language' => $this->resolveFormLanguage(),
        ];

        $result['hash'] = $this->calculateHash(
            $incrementId,
            $grandTotalFormatted,
            $currencyCode,
            TransactionHandler::TYPE_SALE
        );

        if ($this->secureFormConfigProvider->isSendCustomerData()) {
            $address = $order->getBillingAddress();
            $result['customer_name'] = $order->getCustomerFirstname() . ' ' . $order->getCustomerLastname();
            $result['customer_email'] = $order->getCustomerEmail();
            $result['customer_address'] = implode(',', $address->getStreet());
            $result['customer_zip'] = $address->getPostcode();
            $result['customer_city'] = $address->getCity();
            $result['customer_state'] = $address->getRegion();
            $result['customer_country'] = $address->getCountryId();
        }

        $data['fields'] = $result;

        return [$data];
    }

    /**
     * @param OrderInterface $order
     * @return string
     */
    protected function buildTransactionDescription(OrderInterface $order): string
    {
        $description = sprintf(
            'Order #%s, %s (%s)',
            $order->getIncrementId(),
            $order->getCustomerFirstname() . ' ' . $order->getCustomerLastname(),
            $order->getCustomerEmail()
        );

        return $description;
    }

    /**
     * @return string
     */
    private function resolveFormLanguage(): string
    {
        $currentLocaleCode = $this->resolver->getLocale();
        $languageCode = strstr($currentLocaleCode, '_', true);

        return in_array($languageCode, $this->allowedLanguages)
            ? $languageCode
            : self::DEFAULT_SECURE_FORM_LANG;
    }

    /**
     * @param $description
     * @param $amount
     * @param $currency
     * @param $transactionType
     * @return string
     */
    protected function calculateHash($description, $amount, $currency, $transactionType)
    {
        $salt = $this->generalConfigProvider->getHashSalt();
        $hash = sha1(join('|', [$salt, $description, $amount, $currency, $transactionType]));

        return $hash;
    }
}
