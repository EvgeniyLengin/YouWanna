/* js */

function reloadFormPhoneMask() {
    /* Задание маски телефона для его указания в заказе */
    $('.form-item #PHONE').mask('+7 (999) 999-99-99');
}

function reloadDatataFromSaleOrder() {
    /**
     * Скрипт для работы с выбором города (Dadata)
     */
    var $locationInput = $('.bx-ui-sls-container .bx-ui-sls-fake');
    $('.form-item #ADDRESS').suggestions({
        token: DADATA_TOKEN,
        type: 'ADDRESS',
        hint: false,
        constraints: {
            // ограничиваем поиск городом
            locations: {
                city: LANGUAGE_ID !== 'ru' ? '' : $locationInput.val()
            }
        },
        // в списке подсказок не показываем область и город
        restrict_value: true,
        bounds: "city",
        onSelect: function (suggestion) {
            if (LANGUAGE_ID !== 'ru') {
                $('.form-item #ADDRESS').val(yaTranslate(suggestion.data.source, LANGUAGE_ID));
            } else {
                $('.form-item #ADDRESS').val(suggestion.data.source);
            }
            //$locationInput.val(suggestion.data.city);
            /*$("#js-selected-city").text(suggestion.data.city);
             $('.js-choose-city').text();
             $('.js-choose-city-block').modal('close');
             $chooseForm.find('input#js-choose-city').val(suggestion.data.city);
             $chooseForm.find('input#js-region-id').val(suggestion.data.region_kladr_id);
             $chooseForm.find('input#js-city-id').val(suggestion.data.kladr_id);
             $chooseForm.trigger('submit');*/
        }
    });
}

