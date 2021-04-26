<?php
/**
 * Страница регистрации
 *
 * Created by PhpStorm.
 *
 * Date: 03.10.2016
 * Time: 20:14
 *
 * @var CMain $APPLICATION
 */

if (isset($_REQUEST['lang'])) {
    define('LANGUAGE_ID', $_REQUEST['lang']);
}

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php'; ?>
<div id="callback-modal-wrapper">
<?$APPLICATION->IncludeComponent(
	"ctweb:sms.register",
	"",
	Array(
		"REQUIRE_FIELDS" => array()
	)
);?>
</div>
<style>
@font-face {
    font-family: Gilroy;
	src: url("/local/templates/you-wanna_v3/fonts/Gilroy/Gilroy-Bold.eot");
    src: local('Gilroy-Bold'),url("/local/templates/you-wanna_v3/fonts/Gilroy/Gilroy-Bold.eot?#iefix") format('embedded-opentype'),url("/local/templates/you-wanna_v3/fonts/Gilroy/Gilroy-Bold.woff2") format('woff2'),url("/local/templates/you-wanna_v3/fonts/Gilroy/Gilroy-Bold.woff") format('woff');
    font-weight: 600;
    font-style: normal
}

@font-face {
    font-family: Gilroy;
    src: url(fonts/Gilroy/Gilroy-Regular.eot);
    src: local('Gilroy-Regular'),url("/local/templates/you-wanna_v3/fonts/Gilroy/Gilroy-Regular.eot?#iefix") format('embedded-opentype'),url("/local/templates/you-wanna_v3/fonts/Gilroy/Gilroy-Regular.woff2") format('woff2'),url("/local/templates/you-wanna_v3/fonts/Gilroy/Gilroy-Regular.woff") format('woff');
    font-weight: 400;
    font-style: normal
}

@font-face {
    font-family: Gilroy;
    src: url(fonts/Gilroy/Gilroy-Medium.eot);
    src: local('Gilroy-Medium'),url("/local/templates/you-wanna_v3/fonts/Gilroy/Gilroy-Medium.eot?#iefix") format('embedded-opentype'),url("/local/templates/you-wanna_v3/fonts/Gilroy/Gilroy-Medium.woff2") format('woff2'),url("/local/templates/you-wanna_v3/fonts/Gilroy/Gilroy-Medium.woff") format('woff');
    font-weight: 500;
    font-style: normal
}
            .ctweb-smsauth-form, input{
                font-family: Gilroy;
                font-style: normal;
                font-weight: normal;
                letter-spacing: 0.065em;
            }
            input[type="text"]{
                border: 1px solid #bfbfbf;
                box-sizing: border-box;
                padding: 23px 15px;
                font-size: 12px;
                line-height: 14px;
                height: 60px;
                text-transform: uppercase;
                color: rgba(0, 0, 0, 0.5);
                width: 100%;
                outline: none;
            }
            input[type="text"]:focus{
                border: 1px solid #000;
            }
            input[type="submit"],
            input[type="button"]{
                font-weight: 300;
                font-size: 18px;
                text-transform: uppercase;
                color: #FFFFFF;
                background: #000;
                width: 100%;
                line-height: 60px;
                border: 0;
				height: 60px;
                padding: 0 15px;
                letter-spacing: normal;
            }
            label{
                font-size: 14px;
                line-height: 16px;
                text-transform: uppercase;
                color: #000000;
                margin-bottom: 15px;
                display: block;
            }
            h3{
                font-size: 22px;
                line-height: 26px;
                text-transform: uppercase;
                color: #000000;
                margin-bottom: 50px;
                font-weight: normal;
            }
            .form-group{
                margin-bottom: 30px;
            }
input[name="CODE"]{
                border: none;
                background-image: url("data:image/svg+xml;utf8,<svg width='100%' height='100%' xmlns='http://www.w3.org/2000/svg'><line x1='0' y1='100%' x2='100%' y2='100%' style='fill: none; stroke: black; stroke-width: 2; stroke-dasharray: 65 38' /></svg>");
                width: 460px;
                font-size: 30px;
                letter-spacing: 88px;
                padding-left: 25px;
                overflow: hidden;
            }
            input[name="CODE"]:focus{
                border: none;
            }
            .code_inner{
                left: 0;
                position: sticky;
            }
            .code_outer{
                width: 374px;
                overflow: hidden
            }
        </style>

<!-- Этот скрипт bundle.min.js не переноси в продакшн -->
<script src="/local/templates/you-wanna_v3/js/bundle.min.js"></script>

<script>
$('.ctweb-smsauth-form form').submit(function (e) {
    e.preventDefault();

    var form = $(this);
    form.find('input[type=submit]').attr('disabled', 'disabled');

    var formData = new FormData();

    var form_data = form.serializeArray();
    $.each(form_data, function(key, input) {
        formData.append(input.name, input.value);
    });

    $.ajax({
        url: form.attr('action'),
        method: form.attr('method'),
        data: formData,
        dataType: "html",
        cache: false,
        contentType: false,
        processData: false,
    }).done(function(response) {
        form.closest('.ctweb-smsauth-form').parent().parent().html(response)
        form.find('input[type=submit]').removeAttr('disabled');
    });
});
</script>