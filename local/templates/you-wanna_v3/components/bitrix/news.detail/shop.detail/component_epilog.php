<?php
/**
 * @var array $arResult
 * @var array $arParams
 * @var array $templateData
 * @global CMain $APPLICATION
 */

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

# Подключение языковых фраз
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

if (!empty($templateData['SECTION_NAME'])) {
    
    $APPLICATION->SetTitle($templateData['SECTION_NAME'] . ', ' . Loc::getMessage('SHOP_DETAIL_EPILOG'));

}
$APPLICATION->SetPageProperty('title', $templateData['NAME']);
$APPLICATION->AddChainItem($templateData['NAME']);
?>
