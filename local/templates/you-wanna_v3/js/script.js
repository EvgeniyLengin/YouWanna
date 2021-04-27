/**
 * Created by vgagarkin 26.02.2018.
 */
/** Добавление отзыва клиентом на сайт. Обработка формы.*/




$(document).ready(function () {

    $("#box-measurement").find('table').wrap("<div class='wrapper-for-scroll'></div>");
    $(document).on('click', '.sender-btn.btn-subscribe', function(){
    let email = $('.bx-form-control').val();

    $('.custom-input-email-subscribe').val(email);
    $('#asd_subscribe_submit').click();


    });


    // анимации для главной
      // $('span.elem._elem1').addClass('animate-wanna1');
      // $('span.elem._elem2').addClass('animate-wanna2');
      // $('span.elem._elem3').addClass('animate-wanna3');
      // $('span.elem._elem4').addClass('animate-wanna4');
      $('.color-senkt').addClass('animate-you-wanna');
      setTimeout(function(){
          player1.play();
        $('.wrapper-for-video-block').addClass('to-fool-width');

      },2200)


      setTimeout(function(){
          let h = $('#myVideo').height() ;
          // let pluseheight = Number(screenWidth)/100*6;
          // console.log(h);
          // h = Number(h)+pluseheight;
          $('.wrapper-for-video-in-home').css('height', h);

      },1000)



    $(document).keydown(function(event){
            if(event.keyCode == 13) {
              event.preventDefault();
              return false;
          }


      });




// Перезаписываем мейл юзера, который он ввел в оформлении заказа в личный кабинет, до завершения заказа

$(document).on('click', '.cls-next-step-btn', function(){

    let userid =  $(document).find(".useridCustom").val()
    if (Number(userid) != 0) {
    let usermail = $(document).find("#EMAIL").val()


    let check = usermail.includes("@");
    if (check) {
        $.ajax({
            url: '/',
            type: "POST",
            data: {
                useridcheck: userid,
                usermailrefresh: usermail,
            },

            success: function (data) {
                        console.log('OK POST');
            }
        });

    }




    }


});
//* для корзины, расчет кол-ва и стоимости (чисто визуалка) //
    $(document).on('click', '.basket-item-amount-box', function(){


        let countItems = 0;
        let countPrice = 0;
        let allprice = 0;

        $('.basket-item-block-properties').each(function () {
        let currentcountitem = $(this).find('.basket-item-amount-filed').val();

        countItems = Number(countItems) + Number(currentcountitem);

        let countOnePrice = $(this).find('.basket-item-price-current-text').text();

        countOnePrice = countOnePrice.replace(/[^+\d]/g, '');


        let promRes = Number(countOnePrice) * Number(currentcountitem);

        allprice = Number(allprice) + Number(promRes);


        });


        allprice = String(allprice).replace(/\B(?=(\d{3})+(?!\d))/g, " ");
                console.log(allprice);

        $('.yw-basket-checkout-value.basket-checkout-block-items-count-value').text(countItems);
        $('.yw-basket-checkout-value.basket-coupon-block-total-price-current').text(allprice + " ₽");



    });
//end//



    var max_chars = 4;

$(document).on('keydown keyup', '.code_inner .form-control', function(e){

    if ($(this).val().length >= max_chars) {
        return false;
    }
});




    $(document).on('click', '.loadform-js-predz', function () {
    var prodID = $(document).find('.b-sku-panel-item-input').val();

    $.ajax({

    url: "",
    type: "get", //send it through get method
    data: {
    clear: "true",
    prodid: prodID
    },
    success: function(response) {




    let responsBasket = $(response).find('.top__menu.col.col-4').html();
    $('.top__menu.col.col-4').html(responsBasket);

    $('#js-fast-ordering-form .modal-body').load('/ajax/fast_ordering_form.php?LANGUAGE_ID=' +"ru", function () {
        /*console.log($(this));*/
        var $popupId = '#js-fast-ordering-form';
        var $popup = $($popupId);
        $popup.find('.modal-body').append($(this));

        $.modalwindow({
            target: $popupId,
            width: '400'
        });

        $popup.on('close.modal', function () {
            $popup.find('.modal-body').html('');
            $popup.removeClass('is-loading');
        });
    });

    },
    error: function(xhr) {
    console.log('no');
    }
    });






});

    "use strict";

    //Форма предзаказа
    $('.namepred').closest("tr").find("td input").attr('placeholder','Введите полное имя');
    $('.emailpred').closest("tr").find("td input").attr('placeholder','Введите Email адрес');
    $('.telpred').closest("tr").find("td input").attr('placeholder','Введите номер телефона');

    let namezak = $('#current-product-name').text();
    namezak = namezak.replace(/[\s{2,}]+/g, ' ');
    let adr = document.location.href;
    console.log(adr);


    $('input[name="form_hidden_14"]').val(namezak);
    $('input[name="form_hidden_15"]').val(adr);





$(document).on('touchstart', '#bx_basketFKauiI .bx-basket-item-list-button-container', function () {
        window.location.href = "/personal/basket/";
});


    $(document).on('click', '.exit-close', function () {
            $('.fast-check-p').addClass('undisplay');
            $('html').removeClass('fix-html');
            $('.black-fon').removeClass('active-over');

    });

    $(document).on('click', '.in-close-button', function () {
            $('.success-podpis').addClass('undisplay');
            $('html').removeClass('fix-html');
            $('.black-fon').removeClass('active-over');

    });
    $(document).on('click', '.block-ok', function () {
            $('.success-podpis').addClass('undisplay');
            $('html').removeClass('fix-html');
            $('.black-fon').removeClass('active-over');

    });

    $(document).on('click', '.go-to-search', function () {
            let searchtext = $('#title-search-input').val();
            if (searchtext != "") {
                 window.location.href = "/catalog/search/?q="+searchtext;
            }
    });



    $('body').on('click', '.exit-form', function () {
        console.log('s');
        $('.custom-modal-cooming-soon').removeClass('active');
        $('body').removeClass('fix-html');


    });
    $('body').on('click', '.predzakaz', function () {
        console.log('s');
        $('.custom-modal-cooming-soon').addClass('active');
        $('body').addClass('fix-html');

    });




    //Авторизация
    $(document).delegate('#popup-auth-button', 'click', function () {
        $('#USER_LOGIN').val().replace(/\D/g, "");
        $.ajax({
            url: '/ajax/check_auth.php',
            type: 'POST',
            data: {
                USER_LOGIN: $('#USER_LOGIN').val().replace(/\D/g, ""),
                USER_PASSWORD: $('#USER_PASSWORD').val(),
                USER_REMEMBER: $('#USER_REMEMBER').val()
            },
            async: false,
            success: function (data) {
                /*console.log(data);*/
                var json = JSON.parse(data);

                if (json['success'] === 'Y') {
                    /*location.reload(true);*/
                    if (window.service !== 1) {
                        if (json.sms_pass === true){
                            var callbackRequestURL = '/personal/change_passwd.php?lang=' +"ru";
                            $('#callback-modal .callback-modal-body').load(callbackRequestURL, function () {
                                /*$('#callback-modal').addClass('open');*/
                            });
                        } else {
                            document.location.href = '/personal/order/';
                        }
                    } else {
                        $("#myModal").modal('hide');
                        $("#paymentModal").modal('show');
                    }
                }
                else {
                    $('.auth-error').removeClass('hidden');
                }

            },
            cache: false
        });
    });

    /* Обработка отправки формы восстановления пароля */
    if ($("#form-review").length) {
        $("#form-review").load('/ajax/formReview.php?lang=' +"ru", function () {

            $("input[name='PHONE']").mask("+7 (999) 999-99-99");

            $("body").on("submit", ".js-promo-form", function (e) {
                e.preventDefault();
                var $form = $(this);
                var url = $form.attr("action");

                /* проверка на обязательность поля. если обязательное и пустое - пишем ошибку, а так же прокручиваем страницу к первому пустому полю. начало. */
                $form.find(".form-item").removeClass("has-error");
                $form.find(".js-ajax-msg").html("").css('display', 'none');
                var fields = $('.required', this);
                var errors = [];
                $('.error').removeClass('error');
                $('.error-msg').remove();

                var inputErrMsg = '';
                var emailErr = '';
                if (LANGUAGE_ID = 'ru') {
                    inputErrMsg = "Заполните это поле.";
                    emailErr = "Почта введена некорректно.";
                } else {
                    inputErrMsg = "This field required.";
                    emailErr = "Bad E-Mail.";
                }
                for (var i = 0; i < fields.length; i++) {
                    if ($(fields[i]).val() === '') {
                        $(fields[i]).addClass('error');
                        errors.push(fields[i]);
                        $(fields[i]).parent().append('<p class="message error">' + inputErrMsg + '</p>');
                    }
                }

                //console.log(fields);
                if (errors.length > 0) {
                    if ($(window).width() > 767) {
                        var pixels = 180;
                    } else {
                        var pixels = 60;
                    }

                    console.log($('#js-sticker').outerHeight() + 'hh1h1h1h1h1h1h1hh1h');
                    var scrollToScroll = $(errors[0]).offset().top - $('#js-sticker').outerHeight() - 30;
                    $('html').stop().animate({scrollTop: scrollToScroll}, "slow");
                }
                console.log(errors.length);
                /* конец. */

                /* проверка почты на валидность. начало. */
                var mailRegexp = /^[\w-\.]+@[\w-]+\.[a-z]{2,10}$/i;
                var userMail = $("input[name='EMAIL']");
                var valid = mailRegexp.test(userMail.val());
                if (!valid && userMail.val() !== '') {
                    $(userMail).addClass('error').after(emailErr);
                    errors.push('bad_email');
                }
                /* конец. */

                /* перебор массива прикрепленных файлов и передача их в $_REQUEST. начало.*/
                var dataFile = new FormData();
                var myFilesArr = $('input[type=file]', this);

                $.each(myFilesArr, function (i, file) {
                    var myFiles = myFilesArr[i].files;
                    dataFile.append(i, myFiles[0]);
                });
                /* конец. */

                /* recaptcha. start. */
                var isAuth = $('#recaptcha').length;
                console.log(isAuth);
                var google = true;
                if (isAuth != 0) {
                    var response = grecaptcha.getResponse();
                    if (response.length == 0) {
                        $('#recaptcha').after('<p class="message error">' + inputErrMsg + '</p>');
                        var google = false;
                    }
                }
                /* end. */

                /* отправка формы. начало.*/
                if (errors.length < 1 && google != false) {
                    $.ajax({
                        url: url + '&' + $form.serialize(),
                        dataType: 'text',
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: dataFile,
                        type: 'post',
                        success: function (data) {
                            var res = JSON.parse(data);
                            console.log(res.FILES);
                            console.log(res.NEW_ELEMENT);
                            console.log(res);

                            if (res.MSG) {
                                $form.find(".js-ajax-msg").html(res.MSG).css('display', 'table');
                            }
                            if (res.STATUS == 1) {
                                $form.find("input, textarea, input[type='submut'], select").attr("disabled", "disabled");
                                $form.find(".js-promo-form").hide();
                                console.log('Все отлично');
                            }
                        },
                        error: function (data) {
                            $form.find(".js-ajax-msg").html('Ошибка при загрузке').css('display', 'table');
                        }
                    });
                }
                /* конец. */
                return false;
            });

            /**
             * Работа с прикреплением файлов в форму обратной связи на странице Заполните Бриф
             */

            $('body').on('change', '.js-file-block', function () {
                var fileName = $('input[type="file"]', this).val();
                var uploadArray = $('input[type="file"]', this)[0].files;
                var type = uploadArray[0].type;
                //console.log(type);
                if (uploadArray[0].size < 1048577 && (type == 'image/jpeg' || type == 'text/txt' || type == 'image/png' || type == 'application/msword')) {
                    var pos = fileName.lastIndexOf("\\");
                    if (pos != -1) {
                        fileName = fileName.substr(pos + 1);
                    }
                    if (fileName !== '' && !$(this).next().hasClass('js-file-block') && $('.js-file-block').length < 3) {
                        $(this).clone().appendTo('.js-file-block-container');
                        $(this).next().find('input[type="file"]').val('');
                        $(this).find('.js-remove-file').show();
                    } else {
                        if ($('.js-file-block:last-child input[type="file"]').val() !== '') {
                            $('.js-file-block:last-child .js-remove-file').show();
                        }
                    }

                    $('.file-name', this).text(fileName);

                }

            });

            $('body').on('click', '.js-remove-file', function () {
                if ($('.js-file-block').length === 3 && $('.js-file-block:last-child .file-name').text() !== '') {
                    $(this).parent().remove();
                    $(this).parent().clone().appendTo('.js-file-block-container');
                    $('.js-file-block:last-child .file-name').text('');
                    $('.js-file-block:last-child .js-remove-file').hide();
                } else {
                    $(this).parent().remove();
                }
            });

            var topBanner = $('.top-banner-page');
            $('.form-review-btn').click(function () {
                $('.form-review-btn').addClass('open');
                var scrollToReviewForm = topBanner.offset().top + topBanner.outerHeight();
                $('html').stop().animate({scrollTop: scrollToReviewForm}, "slow");
                $('#form-review').toggle("slow");
            });


        });
    }

});

