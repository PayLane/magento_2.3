/**
 * File: paylane.js
 *
 
 */

'use strict';

define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (
        Component,
        rendererList
    ) {
        rendererList.push(
            {
                type: 'paylane_secureform',
                component: 'PeP_PaymentGateway/js/view/payment/method-renderer/paylane-secureform'
            }
        );

        rendererList.push(
            {
                type: 'paylane_creditcard',
                component: 'PeP_PaymentGateway/js/view/payment/method-renderer/paylane-creditcard'
            }
        );

        rendererList.push(
            {
                type: 'paylane_paypal',
                component: 'PeP_PaymentGateway/js/view/payment/method-renderer/paylane-paypal'
            }
        );

        rendererList.push(
            {
                type: 'paylane_sofort',
                component: 'PeP_PaymentGateway/js/view/payment/method-renderer/paylane-sofort'
            }
        );

        rendererList.push(
            {
                type: 'paylane_directdebit',
                component: 'PeP_PaymentGateway/js/view/payment/method-renderer/paylane-directdebit'
            }
        );

        rendererList.push(
            {
                type: 'paylane_banktransfer',
                component: 'PeP_PaymentGateway/js/view/payment/method-renderer/paylane-banktransfer'
            }
        );

        rendererList.push(
            {
                type: 'paylane_ideal',
                component: 'PeP_PaymentGateway/js/view/payment/method-renderer/paylane-ideal'
            }
        );

        rendererList.push(
            {
                type: 'paylane_applepay',
                component: 'PeP_PaymentGateway/js/view/payment/method-renderer/paylane-applepay'
            }
        );

        rendererList.push(
            {
                type: 'paylane_googlepay',
                component: 'PeP_PaymentGateway/js/view/payment/method-renderer/paylane-googlepay'
            }
        );

        rendererList.push(
            {
                type: 'paylane_blik0',
                component: 'PeP_PaymentGateway/js/view/payment/method-renderer/paylane-blik0'
            }
        );

        return Component.extend({});
    }
);