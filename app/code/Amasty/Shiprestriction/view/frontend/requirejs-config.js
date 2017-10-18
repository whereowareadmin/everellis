var config = {
    config: {
        mixins: {
            'Magento_Checkout/js/model/shipping-rates-validation-rules': {
                'Amasty_Shiprestriction/js/model/shipping-rates-validation-rules-mixin': true
            }
        }
    },
	"map": {
		"*": {
			"Magento_SalesRule/js/model/payment/discount-messages" :
                "Amasty_Shiprestriction/js/model/payment/discount-messages"
		}
	}
};
