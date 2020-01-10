/**
 * File: paylane-ideal.js
 *
 
 */

'use strict';

define(
    [
        'ko',
        'jquery',
        'underscore',
        'mage/template',
        'mage/url',
        'Magento_Checkout/js/view/payment/default',
        'Magento_Checkout/js/model/full-screen-loader',
        'Magento_Customer/js/customer-data',
        'Magento_Checkout/js/checkout-data',
        'Magento_Checkout/js/action/select-payment-method'
    ],
    function (
        ko,
        $,
        _,
        mageTemplate,
        url,
        Component,
        fullScreenLoader,
        customerData,
        checkoutData,
        selectPaymentMethodAction
    ) {
        return Component.extend({
            defaults: {
                template: 'PeP_PaymentGateway/payment/ideal',
                code: 'paylane_ideal',
                additional_data: {
                    'bank_code': ''
                },
                bank_codes: ko.observableArray([])
            },

            isActive: function () {
                return this.getCode() === this.isChecked();
            },

            /**
             * Get data
             * @returns {Object}
             */
            getData: function () {
                return {
                    'method': this.item.method,
                    'additional_data': {
                        'bank_code': this.additional_data.bank_code
                    }
                };
            },

            showPaymentMethodImg: function () {
                return window.checkoutConfig.payment.paylane_ideal.show_img;
            },

            getBankCodes: function () {
                return this.bank_codes;
            },

            getPaymentMethodImgUrl: function () {
                return window.paylaneImgPath + '/payment_methods/ideal.png';
            },

            /**
             * @return {Boolean}
             */
            selectPaymentMethod: function () {
                selectPaymentMethodAction(this.getData());
                checkoutData.setSelectedPaymentMethod(this.item.method);

                var self = this;
                $.get(url.build('paylane/ideal/getData'))
                    .done(function (response) {
                        self.bank_codes(response);
                    });

                return true;
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

            placeOrder: function () {
                var _self = this;
                fullScreenLoader.startLoader();
                customerData.invalidate(['cart']);

                var formData = {
                    method_code: this.item.method,
                    po_number: null,
                    additional_data: null
                };

                _.each(_self.getData().additional_data, function (val, key) {
                    formData['additional_data[' + key + ']'] = val;
                });

                _self.build({
                    action: url.build('paylane/transaction/start'),
                    method: 'POST',
                    fields: formData
                }).submit();
            }
        });
    }
);