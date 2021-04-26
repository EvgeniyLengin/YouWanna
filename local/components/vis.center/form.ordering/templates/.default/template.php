<?
/**
 * @var array $arResult
 * @var array $arParams
 */
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);
$this->setFrameMode(false);

//$frame = $this->createFrame()->begin('');

$arInputValue['NAME'] = [];

global $USER;
if ($USER->IsAuthorized()) {
    $rsUser = CUser::GetByID($USER->GetID());
    if ($arUser = $rsUser->Fetch()) {
        $arInputValue['FIO'] = $arUser['NAME'];
        $arInputValue['EMAIL'] = $arUser['EMAIL'];
        $arInputValue['PHONE'] = $arUser['PERSONAL_PHONE'];
    }
} ?>

<script>
	$(document).ready(function () {
        $('.js-phone-mask').mask('+7 (999) 999-99-99');
	});
</script>

    <form id="ordering-form"
          class="js-ordering-form"
          name="FORM_ACTION"
          action="/ajax/fast_ordering_form.php?lang=<?=$arParams['LANGUAGE_ID']?>"
          method="POST"
          enctype="multipart/form-data">

        <?= bitrix_sessid_post() ?>
        <input type="hidden" name="IS_AJAX" value="Y"/>
        <input type="hidden" name="LANGUAGE_ID" value="<?=$arParams['LANGUAGE_ID']?>">
        <?php
        $currentPredID = $arResult['ITEMS']['0']['PRODUCT_ID'];
        // print_r($currentPredID);
        // die('s');
        // Получаем айди товара, в который входит торг. предложение по айдишнику торогового предложения.
        $CurrentPosMassID = CCatalogSKU::GetProductInfo($currentPredID);
        // Айди товара
        $CurrentPosID = $CurrentPosMassID['ID'];
        //Получаем свойства этого товара.
        $propCurrentPos =CIBlockElement::GetByID($CurrentPosID)->GetNextElement()->GetProperties();
        // Получаем значение "предзаказа"
        $ComingSoonCheck = $propCurrentPos['COMING_SON']['VALUE'];
        // Если флаг "предзаказа" установлен "да" - вызываем модифицированную функцию быстрого оформления. Если нет - обычную.

         ?>
        <div class="items-list">
            <div class="items-list-title">
                <b><?= Loc::getMessage('ITEMS_LIST_TITLE') ?>:</b>
            </div>
            <? foreach ($arResult['ITEMS'] as $arItem) { ?>
                <a href="<?= $arItem['DETAIL_PAGE_URL'] ?>" target="_blank">
                    <?= CYouWanna::multiTranslate($arItem['NAME'], $arParams['LANGUAGE_ID']) ?>
                </a>
                <span>
                    - <?= $arItem['QUANTITY'] ?> <?= $arItem['MEASURE_NAME'] ?>
                    = <?= (int)$arItem['PRICE'] * (int)$arItem['QUANTITY'] ?>
                    <span class="rub">&#8381;</span>
                </span><br/>
            <? } ?>
            <div class="items-list-total">
                <b><?= Loc::getMessage('SUM_ORDER') ?>:</b>
                <? if ($arResult['DISCOUNT_SUM'] > 0 ) { ?>
                    <s>
                        <?= $arResult['DISCOUNT_SUM'] ?> <span class="rub">&#8381;</span>
                    </s>
                <? } ?>
                <?= $arResult['TOTAL_SUM'] ?> <span class="rub">&#8381;</span>
            </div>
        </div>

        <div class="js-form-ajax-error error"></div>
        <div class="js-form-ajax-msg success"></div>

        <div class="form-item">
            <label class="modif-label" for="">Имя <span class="spanred">*</span></label>
            <input type="text" name="FIO" class="required"
                   value="<?= $arInputValue['FIO'] ?: $_REQUEST['FIO'] ?>"
                   placeholder="<?= Loc::getMessage('FIO') ?> " />
        </div>

        <div class="form-item">
            <label class="modif-label" for="">Телефон <span class="spanred">*</span></label>
            <input type="tel" name="PHONE" class="required js-phone-mask"
                   value="<?= $arInputValue['PHONE'] ?: $_REQUEST['PHONE'] ?>"
                   placeholder="<?= Loc::getMessage('PHONE') ?> " />
        </div>

        <div class="form-item">
            <label class="modif-label" for="">Email <span class="spanred">*</span></label>
            <input type="text" name="EMAIL"  class="required"
                   value="<?= $arInputValue['EMAIL'] ?: $_REQUEST['EMAIL'] ?>"
                   placeholder="<?= Loc::getMessage('EMAIL') ?> " />
        </div>

        <div class="form-item">
            <input type="submit" class="js-send-form button secondary upper" value="<?php
            if ($ComingSoonCheck != "Да") {
            echo Loc::getMessage('SEND_ORDER');
                } else {
            echo "Оформить предзаказ";
            } ?>" />
        </div>

    </form>

<?
//$frame->end();
?>
