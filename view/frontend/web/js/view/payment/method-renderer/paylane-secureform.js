/**
 * File: paylane-secureform.js
 *
 
 */

'use strict';

define(
    [
        'jquery',
        'underscore',
        'mage/template',
        'mage/url',
        'mage/storage',
        'Magento_Checkout/js/view/payment/default',
        'Magento_Checkout/js/model/full-screen-loader',
        'Magento_Checkout/js/model/error-processor',
        'Magento_Checkout/js/model/url-builder',
        'Magento_Customer/js/customer-data'
    ],
    function (
        $,
        _,
        mageTemplate,
        url,
        storage,
        Component,
        fullScreenLoader,
        errorProcessor,
        urlBuilder,
        customerData
    ) {
        return Component.extend({
            defaults: {
                template: 'PeP_PaymentGateway/payment/secureform'
            },
            redirectAfterPlaceOrder: false,

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

            /**
             * @return {Boolean}
             */
            showPaymentMethodImg: function () {
                return Boolean(window.checkoutConfig.payment.paylane_secureform.show_img);
            },

            /**
             * @return {String}
             */
            getPaymentMethodImgUrl: function () {
                return window.paylaneImgPath + '/payment_methods/secureform.png';
            },

            /**
             * @return {VoidFunction}
             */
            placeOrder: function () {
                const serviceUrl = '/paylane/secure-form/build-payload';
                let payloadBuilderUrl = urlBuilder.createUrl(serviceUrl, {});

                fullScreenLoader.startLoader();
                this._super();

                return storage.get(
                    payloadBuilderUrl
                ).done(function (response) {
                    let form = this.build(response[0]);
                    form.submit();
                }.bind(this)).fail(function (response) {
                    errorProcessor.process(response, this.messageContainer);
                }.bind(this)).always(function () {
                    fullScreenLoader.stopLoader();
                });
            }
        });
    }
);