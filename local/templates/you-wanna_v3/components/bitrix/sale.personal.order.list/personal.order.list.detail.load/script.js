BX.namespace('BX.Sale.PersonalOrderComponent');

(function () {
    BX.Sale.PersonalOrderComponent.PersonalOrderDetail = {
        init: function (params) {
            var linkMoreOrderInformation = document.getElementsByClassName('sale-order-detail-about-order-inner-container-name-read-more')[0];
            var linkLessOrderInformation = document.getElementsByClassName('sale-order-detail-about-order-inner-container-name-read-less')[0];
            var clientInformation = document.getElementsByClassName('sale-order-detail-about-order-inner-container-details')[0];
            var listShipmentWrapper = document.getElementsByClassName('sale-order-detail-payment-options-shipment');
            var listPaymentWrapper = document.getElementsByClassName('sale-order-detail-payment-options-methods');
            var shipmentTrackingId = document.getElementsByClassName('sale-order-detail-shipment-id');

            if (shipmentTrackingId[0]) {
                Array.prototype.forEach.call(shipmentTrackingId, function (blockId) {
                    var clipboard = blockId.parentNode.getElementsByClassName('sale-order-detail-shipment-id-icon')[0];
                    if (clipboard) {
                        BX.clipboard.bindCopyClick(clipboard, {text: blockId.innerHTML});
                    }
                });
            }


            BX.bind(linkMoreOrderInformation, 'click', function () {

                clientInformation.style.display = 'inline-block';
                linkMoreOrderInformation.style.display = 'none';
                linkLessOrderInformation.style.display = 'inline-block';
            }, this);
            BX.bind(linkLessOrderInformation, 'click', function () {
                clientInformation.style.display = 'none';
                linkMoreOrderInformation.style.display = 'inline-block';
                linkLessOrderInformation.style.display = 'none';
            }, this);

            Array.prototype.forEach.call(listShipmentWrapper, function (shipmentWrapper) {
                var detailShipmentBlock = shipmentWrapper.getElementsByClassName('sale-order-detail-payment-options-shipment-composition-map')[0];
                var showInformation = shipmentWrapper.getElementsByClassName('sale-order-detail-show-link')[0];
                var hideInformation = shipmentWrapper.getElementsByClassName('sale-order-detail-hide-link')[0];

                BX.bindDelegate(shipmentWrapper, 'click', {'class': 'sale-order-detail-show-link'}, BX.proxy(function () {
                    showInformation.style.display = 'none';
                    hideInformation.style.display = 'inline-block';
                    detailShipmentBlock.style.display = 'block';
                }, this));
                BX.bindDelegate(shipmentWrapper, 'click', {'class': 'sale-order-detail-hide-link'}, BX.proxy(function () {
                    showInformation.style.display = 'inline-block';
                    hideInformation.style.display = 'none';
                    detailShipmentBlock.style.display = 'none';
                }, this));
            });

            Array.prototype.forEach.call(listPaymentWrapper, function (paymentWrapper) {
                var rowPayment = paymentWrapper.getElementsByClassName('sale-order-detail-payment-options-methods-info')[0];

                BX.bindDelegate(paymentWrapper, 'click', {'class': 'active-button'}, BX.proxy(function () {
                    BX.toggleClass(paymentWrapper, 'sale-order-detail-active-event');
                }, this));

                BX.bindDelegate(rowPayment, 'click', {'class': 'sale-order-detail-payment-options-methods-info-change-link'}, BX.proxy(function (event) {
                    event.preventDefault();

                    var btn = rowPayment.parentNode.getElementsByClassName('sale-order-detail-payment-options-methods-button-container')[0];
                    var linkReturn = rowPayment.parentNode.getElementsByClassName('sale-order-detail-payment-inner-row-template')[0];
                    BX.ajax(
                        {
                            method: 'POST',
                            dataType: 'html',
                            url: params.url,
                            data:
                                {
                                    sessid: BX.bitrix_sessid(),
                                    orderData: params.paymentList[event.target.id]
                                },
                            onsuccess: BX.proxy(function (result) {
                                rowPayment.innerHTML = result;
                                if (btn) {
                                    btn.parentNode.removeChild(btn);
                                }
                                linkReturn.style.display = "block";
                                BX.bind(linkReturn, 'click', function () {
                                    window.location.reload();
                                }, this);
                            }, this),
                            onfailure: BX.proxy(function () {
                                return this;
                            }, this)
                        }, this
                    );

                }, this));
            });
        }
    };
})();

