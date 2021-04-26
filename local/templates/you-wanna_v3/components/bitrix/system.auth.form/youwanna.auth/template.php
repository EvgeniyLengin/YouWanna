<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

CJSCore::Init();
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';
?>

<div class="bx-system-auth-form">

    <?
    if ($arResult['SHOW_ERRORS'] == 'Y' && $arResult['ERROR']) {
        ShowMessage($arResult['ERROR_MESSAGE']);
    }
    ?>

    <? if ($arResult["FORM_TYPE"] == "login"): ?>
    <? if ($_GET["test"] == 1): ?>
	<br />
	<br />
	<br />
	<br />
	<br />
<div id="callback-modal-wrapper" style="max-width: unset;">
<?$APPLICATION->IncludeComponent(
	"ctweb:sms.register",
	"",
	Array(
		"REQUIRE_FIELDS" => array()
	)
);?>
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
<script src="/local/templates/you-wanna_v3/js/bundle.min.js"></script>
<script src="/local/templates/you-wanna_v3/js/inputmask.min.js"></script>
<script src="/local/templates/you-wanna_v3/js/jquery.inputmask-multi.min.js"></script>
<script>
(function($){
	$('#bx_1789522556_code').focus();
	var listCountries = $.masksSort($.masksLoad("/local/templates/you-wanna_v3/js/phone-codes.json"), ['#'], /[0-9]|#/, "mask");
    var maskOpts = {
        inputmask: {
            definitions: {
                '#': {
                    validator: "[0-9]",
                    cardinality: 1
                }
            },
            showMaskOnHover: false,
            autoUnmask: true,
            clearMaskOnLostFocus: false
        },
        match: /[0-9]/,
        replace: '#',
        listKey: "mask"
    };

    var maskChangeWorld = function(maskObj, determined) {
        if (determined) {
            var hint = maskObj.name_ru;
            if (maskObj.desc_ru && maskObj.desc_ru != "") {
                hint += " (" + maskObj.desc_ru + ")";
            }
            $("#descr").html(hint);
        } else {
            $("#descr").html("Маска ввода");
        }
    }

    var phoneInput = $('input[name="PERSONAL_PHONE"]');
    phoneInput.val("7");
    phoneInput.inputmasks($.extend(true, {}, maskOpts, {
        list: listCountries,
        onMaskChange: maskChangeWorld
    }));
})(jQuery);
</script>
</div>
        <?
    else :
        ?>
        <form name="system_auth_form<?= $arResult["RND"] ?>" method="post" target="_top"
              action="<?= $arResult["AUTH_URL"] ?>">
            <?
            if ($arResult["BACKURL"] <> ''): ?>
                <input type="hidden" name="backurl" value="<?= $arResult["BACKURL"] ?>"/>
            <? endif ?>
            <?
            foreach ($arResult["POST"] as $key => $value): ?>
                <input type="hidden" name="<?= $key ?>" value="<?= $value ?>"/>
            <? endforeach ?>
            <input type="hidden" name="AUTH_FORM" value="Y"/>
            <input type="hidden" name="TYPE" value="AUTH"/>

            <h3><?= GetMessage("AUTH_HEAD") ?></h3>
            <p><?= GetMessage("AUTH_ANNOTATION") ?></p>
            <div class="form-item">
                <label><?= GetMessage("AUTH_LOGIN") ?></label>
                <input type="text" name="USER_LOGIN" id="USER_LOGIN" maxlength="50" value="" size="17"/>
            </div>
            <script>
                BX.ready(function () {
                    var loginCookie = BX.getCookie("<?=CUtil::JSEscape($arResult["~LOGIN_COOKIE_NAME"])?>");
                    if (loginCookie) {
                        var form = document.forms["system_auth_form<?=$arResult["RND"]?>"];
                        var loginInput = form.elements["USER_LOGIN"];
                        loginInput.value = loginCookie;
                    }
                });
            </script>

            <div class="form-item password-item">
                <input type="password" name="USER_PASSWORD" id="USER_PASSWORD" maxlength="50" size="17"
                       autocomplete="off"
                       placeholder="<?= GetMessage("AUTH_PASSWORD") ?>"/>
            </div>
            <?
            if ($arResult["SECURE_AUTH"]): ?>
                <span class="bx-auth-secure" id="bx_auth_secure<?= $arResult["RND"] ?>" title="<?
                echo GetMessage("AUTH_SECURE_NOTE") ?>" style="display:none">
					<div class="bx-auth-secure-icon"></div>
				</span>
                <noscript>
				<span class="bx-auth-secure" title="<?
                echo GetMessage("AUTH_NONSECURE_NOTE") ?>">
					<div class="bx-auth-secure-icon bx-auth-secure-unlock"></div>
				</span>
                </noscript>
                <script type="text/javascript">
                    document.getElementById('bx_auth_secure<?=$arResult["RND"]?>').style.display = 'inline-block';
                </script>
            <? endif ?>
            <?/*
            if ($arResult["STORE_PASSWORD"] == "Y"): ?>
                <input type="checkbox" id="USER_REMEMBER_frm" id="REMEMBER_ME" name="USER_REMEMBER" value="Y"/>
                <label for="USER_REMEMBER_frm" title="<?= GetMessage("AUTH_REMEMBER_ME") ?>"><?
                    echo GetMessage("AUTH_REMEMBER_SHORT") ?></label>
            <? endif */ ?>
            <?
            if ($arResult["CAPTCHA_CODE"]): ?>

                <?
                echo GetMessage("AUTH_CAPTCHA_PROMT") ?>:<br/>
                <input type="hidden" name="captcha_sid" value="<?
                echo $arResult["CAPTCHA_CODE"] ?>"/>
                <img src="/bitrix/tools/captcha.php?captcha_sid=<?
                echo $arResult["CAPTCHA_CODE"] ?>" width="180" height="40" alt="CAPTCHA"/><br/><br/>
                <input type="text" name="captcha_word" maxlength="50" value=""/>
            <? endif ?>
            <div class="auth-error hidden">Данные введены неверно</div>
            <noindex>
                <span class="forgotpasswd-link-button" href="<?= $arResult["AUTH_FORGOT_PASSWORD_URL"] ?>"
                      rel="nofollow"><?= GetMessage("AUTH_FORGOT_PASSWORD_2") ?></span>
                <div class="form-item forgot_pass_btn">
                    <a id="send_account_info"
                       class="button secondary upper outline"><?= LANGUAGE_ID === 'ru' ? 'Восстановить' : 'Restore'; ?></a>
                    <span id="countdown" class="invisible"></span>
                </div>
            </noindex>
            <input type="button" id="popup-auth-button" name="Login" class="button secondary upper"
                   value="<?= GetMessage("AUTH_LOGIN_BUTTON") ?>"/>
            <noindex>
                <span class="auth-register-head"><?= GetMessage("AUTH_REGISTER_HEAD") ?></span>
                <input type="button" class="register-link-button button secondary outline upper" href="" rel="nofollow"
                       value="<?= GetMessage("AUTH_REGISTER") ?>"/>
            </noindex>

            <?
            if ($arResult["AUTH_SERVICES"]): ?>
                <div class="bx-auth-lbl"><?= GetMessage("socserv_as_user_form") ?></div>
                <?
                $APPLICATION->IncludeComponent("bitrix:socserv.auth.form", "icons",
                    array(
                        "AUTH_SERVICES" => $arResult["AUTH_SERVICES"],
                        "SUFFIX"        => "form",
                    ),
                    $component,
                    array("HIDE_ICONS" => "Y")
                );
                ?>
            <? endif ?>
        </form>

        <?
        if ($arResult["AUTH_SERVICES"]): ?>
            <?
            $APPLICATION->IncludeComponent("bitrix:socserv.auth.form", "",
                array(
                    "AUTH_SERVICES" => $arResult["AUTH_SERVICES"],
                    "AUTH_URL"      => $arResult["AUTH_URL"],
                    "POST"          => $arResult["POST"],
                    "POPUP"         => "Y",
                    "SUFFIX"        => "form",
                ),
                $component,
                array("HIDE_ICONS" => "Y")
            );
            ?>
        <? endif ?>

        <? endif ?>

        <?
    elseif ($arResult["FORM_TYPE"] == "otp"):
        ?>

        <form name="system_auth_form<?= $arResult["RND"] ?>" method="post" target="_top"
              action="<?= $arResult["AUTH_URL"] ?>">
            <?
            if ($arResult["BACKURL"] <> ''):?>
                <input type="hidden" name="backurl" value="<?= $arResult["BACKURL"] ?>"/>
            <? endif ?>
            <input type="hidden" name="AUTH_FORM" value="Y"/>
            <input type="hidden" name="TYPE" value="OTP"/>
            <?
            echo GetMessage("auth_form_comp_otp") ?><br/>
            <input type="text" name="USER_OTP" maxlength="50" value="" size="17" autocomplete="off"/>
            <?
            if ($arResult["CAPTCHA_CODE"]):?>
                <?
                echo GetMessage("AUTH_CAPTCHA_PROMT") ?>:<br/>
                <input type="hidden" name="captcha_sid" value="<?
                echo $arResult["CAPTCHA_CODE"] ?>"/>
                <img src="/bitrix/tools/captcha.php?captcha_sid=<?
                echo $arResult["CAPTCHA_CODE"] ?>" width="180" height="40" alt="CAPTCHA"/><br/><br/>
                <input type="text" name="captcha_word" maxlength="50" value=""/>
            <? endif ?>
            <?
            if ($arResult["REMEMBER_OTP"] == "Y"):?>
                <input type="checkbox" id="OTP_REMEMBER_frm" name="OTP_REMEMBER" value="Y"/>
                <label for="OTP_REMEMBER_frm" title="<?
                echo GetMessage("auth_form_comp_otp_remember_title") ?>"><?
                    echo GetMessage("auth_form_comp_otp_remember") ?></label>
            <? endif ?>
            <input type="submit" name="Login" value="<?= GetMessage("AUTH_LOGIN_BUTTON") ?>"/>
            <noindex><a href="<?= $arResult["AUTH_LOGIN_URL"] ?>" rel="nofollow"><?
                    echo GetMessage("auth_form_comp_auth") ?></a></noindex>
            <br/>
        </form>

        <?
    else:
        ?>

        <form action="<?= $arResult["AUTH_URL"] ?>">
            <div class="authorized">
                <span class="title"><?= GetMessage("AUTH_IS_AUTG") ?></span>
                <span><?= $arResult["USER_NAME"] ?></span>
                <span>[<?= $arResult["USER_LOGIN"] ?>]</span>
                <span><a href="/personal/order/"
                         title="<?= GetMessage("AUTH_PROFILE") ?>"><?= GetMessage("AUTH_PROFILE") ?></a></span>

                <? foreach ($arResult["GET"] as $key => $value): ?>
                    <input type="hidden" name="<?= $key ?>" value="<?= $value ?>"/>
                <? endforeach ?>
                <input type="hidden" name="logout" value="yes"/>
                <input type="submit" name="logout_butt" class="button secondary upper"
                       value="<?= GetMessage("AUTH_LOGOUT_BUTTON") ?>"/>
            </div>
        </form>
    <? endif ?>
</div>
<script>
    $(document).ready(function () {
        /*$('.form-item #USER_LOGIN').mask('+7 (999) 999-99-99');*/
        if (localStorage.getItem('send_pass_success') !== null && Math.floor(($.now() - localStorage.getItem('send_pass_success')) / 1000) < 60) {
            $('#send_account_info').addClass('disabled-link');
            $('#send_account_info').addClass('send-success');
            $('#countdown').text(60 - Math.floor(($.now() - localStorage.getItem('send_pass_success')) / 1000));
            $('#countdown').removeClass('invisible');
            countDown();
        }

        $('#USER_LOGIN').on('keyup', function () {
            if ($('#send_account_info').hasClass('disabled-link') && !$('#send_account_info').hasClass('send-success')) {
                $('#send_account_info').removeClass('disabled-link');
                $('#countdown').addClass('invisible');
                $('.error-text').remove();
                $('.success-text').remove();
            }
        })
        $('#USER_LOGIN').keydown(function (e) {
            if (e.keyCode == 13) {
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
                                    if (json.sms_pass === true) {
                                        var callbackRequestURL = '/personal/change_passwd.php?lang=' + LANGUAGE_ID;
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
                    }
                )
            }
        })
        $('#USER_PASSWORD').keydown(function (e) {
            if (e.keyCode == 13) {
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
                                    if (json.sms_pass === true) {
                                        var callbackRequestURL = '/personal/change_passwd.php?lang=' + LANGUAGE_ID;
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
                    }
                )
            }
        })
    })

</script>