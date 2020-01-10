<?php

declare(strict_types=1);

/**
 * File: Url.php
 *
 
 */

namespace PeP\PaymentGateway\Block\Adminhtml\System\Config\Field\Notification;

use Exception;
use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\UrlInterface;
use Magento\Store\Api\StoreRepositoryInterface;
use Magento\Store\Api\WebsiteRepositoryInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Url
 * @package PeP\PaymentGateway\Block\Adminhtml\System\Config\Field\Notification
 */
class Url extends Field
{
    /**
     * @var UrlInterface
     */
    private $url;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var StoreRepositoryInterface
     */
    private $storeRepository;

    /**
     * @var WebsiteRepositoryInterface
     */
    private $websiteRepository;

    /**
     * Url constructor.
     *
     * @param Context $context
     * @param RequestInterface $request
     * @param UrlInterface $url
     * @param StoreRepositoryInterface $storeRepository
     * @param WebsiteRepositoryInterface $websiteRepository
     * @param array $data
     */
    public function __construct(
        Context $context,
        RequestInterface $request,
        UrlInterface $url,
        StoreRepositoryInterface $storeRepository,
        WebsiteRepositoryInterface $websiteRepository,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->url = $url;
        $this->request = $request;
        $this->storeRepository = $storeRepository;
        $this->websiteRepository = $websiteRepository;
    }

   /**
    * @param AbstractElement $element
    * @return string
    */
    protected function _getElementHtml(AbstractElement $element): string
    {
        $description = __(
            'Notifications is a service that simplifies automatic communication between your shop and PayLane.
            A notification has information about payment status.<br><br>
		    <h2><strong>It is Highly recommended to enable notifications</strong></h2><br>
            <strong>To enable notifications</strong>:<br>
            - Chose individual login and password and enter them below (it should be safe login and password,
             <strong>not the same as API login/password or merchant panel login/password</strong>)<br><br>
            - Send to us on e-mail (support@paylane.com) you notification login, password and notification address, <br>
            (Your notification address: <code>%1</code>)
            <br><br>
            We will send you Notification token to fill inside this field',
            $this->buildHandleAutoUrl()
        );

        return $description->render();
    }

    /**
     * @return string
     */
    private function buildHandleAutoUrl(): string
    {
        try {
            $storeViewId = $this->resolveUrlInConfiguration();
            return $this->url->getUrl(
                'paylane/notification/handleAuto',
                ['_scope' => $storeViewId, '_nosid' => true]
            );
        } catch (Exception $exception) {
            return '';
        }
    }

    /**
     * @return int
     * @throws LocalizedException
     */
    private function resolveUrlInConfiguration(): int
    {
        //If websiteId is empty we assume that we are in default scope config
        $websiteId = $this->request->getParam(ScopeInterface::SCOPE_WEBSITE, null);
        return $websiteId !== null
            ? (int) $this->websiteRepository->getById($websiteId)->getDefaultStore()->getId()
            : (int) $this->websiteRepository->getDefault()->getDefaultStore()->getId();
    }
}