function JCSmartFilter(ajaxURL, viewMode, params) {
    this.ajaxURL = ajaxURL;
    this.form = null;
    this.timer = null;
    this.cacheKey = '';
    this.cache = [];
    this.popups = [];
    this.viewMode = viewMode;
    if (params && params.SEF_SET_FILTER_URL) {
        this.bindUrlToButton('set_filter', params.SEF_SET_FILTER_URL);
        this.sef = true;
    }
    if (params && params.SEF_DEL_FILTER_URL) {
        this.bindUrlToButton('del_filter', params.SEF_DEL_FILTER_URL);
    }
}

JCSmartFilter.prototype.keyup = function (input) {
    if (!!this.timer) {
        clearTimeout(this.timer);
    }
    this.timer = setTimeout(BX.delegate(function () {
        this.reload(input);
    }, this), 500);
};

JCSmartFilter.prototype.click = function (checkbox) {
    if (!!this.timer) {
        clearTimeout(this.timer);
    }

    this.timer = setTimeout(BX.delegate(function () {
        this.reload(checkbox);
    }, this), 500);
};

JCSmartFilter.prototype.reload = function (input) {
    if (this.cacheKey !== '') {
        //Postprone backend query
        if (!!this.timer) {
            clearTimeout(this.timer);
        }
        this.timer = setTimeout(BX.delegate(function () {
            this.reload(input);
        }, this), 1000);
        return;
    }
    this.cacheKey = '|';

    this.position = BX.pos(input, true);
    this.form = BX.findParent(input, {'tag': 'form'});
    if (this.form) {
        var values = [];
        values[0] = {name: 'ajax', value: 'y'};
        this.gatherInputsValues(values, BX.findChildren(this.form, {'tag': new RegExp('^(input|select)$', 'i')}, true));

        for (var i = 0; i < values.length; i++)
            this.cacheKey += values[i].name + ':' + values[i].value + '|';

        if (this.cache[this.cacheKey]) {
            this.curFilterinput = input;
            this.postHandler(this.cache[this.cacheKey], true);
        }
        else {
            if (this.sef) {
                var set_filter = BX('set_filter');
                set_filter.disabled = true;
            }

            this.curFilterinput = input;
            BX.ajax.loadJSON(
                this.ajaxURL,
                this.values2post(values),
                BX.delegate(this.postHandler, this)
            );
        }
    }
};

JCSmartFilter.prototype.updateItem = function (PID, arItem) {
    if (arItem.PROPERTY_TYPE === 'N' || arItem.PRICE) {
        var trackBar = window['trackBar' + PID];
        if (!trackBar && arItem.ENCODED_ID)
            trackBar = window['trackBar' + arItem.ENCODED_ID];

        if (trackBar && arItem.VALUES) {
            if (arItem.VALUES.MIN) {
                if (arItem.VALUES.MIN.FILTERED_VALUE)
                    trackBar.setMinFilteredValue(arItem.VALUES.MIN.FILTERED_VALUE);
                else
                    trackBar.setMinFilteredValue(arItem.VALUES.MIN.VALUE);
            }

            if (arItem.VALUES.MAX) {
                if (arItem.VALUES.MAX.FILTERED_VALUE)
                    trackBar.setMaxFilteredValue(arItem.VALUES.MAX.FILTERED_VALUE);
                else
                    trackBar.setMaxFilteredValue(arItem.VALUES.MAX.VALUE);
            }
        }
    }
    else if (arItem.VALUES) {
        for (var i in arItem.VALUES) {
            if (arItem.VALUES.hasOwnProperty(i)) {
                var value = arItem.VALUES[i];
                var control = BX(value.CONTROL_ID);

                if (!!control) {
                    var label = document.querySelector('[data-role="label_' + value.CONTROL_ID + '"]');
                    if (value.DISABLED) {
                        if (label)
                            BX.addClass(label, 'disabled');
                        else
                            BX.addClass(control.parentNode, 'disabled');
                    }
                    else {
                        if (label)
                            BX.removeClass(label, 'disabled');
                        else
                            BX.removeClass(control.parentNode, 'disabled');
                    }

                    if (value.hasOwnProperty('ELEMENT_COUNT')) {
                        label = document.querySelector('[data-role="count_' + value.CONTROL_ID + '"]');
                        if (label)
                            label.innerHTML = value.ELEMENT_COUNT;
                    }
                }
            }
        }
    }
};

JCSmartFilter.prototype.postHandler = function (result, fromCache) {
    var hrefFILTER, url, curProp;
    var modef = BX('modef');
    var modef_num = BX('modef_num');

    if (!!result && !!result.ITEMS) {
        for (var popupId in this.popups) {
            if (this.popups.hasOwnProperty(popupId)) {
                this.popups[popupId].destroy();
            }
        }
        this.popups = [];

        for (var PID in result.ITEMS) {
            if (result.ITEMS.hasOwnProperty(PID)) {
                this.updateItem(PID, result.ITEMS[PID]);
            }
        }

        if (!!modef && !!modef_num) {
            modef_num.innerHTML = result.ELEMENT_COUNT;
            hrefFILTER = BX.findChildren(modef, {tag: 'A'}, true);

            if (result.FILTER_URL && hrefFILTER) {
                hrefFILTER[0].href = BX.util.htmlspecialcharsback(result.FILTER_URL);
                location.href = BX.util.htmlspecialcharsback(result.FILTER_URL);
            }

            if (result.FILTER_AJAX_URL && result.COMPONENT_CONTAINER_ID) {
                BX.unbindAll(hrefFILTER[0]);
                BX.bind(hrefFILTER[0], 'click', function (e) {
                    url = BX.util.htmlspecialcharsback(result.FILTER_AJAX_URL);
                    BX.ajax.insertToNode(url, result.COMPONENT_CONTAINER_ID);
                    return BX.PreventDefault(e);
                });
            }

            if (result.INSTANT_RELOAD && result.COMPONENT_CONTAINER_ID) {
                url = BX.util.htmlspecialcharsback(result.FILTER_AJAX_URL);
                BX.ajax.insertToNode(url, result.COMPONENT_CONTAINER_ID);
            }
            else {
                if (modef.style.display === 'none') {
                    modef.style.display = 'inline-block';
                }

                if (this.viewMode == "VERTICAL") {
                    curProp = BX.findChild(BX.findParent(this.curFilterinput, {'class': 'bx-filter-parameters-box'}), {'class': 'bx-filter-container-modef'}, true, false);
                    curProp.appendChild(modef);
                }

                if (result.SEF_SET_FILTER_URL) {
                    this.bindUrlToButton('set_filter', result.SEF_SET_FILTER_URL);
                }
            }
        }
    }

    if (this.sef) {
        var set_filter = BX('set_filter');
        set_filter.disabled = false;
    }

    if (!fromCache && this.cacheKey !== '') {
        this.cache[this.cacheKey] = result;
    }
    this.cacheKey = '';
};

