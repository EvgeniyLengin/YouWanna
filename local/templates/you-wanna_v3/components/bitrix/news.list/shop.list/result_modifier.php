<?php
/**
 * Created by PhpStorm.
 * Date: 22.12.2016
 * Time: 23:52
 *
 * @var array $arResult
 * @var array $arParams
 */

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

CModule::IncludeModule('iblock');

$arResult['FIRST_LETTERS'] = array();
$arResult['SECTIONS'] = array();
$arResult['POINTS'] = array();

$arShopsCountries = array();

$arSectionsId = array();
$arSectionsId[0] = 0;
foreach ($arResult['ITEMS'] as &$arItem) {
    $arShopsCountries[$arItem['ID']] = '';
    $arSectionsId[$arItem['IBLOCK_SECTION_ID']] = $arItem['IBLOCK_SECTION_ID'];

	$tradeCenter = $arItem['PROPERTIES']['TRADE_CENTER_' . strtoupper($arParams["LANGUAGE_ID"])]['VALUE'];
	if ($tradeCenter) {
		$tradeCenter = ', '.$tradeCenter;
	}
	$arItem['PROPERTIES']['ADDRESS_' . strtoupper($arParams["LANGUAGE_ID"])]['VALUE'] .= $tradeCenter;
}
unset($arItem);

$dbSections = CIBlockSection::GetList(
    array(
        'SORT' => 'ASC',
        'NAME' => 'ASC',
    ),
    array(
        'IBLOCK_ID' => $arParams['IBLOCK_ID'],
        'ID'        => $arSectionsId,
    ),
    false,
    array(
        'UF_NAME'
    )
);
while ($arSection = $dbSections->GetNext()) {
    $arSection['ITEMS'] = array();
    $arSection['NAME'] = $arParams['LANGUAGE_ID'] === 'ru' ? $arSection['NAME'] : $arSection['UF_NAME'];
    $arResult['SECTIONS']['' . $arSection['ID']] = $arSection;
}

foreach ($arResult['SECTIONS'] as &$arSection) {
    $firstLetter = substr($arSection['NAME'], 0, 1);
    $arResult['FIRST_LETTERS'][$firstLetter] = $firstLetter;
    $arSection['FIRST_LETTER'] = $firstLetter;
}
unset($arSection);

/*
 * распределяем магазины по разделам-городам
 */
foreach ($arResult['ITEMS'] as $arItem) {
    if (!empty($arResult['SECTIONS']['' . $arItem['IBLOCK_SECTION_ID']])) {
        $arItem['NAME'] = $arItem['PROPERTIES']['ADDRESS_' . strtoupper($arParams["LANGUAGE_ID"])]['VALUE'];
        $arResult['SECTIONS']['' . $arItem['IBLOCK_SECTION_ID']]['ITEMS'][] = $arItem;
    }
}

$size = ceil(0.5 + count($arResult['SECTIONS']) / 4.0);
$arResult['SECTIONS_BY_COLS'] = array_chunk($arResult['SECTIONS'], $size);

$arResult['LIST_PAGE_URL'] = $arResult['SECTION']['PATH'][0]['LIST_PAGE_URL'];

/**
 * выборка стран
 */

$dbSections = CIBlockSection::GetList(
    array(
        'SORT' => 'ASC',
        'NAME' => 'ASC',
    ),
    array(
        'IBLOCK_ID'   => $arParams['IBLOCK_ID'],
        'ACTIVE'      => 'Y',
        'DEPTH_LEVEL' => '1'
    ),
    false,
    array(
        'UF_NAME'
    )
);
while ($arCountry = $dbSections->Fetch()) {
    $arCountry['CITIES'] = array();
    $arResult['COUNTRIES']['' . $arCountry['ID']] = array(
        'ID'     => $arCountry['ID'],
        'NAME'   => $arParams['LANGUAGE_ID'] === 'ru' ? $arCountry['NAME'] : $arCountry['UF_NAME'],
        'CITIES' => array()
    );
}

foreach ($arResult['SECTIONS'] as $arSection) {
    $parentId = '' . $arSection['IBLOCK_SECTION_ID'];
    if ($parentId) {
        if (!empty($arResult['COUNTRIES'][$parentId])) {
            $arMetro = array();
            foreach ($arSection['ITEMS'] as $cityKey => $arCity) {
                $arShopsCountries[$arCity['ID']] = $parentId;
                if ($metro = $arCity['PROPERTIES']['METRO_' . strtoupper($arParams["LANGUAGE_ID"])]['VALUE']) {
                    if (empty($arMetro[$metro])) {
                        $arMetro[$metro] = array(
                            'ID'    => $metro,
                            'NAME'  => $metro,
                            'SHOPS' => array()
                        );
                        $arMetro[$metro]['SHOPS']['' . $arCity['ID']] = array(
                            'ID'              => $arCity['ID'],
                            'NAME'            => $arCity['PROPERTIES']
                            ['ADDRESS_' . strtoupper($arParams["LANGUAGE_ID"])]['VALUE'],
                            'DETAIL_PAGE_URL' => '/' . $arParams['LANGUAGE_ID'] . $arCity['DETAIL_PAGE_URL']
                        );
                    }
                }
            }

            $arShops = array();
            foreach ($arSection['ITEMS'] as $arCity) {
                $arShops['' . $arCity['ID']] = array(
                    'ID'              => $arCity['ID'],
                    'NAME'            => $arCity['PROPERTIES']
                    ['ADDRESS_' . strtoupper($arParams["LANGUAGE_ID"])]['VALUE'],
                    'DETAIL_PAGE_URL' => '/' . $arParams['LANGUAGE_ID'] . $arCity['DETAIL_PAGE_URL']
                );
            }
            $arResult['COUNTRIES'][$parentId]['CITIES']['' . $arSection['ID']] = array(
                'ID'    => $arSection['ID'],
                'NAME'  => $arParams['LANGUAGE_ID'] === 'ru' ? $arSection['NAME'] : $arSection['UF_NAME'],
                'SHOPS' => $arShops,
                'METRO' => $arMetro
            );
        }
    }
}

foreach ($arResult['COUNTRIES'] as $key => $arSection) {//убрать страны без городов
    if (empty($arSection['CITIES'])) {
        unset($arResult['COUNTRIES'][$key]);
    }
}


foreach ($arResult['ITEMS'] as $arItem) {
    $arPoint = array(
        'shopId'    => $arItem['ID'],
        'name'      => $arItem['PROPERTIES']['ADDRESS_' . strtoupper($arParams["LANGUAGE_ID"])]['VALUE'],
        'coords'    => explode(',', $arItem['PROPERTIES']['MAP']['VALUE']),
        'phone'     => $arItem['PROPERTIES']['PHONE_' . strtoupper($arParams["LANGUAGE_ID"])]['VALUE'],
        'time'      => $arItem['PROPERTIES']['WORK_HOURS_' . strtoupper($arParams["LANGUAGE_ID"])]['VALUE'],
        'url'       => '/' . $arParams['LANGUAGE_ID'] . $arItem['DETAIL_PAGE_URL'],
        'cityId'    => $arItem['IBLOCK_SECTION_ID'],
        'countryId' => $arShopsCountries[$arItem['ID']],
        'placemark' => false
    );
    $arResult['POINTS'][] = $arPoint;
}