<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
} ?><?

ShowMessage($arParams["~AUTH_RESULT"]);

?>
<h3><?= GetMessage("FORGOT_PASS_HEAD") ?></h3>
<form name="bform" method="post" target="_top" action="<?= $arResult["AUTH_URL"] ?>">
    <?
    if (strlen($arResult["BACKURL"]) > 0) {
        ?>
        <input type="hidden" name="backurl" value="<?= $arResult["BACKURL"] ?>"/>
        <?
    }
    ?>
    <input type="hidden" name="AUTH_FORM" value="Y">
    <input type="hidden" name="TYPE" value="SEND_PWD">
    <p>
        <?= GetMessage("AUTH_FORGOT_PASSWORD_1") ?>
    </p>
    <div class="form-item">
        <input type="text" name="USER_LOGIN" maxlength="50"
               placeholder="<?= GetMessage("AUTH_LOGIN") ?>"
               value="<?= $arResult["LAST_LOGIN"] ?>"/>&nbsp;<?= GetMessage("AUTH_OR") ?>

        <input type="text" name="USER_EMAIL" maxlength="255" placeholder="<?= GetMessage("AUTH_EMAIL") ?>"/>
    </div>
    <? if ($arResult["USE_CAPTCHA"]): ?>

        <input type="hidden" name="captcha_sid" value="<?= $arResult["CAPTCHA_CODE"] ?>"/>
        <img src="/bitrix/tools/captcha.php?captcha_sid=<?= $arResult["CAPTCHA_CODE"] ?>" width="180" height="40"
             alt="CAPTCHA"/>

        <? echo GetMessage("system_auth_captcha") ?>
        <input type="text" name="captcha_word" maxlength="50" value=""/>
    <? endif ?>
    <div class="form-item">
        <input type="submit" name="send_account_info" class="button secondary upper"
               value="<?= GetMessage("AUTH_SEND") ?>"/></div>
</form>
