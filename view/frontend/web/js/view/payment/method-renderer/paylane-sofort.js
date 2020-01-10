/**
 * File: paylane-sofort.js
 *
 
 */

'use strict';

define(
    [
        'jquery',
        'underscore',
        'mage/template',
        'mage/url',
        'Magento_Checkout/js/view/payment/default',
        'Magento_Checkout/js/model/full-screen-loader',
        'Magento_Customer/js/customer-data'
    ],
    function (
        $,
        _,
        mageTemplate,
        url,
        Component,
        fullScreenLoader,
        customerData
    ) {
        return Component.extend({
            defaults: {
                template: 'PeP_PaymentGateway/payment/sofort',
                code: 'paylane_sofort'
            },

            /**
             * @param {Object} formData
             * @returns {*|jQuery}
             */
            build: function (formData) {
                var formTmpl = mageTemplate('<form action="<%= data.action %>"' +
                    ' method="<%= data.method %>" hidden enctype="application/x-www-form-urlencoded">' +
                    '<% _.each(data.fields, function(val, key){ %>' +
                    '<input value=\'<%= val %>\' name="<%= key %>" type="hidden">' +
                    '<% }); %>' +
                    '</form>');

                return $(formTmpl({
                    data: {
                        action: formData.action,
                        method: formData.method,
                        fields: formData.fields
                    }
                })).appendTo($('[data-container="body"]'));
            },

            showPaymentMethodImg: function () {
                return window.checkoutConfig.payment.paylane_sofort.show_img;
            },

            getPaymentMethodImgUrl: function () {
                return window.paylaneImgPath + '/payment_methods/sofort.png';
            },

            placeOrder: function () {
                var _self = this;
                fullScreenLoader.startLoader();
                customerData.invalidate(['cart']);

                var formData = {
                    method_code: this.item.method,
                    po_number: null,
                    additional_data: null
                };

                _self.build({
                    action: url.build('paylane/transaction/start'),
                    method: 'POST',
                    fields: formData
                }).submit();
            }
        });
    }
);