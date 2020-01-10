<?php

declare(strict_types=1);

/**
 * File: TransactionMock.php
 *
 
 */

namespace PeP\PaymentGateway\Gateway\Http\Client;

use Exception;
use PeP\PaymentGateway\Exception\ClientException;
use Magento\Payment\Gateway\Http\ClientInterface;
use Magento\Payment\Gateway\Http\TransferInterface;

/**
 * TODO: It is just mock to omit Magento 2.3 payment flow. To be deleted, when all payment methods refactored.
 * Class TransactionMock
 * @codeCoverageIgnore
 */
class TransactionMock implements ClientInterface
{
    /**
     * @param TransferInterface $transferObject
     * @return array|mixed
     * @throws ClientException
     */
    public function placeRequest(TransferInterface $transferObject)
    {
        $data = $transferObject->getBody();
        $response['object'] = [];

        try {
            $response['object'] = $this->process($data);
        } catch (Exception $e) {
            $message = __($e->getMessage() ?: 'Sorry, but something went wrong');
            throw new ClientException(__($message));
        }

        return $response;
    }

    /**
     * @param array $data
     * @return array
     */
    protected function process(array $data)
    {
        /**
         * @TODO: Refactor - the best way is to delete that class, but it is not possible
         * because Magento 2 Payment Gateway implementation...
         *
         * @see https://devdocs.magento.com/guides/v2.2/payments-integrations/payment-gateway/gateway-command.html
         */
        return [];
    }
}
