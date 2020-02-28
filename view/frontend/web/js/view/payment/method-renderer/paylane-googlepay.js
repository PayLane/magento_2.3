/**
 * File: paylane-googlepay.js
 *
 
 */

'use strict';

define(
    [
        'mage/url',
        'mage/translate',
        'Magento_Checkout/js/view/payment/default',
        'Magento_Checkout/js/model/full-screen-loader',
        'Magento_Customer/js/customer-data',
        'Magento_Checkout/js/model/error-processor',
        'Magento_Checkout/js/model/quote',
        'mage/storage',
        'Magento_Customer/js/model/customer',
        'Magento_Checkout/js/model/payment/additional-validators',
        'jquery',
        'mage/template',
    ],
    function (
        url,
        $t,
        Component,
        fullScreenLoader,
        customerData,
        errorProcessor,
        quote,
        storage,
        customer,
        additionalValidators,
        $,
        mageTemplate
    ) {
        return Component.extend({
            defaults: {
                template: 'PeP_PaymentGateway/payment/googlepay',
                code: 'paylane_googlepay',
                googlePayActive: true,
                additional_data: {
                    'token': ''
                }
            },

            isActive: function () {
                return this.getCode() === this.isChecked();
            },

            onGooglePaymentButtonClicked: function () {
                fullScreenLoader.startLoader();
                PayLane.googlePay
                    .loadPayment(this.getCartParams())
                    .then(function (paymentData) {
                        const token = btoa(paymentData.paymentMethodData.tokenizationData.token);
                        this.onGooglePayAuthorized(token)
                    }.bind(this))
                    .catch(function (err) {
                        fullScreenLoader.stopLoader();
                        console.error(err);
                    });
            },

            showGooglePayBtn: function () {
                if (!this.googlePayActive) {
                    return;
                }
                // create Google Pay button
                const button = PayLane.googlePay.createButton({
                    onClick: this.onGooglePaymentButtonClicked.bind(this)
                });

                const el = document.getElementById('google-pay-button');
                el.innerHTML = "";
                el.appendChild(button);
            },

            initialize: function () {
                this._super();

                PayLane.setPublicApiKey(window.checkoutConfig.payment.paylane.api_key.trim());

                const googleMerchantId = window.checkoutConfig.payment.paylane_googlepay.google_merchant_id.trim();
                if (googleMerchantId === '') {
                    PayLane.googlePay.init({ environment: 'TEST' });
                } else {
                    PayLane.googlePay.init({
                        environment: 'PRODUCTION',
                        googleMerchantId: googleMerchantId
                    });
                }

                PayLane.googlePay
                    .isReadyToPay()
                    .then(function (response) {
                        if (response.result) {
                            // proceed
                            this.googlePayActive = true;
                        } else {
                            this.googlePayActive = false;
                            console.warn("Google Pay is not available");
                        }
                    }.bind(this))
                    .catch(function (err) {
                        // handle errors here
                        this.googlePayActive = false;
                        console.error(err);
                    }.bind(this))
            },

            /**
             * Get data
             * @returns {Object}
             */
            getData: function () {
                return {
                    'method': this.item.method,
                    'googlePayActive': this.googlePayActive,
                    'additional_data': {
                        'token': this.additional_data.token
                    }
                }
            },

            showPaymentMethodImg: function () {
                return window.checkoutConfig.payment.paylane_googlepay.show_img;
            },

            getPaymentMethodImgUrl: function () {
                return window.paylaneImgPath + '/payment_methods/googlepay.png'
            },

            getCartParams: function () {
                var totals = quote.totals();
                return {
                    currencyCode: totals.base_currency_code,
                    totalPrice: parseFloat(totals.base_grand_total).toFixed(2)
                };
            },

            onGooglePayAuthorized: function (token) {

                var _self = this;
                fullScreenLoader.startLoader();
                customerData.invalidate(['cart']);

                let formData = {
                    method_code: this.item.method,
                    additional_data: null
                };

                formData['additional_data[token]'] = token;

                _self.build({
                    action: url.build('paylane/transaction/start'),
                    method: 'POST',
                    fields: formData
                }).submit();

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

            onError: function (result) {
                var _self = this;
                errorProcessor.process({
                    responseText: JSON.stringify({
                        message: result
                    })
                }, _self.messageContainer);
                fullScreenLoader.stopLoader()
            },

            placeOrder: function () {
                return false;
            }
        })
    }
);