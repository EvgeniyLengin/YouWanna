<?php
/**
 * Created by PhpStorm.
 * Date: 03.02.2017
 * Time: 16:40
 *
 * @var array $arResult
 * @var array $arParams
 */

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

$arItem = $arResult;

$arPoint = array(
    'name'      => $arItem['PROPERTIES']['ADDRESS_' . strtoupper($arParams["LANGUAGE_ID"])]['VALUE'],
    'coords'    => explode(',', $arItem['PROPERTIES']['MAP']['VALUE']),
    'phone'     => $arItem['PROPERTIES']['PHONE_' . strtoupper($arParams["LANGUAGE_ID"])]['VALUE'],
    'time'      => $arItem['PROPERTIES']['WORK_HOURS_' . strtoupper($arParams["LANGUAGE_ID"])]['VALUE'],
    'url'       => $arItem['DETAIL_PAGE_URL'],
    'cityId'    => $arItem['IBLOCK_SECTION_ID'],
    'countryId' => $arItem['IBLOCK_SECTION_ID'],
);
$arResult['POINTS'][] = $arPoint;

if ($arParams['LANGUAGE_ID'] === 'ru') {
    if (empty($arResult['PREVIEW_TEXT'])) {
        $arResult['DETAIL_TEXT'] = "<!-- noindex -->" . $arResult['IBLOCK']['DESCRIPTION'] . "<!-- /noindex -->";
    } else {
        $arResult['DETAIL_TEXT'] = $arResult['PREVIEW_TEXT'];
    }
} else {
    if (empty($arResult['DETAIL_TEXT'])) {
        $arResult['DETAIL_TEXT'] = "<!-- noindex -->" . $arResult['IBLOCK']['DESCRIPTION'] . "<!-- /noindex -->";
    }
}

$resSection = CIBlockSection::GetList(
    array('SORT' => 'ASC'),
    array('IBLOCK_ID' => $arParams['IBLOCK_ID'], 'ID' => $arResult['IBLOCK_SECTION_ID']),
    false,
    array('NAME', 'UF_NAME')
);
if($temp = $resSection->GetNext()) {
    $arResult['SECTION_NAME'] = $arParams['LANGUAGE_ID'] === 'ru' ? $temp['NAME'] : $temp['UF_NAME'];
}

$_SESSION['ACTIVE_SHOP'] = $arResult['ID'];