<?php
/**
 * Модификация результата
 *
 * @var array $arParams
 * @var array $arResult
 * @var CMain $APPLICATION
 */
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

/*foreach ( $arResult['ITEMS'] as $key => $arItem ) { // получение фотографий торговых предложений для вывода в слайдере
    foreach ($arItem['OFFERS'] as $arOffer) {
        if (isset($arOffer['PREVIEW_PICTURE']['SRC']) && $arOffer['PREVIEW_PICTURE']['SRC'] !== '') {
            $arResult['ITEMS'][$key]['OFFER_PICTURES'][] = $arOffer['PREVIEW_PICTURE']['SRC'];
        }
    }
};*/
$isRV = $arParams['IS_RV'];

foreach ( $arResult['ITEMS'] as $key => $arItem ) { // получение фотографий торговых предложений для вывода в слайдере
    $arOffersSlider = CYouWanna::getSliderForOffer(
        $arItem['OFFERS'],
        $arItem['PROPERTIES']['MORE_PHOTO']['VALUE'],
        $arItem['DETAIL_PICTURE']
    );

    if($arParams['NO_CATALOG'] == 'Y') {
        $imgWidth = 372;
        $imgHeight = 450;
    } else {
        $imgWidth = 800;
        $imgHeight = 800;

    }
    if (0 !== count($arOffersSlider)) {
		$arOffers = array();
		foreach($arItem['OFFERS'] as $offer) {
			$color_name = $offer["PROPERTIES"]["COLOR"]["VALUE"];
			
			$res = CIBlockElement::GetByID($offer['ID']);
			if($ar_res = $res->GetNext()) {
				$offer["NAME"] = $ar_res['NAME'];
			}
		}
        foreach ($arOffersSlider as $color => $arOfferSlider) {

            if ($arOfferSlider['SKU_HEX']) {
                $arResult['ITEMS'][$key]['COLORS']['HEX'][$color] = $arOfferSlider['SKU_HEX'];
            }
        }
    }

    unset($arOffersSlider);
}

if($isRV == 'Y') {
	$RecentlyViewed = array_flip(json_decode($_COOKIE["RecentlyViewed"],true));
	usort($arResult['ITEMS'], function($a,$b) use($RecentlyViewed){
        return $RecentlyViewed[$b['ID']] - $RecentlyViewed[$a['ID']];
    });
}
