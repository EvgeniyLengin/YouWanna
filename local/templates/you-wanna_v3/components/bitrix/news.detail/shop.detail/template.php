<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

# Подключение языковых фраз
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

$this->setFrameMode(true);
$arItem = $arResult;
$templateData['SECTION_NAME'] = $arResult['SECTION_NAME'];
$templateData['NAME'] = $arResult['PROPERTIES']['ADDRESS_' . strtoupper($arParams["LANGUAGE_ID"])]['VALUE'];


if($arParams['LANGUAGE_ID'] == 'ru') {
    $arItem['NAME_RU'] = $arItem['NAME'];
} else {
    $arItem['NAME_'.strtoupper($arParams['LANGUAGE_ID'])] = $arItem['PROPERTIES']['NAME_'.strtoupper($arParams['LANGUAGE_ID'])]['VALUE'];
}
?>
<script>
    var shopsAllPoints = false;
    shopsPoints = <?=CUtil::PhpToJSObject($arResult['POINTS'])?>;
    $('document').ready(function() {
        if($(window).outerWidth() > 768) {
            var lMenuTop = $('.shop-one').offset().top;
            $('.shops-menu').css({'top': lMenuTop + 'px'});
        }
    });
</script>
<style>
    .shops-map-search {
        display: none;
    }
    <? if (empty($arItem['PROPERTIES']['MAP']['VALUE'])) { ?>
    .shops-map-c {
        display: none;
    }
    <? } ?>
</style>

<h1 class="page-title"><?= Loc::getMessage('SHOPS_TITLE'); ?></h1>

<div class="shops-c shop-one">

    <div class="row shop-info">
        <div class="col-md-4">
            <h3 class="shop-name"><?= $arItem['NAME_'.strtoupper($arParams['LANGUAGE_ID'])] ?></h3>
            <p class="shop-adress"><?= trim($arResult['PROPERTIES']['ADDRESS_' . strtoupper($arParams["LANGUAGE_ID"])]['VALUE']) ?><?if ($tradeCenter = $arResult['PROPERTIES']['TRADE_CENTER_' . strtoupper($arParams["LANGUAGE_ID"])]['VALUE']) {?>, <?=$tradeCenter?>
            <?}?></p>
        </div>
        <div class="col-md-8">
            <? if($arItem['PROPERTIES']['PHONE_' . strtoupper($arParams["LANGUAGE_ID"])]['VALUE']) { ?>
                <h3 class="shop-phone">
                    <a href="tel:<?=$arItem['PROPERTIES']['PHONE_' . strtoupper($arParams["LANGUAGE_ID"])]['VALUE'][0]?>">
                        <?=$arItem['PROPERTIES']['PHONE_' . strtoupper($arParams["LANGUAGE_ID"])]['VALUE'][0]?>
                    </a>
                </h3>
            <? } ?>
            <? if($arResult["DETAIL_TEXT"]) { ?>
                <p class="shop-desc">
                    <?= $arResult["DETAIL_TEXT"] ?>
                </p>
            <? } ?>
        </div>
    </div>

    <div class="shops-map-c">
        <div id="shops-map"></div>
        <div class="shops-map-search">
            <div class="wrap">
                <form class="form">
                    <div class="form-item">
                        <input type="text" class="form-control"
                               id="js-shops-map-search-input"
                               placeholder="Поиск">
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="row shop-images">
        <? if (is_array($arResult['PROPERTIES']['PHOTOS']['VALUE'])) { ?>
            <? foreach($arResult['PROPERTIES']['PHOTOS']['VALUE'] as $id_picture) { ?>
                <? $picture_src = CFile::GetPath($id_picture); ?>
                <div class="col col-12">
                    <img class="w100" src="<?= $picture_src ?>" alt="<?= $arResult['DETAIL_PICTURE']['ALT'] ?>"
                         title="<?= $arResult['DETAIL_PICTURE']['TITLE'] ?>"/>
                </div>
            <? } ?>
        <? } ?>
    </div>
</div>
