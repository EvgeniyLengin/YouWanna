<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}
?>
<? if ($arResult["isFormNote"] === "Y"){?>
    <div class="cart-form-confirm">
        <?= GetMessage("CART_CALLBACK_CONFIRM");?>
    </div>
<?}?>

<? if ($arResult["isFormNote"] != "Y") {
    ?>
    <?= $arResult["FORM_HEADER"] ?>

    <?
    if ($arResult["isFormDescription"] == "Y" || $arResult["isFormTitle"] == "Y" || $arResult["isFormImage"] == "Y") {
        ?>
        <?
        /***********************************************************************************
         * form header
         ***********************************************************************************/
        if ($arResult["isFormTitle"]) {
            ?>
            <h3><?= GetMessage("CALLBACK_HEAD") ?></h3>
            <p><?= GetMessage("CART_CALLBACK_TEXT") ?></p>
            <?
        } //endif ;

        if ($arResult["isFormImage"] == "Y") {
            ?>
            <a href="<?= $arResult["FORM_IMAGE"]["URL"] ?>" target="_blank" alt="<?= GetMessage("FORM_ENLARGE") ?>"><img
                        src="<?= $arResult["FORM_IMAGE"]["URL"] ?>"
                        <? if ($arResult["FORM_IMAGE"]["WIDTH"] > 300): ?>width="300"
                        <? elseif ($arResult["FORM_IMAGE"]["HEIGHT"] > 200): ?>height="200"<? else: ?><?= $arResult["FORM_IMAGE"]["ATTR"] ?><? endif;
                ?> hspace="3" vscape="3" border="0"/></a>
            <? //=$arResult["FORM_IMAGE"]["HTML_CODE"]
            ?>
            <?
        } //endif
        ?>

        <?
    } // endif
    ?>
    <br/>
    <?
    /***********************************************************************************
     * form questions
     ***********************************************************************************/
    ?>
    <?
    foreach ($arResult["QUESTIONS"] as $FIELD_SID => $arQuestion) {
        if ($arParams['LANGUAGE_ID'] !== 'ru'){
            $arQuestion['CAPTION'] = CYouWanna::multiTranslate($arQuestion['CAPTION'], $arParams['LANGUAGE_ID']);
            $arResult["arForm"]["BUTTON"] = CYouWanna::multiTranslate($arResult["arForm"]["BUTTON"], $arParams['LANGUAGE_ID']);
        }
        if ($arQuestion['STRUCTURE'][0]['FIELD_TYPE'] == 'hidden') {
            echo $arQuestion["HTML_CODE"];
        } else { ?>

                <?
                if (is_array($arResult["FORM_ERRORS"]) && array_key_exists($FIELD_SID, $arResult['FORM_ERRORS'])): ?>
                    <span class="error-fld"
                          title="<?= htmlspecialcharsbx($arResult["FORM_ERRORS"][$FIELD_SID]) ?>"></span>
                <? endif; ?>
                <?if ($arQuestion['STRUCTURE'][0]['FIELD_TYPE'] === 'file'){?>
                <div class="file-upload-header"><?= GetMessage("LOAD_RESUME") ?></div>
                <div class="form-item">
                    <div class="file-block row">
                        <button class="col col-6 button secondary upper outline" id="browse">Выбрать файл</button>
                        <div class="col col-6 file-name">Файл не выбран</div>
                        <input type="<?= $arQuestion['STRUCTURE'][0]['FIELD_TYPE']?>"
                               id="browse-real"
                               class="hidden"
                               name="form_<?= $arQuestion['STRUCTURE'][0]['FIELD_TYPE']?>_<?= $arQuestion['STRUCTURE'][0]['ID']?>"
                               value="<?= $arQuestion['VALUE']?>">
                    </div>
                </div>
                <?} elseif ($arQuestion['STRUCTURE'][0]['ID'] === '6') {?>
                <input type="<?= $arQuestion['STRUCTURE'][0]['FIELD_TYPE']?>"
                       style="display: none;"
                       name="form_<?= $arQuestion['STRUCTURE'][0]['FIELD_TYPE']?>_<?= $arQuestion['STRUCTURE'][0]['ID']?>"
                       value="<?= $_REQUEST['id']?>">
                <?} elseif ($arQuestion['STRUCTURE'][0]['FIELD_TYPE'] === 'textarea') {?>
                        <?continue;?>
                <?} else {?>
                    <div class="form-item">
                    <input type="<?= $arQuestion['STRUCTURE'][0]['FIELD_TYPE']?>"
                           class=""
                           name="form_<?= $arQuestion['STRUCTURE'][0]['FIELD_TYPE']?>_<?= $arQuestion['STRUCTURE'][0]['ID']?>"
                           value="<?= $arQuestion['VALUE']?>"
                           size="0"
                           placeholder="<?= $arQuestion['CAPTION']?>">
                    </div>
                <? } ?>


            <?
        }
    } //endwhile
    ?>
    <?
    if ($arResult["isUseCaptcha"] == "Y") {
        ?>
        <b><?= GetMessage("FORM_CAPTCHA_TABLE_TITLE") ?></b><input type="hidden" name="captcha_sid"
                                                                   value="<?= htmlspecialcharsbx($arResult["CAPTCHACode"]); ?>"/>
        <img src="/bitrix/tools/captcha.php?captcha_sid=<?= htmlspecialcharsbx($arResult["CAPTCHACode"]); ?>"
             width="180" height="40"/>
        <?= GetMessage("FORM_CAPTCHA_FIELD_TITLE") ?><?= $arResult["REQUIRED_SIGN"]; ?>
        <input type="text" name="captcha_word" size="30" maxlength="50" value="" class="inputtext"/>
        <?
    } // isUseCaptcha
    ?>
    <? if ($arResult["isFormErrors"] == "Y"): ?><?= $arResult["FORM_ERRORS_TEXT"]; ?><? endif; ?>
    <input <?= (intval($arResult["F_RIGHT"]) < 10 ? "disabled=\"disabled\"" : ""); ?> type="submit"
                                                                                      class="button secondary upper"
                                                                                      name="web_form_submit"
                                                                                      value="<?= htmlspecialcharsbx(strlen(trim($arResult["arForm"]["BUTTON"])) <= 0 ? GetMessage("FORM_ADD") : $arResult["arForm"]["BUTTON"]); ?>"/>

    <?= $arResult["FORM_FOOTER"] ?>
    <?
} //endif (isFormNote)
?>
<?if ($arParams['CALLBACK'] !== 'Y'){?>
    <script type="text/javascript">
        //обработка нажатия кнопки "обзор"
        $( document ).ready(function() {
            $('.popup-vacancy-inner form').addClass('form')
            var browse = document.getElementById("browse");
            var browse_real = document.getElementById("browse-real");
            browse.addEventListener("click", function(e) {
                browse_real.click();
                e.preventDefault();
            }, false);
            browse_real.addEventListener('change', function() {
                var val = browse_real.value;
                //в opera и chrome путь полный с косыми чертами - разделим на массив с разделителями "\" и отобразим последний элемент:
                var mas = val.split('\\')
                $('.file-name').html(mas[mas.length - 1]);
            });
        });
</script>
<? } ?>