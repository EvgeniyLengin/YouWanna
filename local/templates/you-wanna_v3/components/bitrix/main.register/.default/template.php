<?
/**
 * Bitrix Framework
 *
 * @package    bitrix
 * @subpackage main
 * @copyright  2001-2014 Bitrix
 */

/**
 * Bitrix vars
 *
 * @global CMain                   $APPLICATION
 * @global CUser                   $USER
 *
 * @param array                    $arParams
 * @param array                    $arResult
 * @param CBitrixComponentTemplate $this
 */

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}
global $USER;
$errorPass = isset($arResult['ERRORS']['PASSWORD']);
$errorConfirmPass = isset($arResult['ERRORS']['CONFIRM_PASSWORD']);
$errorEmail = isset($arResult['ERRORS']['EMAIL']);
$errorPhone = isset($arResult['ERRORS']['PERSONAL_PHONE']);
/*$errorSmsCode = isset($arResult['ERRORS']['UF_PHONE_CONFIRM']);*/
?>

<div class="bx-auth-reg">

    <? if ($USER->IsAuthorized()): ?>

        <p class="auth_yes"><? echo GetMessage("MAIN_REGISTER_AUTH") ?></p>

    <? else: ?>
    <?
if (count($arResult["ERRORS"][0]) > 0){?>
    <p class="register_error">
	<?foreach ($arResult["ERRORS"] as $key => $error)
		{if (intval($key) == 0 && $key !== 0)
			{$arResult["ERRORS"][$key] = str_replace("#FIELD_NAME#", "&quot;".GetMessage("REGISTER_FIELD_".$key)."&quot;", $error);}}

	ShowError(implode("<br />", $arResult["ERRORS"]));?>
	</p>
	<?}
