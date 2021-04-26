$(document).ready(function () {

    //отправленнные
    var list_page = $('.iqsms_sender_list_table_box');
    if (list_page.length) {
        // спсиок сообщений
        var descr_error = $('.iqsms-sender_error_description_box');
        list_page.on("click", '.sms_comment_box > span', function () {

            descr_error.find('.descr').html('<pre>' + $(this).parent().find('.more').html() + '</pre>');
            descr_error.css({
                width: list_page.width() - 30
            }).fadeIn(500);

            //прокрутка вверх
            $('html, body').animate({
                scrollTop: descr_error.offset().top - 30
            }, 1000);
        });

        //ошибка
        descr_error.find('.close').on("click", function () {
            descr_error.fadeOut(500);
        });
    }


    // edit sms template
    $('form[name="iqsms_sender_template_edit_form"]').each(function () {
        var box = $(this);
        var templateFieldBox = box.find(".template_fields_box");
        var typeBox = box.find(".type_box");
        var event = box.find('input[name="EVENT"]');
        var eventTypeBox = box.find(".event_type_box");
        var eventTypeSelect = box.find("select[name='EVENT_TYPE']");

        box.on("change", 'input[name="EVENT"]', function () {
            var eventTypeOn = $(this);
            if (eventTypeOn.is(":checked")) {
                typeBox.addClass('hide');
                eventTypeBox.addClass('active');
                eventTypeSelect.change();
            }
            else {
                typeBox.removeClass('hide');
                eventTypeBox.removeClass('active');
                templateFieldBox.empty();
            }
        });

        eventTypeSelect.on("change", function () {
            var sel = $(this);

            templateFieldBox.empty();

            if (!!IQsmsSenderEventType[sel.val()]) {
                templateFieldBox.html(IQsmsSenderEventType[sel.val()].replace(/(#[\w\d]+#)+/mg, "<span>\$1</span>"));
                templateFieldBox.find('span').each(function (i) {
                    if (i) {
                        $('<br>').insertBefore($(this));
                    }
                });

            }

        });

        templateFieldBox.on("click", function (e) {
            var el = $(e.target);
            if (el.is('span')) {
                box.find('textarea[name="TEXT"]').val(box.find('textarea[name="TEXT"]').val() + ' ' + el.text() + ' ');
            }
        });

        if(event.is(":checked"))
        {
            eventTypeSelect.change();
        }

    });


    //отправка
    $('.ap-iqsms-sender-send-box').each(function () {

        var box = $(this);
        var textarea = box.find('textarea[name="text"]');
        var btnTranslit = box.find('[name="translit"]');
        var btnScheduleOn = box.find('[name="schedule_on"]');
        var textarea_box = box.find('.textarea_box');
        var schedule_box = box.find('.schadule_box');
        var template_type = box.find('select[name="template"]');
        var fileds_box = box.find('.fileds_box');
        var info_box = box.find('.info_box');
        var msgBox = $('.iqsms__sender__msg .msg_box');

        //сообщение
        function showMsg(msg, error) {
            var error = error || false;
            msgBox.removeClass('error success extend').empty().fadeOut(300);

            if (msg == undefined) return;

            if (error) msgBox.addClass('error').html(msg);
            else msgBox.addClass("success").html(msg);
        }

        //отправка смс
        box.find('.btn_send').on("click", function () {
            var btn = $(this);
            if (btn.hasClass('preloader')) return;
            btn.addClass('preloader');

            $.ajax({
                type: 'POST',
                dataType: 'json',
                data: {
                    sessid: BX.bitrix_sessid(),
                    method: 'send_sms',
                    phone: box.find('[name="phone"]').val(),
                    text: getRealText(),
                    sender: box.find('[name="sender"]').val(),
                    schedule: box.find('[name="schedule_date"]').val(),
                    schedule_on: box.find('[name="schedule_on"]:checked').length
                },
                error: function (r) {
                    showMsg('Error connection!', true);
                    btn.removeClass('preloader');
                },
                success: function (r) {
                    if (!!r.response) {

                        msgBox.removeClass('error success extend').empty().fadeOut(300);
                        msgBox.addClass("extend").html('<div class="item_success" >' + r.response.msg + '</div>');

                        if (!!r.response.errors) {
                            for (var ie in r.response.errors) {
                                msgBox.append('<div class="item_error" >' + ie + ': ' + r.response.errors[ie] + '</div>');
                            }
                        }

                    }
                    else if (!!r.error) {
                        showMsg(r.error.msg, true);
                    }
                    btn.removeClass('preloader');

                }
            })
        });

        // очистка поля с текстом
        box.find('.btn_clean').on("click", function () {
            textarea.val('');
            template_type.val('');
            changeText();
        });

        //подсчет введенных символов
        textarea.on("keyup", changeText);

        // вывод полей из шаблона
        template_type.on("change", changeTemplateType);

        //транслит
        btnTranslit.on("change", changeText);
        //планировщик
        btnScheduleOn.on("change", function () {
            if (btnScheduleOn.is(':checked')) {
                schedule_box.show();
            }
            else {
                schedule_box.hide();
            }
        });

        //заполнение полей
        fileds_box.on("keyup", 'input', changeText);


        // подготовка полей для ввода
        function changeTemplateType() {
            var template_types = BX.message('iqsms_sender_template_type');

            if (template_type.val() != '0') {
                if (!!template_types[template_type.val()]) {
                    var type = template_types[template_type.val()];

                    textarea.val(type.TEXT);//.prop('disabled', true);
                }
            }
            changeText();
        }

        // текст с автозаменой
        function getRealText() {
            var text = textarea.val();

            //если шаблон используется
            if (template_type.val() != '0') {
                fileds_box.find('input').each(function () {
                    var inp = $(this);
                    text = text.replace(new RegExp(inp.attr('name'), 'g'), inp.val().trim());
                });
            }

            //транслит
            if (btnTranslit.is(":checked")) {

                var trasObj = BX.message('iqsms_sender_translit');
                var newText = '';
                var char = '';
                for (var i = 0; i < text.length; i++) {

                    // Если символ найден в массиве то меняем его
                    if (trasObj[text[i].toLowerCase()] != undefined) {
                        char = trasObj[text[i].toLowerCase()];
                        if (text[i] != text[i].toLowerCase()) {
                            char = char.charAt(0).toUpperCase() + char.substr(1)
                        }
                        newText += char;
                    }
                    // Если нет, то оставляем так как есть
                    else {
                        newText += text[i];
                    }
                }
                text = newText;
            }

            text = text.replace(/^\s+/, '').replace(/\s+$/, '');

            return text;
        }

        function countSmsSize(text) {
            var l = 0;
            info_box.find('.text_size .count').text(text.length);

            if (btnTranslit.is(":checked")) {
                l = Math.floor(text.length / 160) + (text.length % 160 > 0 ? 1 : 0);
                info_box.find('.text_size .limit').text(160);
            }
            else {
                l = Math.floor(text.length / 70) + (text.length % 70 > 0 ? 1 : 0);
                info_box.find('.text_size .limit').text(70);
            }
            info_box.find('.text_size .sms').text(l > 0 ? l : 1);
        }

        function changeText() {
            var text = getRealText();
            countSmsSize(text);
        }

    });

    // filter
    $('.filter__box').each(function () {

        var box = $(this);
        var textarea = $('.ap-iqsms-sender-send-box textarea[name="phone"]');

        box.on("click", '.filter__box-row-view', function(){
            if(box.hasClass('active'))
            {
                box.removeClass('active');
            }
            else
            {
                box.addClass('active');
            }
        });

        //отправка смс
        box.on("click" , '.filter__box-btn', function () {
            var btn = $(this);

            if (btn.hasClass('preloader')) return;
            btn.addClass('preloader');

            $.ajax({
                type: 'POST',
                dataType: 'json',
                data: {
                    sessid: BX.bitrix_sessid(),
                    method: 'filter_phone',
                    order_payed: box.find('[name="ORDER_PAYED"]').val(),
                    order_canceled: box.find('[name="ORDER_CANCELED"]').val(),
                    date_from: box.find('[name="DATE_FROM"]').val(),
                    date_to: box.find('[name="DATE_TO"]').val()
                },
                error: function (r) {
                    btn.removeClass('preloader');
                },
                success: function (r) {
                    if (!!r.response) {
                        textarea.val(r.response.phones);
                    }
                    else if (!!r.error) {
                        textarea.val(r.error.msg);
                    }

                    btn.removeClass('preloader');
                }
            })
        });
    });


});