JCSmartFilter.prototype.bindUrlToButton = function (buttonId, url) {
    var button = BX(buttonId);
    if (button) {
        var proxy = function (j, func) {
            return function () {
                return func(j);
            }
        };

        if (button.type == 'submit')
            button.type = 'button';

        BX.bind(button, 'click', proxy(url, function (url) {
            window.location.href = url;
            return false;
        }));
    }
};

JCSmartFilter.prototype.gatherInputsValues = function (values, elements) {
    if (elements) {
        for (var i = 0; i < elements.length; i++) {
            var el = elements[i];
            if (el.disabled || !el.type)
                continue;

            switch (el.type.toLowerCase()) {
                case 'text':
                case 'textarea':
                case 'password':
                case 'hidden':
                case 'select-one':
                    if (el.value.length)
                        values[values.length] = {name: el.name, value: el.value};
                    break;
                case 'radio':
                case 'checkbox':
                    if (el.checked)
                        values[values.length] = {name: el.name, value: el.value};
                    break;
                case 'select-multiple':
                    for (var j = 0; j < el.options.length; j++) {
                        if (el.options[j].selected)
                            values[values.length] = {name: el.name, value: el.options[j].value};
                    }
                    break;
                default:
                    break;
            }
        }
    }
};

JCSmartFilter.prototype.values2post = function (values) {
    var post = [];
    var current = post;
    var i = 0;

    while (i < values.length) {
        var p = values[i].name.indexOf('[');
        if (p == -1) {
            current[values[i].name] = values[i].value;
            current = post;
            i++;
        }
        else {
            var name = values[i].name.substring(0, p);
            var rest = values[i].name.substring(p + 1);
            if (!current[name])
                current[name] = [];

            var pp = rest.indexOf(']');
            if (pp == -1) {
                //Error - not balanced brackets
                current = post;
                i++;
            }
            else if (pp == 0) {
                //No index specified - so take the next integer
                current = current[name];
                values[i].name = '' + current.length;
            }
            else {
                //Now index name becomes and name and we go deeper into the array
                current = current[name];
                values[i].name = rest.substring(0, pp) + rest.substring(pp + 1);
            }
        }
    }
    return post;
};

JCSmartFilter.prototype.hideFilterProps = function (element) {
    var obj = element.parentNode,
        filterBlock = obj.querySelector("[data-role='bx_filter_block']"),
        propAngle = obj.querySelector("[data-role='prop_angle']");

    if (BX.hasClass(obj, "bx-active")) {
        new BX.easing({
            duration: 300,
            start: {opacity: 1, height: filterBlock.offsetHeight},
            finish: {opacity: 0, height: 0},
            transition: BX.easing.transitions.quart,
            step: function (state) {
                filterBlock.style.opacity = state.opacity;
                filterBlock.style.height = state.height + "px";
            },
            complete: function () {
                filterBlock.setAttribute("style", "");
                BX.removeClass(obj, "bx-active");
            }
        }).animate();

        BX.addClass(propAngle, "fa-angle-down");
        BX.removeClass(propAngle, "fa-angle-up");
    }
    else {
        filterBlock.style.display = "block";
        filterBlock.style.opacity = 0;
        filterBlock.style.height = "auto";

        var obj_children_height = filterBlock.offsetHeight;
        filterBlock.style.height = 0;

        new BX.easing({
            duration: 300,
            start: {opacity: 0, height: 0},
            finish: {opacity: 1, height: obj_children_height},
            transition: BX.easing.transitions.quart,
            step: function (state) {
                filterBlock.style.opacity = state.opacity;
                filterBlock.style.height = state.height + "px";
            },
            complete: function () {
            }
        }).animate();

        BX.addClass(obj, "bx-active");
        BX.removeClass(propAngle, "fa-angle-down");
        BX.addClass(propAngle, "fa-angle-up");
    }
};

JCSmartFilter.prototype.showDropDownPopup = function (element, popupId) {
    var contentNode = element.querySelector('[data-role="dropdownContent"]');
    this.popups["smartFilterDropDown" + popupId] = BX.PopupWindowManager.create("smartFilterDropDown" + popupId, element, {
        autoHide: true,
        offsetLeft: 0,
        offsetTop: 3,
        overlay: false,
        draggable: {restrict: true},
        closeByEsc: true,
        content: BX.clone(contentNode)
    });
    this.popups["smartFilterDropDown" + popupId].show();
};

JCSmartFilter.prototype.selectDropDownItem = function (element, controlId) {
    this.keyup(BX(controlId));

    var wrapContainer = BX.findParent(BX(controlId), {className: "bx-filter-select-container"}, false);

    var currentOption = wrapContainer.querySelector('[data-role="currentOption"]');
    currentOption.innerHTML = element.innerHTML;
    BX.PopupWindowManager.getCurrentPopup().close();
};

