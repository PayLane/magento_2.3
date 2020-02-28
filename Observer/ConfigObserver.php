<?php

declare (strict_types = 1);

/**
 * File: ConfigObserver.php
 *
 
 */

namespace PeP\PaymentGateway\Observer;

use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Cache\Type\Config;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use PeP\PaymentGateway\Api\Config\GeneralConfigProviderInterface;
use PeP\PaymentGateway\Api\Config\Methods\ApplePayConfigProviderInterface;
use PeP\PaymentGateway\Api\Config\Methods\GooglePayConfigProviderInterface;
use PeP\PaymentGateway\Model\Source\Payment\Mode;

/**
 * Class ConfigObserver
 * @package PeP\PaymentGateway\Observer
 */
class ConfigObserver implements ObserverInterface
{
    /**
     * @var GeneralConfigProviderInterface
     */
    private $generalConfigProvider;

    /**
     * @var WriterInterface
     */
    private $configWriter;

    /**
     * @var TypeListInterface
     */
    private $cacheTypeList;

    /**
     * @var ApplePayConfigProviderInterface
     */
    private $applePayConfigProvider;

    /**
     * @var GooglePayConfigProviderInterface
     */
    private $googlePayConfigProvider;

    /**
     * ConfigObserver constructor.
     * @param GeneralConfigProviderInterface $generalConfigProvider
     * @param WriterInterface $configWriter
     * @param TypeListInterface $cacheTypeList
     * @param ApplePayConfigProviderInterface $applePayConfigProvider
     * @param GooglePayConfigProviderInterface $googlePayConfigProvider
     * @SuppressWarnings(PHPMD.LongVariable)
     */
    public function __construct(
        GeneralConfigProviderInterface $generalConfigProvider,
        WriterInterface $configWriter,
        TypeListInterface $cacheTypeList,
        ApplePayConfigProviderInterface $applePayConfigProvider,
        GooglePayConfigProviderInterface $googlePayConfigProvider
    ) {
        $this->generalConfigProvider = $generalConfigProvider;
        $this->configWriter = $configWriter;
        $this->cacheTypeList = $cacheTypeList;
        $this->applePayConfigProvider = $applePayConfigProvider;
        $this->googlePayConfigProvider = $googlePayConfigProvider;
    }

    /**
     * @param Observer $observer
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(Observer $observer): void
    {
        $paymentMode = $this->generalConfigProvider->getPaymentMode();

        if ($paymentMode === Mode::SECURE_FORM_MODE) {
            $this->togglePaymentMethod('payment/paylane_creditcard/active', 0);
            $this->togglePaymentMethod('payment/paylane_banktransfer/active', 0);
            $this->togglePaymentMethod('payment/paylane_paypal/active', 0);
            $this->togglePaymentMethod('payment/paylane_directdebit/active', 0);
            $this->togglePaymentMethod('payment/paylane_sofort/active', 0);
            $this->togglePaymentMethod('payment/paylane_ideal/active', 0);
            $this->togglePaymentMethod('payment/paylane_applepay/active', 0);
            $this->togglePaymentMethod('payment/paylane_googlepay/active', 0);
            $this->togglePaymentMethod('payment/paylane_blik0/active', 0);
            $this->togglePaymentMethod('payment/paylane_secureform/active', 1);
        }

        if ($paymentMode === Mode::API_MODE) {
            $this->togglePaymentMethod('payment/paylane_secureform/active', 0);
        }

        $this->cacheTypeList->cleanType(Config::TYPE_IDENTIFIER);

        $this->storeApplePayCertificate();
    }

    /**
     * @param string $path
     * @param int $enabled
     * @return void
     */
    protected function togglePaymentMethod(string $path, int $enabled): void
    {
        $this->configWriter->save($path, $enabled);
    }

    /**
     * @return void
     */
    protected function storeApplePayCertificate()
    {
        $cert = $this->applePayConfigProvider->getCertificate();

        if (empty($cert)) {
            return;
        }

        try {
            $path = rtrim( $_SERVER['DOCUMENT_ROOT'], '/\\' );
            $dir = '.well-known';
            $file = 'apple-developer-merchantid-domain-association.txt';
            $fullpath = $path . '/' . $dir . '/' . $file;

            if (!file_exists($fullpath)) {
                if (!file_exists($path . '/' . $dir)) {
                    if (!@mkdir($path . '/' . $dir, 0755)) {
                        throw new \Exception(('Unable to create certificate folder. Please create "./well-known/apple-developer-merchantid-domain-association.txt" file into your main domain directory with certificate text.'));
                    }
                }

                $this->storeApplePayCert($fullpath, $cert);
            } else {
                $myfile = @fopen($fullpath, "r");
                $content = @fread($myfile, filesize($fullpath));
                @fclose($myfile);

                if ($cert != $content) {
                    $this->storeApplePayCert($fullpath, $cert);
                }
            }

        } catch (\Exception $e) {
            $this->configWriter->save('payment/paylane_applepay/certificate', '');
            $cert = '';
            throw new \Exception($e->getMessage());
        }
    }

    private function storeApplePayCert($fullpath, $content)
    {
        if (file_exists($fullpath) && !is_writable($fullpath)) {
            throw new \Exception(('Unable to write certificate file. Please create "./well-known/apple-developer-merchantid-domain-association.txt" file into your main domain directory with certificate text.'));
        } else {
            $myfile = @fopen($fullpath, "w");
            @fwrite($myfile, trim($content));
            @fclose($myfile);
        }
    }
}
