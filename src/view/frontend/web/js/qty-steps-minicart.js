/**
 * @author      Andreas Knollmann
 * @copyright   2014-2025 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

define([
    'jquery',
    'domReady!'
], function ($) {
    'use strict';

    var globalOptions = {
        steps: {}
    };

    $.widget('mage.productQtyStepsMinicart', {
        options: globalOptions,

        _create: function() {
        },

        _init: function() {
            var self = this;

            $('[data-block="minicart"]').on('contentUpdated', function() {
                var miniCart = $(this);

                $.each(self.options.steps, function(key, itemSteps) {
                    var itemId = itemSteps.itemId;

                    var qtySelect = miniCart.find('#qty-item-steps-' + itemId);

                    if (qtySelect.length === 0) {
                        var qtyNode = miniCart.find('#cart-item-' + itemId + '-qty');
                        var qtyValue = parseInt($(qtyNode).val());

                        $(qtyNode).hide();

                        qtySelect = $('<select>', {
                            name: 'qty_item_steps_' + itemId,
                            id: 'qty-item-steps-' + itemId,
                            class: 'qty-steps',
                        });

                        var selected = false;
                        $(itemSteps.steps).each(function(key, value) {
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
                                $(qtyNode).trigger('change');
                            }
                        });

                        $(qtyNode).after(qtySelect);
                    }
                });
            });
        },
    });

    return $.mage.productQtyStepsMinicart;
});