(function () {
    BX.Sale.PersonalOrderComponent.PersonalOrderList = {
        init: function (params) {
            var rowWrapper = document.getElementsByClassName('sale-order-list-inner-row');

            params.paymentList = params.paymentList || {};
            params.url = params.url || "";

            Array.prototype.forEach.call(rowWrapper, function (wrapper) {
                var shipmentTrackingId = wrapper.getElementsByClassName('sale-order-list-shipment-id');
                if (shipmentTrackingId[0]) {
                    Array.prototype.forEach.call(shipmentTrackingId, function (blockId) {
                        var clipboard = blockId.parentNode.getElementsByClassName('sale-order-list-shipment-id-icon')[0];
                        if (clipboard) {
                            BX.clipboard.bindCopyClick(clipboard, {text: blockId.innerHTML});
                        }
                    });
                }

                BX.bindDelegate(wrapper, 'click', {'class': 'ajax_reload'}, BX.proxy(function (event) {
                    var block = wrapper.getElementsByClassName('sale-order-list-inner-row-body')[0];
                    var template = wrapper.getElementsByClassName('sale-order-list-inner-row-template')[0];
                    var cancelPaymentLink = template.getElementsByClassName('sale-order-list-cancel-payment')[0];

                    BX.ajax(
                        {
                            method: 'POST',
                            dataType: 'html',
                            url: event.target.href,
                            data:
                                {
                                    sessid: BX.bitrix_sessid()
                                },
                            onsuccess: BX.proxy(function (result) {
                                var resultDiv = document.createElement('div');
                                resultDiv.innerHTML = result;
                                template.insertBefore(resultDiv, cancelPaymentLink);
                                block.style.display = 'none';
                                template.style.display = 'block';

                                BX.bind(cancelPaymentLink, 'click', function () {
                                    block.style.display = 'block';
                                    template.style.display = 'none';
                                    resultDiv.remove();
                                }, this);

                            }, this),
                            onfailure: BX.proxy(function () {
                                return this;
                            }, this)
                        }, this
                    );
                    event.preventDefault();
                }, this));

                BX.bindDelegate(wrapper, 'click', {'class': 'sale-order-list-change-payment'}, BX.proxy(function (event) {
                    event.preventDefault();

                    var block = wrapper.getElementsByClassName('sale-order-list-inner-row-body')[0];
                    var template = wrapper.getElementsByClassName('sale-order-list-inner-row-template')[0];
                    var cancelPaymentLink = template.getElementsByClassName('sale-order-list-cancel-payment')[0];

                    BX.ajax(
                        {
                            method: 'POST',
                            dataType: 'html',
                            url: params.url,
                            data:
                                {
                                    sessid: BX.bitrix_sessid(),
                                    orderData: params.paymentList[event.target.id]
                                },
                            onsuccess: BX.proxy(function (result) {
                                var resultDiv = document.createElement('div');
                                resultDiv.innerHTML = result;
                                template.insertBefore(resultDiv, cancelPaymentLink);
                                event.target.style.display = 'none';
                                block.parentNode.removeChild(block);
                                template.style.display = 'block';
                                BX.bind(cancelPaymentLink, 'click', function () {
                                    window.location.reload();
                                }, this);

                            }, this),
                            onfailure: BX.proxy(function () {
                                return this;
                            }, this)
                        }, this
                    );

                }, this));
            });
        }
    };
})();