BX.namespace("BX.Iblock.SmartFilter");
BX.Iblock.SmartFilter = (function () {
    /** @param {{
			leftSlider: string,
			rightSlider: string,
			tracker: string,
			trackerWrap: string,
			minInputId: string,
			maxInputId: string,
			minPrice: float|int|string,
			maxPrice: float|int|string,
			curMinPrice: float|int|string,
			curMaxPrice: float|int|string,
			fltMinPrice: float|int|string|null,
			fltMaxPrice: float|int|string|null,
			precision: int|null,
			colorUnavailableActive: string,
			colorAvailableActive: string,
			colorAvailableInactive: string
		}} arParams
     */
    var SmartFilter = function (arParams) {
        if (typeof arParams === 'object') {
            this.leftSlider = BX(arParams.leftSlider);
            this.rightSlider = BX(arParams.rightSlider);
            this.tracker = BX(arParams.tracker);
            this.trackerWrap = BX(arParams.trackerWrap);

            this.minInput = BX(arParams.minInputId);
            this.maxInput = BX(arParams.maxInputId);

            this.minPrice = parseFloat(arParams.minPrice);
            this.maxPrice = parseFloat(arParams.maxPrice);

            this.curMinPrice = parseFloat(arParams.curMinPrice);
            this.curMaxPrice = parseFloat(arParams.curMaxPrice);

            this.fltMinPrice = arParams.fltMinPrice ? parseFloat(arParams.fltMinPrice) : parseFloat(arParams.curMinPrice);
            this.fltMaxPrice = arParams.fltMaxPrice ? parseFloat(arParams.fltMaxPrice) : parseFloat(arParams.curMaxPrice);

            this.precision = arParams.precision || 0;

            this.priceDiff = this.maxPrice - this.minPrice;

            this.leftPercent = 0;
            this.rightPercent = 0;

            this.fltMinPercent = 0;
            this.fltMaxPercent = 0;

            this.colorUnavailableActive = BX(arParams.colorUnavailableActive);//gray
            this.colorAvailableActive = BX(arParams.colorAvailableActive);//blue
            this.colorAvailableInactive = BX(arParams.colorAvailableInactive);//light blue

            this.isTouch = false;

            this.init();

            if ('ontouchstart' in document.documentElement) {
                this.isTouch = true;

                BX.bind(this.leftSlider, "touchstart", BX.proxy(function (event) {
                    this.onMoveLeftSlider(event)
                }, this));

                BX.bind(this.rightSlider, "touchstart", BX.proxy(function (event) {
                    this.onMoveRightSlider(event)
                }, this));
            }
            else {
                BX.bind(this.leftSlider, "mousedown", BX.proxy(function (event) {
                    this.onMoveLeftSlider(event)
                }, this));

                BX.bind(this.rightSlider, "mousedown", BX.proxy(function (event) {
                    this.onMoveRightSlider(event)
                }, this));
            }

            BX.bind(this.minInput, "keyup", BX.proxy(function (event) {
                this.onInputChange();
            }, this));

            BX.bind(this.maxInput, "keyup", BX.proxy(function (event) {
                this.onInputChange();
            }, this));
        }
    };

    SmartFilter.prototype.init = function () {
        var priceDiff;

        if (this.curMinPrice > this.minPrice) {
            priceDiff = this.curMinPrice - this.minPrice;
            this.leftPercent = (priceDiff * 100) / this.priceDiff;

            this.leftSlider.style.left = this.leftPercent + "%";
            this.colorUnavailableActive.style.left = this.leftPercent + "%";
        }

        this.setMinFilteredValue(this.fltMinPrice);

        if (this.curMaxPrice < this.maxPrice) {
            priceDiff = this.maxPrice - this.curMaxPrice;
            this.rightPercent = (priceDiff * 100) / this.priceDiff;

            this.rightSlider.style.right = this.rightPercent + "%";
            this.colorUnavailableActive.style.right = this.rightPercent + "%";
        }

        this.setMaxFilteredValue(this.fltMaxPrice);
    };

    SmartFilter.prototype.setMinFilteredValue = function (fltMinPrice) {
        this.fltMinPrice = parseFloat(fltMinPrice);
        if (this.fltMinPrice >= this.minPrice) {
            var priceDiff = this.fltMinPrice - this.minPrice;
            this.fltMinPercent = (priceDiff * 100) / this.priceDiff;

            if (this.leftPercent > this.fltMinPercent)
                this.colorAvailableActive.style.left = this.leftPercent + "%";
            else
                this.colorAvailableActive.style.left = this.fltMinPercent + "%";

            this.colorAvailableInactive.style.left = this.fltMinPercent + "%";
        }
        else {
            this.colorAvailableActive.style.left = "0%";
            this.colorAvailableInactive.style.left = "0%";
        }
    };

    SmartFilter.prototype.setMaxFilteredValue = function (fltMaxPrice) {
        this.fltMaxPrice = parseFloat(fltMaxPrice);
        if (this.fltMaxPrice <= this.maxPrice) {
            var priceDiff = this.maxPrice - this.fltMaxPrice;
            this.fltMaxPercent = (priceDiff * 100) / this.priceDiff;

            if (this.rightPercent > this.fltMaxPercent)
                this.colorAvailableActive.style.right = this.rightPercent + "%";
            else
                this.colorAvailableActive.style.right = this.fltMaxPercent + "%";

            this.colorAvailableInactive.style.right = this.fltMaxPercent + "%";
        }
        else {
            this.colorAvailableActive.style.right = "0%";
            this.colorAvailableInactive.style.right = "0%";
        }
    };

    SmartFilter.prototype.getXCoord = function (elem) {
        var box = elem.getBoundingClientRect();
        var body = document.body;
        var docElem = document.documentElement;

        var scrollLeft = window.pageXOffset || docElem.scrollLeft || body.scrollLeft;
        var clientLeft = docElem.clientLeft || body.clientLeft || 0;
        var left = box.left + scrollLeft - clientLeft;

        return Math.round(left);
    };

    SmartFilter.prototype.getPageX = function (e) {
        e = e || window.event;
        var pageX = null;

        if (this.isTouch && event.targetTouches[0] != null) {
            pageX = e.targetTouches[0].pageX;
        }
        else if (e.pageX != null) {
            pageX = e.pageX;
        }
        else if (e.clientX != null) {
            var html = document.documentElement;
            var body = document.body;

            pageX = e.clientX + (html.scrollLeft || body && body.scrollLeft || 0);
            pageX -= html.clientLeft || 0;
        }

        return pageX;
    };

    SmartFilter.prototype.recountMinPrice = function () {
        var newMinPrice = (this.priceDiff * this.leftPercent) / 100;
        newMinPrice = (this.minPrice + newMinPrice).toFixed(this.precision);

        if (newMinPrice != this.minPrice)
            this.minInput.value = newMinPrice;
        else
            this.minInput.value = "";
        /** @global JCSmartFilter smartFilter */
        smartFilter.keyup(this.minInput);
    };

    SmartFilter.prototype.recountMaxPrice = function () {
        var newMaxPrice = (this.priceDiff * this.rightPercent) / 100;
        newMaxPrice = (this.maxPrice - newMaxPrice).toFixed(this.precision);

        if (newMaxPrice != this.maxPrice)
            this.maxInput.value = newMaxPrice;
        else
            this.maxInput.value = "";
        /** @global JCSmartFilter smartFilter */
        smartFilter.keyup(this.maxInput);
    };

    SmartFilter.prototype.onInputChange = function () {
        var priceDiff;
        if (this.minInput.value) {
            var leftInputValue = this.minInput.value;
            if (leftInputValue < this.minPrice)
                leftInputValue = this.minPrice;

            if (leftInputValue > this.maxPrice)
                leftInputValue = this.maxPrice;

            priceDiff = leftInputValue - this.minPrice;
            this.leftPercent = (priceDiff * 100) / this.priceDiff;

            this.makeLeftSliderMove(false);
        }

        if (this.maxInput.value) {
            var rightInputValue = this.maxInput.value;
            if (rightInputValue < this.minPrice)
                rightInputValue = this.minPrice;

            if (rightInputValue > this.maxPrice)
                rightInputValue = this.maxPrice;

            priceDiff = this.maxPrice - rightInputValue;
            this.rightPercent = (priceDiff * 100) / this.priceDiff;

            this.makeRightSliderMove(false);
        }
    };

    SmartFilter.prototype.makeLeftSliderMove = function (recountPrice) {
        recountPrice = (recountPrice !== false);

        this.leftSlider.style.left = this.leftPercent + "%";
        this.colorUnavailableActive.style.left = this.leftPercent + "%";

        var areBothSlidersMoving = false;
        if (this.leftPercent + this.rightPercent >= 100) {
            areBothSlidersMoving = true;
            this.rightPercent = 100 - this.leftPercent;
            this.rightSlider.style.right = this.rightPercent + "%";
            this.colorUnavailableActive.style.right = this.rightPercent + "%";
        }

        if (this.leftPercent >= this.fltMinPercent && this.leftPercent <= (100 - this.fltMaxPercent)) {
            this.colorAvailableActive.style.left = this.leftPercent + "%";
            if (areBothSlidersMoving) {
                this.colorAvailableActive.style.right = 100 - this.leftPercent + "%";
            }
        }
        else if (this.leftPercent <= this.fltMinPercent) {
            this.colorAvailableActive.style.left = this.fltMinPercent + "%";
            if (areBothSlidersMoving) {
                this.colorAvailableActive.style.right = 100 - this.fltMinPercent + "%";
            }
        }
        else if (this.leftPercent >= this.fltMaxPercent) {
            this.colorAvailableActive.style.left = 100 - this.fltMaxPercent + "%";
            if (areBothSlidersMoving) {
                this.colorAvailableActive.style.right = this.fltMaxPercent + "%";
            }
        }

        if (recountPrice) {
            this.recountMinPrice();
            if (areBothSlidersMoving)
                this.recountMaxPrice();
        }
    };

    SmartFilter.prototype.countNewLeft = function (event) {
        var pageX = this.getPageX(event);

        var trackerXCoord = this.getXCoord(this.trackerWrap);
        var rightEdge = this.trackerWrap.offsetWidth;

        var newLeft = pageX - trackerXCoord;

        if (newLeft < 0)
            newLeft = 0;
        else if (newLeft > rightEdge)
            newLeft = rightEdge;

        return newLeft;
    };

    SmartFilter.prototype.onMoveLeftSlider = function (e) {
        if (!this.isTouch) {
            this.leftSlider.ondragstart = function () {
                return false;
            };
        }

        if (!this.isTouch) {
            document.onmousemove = BX.proxy(function (event) {
                this.leftPercent = ((this.countNewLeft(event) * 100) / this.trackerWrap.offsetWidth);
                this.makeLeftSliderMove();
            }, this);

            document.onmouseup = function () {
                document.onmousemove = document.onmouseup = null;
            };
        }
        else {
            document.ontouchmove = BX.proxy(function (event) {
                this.leftPercent = ((this.countNewLeft(event) * 100) / this.trackerWrap.offsetWidth);
                this.makeLeftSliderMove();
            }, this);

            document.ontouchend = function () {
                document.ontouchmove = document.touchend = null;
            };
        }

        return false;
    };

    SmartFilter.prototype.makeRightSliderMove = function (recountPrice) {
        recountPrice = (recountPrice !== false);

        this.rightSlider.style.right = this.rightPercent + "%";
        this.colorUnavailableActive.style.right = this.rightPercent + "%";

        var areBothSlidersMoving = false;
        if (this.leftPercent + this.rightPercent >= 100) {
            areBothSlidersMoving = true;
            this.leftPercent = 100 - this.rightPercent;
            this.leftSlider.style.left = this.leftPercent + "%";
            this.colorUnavailableActive.style.left = this.leftPercent + "%";
        }

        if ((100 - this.rightPercent) >= this.fltMinPercent && this.rightPercent >= this.fltMaxPercent) {
            this.colorAvailableActive.style.right = this.rightPercent + "%";
            if (areBothSlidersMoving) {
                this.colorAvailableActive.style.left = 100 - this.rightPercent + "%";
            }
        }
        else if (this.rightPercent <= this.fltMaxPercent) {
            this.colorAvailableActive.style.right = this.fltMaxPercent + "%";
            if (areBothSlidersMoving) {
                this.colorAvailableActive.style.left = 100 - this.fltMaxPercent + "%";
            }
        }
        else if ((100 - this.rightPercent) <= this.fltMinPercent) {
            this.colorAvailableActive.style.right = 100 - this.fltMinPercent + "%";
            if (areBothSlidersMoving) {
                this.colorAvailableActive.style.left = this.fltMinPercent + "%";
            }
        }

        if (recountPrice) {
            this.recountMaxPrice();
            if (areBothSlidersMoving)
                this.recountMinPrice();
        }
    };

    SmartFilter.prototype.onMoveRightSlider = function (e) {
        if (!this.isTouch) {
            this.rightSlider.ondragstart = function () {
                return false;
            };
        }

        if (!this.isTouch) {
            document.onmousemove = BX.proxy(function (event) {
                this.rightPercent = 100 - (((this.countNewLeft(event)) * 100) / (this.trackerWrap.offsetWidth));
                this.makeRightSliderMove();
            }, this);

            document.onmouseup = function () {
                document.onmousemove = document.onmouseup = null;
            };
        }
        else {
            document.ontouchmove = BX.proxy(function (event) {
                this.rightPercent = 100 - (((this.countNewLeft(event)) * 100) / (this.trackerWrap.offsetWidth));
                this.makeRightSliderMove();
            }, this);

            document.ontouchend = function () {
                document.ontouchmove = document.ontouchend = null;
            };
        }

        return false;
    };

    return SmartFilter;
})();


