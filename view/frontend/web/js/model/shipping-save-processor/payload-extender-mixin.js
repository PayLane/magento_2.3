/**
 * File: payload-extender-mixin.js
 *
 
 */

'use strict';

define([
    'Magento_Checkout/js/model/quote',
    'mage/utils/wrapper'
], function (quote, wrapper) {
    return function (payloadExtender) {

        return wrapper.wrap(payloadExtender, function (originalAction, payload) {
            if (payload.addressInformation.billing_address) {
                payload.addressInformation.billing_address.email = quote.guestEmail;
            }

            if (payload.addressInformation.shipping_address) {
                payload.addressInformation.shipping_address.email = quote.guestEmail;
            }

            return originalAction(payload);
        });
    };
});
