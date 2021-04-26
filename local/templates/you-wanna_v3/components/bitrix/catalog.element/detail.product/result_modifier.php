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

if (0 !== count($arResult['OFFERS'])) {

    // Получаем слайдер и лоскутки для Торговых предложений
    $arResult['SLIDER'] = [];
    $arOffersSlider = CYouWanna::getSliderForOffer(
        $arResult['OFFERS'],
        $arResult['PROPERTIES']['MORE_PHOTO']['VALUE'],
        $arResult['DETAIL_PICTURE']
    );

    if (0 !== count($arOffersSlider)) {
        foreach ($arOffersSlider as $color => $arOfferSlider) {
            foreach ((array)$arOfferSlider['PICTURES'] as $arPicture) {
                /*$resizeSmallImage = CFile::ResizeImageGet(
                    CFile::GetFileArray($arPicture['ID']),
                    [
                        'width'  => 800,
                        'height' => 800
                    ],
                    BX_RESIZE_IMAGE_PROPORTIONAL,
                    false
                );*/
                if($arParams['IS_AJAX'] === 'Y'){
                    $resizeBigImage = CFile::ResizeImageGet(
                        CFile::GetFileArray($arPicture['ID']),
                        [
                            'width'  => 500,
                            'height' => 700
                        ],
                        BX_RESIZE_IMAGE_PROPORTIONAL,
                        false
                    );
                } else {
                    $resizeBigImage = CFile::ResizeImageGet(
                        CFile::GetFileArray($arPicture['ID']),
                        [
                            'width'  => 3500,
                            'height' => 3500
                        ],
                        BX_RESIZE_IMAGE_PROPORTIONAL,
                        false
                    );
                }
                $arResult['SLIDER_BIG'][$color][] = $resizeBigImage['src'];
                //$arResult['SLIDER'][$color][] = $resizeSmallImage['src'];
            }
            if ($arOfferSlider['SKU_IMAGE']) {
                $arResult['COLORS']['IMAGES'][$color] = $arOfferSlider['SKU_IMAGE'];
            }
            if ($arOfferSlider['SKU_HEX']) {
                $arResult['COLORS']['HEX'][$color] = $arOfferSlider['SKU_HEX'];
            }
            $arResult['COLORS_NAME'][$color] = $arOfferSlider['COLOR_NAME'];
        }
    }

    // Пересобираем массив Торговых предложений
    foreach ((array)$arResult['OFFERS'] as $offer) {
        $arResult['NEW_OFFERS'][$offer['PROPERTIES']['COLOR']['VALUE']]['NAME'] = $arResult['COLORS_NAME'][$offer['PROPERTIES']['COLOR']['VALUE']];
        $arResult['NEW_OFFERS'][$offer['PROPERTIES']['COLOR']['VALUE']]['ITEMS'][$offer['PROPERTIES']['SIZE']['VALUE']] = $offer;
    }
    unset($arResult['OFFERS']);

    // Получаем ссылки на Следующий/Предыдущий товар
    $rsElement = CIBlockElement::GetList(
        [
            'SORT' => 'ASC',
        ],
        [
            'IBLOCK_ID'         => $arResult['IBLOCK_ID'],
            'SECTION_ID'        => $arResult['IBLOCK_SECTION_ID'],
            'CATALOG_AVAILABLE' => 'Y',
            'ACTIVE'            => 'Y',
        ],
        false,
        [
            'nPageSize'  => 1,
            'nElementID' => $arResult['ID'],
        ],
        [
            'ID',
            'IBLOCK_ID',
            'NAME',
            'DETAIL_PAGE_URL'
        ]
    );
    $arItems = [];
    while ($obElement = $rsElement->GetNext()) {
        $arItems[] = $obElement;
    }

    $arResult['LEFT_ELEMENT'] = [];
    $arResult['RIGHT_ELEMENT'] = [];
    if (count($arItems) === 3) {
        $arResult['RIGHT_ELEMENT'] = [
            'NAME' => $arItems[0]['NAME'],
            'URL'  => $arItems[0]['DETAIL_PAGE_URL']
        ];
        $arResult['LEFT_ELEMENT'] = [
            'NAME' => $arItems[2]['NAME'],
            'URL'  => $arItems[2]['DETAIL_PAGE_URL']
        ];
    } elseif (count($arItems) === 2) {
        if ($arItems[0]['ID'] !== $arResult['ID']) {
            $arResult['RIGHT_ELEMENT'] = [
                'NAME' => $arItems[0]['NAME'],
                'URL'  => $arItems[0]['DETAIL_PAGE_URL']
            ];
        } else {
            $arResult['LEFT_ELEMENT'] = [
                'NAME' => $arItems[1]['NAME'],
                'URL'  => $arItems[1]['DETAIL_PAGE_URL']
            ];
        }
    }
    if($arParams['LANGUAGE_ID'] !== 'ru') {
        $arResult['DETAIL_TEXT'] = $arResult['PROPERTIES']['DETAIL_TEXT_'.strtoupper(LANGUAGE_ID)]['VALUE']['TEXT'];
    }

}

// Выведем актуальную корзину для текущего пользователя

$arBasketItems = array();

$dbBasketItems = CSaleBasket::GetList(
    array(
        "NAME" => "ASC",
        "ID" => "ASC"
    ),
    array(
        "FUSER_ID" => CSaleBasket::GetBasketUserID(),
        "LID" => SITE_ID,
        "ORDER_ID" => "NULL"
    ),
    false,
    false,
    array("ID", "CALLBACK_FUNC", "MODULE",
        "PRODUCT_ID", "QUANTITY", "DELAY",
        "CAN_BUY", "PRICE", "WEIGHT")
);
while ($arItems = $dbBasketItems->Fetch())
{
    if (strlen($arItems["CALLBACK_FUNC"]) > 0)
    {
        CSaleBasket::UpdatePrice($arItems["ID"],
            $arItems["CALLBACK_FUNC"],
            $arItems["MODULE"],
            $arItems["PRODUCT_ID"],
            $arItems["QUANTITY"]);
        $arItems = CSaleBasket::GetByID($arItems["ID"]);
    }

    $arBasketItems[] = $arItems;
}
$arResult['IN_BASKET'] = '';
foreach ($arResult['NEW_OFFERS'] as $offer){
    foreach ($offer['ITEMS'] as $offerItem){
        foreach($arBasketItems as $basket) {
            if (intval($basket['PRODUCT_ID']) === intval($offerItem['ID']) && $arResult['IN_BASKET'] === '') {
                $arResult['IN_BASKET'] = 'Y';
            }
        }
    }
}

$RecentlyViewed = json_decode($_COOKIE["RecentlyViewed"],true);
$RecentlyViewed[time()] = $arResult['ID'];
$RecentlyViewed = array_unique($RecentlyViewed);
$RecentlyViewed = array_slice($RecentlyViewed, -9, 9);

setcookie("RecentlyViewed", json_encode($RecentlyViewed), time()+3600*24*7, "/");

$arResult['RA_STORES'] = Redaper\ImportFrom1C::getStores();