function setSmartFilterCheckbox(element) {
    if ($('input:checked', element).length === 1) {
        $('.filter-select-text .bx-filter-title-desktop', element).text($('input:checked', element).next().text());
    } else if ($('input:checked', element).length > 1) {
        $('.filter-select-text .bx-filter-title-desktop', element).text('Выбрано ' + $('input:checked', element).length);
    }
}

$(document).ready(function () {
    $('#fast-basket.modal-box .close').click(function(){
        $('#fast-basket').removeClass('open');
    });
    $('.filter-select-box').click(function () {
        $('.flex-main-block .flex-item').removeClass('open-block');
        if ($('i', this).hasClass('fa-angle-down')) {
            $('.filter-select-box i.fa-angle-up').toggleClass('fa-angle-down').toggleClass('fa-angle-up')
                .parents('.filter-select-box').next().slideUp();
            $(this).next().slideToggle();
        } else {
            $(this).next().slideUp();
        }

        $('i', this).toggleClass('fa-angle-down').toggleClass('fa-angle-up');
        var $parent = $(this).parents('.flex-item');
        if ($('i', this).hasClass('fa-angle-down')) {
            $parent.removeClass('open-block');
        } else {
            $parent.addClass('open-block');
        }
    });

    $('.multiselect-checkbox-block').each(function () {
        setSmartFilterCheckbox(this);
    });

    $('.multiselect-checkbox-block input').change(function () {
        var $parent = $(this).parents('.multiselect-checkbox-block');
        setSmartFilterCheckbox($parent);
    });
});

$(document).click(function (event) {
    if ($(event.target).closest(".multiselect-checkbox-block").length)
        return;
    $('.multiselect-checkbox-block .checkbox-block').slideUp();
    $('.multiselect-checkbox-block i').addClass('fa-angle-down').removeClass('fa-angle-up');
    event.stopPropagation();
});

function initListOwl() {
    $('.owl-carousel.single-item').owlCarousel({
        items: 1,
        lazyLoad: true,
        loop: true,
        margin: 0,
        nav: true,
        dots: false,
        navText: ''
    });
}

function initDetailOwl() {
    if ($(window).outerWidth() < 769) {
        var nav = false;
        var dots = true;
    } else {
        var nav = true;
        var dots = false;
    }
    $('.owl-carousel.detail-product-slider').owlCarousel({
        items: 1,
        lazyLoad: true,
        loop: true,
        margin: 0,
        nav: nav,
        dots: dots,
        navText: false
    });
}

function destroyDetailOwl() {
    $('.owl-carousel.detail-product-slider').trigger('destroy.owl.carousel');
}

function initSearchOwl() {
    $('#header-search-carousel').owlCarousel({
        dots: true,
        loop: false,
        nav: false,
        pagination: false,
        /*slideBy: 'page',*/
        scrollPerPage: true,
        itemsTablet: [768, 4],
        itemsDesktopSmall: [979, 6],
        itemsDesktop: [1199, 6],
        navText: [
            '<<', '>>'
        ],
        margin: 0,
        items: 6,
        responsiveClass: true,
        responsive: {
            0: {
                items: 4
            },
            768: {
                items: 4
            },
            1000: {
                items: 6
            }
        }
    });

    carouselScrolling();

}

/* Прокрутка результатов поиска скроллом */
function carouselScrolling() {
    if (document.getElementById('header-search-carousel') !== null) {
        var elem = document.getElementById('header-search-carousel');

        if (elem.addEventListener) {
            if ('onwheel' in document) {
                // IE9+, FF17+
                elem.addEventListener("wheel", onWheel);
            } else if ('onmousewheel' in document) {
                // устаревший вариант события
                elem.addEventListener("mousewheel", onWheel);
            } else {
                // Firefox < 17
                elem.addEventListener("MozMousePixelScroll", onWheel);
            }
        } else { // IE8-
            elem.attachEvent("onmousewheel", onWheel);
        }
    }
}

var scrollFlagHead = false;

function scrollImages(data, carousel) {
    if (scrollFlagHead === true) {
        return false;
    }
    scrollFlagHead = true;

    if (data === 'next') {
        carousel.next();
        setTimeout(function () {
            scrollFlagHead = false;
        }, 100);
    }
    if (data === 'prev') {
        carousel.prev();
        setTimeout(function () {
            scrollFlagHead = false;
        }, 100);
    }

}

function onWheel(e) {
    e = e || window.event;

    e.stopPropagation();

    var searchCarousel = $('#header-search-carousel').data()['owl.carousel'];

    // deltaY, detail содержат пиксели
    // wheelDelta не дает возможность узнать количество пикселей
    // onwheel || MozMousePixelScroll || onmousewheel
    var delta = e.deltaY || e.detail || e.wheelDelta;

    if (delta > 0) {
        scrollImages('next', searchCarousel);
        console.log(delta);
    } else {
        scrollImages('prev', searchCarousel);
        console.log(delta);
    }

    e.preventDefault ? e.preventDefault() : (e.returnValue = false);
}

/*$(window).on('load', function (e) {
    // e.preventDefault();
    if( Cookies.get('delivery-popup') != 'showed' ) {
    $('#js-delivery-free .modal-body').load('/ajax/free-delivery.php', function () {
        var $popupId = '#js-delivery-free';
        var $popup = $($popupId);
        $popup.find('.modal-body').append($(this));

        setTimeout(function() {
            $.modalwindow({
                target: $popupId,
                width: '500',

            });
        }, 500);

        $popup.on('close.modal', function () {
            Cookies.set('delivery-popup', 'showed');
            $popup.find('.modal-body').html('');
            $popup.removeClass('is-loading');
        });
    });
    }*/

    // e.preventDefault();
    // var $popupFreeId = '#js-delivery-free';

    // $.modalwindow({
    //     target: $popupFreeId,
    //     width: '60%',
    //     position: 'center'
    // });

    // $('#js-delivery-free .close').click(function (e) {
    //     e.preventDefault();
    //     $('#js-delivery-free').addClass('hide');
    // });
//});

function initDetailCatalogTrigger() {
    //Модальное окно
    var $popupId = '#detail-item-popup';
    var $popup = $($popupId);

    var products = [];
    $('.element-list-preview-button').each(function () {
        products.push($(this).attr('data-item-id'));
    });
    var products_max_range = products.length - 1;



    //Для всех товаров на странице
    $.each($('[data-show-detail-catalog-item]'), function () {
        var $item = $(this);
        var item_id = $item.attr('data-item-id');
        var item_link = $item.attr('data-item-url');
        var indexOf = products.indexOf(item_id);
        var prev = indexOf - 1;
        var next = indexOf + 1;
        prev = prev >= 0 ? prev : products_max_range;
        next = next <= products_max_range ? next : 0;

        // для каждого товара
        $item.off().on('click', function () {
            if ('undefined' != $.type(item_id)) {
                $.ajax({
                    type: "POST",
                    async: true,
                    data: {
                        fromProductList: 'Y'
                    },
                    dataType: 'html',
                    url: item_link
                }).done(function (data) {
                    //$popup.find('.js-message-field').append(data);
                    $popup.find('.modal-body').append(data);
                    $popup.attr('data-item-id', item_id);
                    /*console.log(item_id);
                    console.log(products[prev]);
                    console.log(products[next]);
                    console.log(products);*/
                    $('#prev-product-item').attr('data-item-id', products[prev]);
                    $('#next-product-item').attr('data-item-id', products[next]);

                    //$popup.addClass('open');
                    $.modalwindow({
                        target: $popupId,
                        width: '900'
                    });

                    $popup.on('close.modal', function () {
                        $popup.find('.modal-body').html('');
                        $popup.removeClass('is-loading');
                    });
                    /*$popup.bPopup({
                            follow: ([false, false]),
                            onClose: function () {
                                $popup.find('.js-message-field').children().remove();
                                $popup.removeClass('is-loading');
                            }
                        },
                        function () {
                            //initOwl();
                        }
                    );*/
                })
            }

            return false;
        })
    });
}

function initDetailSkuTrigger() {
    var $color = $('.b-sku-panel-parent-item-name').parent();
    $color.off().on('click', function () {
        if (!$(this).hasClass('active')) {
            var activeColor = $('.b-sku-panel-parent-item-name', this).prop('title');
            $('.b-sku-active-color').html(activeColor);
            $color.removeClass('active').find('.b-sku-panel-item-input').prop('checked', false);
            $(this).addClass('active');
            $(this).find('.b-sku-panel-item').eq(0).find('.b-sku-panel-item-input').prop('checked', true).trigger('change');

	    $(".stock-table").hide();
	    $("#" + $(this).find('.b-sku-panel-item').eq(0).find('.b-sku-panel-item-input').attr('data-color') + "-stores").show();
        }
    });
}


