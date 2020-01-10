/**
 * File: paylane-creditcard.js
 *
 
 */

'use strict';

define(
    [
        'jquery',
        'ko',
        'mage/url',
        'Magento_Checkout/js/model/error-processor',
        'Magento_Payment/js/view/payment/cc-form',
        'jquery_mask_plugin'
    ],
    function (
        $,
        ko,
        url,
        errorProcessor,
        Component,
        jquery_mask_plugin
    ) {
        return Component.extend({
            defaults: {
                template: 'PeP_PaymentGateway/payment/creditcard',
                code: 'paylane_creditcard',
                additionalData: {},
                creditCardHolderName: '',
                token: ko.observable(null),
                redirectAfterPlaceOrder: !Boolean(window.checkoutConfig.payment.paylane_creditcard.is_3ds_check_enabled),
                redirectUrl: url.build('paylane/creditcard/redirectto3dsecureproviderpage')
            },

            initialize: function () {
                this._super();
                PayLane.setPublicApiKey(window.checkoutConfig.payment.paylane.api_key);

                $('.paylane-cardnumber').mask('0000000000000000');
                $('.paylane-cardsecuritycode').mask('000');
            },

            /**
             * @returns {Object}
             */
            getData: function () {
                return {
                    method: this.getCode(),
                    additional_data: {
                        token: this.token(),
                    }
                };
            },

            /**
             * @return {Boolean}
             */
            showPaymentMethodImg: function () {
                return Boolean(window.checkoutConfig.payment.paylane_creditcard.show_img);
            },

            /**
             * @return {String}
             */
            getPaymentMethodImgUrl: function () {
                return String(window.paylaneImgPath + '/payment_methods/creditcard.png');
            },

            /**
             * @return {String}
             */
            getCode: function () {
                return String(this.code);
            },

            /**
             * @return {Boolean}
             */
            isActive: function () {
                return Boolean(this.getCode() === this.isChecked());
            },

            /**
             * @param {String} message
             * @return {void}
             */
            handleCheckoutError: function (message) {
                let self = this;
                errorProcessor.process({
                    responseText: JSON.stringify({
                        message: message
                    })
                }, self.messageContainer);
            },

            /**
             * @return {void}
             */
            generateToken: function () {
                let data = this.getData();

                let formData = {
                    method_code: data.method
                };

                let requestData = {
                    cardNumber: this.creditCardNumber(),
                    expirationMonth: (this.creditCardExpMonth() > 0 && this.creditCardExpMonth() < 10) ? '0' + this.creditCardExpMonth() : this.creditCardExpMonth(),
                    expirationYear: this.creditCardExpYear(),
                    nameOnCard: this.creditCardHolderName,
                    cardSecurityCode: this.creditCardVerificationNumber()
                };

                try {
                    PayLane.card.generateToken(
                        requestData,
                        function (token) {
                            formData['additional_data[token]'] = token;
                            this.token(token);
                            this.placeOrder();
                        }.bind(this),
                        function (code, result) {
                            this.handleCheckoutError(result);
                        }.bind(this)
                    );
                } catch (error) {
                    this.handleCheckoutError(error.message);
                }
            },

            /**
             * @return {void}
             */
            afterPlaceOrder: function () {
                window.location.replace(this.redirectUrl)
            },
        });
    }
);