$(document).ready(function () {
    /*var elementId = $('.collapse-box.open').attr('id').substr(4);
    BX.ajax({
        url: '/ajax/order_detail_item.php',
        method: 'POST',
        data: {
            ELEMENT_CODE: elementId,
            lang: 'ru'
        },
        onsuccess: function (data) {
            $('.collapse-box.open td').html(data);
        }
    });
    $('#order-list-collapse').on('opened.collapse', function()
    {
        console.log($(this)[0].defaults.currentItem.hash.substr(5));
        var elementId = $(this)[0].defaults.currentItem.hash.substr(5);
        BX.ajax({
            url: '/ajax/order_detail_item.php',
            method: 'POST',
            data: {
                ELEMENT_CODE: elementId,
                lang: 'ru'
            },
            onsuccess: function (data) {
                $('.collapse-box.open td').html(data);
            }
        });
    });*/
    function openCurrentOrderBox($elem, $opened, $container) {
        $opened.hide(500);
        $opened.removeClass("opened");

        var timeoutDelay = $opened.length ? 600 : 0;

        setTimeout(function () {
            $container.addClass("opened");
            $container.show(500)
        }, timeoutDelay);
        setTimeout(function () {
            $('html, body').animate({scrollTop: $elem.offset().top - 200}, 300)
        }, timeoutDelay);
    }

    var containerFirst = $('.collapse-box.opened');
    var containerFirstItem = containerFirst
        .closest(".personal-order__item")
        .find(".js-personal-order-item");

    $.ajax({
        url: '/ajax/order_detail_item.php',
        method: 'POST',
        data: {
            ELEMENT_CODE: containerFirstItem.data("item-id"),
            lang: 'ru'
        },
        success: function (data) {
            $('.collapse-box.opened').html(data);
        }
    });
    $(containerFirst).show();

    $('.js-personal-order-item').on('click', function (e) {
        var $this = $(this);
        var $container = $(this).parent().find(".collapse-box");
        var $opened = $('.collapse-box.opened');

        if(!$container.hasClass("opened")) {
            if($container.html().length > 0) {
                openCurrentOrderBox($this, $opened, $container);
                return;
            }

            $.ajax({
                url: '/ajax/order_detail_item.php',
                method: 'POST',
                data: {
                    ELEMENT_CODE: $(this).data("item-id"),
                    lang: 'ru'
                },
                success: function (data) {
                    $container.html(data);

                    openCurrentOrderBox($this, $opened, $container);
                }
            });
        }
        else {
            $opened.hide(500);
            $opened.removeClass("opened");
        }

    });
    /*main-row-tr*/
    $('.js-open-order-details').on('click', function (e) {
        e.preventDefault();

        var $this = $(this).find('.collapse-toggle');
        var container = $(this).find('.collapse-toggle').attr('href');
        var opened = $('.collapse-box.opened');

        if(!$(this).hasClass('disabled-row')) {
            if (!$(container).hasClass('opened')) {
                if ($('.collapse-box' + container + ' td').html().trim() === '') {
                    $this.parent().parent('.main-row-tr').addClass('disabled-row');
                    BX.ajax({
                        url: '/ajax/order_detail_item.php',
                        method: 'POST',
                        data: {
                            ELEMENT_CODE: $(this).find('.collapse-toggle').attr('href').substr(5),
                            lang: 'ru'
                        },
                        onsuccess: function (data) {
                            $('.collapse-box' + container + ' td').html(data);

                            opened.hide(500);
                            opened.removeClass('opened');
                            $(container).addClass('opened');

                            setTimeout(function () {$(container).show(500)}, 600);
                            setTimeout(function () {
                                $('html, body').animate({scrollTop: $this.offset().top - 200}, 300)
                            }, 600);

                            $this.parent().parent('.main-row-tr').removeClass('disabled-row')
                        }
                    });
                } else {
                    opened.hide(500);
                    opened.removeClass('opened');
                    $(container).addClass('opened');

                    setTimeout(function () {$(container).show(500)}, 600);
                    setTimeout(function () {
                        $('html, body').animate({scrollTop: $this.offset().top - 200}, 300)
                    }, 600);
                }

            } else {
                $(container).removeClass('opened');
                opened.hide(500);
            }
            /*$(container).toggle(500);*/
        }
    })
})
/**
 * Ajax подгрузка заказов, вместо пагинации
 */
/*function ajaxLoadOrderlistElements(element) {

    var itemLink = '';
    var container = $('#order-list-collapse');
    var itemNextPage = parseInt(container.attr('data-next-page'));
    var itemNavNum = parseInt(container.attr('data-nav-num'));
    if (!itemNextPage) {
        itemNextPage = 2;
    }
    var itemLastPage = container.attr('data-last-page');
    var favoriteUrl = $(element).attr('data-ajax-url');
    var favoriteArray = $(element).attr('data-favorites-list');
    if (favoriteUrl !== '' && favoriteArray !== '') {
        itemLink = favoriteUrl + '?lang=' + LANGUAGE_ID
            + '&json_favorites=' + favoriteArray
            + '&ajax_page=Y&PAGEN_' + itemNavNum + '=' + itemNextPage
            + '&items=Y';
    } else {
        itemLink = document.location.pathname;
        itemLink += "?ajax_page=Y&PAGEN_" + itemNavNum + "=" + itemNextPage;
    }
console.log(itemNextPage <= itemLastPage);
    if (itemNextPage <= itemLastPage) {
        $('.js-ajax-load-items-button').css('opacity', '0.5');
        $.get(
            itemLink,
            {},
            function (data) {
                container.attr('data-next-page', itemNextPage+1);
                $('#order-list-collapse tbody').append(data);
                $('.js-ajax-load-items-button').eq(0).remove();
            }
        );
    }

    return false;
}*/