?>

        <form class="js-check-sms-code-form" method="post" action="<?= POST_FORM_ACTION_URI ?>" name="regform" enctype="multipart/form-data">
            <?
            if ($arResult["BACKURL"] <> ''):
                ?>
                <input type="hidden" name="backurl" value="<?= $arResult["BACKURL"] ?>"/>
                <?
            endif;
            ?>

            <h3><?= GetMessage("AUTH_REGISTER") ?></h3>

                <? foreach ($arResult["SHOW_FIELDS"] as $FIELD): ?>
                    <? if ($FIELD == "AUTO_TIME_ZONE" && $arResult["TIME_ZONE_ENABLED"] == true): ?>
                                <select name="REGISTER[AUTO_TIME_ZONE]"
                                        onchange="this.form.elements['REGISTER[TIME_ZONE]'].disabled=(this.value != 'N')">
                                    <option value=""><? echo GetMessage("main_profile_time_zones_auto_def") ?></option>
                                    <option value="Y"<?= $arResult["VALUES"][$FIELD] == "Y" ? " selected=\"selected\"" : "" ?>><? echo GetMessage("main_profile_time_zones_auto_yes") ?></option>
                                    <option value="N"<?= $arResult["VALUES"][$FIELD] == "N" ? " selected=\"selected\"" : "" ?>><? echo GetMessage("main_profile_time_zones_auto_no") ?></option>
                                </select>
                                <? echo GetMessage("main_profile_time_zones_zones") ?>

                                <select name="REGISTER[TIME_ZONE]"<? if (!isset($_REQUEST["REGISTER"]["TIME_ZONE"])) echo 'disabled="disabled"' ?>>
                                    <? foreach ($arResult["TIME_ZONE_LIST"] as $tz => $tz_name): ?>
                                        <option value="<?= htmlspecialcharsbx($tz) ?>"<?= $arResult["VALUES"]["TIME_ZONE"] == $tz ? " selected=\"selected\"" : "" ?>><?= htmlspecialcharsbx($tz_name) ?></option>
                                    <? endforeach ?>
                                </select>

                    <? else: ?>

                        <?/*= GetMessage("REGISTER_FIELD_" . $FIELD) */?>

                                <?
                                switch ($FIELD) {
                                    case "PASSWORD":
                                        ?>
                                        <div class="form-item">
                                        <input size="30" type="password" name="REGISTER[<?= $FIELD ?>]"
                                                 value="<?= $arResult["VALUES"][$FIELD] ?>"
                                                 placeholder="<?= GetMessage("REGISTER_FIELD_" . $FIELD) ?>"
                                                 autocomplete="off"
                                                 class="bx-auth-input<?= $errorPass ? ' error' : ''?>"/>
                                        </div>
                                    <?
                                    if ($arResult["SECURE_AUTH"]): ?>
                                        <span class="bx-auth-secure" id="bx_auth_secure" title="<?
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
                                            document.getElementById('bx_auth_secure').style.display = 'inline-block';
                                        </script>
                                    <?
                                    endif ?>
                                        <?
                                        break;
                                case "CONFIRM_PASSWORD":
                                    ?>
                                <div class="form-item">
                                    <input size="30" type="password" name="REGISTER[<?= $FIELD ?>]"
                                            <?= $errorConfirmPass ? 'class="error"' : '';?>
                                             placeholder="<?= GetMessage("REGISTER_FIELD_" . $FIELD) ?>"
                                             value="<?= $arResult["VALUES"][$FIELD] ?>" autocomplete="off" />
                                    </div>
                                             <?
                                break;
                                case "EMAIL":
                                    ?>
                                <div class="form-item">
                                    <input size="30" type="email" name="REGISTER[<?= $FIELD ?>]"
                                            <?= $errorEmail ? 'class="error"' : '';?>
                                             placeholder="<?= GetMessage("REGISTER_FIELD_" . $FIELD) ?>"
                                             value="<?= $arResult["VALUES"][$FIELD] ?>" autocomplete="off" />
                                    </div>
                                             <?
                                break;
                                case "UF_PHONE_CONFIRM":
                                    ?>
                                    <input size="30" type="hidden" name="REGISTER[<?= $FIELD ?>]"
                                    id="confirm_phone"
                                             placeholder="<?= GetMessage("REGISTER_FIELD_" . $FIELD) ?>"
                                             value="Y<?/*= $arResult["VALUES"][$FIELD] */?>" autocomplete="off" />
                                             <?
                                break;
                                case "LOGIN":
                                    ?><input size="30" type="hidden" name="REGISTER[<?= $FIELD ?>]"
                                             placeholder="<?= GetMessage("REGISTER_FIELD_" . $FIELD) ?>"
                                             value="phone" autocomplete="off" />
                                             <?
                                break;

                                case "PERSONAL_PHONE":
                                ?>
                                <div class="phone-fields row">
                                    <div class="form-item">
                                        <input type="tel"
                                               class="form-control<?= $errorPhone ? ' error' : '';?>"
                                               id="REG_PHONE" placeholder="<?= GetMessage("TELEPHONE") ?>"
                                               name="REGISTER[PERSONAL_PHONE]" value="<?= $arResult['VALUES']['PERSONAL_PHONE']?>">

                                        <?/*?><a href="javascript:void(0);" name="web_form_submit"
                                            class="promo-button btn btn-default js-send-sms-code mt-0 mb-0"><?= GetMessage("GET_CODE") ?></a><?*/?>
                                    </div>

                                    <?/*?><div class="form-item col col-5">
                                        <input type="text" class="form-control<?= $errorSmsCode ? ' error' : '';?>"
                                               id="REG_SMS_CODE"
                                               placeholder="<?= GetMessage("ENTER_CODE") ?>"
                                               name="SMS_CODE" value="<?= $_REQUEST['SMS_CODE']?>">
                                     </div><?*/?>
                                </div>
                                    <?
                                    break;

                                    case "PERSONAL_GENDER":
                                        ?><select name="REGISTER[<?= $FIELD ?>]">
                                        <option value=""><?= GetMessage("USER_DONT_KNOW") ?></option>
                                        <option value="M"<?= $arResult["VALUES"][$FIELD] == "M" ? " selected=\"selected\"" : "" ?>><?= GetMessage("USER_MALE") ?></option>
                                        <option value="F"<?= $arResult["VALUES"][$FIELD] == "F" ? " selected=\"selected\"" : "" ?>><?= GetMessage("USER_FEMALE") ?></option>
                                        </select><?
                                        break;

                                    case "PERSONAL_COUNTRY":
                                    case "WORK_COUNTRY":
                                        ?><select name="REGISTER[<?= $FIELD ?>]"><?
                                        foreach ($arResult["COUNTRIES"]["reference_id"] as $key => $value) {
                                            ?>
                                            <option value="<?= $value ?>"<?
                                            if ($value == $arResult["VALUES"][$FIELD]):?> selected="selected"<? endif ?>><?= $arResult["COUNTRIES"]["reference"][$key] ?></option>
                                            <?
                                        }
                                        ?></select><?
                                        break;

                                    case "UF_USER_REGISTER":
                                        ?>
                                        <input type="hidden" name="REGISTER[<?= $FIELD ?>]" value="Y" />
                                        <?
                                        break;

                                    case "PERSONAL_PHOTO":
                                    case "WORK_LOGO":
                                        ?><input size="30" type="file" name="REGISTER_FILES_<?= $FIELD ?>" /></div><?
                                        break;

                                    case "PERSONAL_NOTES":
                                case "WORK_NOTES":
                                    ?><textarea cols="30" rows="5"
                                                name="REGISTER[<?= $FIELD ?>]"><?= $arResult["VALUES"][$FIELD] ?></textarea><?
                                break;
                                default:
                                if ($FIELD == "PERSONAL_BIRTHDAY"): ?>
                                    <small><?= $arResult["DATE_FORMAT"] ?></small><br/><?
                                endif;
                                    ?>
                                    <div class="form-item">
                                    <input size="30" type="text" name="REGISTER[<?= $FIELD ?>]"
                                             placeholder="<?= GetMessage("REGISTER_FIELD_" . $FIELD) ?>"
                                             value="<?= $arResult["VALUES"][$FIELD] ?>" /></div><?
                                    if ($FIELD == "PERSONAL_BIRTHDAY") {
                                        $APPLICATION->IncludeComponent(
                                            'bitrix:main.calendar',
                                            '',
                                            array(
                                                'SHOW_INPUT' => 'N',
                                                'FORM_NAME'  => 'regform',
                                                'INPUT_NAME' => 'REGISTER[PERSONAL_BIRTHDAY]',
                                                'SHOW_TIME'  => 'N'
                                            ),
                                            null,
                                            array("HIDE_ICONS" => "Y")
                                        );
                                    }
                                    ?><?
                                } ?>

                    <? endif ?>
                <? endforeach ?>
                <? // ********************* User properties ***************************************************?>
                <? if ($arResult["USER_PROPERTIES"]["SHOW"] == "Y"): ?>

                    <?= strlen(trim($arParams["USER_PROPERTY_NAME"])) > 0 ? $arParams["USER_PROPERTY_NAME"] : GetMessage("USER_TYPE_EDIT_TAB") ?>

                    <? foreach ($arResult["USER_PROPERTIES"]["DATA"] as $FIELD_NAME => $arUserField): ?>
                        <?= $arUserField["EDIT_FORM_LABEL"] ?>

                                <? $APPLICATION->IncludeComponent(
                                    "bitrix:system.field.edit",
                                    $arUserField["USER_TYPE"]["USER_TYPE_ID"],
                                    array(
                                        "bVarsFromForm" => $arResult["bVarsFromForm"],
                                        "arUserField"   => $arUserField,
                                        "form_name"     => "regform"
                                    ), null, array("HIDE_ICONS" => "Y")); ?>

                    <? endforeach; ?>
                <? endif; ?>
                <? // ******************** /User properties ***************************************************?>
                <?
                /* CAPTCHA */
                if ($arResult["USE_CAPTCHA"] == "Y") {
                    ?>
                    <b><?= GetMessage("REGISTER_CAPTCHA_TITLE") ?></b>

                            <input type="hidden" name="captcha_sid" value="<?= $arResult["CAPTCHA_CODE"] ?>"/>
                            <img src="/bitrix/tools/captcha.php?captcha_sid=<?= $arResult["CAPTCHA_CODE"] ?>"
                                 width="180" height="40" alt="CAPTCHA"/>
                            <?= GetMessage("REGISTER_CAPTCHA_PROMT") ?>
                            <input type="text" name="captcha_word" maxlength="50" value=""/>
                    <?
                }
                /* !CAPTCHA */
                if ($arResult["USE_EMAIL_CONFIRMATION"] === "Y"):
                    ?>
                    <p><? echo GetMessage("REGISTER_EMAIL_WILL_BE_SENT") ?></p>
                <? endif
                ?>
                <input type="hidden" name="user-register" value="yes"/>
                <input type="submit" class="button secondary upper" name="register_submit_button" value="<?= GetMessage("AUTH_REGISTER") ?>"/>


        </form>
    <? endif ?>
</div>
<script>
    /*function reloadFormPhoneMask () {
    $('.form-item #REG_PHONE').mask('+7 (999) 999-99-99');
}*/
/*reloadFormPhoneMask ();*/
</script>