function changePriceForDetailPage($element) {
    var dataDiscountPrice = $element.attr('data-discount-price');
    var dataPrice = $element.attr('data-price');

    $('.detail-element-list-price .price').html(dataPrice);
    if (dataDiscountPrice) {
        $('.detail-element-list-price .price.discount-price').remove();
        $('.detail-element-list-price .price:not(.js-price)').addClass('col-6');
        $('.detail-element-list-price .price').after(
            '<span class="price col col-6 discount-price js-discount-price">' + dataPrice + '</span>'
        );
        $('.detail-element-list-price .js-price').html(dataDiscountPrice).show();
        $('.discount-badge').show();
    } else {
        $('.detail-element-list-price .price').html(dataPrice).removeClass('col-6');
        $('.detail-element-list-price .price.discount-price').remove();
        $('.discount-badge').hide();
    }
}

// function zoomProductItem() {
//     var $productItem = $(".js-zoom-item");
//     var $fullPhotoContainer = $(".js-full-image");
//     var $clearPhotoContainer = $(".js-clear-photo-container");
//
//     /**
//      * Дублирует изображение в отдельный контейнер поверх других фотографий
//      */
//     $productItem.each(function () {
//         $(this).on("click", function () {
//             var $itemContent = $(this).html();
//
//             $productItem.hide();
//             $fullPhotoContainer.append($itemContent);
//             $fullPhotoContainer.addClass("product-gallery__full--opened");
//         })
//     });
//
//     /**
//      * Удаляет изображение из отдельного контейнера и показывает сетку других фотографий
//      */
//     $clearPhotoContainer.each(function () {
//         $(this).on("click", function () {
//             $productItem.show();
//             $fullPhotoContainer.find("img").remove();
//             $fullPhotoContainer.removeClass("product-gallery__full--opened");
//         })
//     })
// }

/**
 * Обновляем количество товаров в избранном
 */
function updateFavoriteLinkLikeText() {
    var favoriteElements = JSON.parse(localStorage.getItem('favoriteProds'));
    if (favoriteElements !== null && favoriteElements.length !== 0) {
        $('.js-link-like-quantity').show().text(favoriteElements.length);
        $('.js-link-like-quantity').parent('.js-link-like').parent('.list__item.hide-mobile').removeClass('empty');
    } else {
        $('.js-link-like-quantity').text('');
        $('.js-link-like-quantity').hide();
        $('.js-link-like-quantity').parent('.js-link-like').parent('.list__item.hide-mobile').addClass('empty');
    }
}

/**
 * Присвоение класса иконке избранного товара
 */
function updateFavoriteLikeProducts() {
    var favoriteElements = JSON.parse(localStorage.getItem('favoriteProds'));
    if (favoriteElements !== null) {
        for (var i = 0; i < favoriteElements.length; i++) {
            $('.js-element-favorite').each(function () {
                if ($(this).data('element-id') == favoriteElements[i]) {
                    $(this).find('.js-add-to-favorite').addClass('favorited');
                }
            })
        }
    }
}

/**
 * Обновление количества товаров в малой корзине
 */
function updateSmallBasketQuantity() {
    $('.js-link-cart-quantity').load('/ajax/get_small_basket_quantity.php', function () {
        var $buttonfastOrder = $('.js-load-form-order');
        if ($(this).text() !== '' && $(this).text() !== '0') {
            $(this).show();
            $(this).parent('.list__link.cart.js-small-cart-button').parent('.list__item').removeClass('empty');
            if ($buttonfastOrder.length > 0) {
                $buttonfastOrder.show();
            }
        } else {
            $(this).parent('.list__link.cart.js-small-cart-button').parent('.list__item').addClass('empty');
            if ($buttonfastOrder.length > 0) {
                $buttonfastOrder.hide();
                $buttonfastOrder.removeClass('avaliable');
            }
        }
    });
}

/**
 * Проверка количества товаров в корзине
 */
function chekSmallBasketQuantity() {
    var $buttonfastOrder = $('.js-link-cart-quantity');
    return $buttonfastOrder.text();
}
/**
 * Валидация смс кода
 * @param element
 */
function checkSmsCode(element) {
    var $smsCode = $(element);
    var newcode = $smsCode.val();
    var $smsError = $('#sms_error');
    if (newcode) {
        $.post(
            '/ajax/send_sms_code.php',
            {CODE: newcode},
            function (data) {
                var res = JSON.parse(data);
                if (res === 'Y') {
                    $smsCode.removeClass('error').addClass('success');
                    $smsError.val('N');
                    /*console.log('Код введен верно');*/
                } else {
                    $smsCode.removeClass('success').addClass('error');
                    $smsError.val('Y');
                    /*console.log('Код введен НЕ верно');*/
                }
            }
        );
    }
}


/**
 * Анимация сворачивания/разворачивания шапки сайта при скроле
 * @param direction
 */
function headerSkickAnimation(direction) {
    let check = $(document).find('.colab-youwanna').length;
    console.log(check);
    if(check*1 > 0){

    }else{

        var stickerBlock = $('#js-sticker');
        if (direction === 'down') {
            if (!stickerBlock.hasClass('small-block')) {
                stickerBlock.addClass('small-block');
                $('.header-search-result-container').removeClass('open').hide();
            }
        } else {
            if (stickerBlock.hasClass('small-block')) {
                stickerBlock.removeClass('small-block');
            }
        }
    }

}

function toggleShare() {
    $('.share-section .share-wrapper').toggle();
}