BX.saleOrderAjax = { // bad solution, actually, a singleton at the page
    BXCallAllowed: false,
    options: {},
    indexCache: {},
    controls: {},
    modes: {},
    properties: {},

    // called once, on component load
    init: function (options) {
        var ctx = this;
        this.options = options;

        window.submitFormProxy = BX.proxy(function () {
            ctx.submitFormProxy.apply(ctx, arguments);
        }, this);

        BX(function () {
            ctx.initDeferredControl();
        });
        BX(function () {
            ctx.BXCallAllowed = true; // unlock form refresher
        });

        this.controls.scope = BX('order_form_div');

        // user presses "add location" when he cannot find location in popup mode
        BX.bindDelegate(this.controls.scope, 'click', {className: '-bx-popup-set-mode-add-loc'}, function () {

            var input = BX.create('input', {
                attrs: {
                    type: 'hidden',
                    name: 'PERMANENT_MODE_STEPS',
                    value: '1'
                }
            });

            BX.prepend(input, BX('ORDER_FORM'));

            ctx.BXCallAllowed = false;
            submitForm();
        });
    },
    cleanUp: function () {
        for (var k in this.properties) {
            if (this.properties.hasOwnProperty(k)) {
                if ('undefined' != typeof this.properties[k].input) {
                    BX.unbindAll(this.properties[k].input);
                    this.properties[k].input = null;
                }

                if ('undefined' != typeof this.properties[k].control) {
                    BX.unbindAll(this.properties[k].control);
                }
            }
        }
        this.properties = {};
    },

    addPropertyDesc: function (desc) {
        this.properties[desc.id] = desc.attributes;
        this.properties[desc.id].id = desc.id;
    },

    // called each time form refreshes
    initDeferredControl: function () {
        var ctx = this,
            k,
            row,
            input,
            locPropId,
            m,
            control,
            code,
            townInputFlag,
            adapter;

        // first, init all controls
        if ('undefined' != typeof window.BX.locationsDeferred) {
            this.BXCallAllowed = false;

            for (k in window.BX.locationsDeferred) {
                window.BX.locationsDeferred[k].call(this);
                window.BX.locationsDeferred[k] = null;
                delete(window.BX.locationsDeferred[k]);
                this.properties[k].control = window.BX.locationSelectors[k];
                delete(window.BX.locationSelectors[k]);
            }
        }

        for (k in this.properties) {
            // zip input handling
            if (this.properties[k].isZip) {
                row = this.controls.scope.querySelector('[data-property-id-row="' + k + '"]');
                if (BX.type.isElementNode(row)) {
                    input = row.querySelector('input[type="text"]');
                    if (BX.type.isElementNode(input)) {
                        this.properties[k].input = input;

                        // set value for the first "location" property met
                        locPropId = false;
                        for (m in this.properties) {
                            if ('LOCATION' == this.properties[m].type) {
                                locPropId = m;
                                break;
                            }
                        }

                        if (false !== locPropId) {
                            BX.bindDebouncedChange(input, function (value) {
                                input = null;
                                row = null;

                                if (BX.type.isNotEmptyString(value) && /^\s*\d+\s*$/.test(value) && 3 < value.length) {
                                    ctx.getLocationByZip(value, function (locationId) {
                                        ctx.properties[locPropId].control.setValueByLocationId(locationId);
                                    }, function () {
                                        try {
                                            ctx.properties[locPropId].control.clearSelected(locationId);
                                        } catch (e) {
                                        }
                                    });
                                }
                            });
                        }
                    }
                }
            }

            // location handling, town property, etc...
            if ('LOCATION' == this.properties[k].type) {
                if ('undefined' != typeof this.properties[k].control) {
                    control = this.properties[k].control; // reference to sale.location.selector.*
                    code = control.getSysCode();

                    // we have town property (alternative location)
                    if ('undefined' != typeof this.properties[k].altLocationPropId) {
                        if ('sls' == code) // for sale.location.selector.search
                        {
                            // replace default boring "nothing found" label for popup with "-bx-popup-set-mode-add-loc"
                            // inside
                            control.replaceTemplate('nothing-found', this.options.messages.notFoundPrompt);
                        }

                        if ('slst' == code)  // for sale.location.selector.steps
                        {
                            (function (k, control) {
                                // control can have "select other location" option
                                control.setOption('pseudoValues', ['other']);
                                // insert "other location" option to popup
                                control.bindEvent('control-before-display-page', function (adapter) {
                                    control = null;

                                    var parentValue = adapter.getParentValue();

                                    // you can choose "other" location only if parentNode is not root and is selectable
                                    if (parentValue == this.getOption('rootNodeValue') || !this.checkCanSelectItem(parentValue)) {
                                        return;
                                    }

                                    var controlInApater = adapter.getControl();

                                    if ('undefined' == typeof controlInApater.vars.cache.nodes['other']) {
                                        controlInApater.fillCache([{
                                            CODE: 'other',
                                            DISPLAY: ctx.options.messages.otherLocation,
                                            IS_PARENT: false,
                                            VALUE: 'other'
                                        }], {
                                            modifyOrigin: true,
                                            modifyOriginPosition: 'prepend'
                                        });
                                    }
                                });

                                townInputFlag = BX('LOCATION_ALT_PROP_DISPLAY_MANUAL[' + parseInt(k) + ']');

                                control.bindEvent('after-select-real-value', function () {
                                    // some location chosen
                                    if (BX.type.isDomNode(townInputFlag)) {
                                        townInputFlag.value = '0';
                                    }
                                });
                                control.bindEvent('after-select-pseudo-value', function () {
                                    // option "other location" chosen
                                    if (BX.type.isDomNode(townInputFlag)) {
                                        townInputFlag.value = '1';
                                    }
                                });

                                // when user click at default location or call .setValueByLocation*()
                                control.bindEvent('before-set-value', function () {
                                    if (BX.type.isDomNode(townInputFlag)) {
                                        townInputFlag.value = '0';
                                    }
                                });

                                // restore "other location" label on the last control
                                if (BX.type.isDomNode(townInputFlag) && '1' == townInputFlag.value) {
                                    // a little hack: set "other location" text display
                                    adapter = control.getAdapterAtPosition(control.getStackSize() - 1);

                                    if ('undefined' != typeof adapter && null !== adapter) {
                                        adapter.setValuePair('other', ctx.options.messages.otherLocation);
                                    }
                                }

                            })(k, control);
                        }
                    }
                }
            }
        }

        this.BXCallAllowed = true;
    },

    checkMode: function (propId, mode) {
        if ('altLocationChoosen' == mode) {
            if (this.checkAbility(propId, 'canHaveAltLocation')) {
                var input = this.getInputByPropId(this.properties[propId].altLocationPropId);
                var altPropId = this.properties[propId].altLocationPropId;

                if (false !== input && 0 < input.value.length && !input.disabled && 'default' != this.properties[altPropId].valueSource) {
                    return true;
                }
            }
        }

        return false;
    },

    checkAbility: function (propId, ability) {
        if ('undefined' == typeof this.properties[propId]) {
            this.properties[propId] = {};
        }

        if ('undefined' == typeof this.properties[propId].abilities) {
            this.properties[propId].abilities = {};
        }

        if ('undefined' != typeof this.properties[propId].abilities && this.properties[propId].abilities[ability]) {
            return true;
        }

        if ('canHaveAltLocation' == ability) {
            if ('LOCATION' == this.properties[propId].type) {
                // try to find corresponding alternate location prop
                if ('undefined' != typeof this.properties[propId].altLocationPropId && typeof this.properties[this.properties[propId].altLocationPropId]) {
                    var altLocPropId = this.properties[propId].altLocationPropId;

                    if ('undefined' != typeof this.properties[propId].control && 'slst' == this.properties[propId].control.getSysCode()) {
                        if (false !== this.getInputByPropId(altLocPropId)) {
                            this.properties[propId].abilities[ability] = true;
                            return true;
                        }
                    }
                }
            }
        }

        return false;
    },
    getInputByPropId: function (propId) {
        if ('undefined' != typeof this.properties[propId].input) {
            return this.properties[propId].input;
        }

        var row = this.getRowByPropId(propId);
        if (BX.type.isElementNode(row)) {
            var input = row.querySelector('input[type="text"]');
            if (BX.type.isElementNode(input)) {
                this.properties[propId].input = input;
                return input;
            }
        }

        return false;
    },
    getRowByPropId: function (propId) {
        if ('undefined' != typeof this.properties[propId].row) {
            return this.properties[propId].row;
        }

        var row = this.controls.scope.querySelector('[data-property-id-row="' + propId + '"]');

        if (BX.type.isElementNode(row)) {
            this.properties[propId].row = row;
            return row;
        }

        return false;
    },
    getAltLocPropByRealLocProp: function (propId) {
        if ('undefined' != typeof this.properties[propId].altLocationPropId) {
            return this.properties[this.properties[propId].altLocationPropId];
        }

        return false;
    },
    toggleProperty: function (propId, way, dontModifyRow) {
        var prop = this.properties[propId];

        if ('undefined' == typeof prop.row) {
            prop.row = this.getRowByPropId(propId);
        }

        if ('undefined' == typeof prop.input) {
            prop.input = this.getInputByPropId(propId);
        }

        if (!way) {
            if (!dontModifyRow) {
                BX.hide(prop.row);
            }
            prop.input.disabled = true;
        }
        else {
            if (!dontModifyRow) {
                BX.show(prop.row);
            }
            prop.input.disabled = false;
        }
    },
    submitFormProxy: function (item, control) {
        var propId = false;
        for (var k in this.properties) {
            if ('undefined' != typeof this.properties[k].control && this.properties[k].control == control) {
                propId = k;
                break;
            }
        }

        // turning LOCATION_ALT_PROP_DISPLAY_MANUAL on\off
        if ('other' != item) {
            if (this.BXCallAllowed) {
                this.BXCallAllowed = false;
                submitForm();
            }
        }
    },

    getPreviousAdapterSelectedNode: function (control, adapter) {
        var index = adapter.getIndex();
        var prevAdapter = control.getAdapterAtPosition(index - 1);

        if ('undefined' !== typeof prevAdapter && null !== prevAdapter) {
            var prevValue = prevAdapter.getControl().getValue();

            if ('undefined' != typeof prevValue) {
                var node = control.getNodeByValue(prevValue);

                if ('undefined' != typeof node) {
                    return node;
                }

                return false;
            }
        }

        return false;
    },
    getLocationByZip: function (value, successCallback, notFoundCallback) {
        if ('undefined' != typeof this.indexCache[value]) {
            successCallback.apply(this, [this.indexCache[value]]);
            return;
        }

        ShowWaitWindow();

        var ctx = this;

        BX.ajax({
            url: this.options.source,
            method: 'post',
            dataType: 'json',
            async: true,
            processData: true,
            emulateOnload: true,
            start: true,
            data: {'ACT': 'GET_LOC_BY_ZIP', 'ZIP': value},
            //cache: true,
            onsuccess: function (result) {
                CloseWaitWindow();
                if (result.result) {
                    ctx.indexCache[value] = result.data.ID;
                    successCallback.apply(ctx, [result.data.ID]);
                }
                else {
                    notFoundCallback.call(ctx);
                }
            },
            onfailure: function (type, e) {
                CloseWaitWindow();
                // on error do nothing // прекрасный подход
            }
        });
    }
};

// Отправка формы заказа (на кнопке)
function checkOrderForm() {

    submitForm('Y');
    return true;
}
$(document).ready(function () {
    if (window.location.href.slice(0,window.location.href.indexOf('\?')) !== 'https://test.you-wanna.ru/personal/make/') {
        var time = 180000;
        setTimeout(function () {
            var callbackRequestURL = '/ajax/cart_callback_form.php?lang=' + LANGUAGE_ID;
            $('#callback-modal .callback-modal-body').load(callbackRequestURL, function () {
                $('#callback-modal').addClass('open');
                $('.form-item input[name="form_text_8"]').mask('+7 (999) 999-99-99');
            });
        }, time)
    };
    /*reloadFormPhoneMask();*/
})
$(document).on("click", ".pay-system-container input[type=radio]", function() {
    $('#ORDER_CONFIRM_BUTTON').text('Оплатить');
});
$(document).on("click", "#ID_PAY_SYSTEM_ID_6, #ID_PAY_SYSTEM_ID_2", function() {
    if ( $(this).is(':checked') ) {
        $('#ORDER_CONFIRM_BUTTON').text('Оформить заказ');
    } else {
        $('#ORDER_CONFIRM_BUTTON').text('Оплатить');
    }
});
