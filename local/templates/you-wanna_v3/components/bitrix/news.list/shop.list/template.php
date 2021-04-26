<?php
/**
 * @var array $arResult
 * @var array $arParams
 */

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

# Подключение языковых фраз
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

$this->setFrameMode(true);

$lang = $arParams['LANGUAGE_ID'] === "ru" ? '' : '/en';
$templateData['CHAIN_ITEM'] = array();
$templateData['LIST_PAGE_URL'] = $arResult['LIST_PAGE_URL'];
if ($arParams['PARENT_SECTION_CODE'] && $arParams['PARENT_SECTION']) {
	$arPath = end($arResult['SECTION']['PATH']);
	if (!empty($arPath)) {
		$templateData['CHAIN_ITEM'] = array(
			'NAME' => $arPath['NAME'],
			'SECTION_PAGE_URL' => $arPath['SECTION_PAGE_URL']
		);
	}
}

$templateData['SHOP_LIST_TITLE'] = Loc::getMessage('SHOP_LIST_TITLE');

?>
<script>
    shopsPoints = <?=CUtil::PhpToJSObject($arResult['POINTS'])?>;
    var shopsFilter = <?=CUtil::PhpToJSObject($arResult['COUNTRIES'])?>;
</script>

<? if (empty($arParams['PARENT_SECTION_CODE']) && empty($arParams['PARENT_SECTION'])) { ?>
    <script>
        var shopsAllPoints = true;
    </script>
    <? if (!empty($arResult['COUNTRIES'])) { ?>
        <div class="shops-filter-c">
            <select class="form-control js-filter-select-country"
                    title="<?= Loc::getMessage('SHOP_LIST_COUNTRY'); ?>">
                <option value=""><?= Loc::getMessage('SHOP_LIST_COUNTRY'); ?></option>
                <? foreach ($arResult['COUNTRIES'] as $arCountry) { ?>
                    <option value="<?= $arCountry['ID'] ?>"><?= $arCountry['NAME'] ?></option>
                <? } ?>
            </select>
            <select class="form-control js-filter-select-city"
                    data-lang="<?= Loc::getMessage('SHOP_LIST_CITY'); ?>"
                    title="<?= Loc::getMessage('SHOP_LIST_CITY'); ?>" disabled="disabled">
                <option value=""><?= Loc::getMessage('SHOP_LIST_CITY'); ?></option>
            </select>
            <select class="form-control js-filter-select-metro"
                    data-lang="<?= Loc::getMessage('SHOP_LIST_METRO'); ?>"
                    title="<?= Loc::getMessage('SHOP_LIST_METRO'); ?>" disabled="disabled" style="display: none;">
                <option value=""><?= Loc::getMessage('SHOP_LIST_METRO'); ?></option>
            </select>
            <select class="form-control js-filter-select-shop"
                    data-lang="<?= Loc::getMessage('SHOP_LIST_SHOP'); ?>"
                    title="<?= Loc::getMessage('SHOP_LIST_SHOP'); ?>" disabled="disabled">
                <option value=""><?= Loc::getMessage('SHOP_LIST_SHOP'); ?></option>
            </select>
        </div>
    <? } ?>
    <div class="row">
        <div class="col-md-3">&nbsp;</div>
        <div class="col-md-6">
            <ul class="js-shops-search-result shops-search-result">
            </ul>
        </div>
        <div class="col-md-3">&nbsp;</div>
    </div>
    <div class="shops-c">
        <div class="cities-c">
            <? if (!empty($arResult['FIRST_LETTERS'])) { ?>
                <div class="letters">
                    <? foreach ($arResult['FIRST_LETTERS'] as $letter) { ?>
                        <a href="#" class="js-select-cities-letter"><?= $letter ?></a>
                    <? } ?>
                </div>
            <? } ?>
            <div class="row">
                <? foreach ($arResult['SECTIONS_BY_COLS'] as $arSectionsPart) { ?>
                    <div class="col-md-3">
                        <? foreach ($arSectionsPart as $arSection) { ?>
                            <div class="item">
                                <a class="js-select-cities-show-city-shops" data-city-id="<?= $arSection['ID'] ?>"
                                   data-letter="<?= $arSection['FIRST_LETTER'] ?>"
                                   href="/<?= $lang ?><?= $arSection['SECTION_PAGE_URL'] ?>">
                                    <?= $arSection['NAME'] ?></a>
                                <? if (!empty($arSection['ITEMS'])) { ?>
                                    <ul class="city-shops">
                                        <? foreach ($arSection['ITEMS'] as $arShop) { ?>
                                            <li class="city-shop">
                                                <a data-shop-id="<?= $arShop['ID'] ?>"
                                                   href="<?= $lang ?><?= $arShop['DETAIL_PAGE_URL'] ?>">
                                                    <?= $arShop['NAME'] ?></a>
                                            </li>
                                        <? } ?>
                                    </ul>
                                <? } ?>
                            </div>
                        <? } ?>
                    </div>
                <? } ?>
            </div>
        </div>
    </div>
<? } else { ?>
    <style>
        .shops-map-c .shops-map-search {
            display: none;
        }
    </style>
    <script>
        var shopsAllPoints = true;
    </script>
    <ul class="shops-menu shops-page">
        <li class="shop-item show-mobile"><a class="toggle"><?= Loc::getMessage('SELECT_SHOP') ?></a></li>
        <?$itemsQty = count($arResult['ITEMS'])?>
        <? foreach ($arResult['ITEMS'] as $arItem) { ?>
            <? if($arItem['ID'] != '1205') { ?>
            <?
                if($arParams['LANGUAGE_ID'] != 'ru') {
                    $arItem['NAME'] = $arItem['PROPERTIES']['NAME_'.strtoupper($arParams['LANGUAGE_ID'])]['VALUE'];
                }
            ?>
            <li class="shop-item hm">
                <a href="<?= $arItem['DETAIL_PAGE_URL'] ?>"><?= $arItem['NAME'] ?></a>
            </li>
            <? } ?>
        <? } ?>
    </ul>
<? } ?>