$(document).ready(function () {

    // Прилипание шапки при скроле
    $('#js-sticker').sticky({
        topSpacing: 0
    });
    var tempScrollTop = 0;
    var currentScrollTop = 0;
    var scrollFlag = false;
    $(window).scroll(function (e) {
        if (scrollFlag === true) {
            return false;
        }
        scrollFlag = true;
        setTimeout(function () {
            e.stopPropagation();
            currentScrollTop = $(window).scrollTop();
            if (tempScrollTop < (currentScrollTop - 30)) { //scrolling down
                headerSkickAnimation('down');
                tempScrollTop = currentScrollTop;
            } else if ((tempScrollTop > (currentScrollTop + 120)) || (currentScrollTop < 10)) { //scrolling up
                headerSkickAnimation('up');
                tempScrollTop = currentScrollTop;
            }
            scrollFlag = false;
        }, 200);
    });


    //каталог маленькие экраны
    $('.open-catalog').click(function () {
        $('.mobile').toggle();
    });


    $('.js-small-cart-button').click(function (e) {
        e.preventDefault();

        /* Отмена открытия корзины, если в ней нет товаров */

        if ($(this).children('span.js-link-cart-quantity').text() === '0') {

        } else {
            $('#js-popup-cart-block .js-content-basket').load('/ajax/get_basket.php?lang=' +"ru", function () {
                $('#js-popup-cart-block').addClass('open');
                updateUlSkuListText();
            });
        }
    });
    $('#js-popup-cart-block .close').click(function (e) {
        e.preventDefault();
        $('#js-popup-cart-block').removeClass('open');
    });
    $(document).click(function (event) {
        if ($(event.target).closest(".popup-cart-block").length)
            return;
        $('#js-popup-cart-block').removeClass('open');
        event.stopPropagation();
    });

    /* Скрипт для всплывашки заказа обратного звонка */
    $('.callback-modal-button').click(function (e) {
        e.preventDefault();
        var callbackRequestURL = '/ajax/callback_form.php?lang=' +"ru";
        $('#callback-modal .callback-modal-body').load(callbackRequestURL, function () {
            $('#callback-modal').addClass('open');
        });
    });
    /*$('#callback-modal .close').click(function (e) {
        e.preventDefault();
        $('#callback-modal').removeClass('open');
    });
    $(document).click(function (event) {
        if ($(event.target).closest(".callback-modal-wind").length)
            return;
        $('#callback-modal').removeClass('open');
        event.stopPropagation();
        console.log('close');
    });*/

    /* Скрипт для всплывашки авторизации */
    $('.list__link.profile').click(function (e) {
        if (!$(this).data('user-auth')) {
            e.preventDefault();
            var callbackRequestURL = '/personal/auth1.php?lang=' +"ru";
            $('#callback-modal .callback-modal-body').load(callbackRequestURL, function () {
                $('#callback-modal').addClass('open');
                $('.register-link-button').click(function (e) {
                    e.preventDefault();
                    var callbackRequestURL = '/personal/register.php?lang=' +"ru";
                    $('#callback-modal .callback-modal-body').load(callbackRequestURL, function () {
                        /*$('#callback-modal').addClass('open');*/
                    });
                });
            });
        }
        ;
    });

    // Загрузка всплывающего окна с видео
    $('body').on('click', '.js-video-modal', function (e) {
        e.preventDefault();

        /*console.log('click');*/

        $('#video-popup .modal-body').load('/ajax/video_modal.php?img=' + $(this).data('img') + '&src=' + $(this).data('src') + '&LANGUAGE_ID=' +"ru", function () {
            var $popupId = '#video-popup';
            var $popup = $($popupId);
            $popup.find('.modal-body').append($(this));

            $.modalwindow({
                target: $popupId,
                width: '60%',
                position: 'center'
            });

            $popup.on('close.modal', function () {
                $popup.find('.modal-body').html('');
                $popup.removeClass('is-loading');
            });
        });
        //return false;
    });

    /*$('#detail-item-popup .close').click(function (e) {
        e.preventDefault();
        $('#detail-item-popup').removeClass('open');
        $('#detail-item-popup').find('.modal-body').html('');
        $('#detail-item-popup').removeClass('is-loading');
    });
    $(document).click(function(event){
        if($(event.target).closest(".detail-item-inner").length)
            return;
        $('#detail-item-popup').removeClass('open');
        $('#detail-item-popup').find('.modal-body').html('');
        $('#detail-item-popup').removeClass('is-loading');
        event.stopPropagation();
    });*/

    /* Скрипт для всплывашки отклика на вакансию */
    $('.vacancy-respond-button').click(function (e) {
        e.preventDefault();
        var requestURL = '/ajax/respond_vacancy_form.php?lang=' +"ru"
            + '&id=' + $(this).data('id')
            + '&vacancy_name=' + $(this).data('vacancy_name');
        $('#callback-modal .callback-modal-body').load(requestURL, function () {
            $('#callback-modal').addClass('open');
        });
    });
    $('#callback-modal .close').click(function (e) {
        e.preventDefault();
        $('#callback-modal').removeClass('open');
    });
    $(document).click(function (event) {
        if ($(event.target).closest(".callback-modal-wind").length || $(event.target).closest(".noclickable").length)
            return;
        $('#callback-modal').removeClass('open');
        event.stopPropagation();
    });

    /* Отмена перехода по ссылке, если нет избранных товаров */
    $('.list__link.like.js-link-like').click(function (event) {
        if ($(this).children('span.js-link-like-quantity').text() === '') {
            event.preventDefault();
        }
    });

    $(document).delegate('#prev-product-item, #next-product-item', 'click', function () {
        var $popup = $('#detail-item-popup'),
            showing_id = $popup.attr('data-item-id'),
            click_id = $(this).attr('data-item-id'),
            format_id = '#quick-view-' + click_id,
            $item = $(format_id);


        if ($item.length) {
            var item_link = $item.attr('data-item-url');
            $popup.addClass('is-loading');

            $('#prev-product-item, #next-product-item').show();

            var products = [];
            $('.element-list-preview-button').each(function () {
                products.push($(this).attr('data-item-id'));
            });
            var products_max_range = products.length - 1;

            var indexOf = products.indexOf(click_id);
            var prev = indexOf - 1;
            var next = indexOf + 1;
            prev = prev >= 0 ? prev : products_max_range;
            next = next <= products_max_range ? next : 0;

            $.ajax({
                type: "POST",
                async: true,
                data: {
                    fromProductList: 'Y'
                },
                dataType: 'html',
                url: item_link
            }).done(function (data) {
                //$popup.find('.js-message-field').children().remove();
                //$popup.find('.js-message-field').append(data);
                $popup.find('.modal-body').html('').append(data);
                $popup.attr('data-item-id', click_id);

                $('#prev-product-item').attr('data-item-id', products[prev]);
                $('#next-product-item').attr('data-item-id', products[next]);

                //initOwl();

                $popup.removeClass('is-loading');

            })
        }

    });

    // AJAX -> add2favorite
    $(document).on('click', '.js-add-to-favorite', function () {
        if ($(this).hasClass('favorited')) {
            $(this).removeClass('favorited');
        } else {
            $(this).addClass('favorited');
        }
        var $linkObj = $(this);
        var id = parseInt($linkObj.parents('.js-element-favorite').data('element-id'));
        //var url = $linkObj.parents('.element-inner').data('detail')+'?AJAX_CALL=Y&action=add2favorite';

        var obj = JSON.parse(localStorage.getItem('favoriteProds'));

        if (obj !== null) {
            if (obj.indexOf(id, 0) === -1) {
                obj.push(id);
            } else {
                obj.splice(obj.indexOf(id, 0), 1);
            }
        } else {
            obj = [id];
        }

        var serialObj = JSON.stringify(obj);

        localStorage.setItem('favoriteProds', serialObj);

        updateFavoriteLinkLikeText();

        /*console.log(obj);
        $.ajax({
            type: "POST",
            url: url,
            dataType: '_default',
            data: 'element_id='+id,
            success: function(msg){

            }
        });*/
        return false;
    });

    updateFavoriteLinkLikeText();
    updateSmallBasketQuantity();

    /* действия при открытии-закрытии меню*/
    $('#top-catalog-menu').on('open.toggleme', function () {
        if ($('.copyright').hasClass('white')) {
            $('.copyright').removeClass('white').addClass('black');
        }
        $('.copyright .center').addClass('hidden');
        $('.menu-toggle').addClass('open');
        if (outerWidth < 769) {
            $('.top__logo').addClass('hide-mobile');
            $('.top__menu .list .list__item').removeClass('hide-mobile');
            $('.language-change-block.hidden').stop().toggle();
            $('body').addClass('mobile-menu-open');
        }
    });

    $('#top-catalog-menu').on('close.toggleme', function () {
        if ($('.copyright').hasClass('black')) {
            $('.copyright').removeClass('black').addClass('white');
        }
        $('.copyright .center').removeClass('hidden');
        $('.menu-toggle').removeClass('open');
        if (outerWidth < 769) {
            $('.top__logo').removeClass('hide-mobile');
            $('.language-change-block.hidden').stop().toggle();
            $('.top__menu .list .list__item:not(:nth-child(3))').addClass('hide-mobile');
            $('body').removeClass('mobile-menu-open');
        }
    });

    /**
     * Скрипт для подгрузки по ajax результатов поиска
     */
    $('#js-header-search-form').submit(function (e) {
        e.preventDefault();

        var link = $(this).attr('action');
        var q = $(this).find('input').val();

        if (q != 'undefined') {
            $('#js-header-search-result').slideUp('slow', function () {
                $('#js-header-search-result').html('');
                $.get(
                    link,
                    {
                        q: q,
                        ajax_search: 'Y',
                        lang: "ru"
                    },
                    function (data) {
                        $('#js-header-search-result').html(data).slideDown('slow');
                        initSearchOwl();
                    }
                );
            });
        }

        return false;
    });

    /**
     * Скрипт для подгрузки по ajax результатов поиска с задержкой во время ввода
     */
    var delaySearchKeyup = (function () {
        var timer = 0;
        return function (callback, ms) {
            clearTimeout(timer);
            timer = setTimeout(callback, ms);
        };
    })();
    $('#js-header-search-form input').keyup(function () {
        delaySearchKeyup(function () {
            $('#js-header-search-form').submit();
        }, 1000);
    });

    /**
     * Скрипт для показа поиска в шапке сайта
     */
    if (window.innerWidth > 768) {
        $('.js-header-title-search-open').click(function () {
            if (!$('.header-search-result-container').hasClass('open')) {
                $('.header-search-result-container').addClass('open').slideDown().find('input').focus();
                initSearchOwl();
            }
            return false;
        });
    }

    /*$('#ORDER_FORM').on('submit', function () {
        var $element = $(this).find('#PHONE').val().replace(/[^0-9]/gim,'').substring(1);
        var $email = $(this).find('#EMAIL');
        if($email.val() === "" && $('#PHONE').val() !== "") {
            $email.val($element + '@you-wanna.ru')
        }
    });

    $('#js-fast-ordering-form').on('opened.modal', function(){$('#ordering-form').on('submit', function () {
        var $element = $(this).find('input[name=PHONE]').val().replace(/[^0-9]/gim,'').substring(1);
        console.log($element);
        var $email = $(this).find('input[name=EMAIL]');
        $email.val($element + '@you-wanna.ru')
    });})*/

});
/**
 * Скрипт для скрытия поиска в шапке сайта
 */
$(document).click(function (event) {
    if ($(event.target).closest("#js-header-title-search, #js-header-search-result").length)
        return;
    $('.header-search-result-container').removeClass('open').slideUp();
    //$('#js-header-title-search').fadeOut();
    //$('#js-header-search-result').hide();
    event.stopPropagation();
});


/**
 * Работа с верификацией телефона при оформлении заказа
 */
$(function () {

    $('body').on('click', '.js-send-sms-code', function (e) {
        e.preventDefault();
        var $form = $(this).parents('.js-check-sms-code-form');
        var $phone = $('input#PHONE', $form);
        var $smsCode = $('input#SMS_CODE', $form);
        /*console.log($phone.val());*/
        $phone.removeClass('error');
        $smsCode.removeClass('error').removeClass('success');
        if ($phone.val()) {
            $.post(
                '/ajax/send_sms_code.php',
                {PHONE: $phone.val()},
                function (data) {
                    var res = JSON.parse(data);
                    console.log(res);
                    $smsCode.val('').show().focus();
                    $('.js-send-sms-code-text', $form).show();
                    if (res === 'Y') {
                        $('input#SMS_CODE', $form).attr('focus', true);
                        $('input#confirm_phone').val('1');
                    } else {
                        $('input#SMS_CODE', $form).removeClass('success').addClass('error');
                        $('input#confirm_phone').val('');
                    }
                }
            );
        } else {
            $phone.addClass('error');
        }

        return false;
    });

    $('body').on('keyup', 'input#SMS_CODE', function () {
        var $element = $(this);
        setTimeout(function () {
            checkSmsCode($element);
        }, 500);
    });
    /*
     * Проверка верификации номера телефона при отправке формы регистрации
     */
    $('form.js-check-sms-code-form').on('submit', function (e) {
        e.preventDefault();
        if (!$('input#SMS_CODE').hasClass('success')) {
            /*console.log('Не введен код подтверждения номера телефона');*/
            $('.form-error-text').css('display', 'block');
            return false;
        }
        return true;
    });

    $('body').on('click', '#send_account_info', function (e) {
        e.preventDefault();
        if (!$(this).hasClass('disabled-link')) {
            if($('input#USER_LOGIN').val().trim() === ''){
                $('#USER_LOGIN').addClass('error');
            } else {
                $('#send_account_info').addClass('disabled-link');
                $.post(
                    '/ajax/forgot_pass.php',
                    {USER_EMAIL: $('input#USER_LOGIN').val()},
                    function (data) {
                        var res = JSON.parse(data);
                        console.log(res);
                        $('.form-item.forgot_pass_btn').append(res.MSG);
                        if(res.STATUS === 1 ){
                            localStorage.setItem('send_pass_success', $.now());
                            $('#send_account_info').addClass('send-success');
                            $('#countdown').removeClass('invisible');
                            $('#countdown').text('60');
                            countDown();
                        }
                    }
                );
            }

            return false;
        }
    });
});
function countDown() {
    var sec = $('#countdown');
    var secVal = parseInt(sec.text());

    var timer = setTimeout(function tick() {
        if (secVal > 0) {
            sec.text(--secVal);
            timer = setTimeout(tick, 1000);
        } else {
            $('#send_account_info').removeClass('disabled-link');
            $('#send_account_info').removeClass('send-success');
            $('#countdown').addClass('invisible');
        }
    }, 1000);
}
/**
 * Валидация смс кода при регистрации
 * @param element
 */
