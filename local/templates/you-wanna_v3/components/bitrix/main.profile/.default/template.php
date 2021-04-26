<?
/**
 * @global CMain $APPLICATION
 * @var array    $arParams
 * @var array    $arResult
 */
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}
$arResult = array_merge($arResult, $arParams['INCLUDE_HIDE']);
?>

<div class="personal__box">

    <div class="bx-auth-profile">

        <? ShowError($arResult["strProfileError"]); ?>
        <?
        if ($arResult['DATA_SAVED'] == 'Y') {
            ShowNote(GetMessage('PROFILE_DATA_SAVED'));
        }
        ?>
        <script type="text/javascript">
            <!--
            var opened_sections = [<?
                $arResult["opened"] = $_COOKIE[$arResult["COOKIE_PREFIX"] . "_user_profile_open"];
                $arResult["opened"] = preg_replace("/[^a-z0-9_,]/i", "", $arResult["opened"]);
                if (strlen($arResult["opened"]) > 0) {
                    echo "'" . implode("', '", explode(",", $arResult["opened"])) . "'";
                } else {
                    $arResult["opened"] = "reg";
                    echo "'reg'";
                }
                ?>];
            //-->

            var cookie_prefix = '<?=$arResult["COOKIE_PREFIX"]?>';
        </script>
        <form method="post" name="form1" class="form" action="<?= $arResult["FORM_TARGET"] ?>"
              enctype="multipart/form-data">
            <?= $arResult["BX_SESSION_CHECK"] ?>
            <input type="hidden" name="lang" value="<?= LANG ?>"/>
            <input type="hidden" name="ID" value=<?= $arResult["ID"] ?>/>

            <? /*?><div class="profile-link profile-user-div-link"><a title="<?= GetMessage("REG_SHOW_HIDE") ?>"
                                                               href="javascript:void(0)"
                                                               onclick="SectionClick('reg')"><?= GetMessage("REG_SHOW_HIDE") ?></a>
            </div><?*/ ?>
                <div class="form-item">
                    <label><?= GetMessage('NAME') ?></label>
                    <input type="text" name="NAME" maxlength="50"
                           value="<?= $arResult["arUser"]["NAME"] ?>"/>
                </div>
                <div class="form-item">
                    <label><?= GetMessage('USER_PHONE') ?></label>
                    <input type="text" name="PERSONAL_PHONE" maxlength="255"
                           value="<?= $arResult["arUser"]["PERSONAL_PHONE"] ?>"/>
                </div>
                <div class="form-item">
                    <label><?= GetMessage('EMAIL') ?><? if ($arResult["EMAIL_REQUIRED"]): ?><span
                                class="starrequired">*</span><? endif ?></label>
                    <input type="text" name="EMAIL" maxlength="50"
                           value="<? echo $arResult["arUser"]["EMAIL"] ?>"/>
                </div>
                <? if ($arResult["arUser"]["EXTERNAL_AUTH_ID"] == ''): ?>
                    <div class="form-item">
                        <label><?= GetMessage('NEW_PASSWORD_REQ') ?></label>
                        <input type="password" name="NEW_PASSWORD" maxlength="50" value="" autocomplete="off"
                               class="bx-auth-input"/>
                    </div>
                    <? if ($arResult["SECURE_AUTH"]): ?>
                        <span class="bx-auth-secure" id="bx_auth_secure"
                              title="<? echo GetMessage("AUTH_SECURE_NOTE") ?>" style="display:none">
                            <div class="bx-auth-secure-icon"></div>
                        </span>
                        <noscript>
                            <span class="bx-auth-secure" title="<? echo GetMessage("AUTH_NONSECURE_NOTE") ?>">
                                <div class="bx-auth-secure-icon bx-auth-secure-unlock"></div>
                            </span>
                        </noscript>
                        <script type="text/javascript">
                            document.getElementById('bx_auth_secure').style.display = 'inline-block';
                        </script>
                    <? endif ?>
                    <div class="form-item">
                        <?/*?><label><?= GetMessage('NEW_PASSWORD_CONFIRM') ?></label><?*/?>
                        <input type="hidden" name="NEW_PASSWORD_CONFIRM" maxlength="50" value=""
                               autocomplete="off"/>
                    </div>
                <? endif ?>
                <div class="form-item">
                    <label class="checkbox"><input type="checkbox" class="checkbox-invisible"><span class="checkbox-custom"></span><span class="checkbox-label"><?= GetMessage('EMAIL_SUBSCRIBE') ?></label>
                </div>
                <? /*?><tr>
                        <td><?= GetMessage('LAST_NAME') ?></td>
                        <td><input type="text" name="LAST_NAME" maxlength="50"
                                   value="<?= $arResult["arUser"]["LAST_NAME"] ?>"/></td>
                    </tr>
                    <tr>
                        <td><?= GetMessage('SECOND_NAME') ?></td>
                        <td><input type="text" name="SECOND_NAME" maxlength="50"
                                   value="<?= $arResult["arUser"]["SECOND_NAME"] ?>"/></td>
                    </tr><?*/ ?>

            <? /* if ($arResult["IS_ADMIN"]): ?>
                <div class="profile-link profile-user-div-link"><a title="<?= GetMessage("USER_SHOW_HIDE") ?>"
                                                                   href="javascript:void(0)"
                                                                   onclick="SectionClick('admin')"><?= GetMessage("USER_ADMIN_NOTES") ?></a>
                </div>
                <div id="user_div_admin" class="profile-block-<?= strpos($arResult["opened"],
                    "admin") === false ? "hidden" : "shown" ?>">
                    <?= GetMessage("USER_ADMIN_NOTES") ?>:
                    <textarea cols="30" rows="5"
                              name="ADMIN_NOTES"><?= $arResult["arUser"]["ADMIN_NOTES"] ?></textarea>
                </div>
            <? endif; */?>

            <? // ******************** /User properties ***************************************************?>
            <?/*?><p><? echo $arResult["GROUP_POLICY"]["PASSWORD_REQUIREMENTS"]; ?></p><?*/?>
            <p><input type="submit" name="save" class="button secondary upper outline"
                      value="<?= (($arResult["ID"] > 0) ? GetMessage("MAIN_SAVE") : GetMessage("MAIN_ADD")) ?>">&nbsp;&nbsp;</p>
        </form>
        <?
        if ($arResult["SOCSERV_ENABLED"]) {
            $APPLICATION->IncludeComponent("bitrix:socserv.auth.split", ".default", array(
                "SHOW_PROFILES" => "Y",
                "ALLOW_DELETE"  => "Y"
            ),
                false
            );
        }
        ?>
    </div>
</div>
<script>
    $(document).ready(function(){
        /* Задание маски телефона для его указания в профиле */
        $("input[name='PERSONAL_PHONE']").mask('+7 (999) 999-99-99');

        $("input[name='NEW_PASSWORD']" ).change(function() {
            $( "input[name='NEW_PASSWORD_CONFIRM']" ).val( $(this).val() );
        });
    })
</script>