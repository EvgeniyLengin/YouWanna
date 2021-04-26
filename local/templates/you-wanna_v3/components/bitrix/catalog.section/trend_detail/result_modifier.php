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
			
			$min_price = $offer['PRICES']['BASE']['DISCOUNT_VALUE'];
			if(
				$min_price > $offer['PRICES']['BASE']['DISCOUNT_VALUE'] || 
				!$arOffers[$color_name]['DISCOUNT_VALUE']
			) {
				$min_price = $offer['PRICES']['BASE']['DISCOUNT_VALUE'];
				$arOffers[$color_name]['NAME'] = $offer['NAME'];
				$arOffers[$color_name]['DISCOUNT_VALUE'] = $offer['PRICES']['BASE']['PRINT_DISCOUNT_VALUE'];
				$arOffers[$color_name]['VALUE_VAT'] = $offer['PRICES']['BASE']['PRINT_VALUE_VAT'];
				$arOffers[$color_name]['DISCOUNT_DIFF_PERCENT'] = $offer['PRICES']['BASE']['DISCOUNT_DIFF_PERCENT'];
			}
		}
        foreach ($arOffersSlider as $color => $arOfferSlider) {
            foreach ((array)$arOfferSlider['PICTURES'] as $arPicture) {
                $resizeImage = CFile::ResizeImageGet(
                    CFile::GetFileArray($arPicture['ID']),
                    [
                        'width'  => $imgWidth,
                        'height' => $imgHeight
                    ],
                    BX_RESIZE_IMAGE_PROPORTIONAL,
                    false
                );
                $arResult['ITEMS'][$key]['SLIDER'][$color][] = $resizeImage['src'];
				$arOffers[$color]['PREVIEW_PICTURE'][] = $resizeImage['src'];
            }
			
			
			
            if ($arOfferSlider['SKU_IMAGE']) {
                $arResult['ITEMS'][$key]['COLORS']['IMAGES'][$color] = $arOfferSlider['SKU_IMAGE'];
            }
            if ($arOfferSlider['SKU_HEX']) {
                $arResult['ITEMS'][$key]['COLORS']['HEX'][$color] = $arOfferSlider['SKU_HEX'];
            }
           
			$arResult['ITEMS'][$key]['COLORS'][$color]['DATA'] = $arOffers[$color];
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