function checkSmsCodeReg(element) {
    var $smsCode = $(element);
    var newcode = $smsCode.val();
    var $smsError = $('#sms_error');
    if (newcode) {
        $.post(
            '/ajax/send_sms_code.php',
            {CODE: newcode},
            function (data) {
                var res = JSON.parse(data);
                if (res === 'Y') {
                    $smsCode.removeClass('error').addClass('success');
                    $smsError.val('N');
                    $('input#confirm_phone').val('1');
                    /*console.log('Код введен верно');*/
                } else {
                    $smsCode.removeClass('success').addClass('error');
                    $smsError.val('Y');
                    $('input#confirm_phone').val('');
                    /*console.log('Код введен НЕ верно');*/
                }
            }
        );
    }
}

/* Галереи для главной и страниц трендов */

function initTrendSlide() {
    if($('.js-trendslide:not(.slick-initialized)').length > 0) {

        $('.js-trendslide:not(.slick-initialized)').slick({
          infinite: true,
          slidesToShow: 2,
          slidesToScroll: 1,
          arrows: false,
          dots: true,
          responsive: [
            {
              breakpoint: 767,
              settings: {
                slidesToShow: 1
              }
            }
          ]
        });
    }
}

$(document).on('initTS', function(){
    setTimeout(initTrendSlide, 1000);
})

$(function() {
    if($('.yw-trendslider__list').length > 0) {
        $('.yw-trendslider__list').slick({
          infinite: true,
          slidesToShow: 2,
          slidesToScroll: 1,
          arrows: true,
          responsive: [
            {
              breakpoint: 767,
              settings: {
                slidesToShow: 1
              }
            }
          ]
        });
    }

    if($('.yw-trendinsta .instashop-page-wrapper').length > 0) {
        $('.yw-trendinsta .instashop-page-wrapper').slick({
          infinite: true,
          slidesToShow: 3,
          slidesToScroll: 1,
          arrows: false,
          dots: true,
          responsive: [
            {
              breakpoint: 991,
              settings: {
                slidesToShow: 2
              }
            },
            {
              breakpoint: 767,
              settings: {
                slidesToShow: 1
              }
            }
          ]
        });
    }

    initTrendSlide();
});

/**
 * Работа с верификацией телефона при регистрации
 */
$(function () {

    $('body').on('click', '.js-send-sms-code', function (e) {
        e.preventDefault();
        var $form = $(this).parents('.js-check-sms-code-form');
        var $phone = $('input#REG_PHONE', $form);
        var $smsCode = $('input#REG_SMS_CODE', $form);
        /*console.log($phone.val());*/
        $phone.removeClass('error');
        $smsCode.removeClass('error').removeClass('success');
        if ($phone.val()) {
            $.post(
                '/ajax/send_sms_code.php',
                {PHONE: $phone.val()},
                function (data) {
                    var res = JSON.parse(data);
                    /*console.log(res);*/
                    $smsCode.val('').show().focus();
                    $('.js-send-sms-code-text', $form).show();
                    /*if (res === 'Y') {
                        $('input#SMS_CODE', $form).attr('focus', true);
                    } else {
                        $('input#SMS_CODE', $form).removeClass('success').addClass('error');
                    }*/
                }
            );
        } else {
            $phone.addClass('error');
        }

        return false;
    });

    $('body').on('keyup', 'input#REG_SMS_CODE', function () {
        var $element = $(this);
        setTimeout(function () {
            checkSmsCodeReg($element);
        }, 500);
    });


    // Загрузка формы "Быстрый заказ"
    $('body').on('click', '.js-load-form-order', function (e) {



        e.preventDefault();


        var currentURL = $('.cuRpage').val();
        console.log(currentURL);
        var product = $('a.add-cart').attr('data-product-id');
        console.log(product);
        $.ajax({
            url: currentURL+'?action=BUY&ajax_basket=Y&id='+product,
            //Функция находится в шаблоне gulliver.wear.sale.basket.small
            // компонента sale.basket.basket.small ?>
            success: function () {
                $('#js-fast-ordering-form .modal-body').load('/ajax/fast_ordering_form.php?LANGUAGE_ID=' +"ru", function () {
                    /*console.log($(this));*/
                    var $popupId = '#js-fast-ordering-form';
                    var $popup = $($popupId);
                    $popup.find('.modal-body').append($(this));

                    $.modalwindow({
                        target: $popupId,
                        width: '400'
                    });

                    $popup.on('close.modal', function () {
                        $popup.find('.modal-body').html('');
                        $popup.removeClass('is-loading');
                    });
                });

            }
        });

        /*console.log('click');*/


        //return false;
    });

    // Обновление результатов формы "Быстрый заказ"
    $('body').on('submit', '.js-ordering-form', function (e) {


        e.preventDefault();
        var $form = $(this);
        var $action = $form.attr('action');
        $('.js-form-ajax-error').html('').hide();
        $('.js-form-ajax-msg').html('').hide();
        $form.find("input, textarea, input[type='submut']").removeClass('error');//.removeClass('success');
        $.post(
            $action,
            $form.serialize(),
            function (data) {

                // console.log(data);

                let indexof = data.indexOf("{", data);
                let replaceObject = data.substr(0, indexof);
                // console.log(indexof);
                data = data.replace(replaceObject, "");


                var res = JSON.parse(data);
                // console.log("ZZZZZZZZZZZZZZZZZZZ");
                // console.log(res);
                if (res.MSG) {
                    $form.find('.js-form-ajax-msg').html(res.MSG).show();
                } else if (res.MSG_ERROR) {
                    $form.find('.js-form-ajax-error').html(res.MSG_ERROR).show();
                }




                if (res.STATUS === 1) {
                    updateSmallBasketQuantity();
                    setTimeout(function () {
                        $('#js-fast-ordering-form .close').trigger('click');
                    }, 2000);
                } else {
                    for (var key in res.ERRORS) {
                        $form.find('input[name="' + key + '"]').addClass('error');
                        if (key !== 'FIO' && key !== 'PHONE') {
                            $form.find('.js-form-ajax-error').append('<p class="control-label">' + res.ERRORS[key] + '</p>').show();
                        }
                    }
                }
            }
        );
        return false;
    });

    // Кнопка наверх
	$(window).scroll(function(){
		if ($(this).scrollTop() > 300) {
			$('.js-scroll-top').fadeIn();
		} else {
			$('.js-scroll-top').fadeOut();
		}
	});

	// Событие по нажатию кнопки наверх
	$('.js-scroll-top').click(function() {
		$('html, body').animate({scrollTop : 0},500);
		return false;
	});

	// Аккордион атрибутов товара (состав, уход и пр.)
	var allPanels = $('.detail-product-params > .detail-product-params__content');

	$('.detail-product-params > .detail-product-params__title a').click(function() {
		allPanels.slideUp();
		$(this).parent().next().slideDown();
		return false;
	});

	var productSliderOptions = {
        slidesToShow: 4,
        slidesToScroll: 4,
        arrows: false,
        dots: false,
        responsive: [
            {
                breakpoint: 1200,
                settings: {
                    slidesToShow: 3,
                    slidesToScroll: 3
                }
            },
            {
                breakpoint: 992,
                settings: {
                    slidesToShow: 2,
                    slidesToScroll: 2
                }
            },
            {
                breakpoint: 576,
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1
                }
            }
        ]
    }

    $('.js-product-slider').slick(productSliderOptions);

    $('.js-slick-refresh').on('click', function () {
        $('.js-product-slider').slick('refresh');
    })

});



/**
 * Зум изображений в детальной карточке товара
 */
function zoomDetailImage() {
    $('.js-zoom-item').each(function () {
        if (outerWidth > 992) {
            var url = $(this).attr('src');

            $(this).zoom({
                url: url
            });
        } else {

        }
    });
}

function productGallerySwiper() {
    var productSwiper = new Swiper ('.js-product-gallery-slider .swiper-container', {
        direction: 'horizontal',
        loop: true,
        lazy: true,

        observer: true,
        observeParents: true,

        pagination: {
            el: '.js-product-gallery-slider .swiper-pagination',
        }
    })
}

// $(document).on('keydown keyup', '.form-group input.form-control', function(e){
//
//     let promoVal = $('.form-group input.form-control').val();
//     if (promoVal == "YOU15") {
//         $('.form-group input.form-control').val("");
//     }
//     console.log(promoVal);
// });

$(document).ready(function (){
    $('#slider-colab').slick({
        infinite: true,
        slidesToShow: 3,
        slidesToScroll: 1,
        arrows: true,
        dots: false,
        responsive: [
          {
            breakpoint: 767,
            settings: {
              slidesToShow: 1,
              slidesToScroll: 1,
            }
          }
        ]
    });
});
