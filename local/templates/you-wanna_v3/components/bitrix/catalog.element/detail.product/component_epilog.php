<?php
/**
 * @var CMain $APPLICATION
 * @var array $arParams
 * @var array $arResult
 */

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

if ($templateData['AJAX_HTML']) {
    $APPLICATION->RestartBuffer();
    echo $templateData['AJAX_HTML'];
    die();
}

//if($arResult['SECTION']['CODE'] && $arResult['ID']) {
if($arResult['ID']) {
    global $recFilter;
    $recFilter =array (
        'ACTIVE'     => 'Y',
        '!ID'        => $arResult['ID'],
        //'=SECTION_CODE' => $arResult['SECTION']['CODE']
    );
}