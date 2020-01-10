/**
 * File: requirejs-config.js
 *
 
 */

var config = {
    config: {
        mixins: {
            'Magento_Checkout/js/model/shipping-save-processor/payload-extender': {
                'PeP_PaymentGateway/js/model/shipping-save-processor/payload-extender-mixin': true
            }
        }
    },
    map: {
        '*': {
            jquery_mask_plugin: 'PeP_PaymentGateway/js/jquery.mask.min',
        }
    }
};