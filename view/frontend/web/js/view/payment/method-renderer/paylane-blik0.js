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
        'Magento_Customer/js/customer-data',
        'jquery_mask_plugin'
    ],
    function (
        $,
        _,
        mageTemplate,
        url,
        Component,
        fullScreenLoader,
        customerData,
        // jquery_mask_plugin
    ) {
        return Component.extend({
            defaults: {
                template: 'PeP_PaymentGateway/payment/blik0',
                code: 'paylane_blik0',
                additional_data: {
                    'blik0_code': ''
                },
            },

            initialize: function () {
                this._super();

                $('#paylane_blik0_code').mask('000000');
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
                return window.checkoutConfig.payment.paylane_blik0.show_img;
            },

            getPaymentMethodImgUrl: function () {
                return window.paylaneImgPath + '/banks/BLIK.png';
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
                        'blik0_code': this.additional_data.blik0_code
                    }
                };
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

                if(formData['additional_data[blik0_code]'].length != 6){
                    fullScreenLoader.stopLoader();
                    return;
                }

                _self.build({
                    action: url.build('paylane/transaction/start'),
                    method: 'POST',
                    fields: formData
                }).submit();
            }
        });
    }
);