<?php
/**
 * @var array $arResult
 * @var array $arParams
 * @global CMain $APPLICATION
 */

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

if (!empty($templateData['CHAIN_ITEM'])) {

    $APPLICATION->AddChainItem(
        $templateData['SHOP_LIST_TITLE'],
        '/' . LANGUAGE_ID . $templateData['LIST_PAGE_URL']
    );

    $APPLICATION->AddChainItem(
	    $templateData['CHAIN_ITEM']['NAME'],
        '/' . LANGUAGE_ID . $templateData['CHAIN_ITEM']['SECTION_PAGE_URL']
    );

} else {

    $APPLICATION->AddChainItem($templateData['SHOP_LIST_TITLE']);

}