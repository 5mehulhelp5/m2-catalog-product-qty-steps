/**
 * @author      Andreas Knollmann
 * @copyright   2014-2025 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

define([
    'jquery',
    'domReady',
    'priceBox',
    'addToCart'
], function ($, domReady) {
    'use strict';

    var globalOptions = {
        productId: 0,
        itemId: 0,
        steps: {}
    };

    $.widget('mage.productQtySteps', {
        options: globalOptions,

        _create: function() {
        },

        _init: function() {
            var self = this;

            domReady(function() {
                var qtyNode = $('#product_addtocart_form .box-tocart .field.qty #qty');
                var name = 'qty_steps';
                var id = 'qty-steps';

                if (qtyNode.length === 0 && self.options.itemId > 0) {
                    qtyNode = $('#cart-' + self.options.itemId + '-qty');
                    name = 'qty_steps_' + self.options.itemId;
                    id = 'qty-steps-' + self.options.itemId;
                }

                var qtyValue = parseInt($(qtyNode).val());

                $(qtyNode).hide();

                var qtySelect = $('<select>', {
                    name: name,
                    id: id,
                    class: 'qty-steps',
                });

                var selected = false;
                $(self.options.steps).each(function(key, value) {
                    var qtySelectOption = $('<option>');
                    qtySelectOption.attr('value', value);
                    qtySelectOption.text(value);

                    if (parseInt(value) === qtyValue) {
                        qtySelectOption.attr('selected', 'selected');
                        selected = true;
                    }

                    qtySelect.append(qtySelectOption);
                });

                qtySelect.on('change', function() {
                    if ($(qtyNode).val() !== $(this).val()) {
                        $(qtyNode).val($(this).val());
                        $(qtyNode).trigger('input');
                    }
                });

                $(qtyNode).after(qtySelect);

                if (! selected) {
                    $(qtyNode).val($(this).val());
                }

                $(qtyNode).trigger('input');

                qtySelect.trigger('change');

                var priceBoxes = $('[data-role="priceBox"]');

                priceBoxes.on('price-box-initialized', function() {
                    var productId = $(this).data('product-id');

                    if (productId === self.options.productId) {
                        qtySelect.trigger('change');
                    }
                });
            });
        },
    });

    return $.mage.productQtySteps;
});
