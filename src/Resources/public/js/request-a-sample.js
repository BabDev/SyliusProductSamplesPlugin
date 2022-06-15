(function ($) {
    'use strict';

    var handleProductOptionsChange = function () {
        $('[name*="sylius_add_to_cart[cartItem][variant]"]').on('change', function () {
            var requestASampleButton = $('#sylius-request-a-sample');
            var selector = '';

            $('#sylius-product-adding-to-cart select[data-option]').each(function (index, element) {
                var select = $(element);
                var option = select.find('option:selected').val();
                selector += '[data-' + select.attr('data-option') + '="' + option + '"]';
            });

            var isFreeSample = $('#sylius-variants-pricing').find(selector).attr('data-free-sample') === 'yes';

            if (isFreeSample) {
                requestASampleButton.find('.button-message').text(requestASampleButton.attr('data-free-sample-message'));
            } else {
                var samplePrice = $('#sylius-variants-pricing').find(selector).attr('data-sample-price');

                requestASampleButton.find('.button-message').text(requestASampleButton.attr('data-paid-sample-message').replace('%price%', samplePrice));
            }
        });
    };

    const handleProductVariantsChange = function () {
        $('[name="sylius_add_to_cart[cartItem][variant]"]').on('change', function (event) {
            var requestASampleButton = $('#sylius-request-a-sample');
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
            var requestASampleButton = $('#sylius-request-a-sample');

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
