(function ($) {
    'use strict';

    var handleProductOptionsChange = function () {
        $('[name*="sylius_add_to_cart[cartItem][variant]"]').on('change', function () {
            var requestASampleButton = $('#sylius_add_to_cart_requestSample');
            var selector = '';

            $('#sylius-product-adding-to-cart select[data-option]').each(function (index, element) {
                var select = $(element);
                var option = select.find('option:selected').val();
                selector += '[data-' + select.attr('data-option') + '="' + option + '"]';
            });

            var isFreeSample = $('#sylius-variants-pricing').find(selector).attr('data-free-sample') === 'yes';

            if (isFreeSample) {
                var freeSampleMessage = requestASampleButton.attr('data-free-sample-message');

                if (freeSampleMessage) {
                    requestASampleButton.find('.button-message').text(requestASampleButton.attr('data-free-sample-message'));
                }
            } else {
                var paidSampleMessage = requestASampleButton.attr('data-paid-sample-message');

                if (paidSampleMessage) {
                    var samplePrice = $('#sylius-variants-pricing').find(selector).attr('data-sample-price');

                    requestASampleButton.find('.button-message').text(paidSampleMessage.replace('%price%', samplePrice));
                }
            }
        });
    };

    const handleProductVariantsChange = function () {
        $('[name="sylius_add_to_cart[cartItem][variant]"]').on('change', function (event) {
            var requestASampleButton = $('#sylius_add_to_cart_requestSample');
            var priceRow = $(event.currentTarget).parents('tr').find('.sylius-product-variant-price');
            var isFreeSample = priceRow.attr('data-free-sample') === 'yes';

            if (isFreeSample) {
                requestASampleButton.find('.button-message').text(requestASampleButton.attr('data-free-sample-message'));
            } else {
                var samplePrice = priceRow.attr('data-sample-price');

                requestASampleButton.find('.button-message').text(requestASampleButton.attr('data-paid-sample-message').replace('%price%', samplePrice));
            }
        });
    };

    $.fn.extend({
        requestASample: function () {
            var requestASampleButton = $('#sylius_add_to_cart_requestSample');

            if (!requestASampleButton.length) {
                return;
            }

            if ($('#sylius-variants-pricing').length > 0) {
                handleProductOptionsChange();
            } else if ($('#sylius-product-variants').length > 0) {
                handleProductVariantsChange();
            }
        }
    });

    $(document).ready(function () {
        $(document).requestASample();
    });
})(jQuery);
