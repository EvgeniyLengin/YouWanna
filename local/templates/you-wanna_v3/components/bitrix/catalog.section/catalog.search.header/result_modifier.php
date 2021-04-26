<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

if(0 !== count($arResult['ITEMS'])) {
    foreach ((array)$arResult['ITEMS'] as &$arItem) {
        $resizeImage = CFile::ResizeImageGet(
            $arItem['PREVIEW_PICTURE'],
            [
                'width'  => 194,
                'height' => 240
            ],
            BX_RESIZE_IMAGE_EXACT,
            false
        );
        $arItem['PREVIEW_PICTURE']['SRC'] = $resizeImage['src'];

        $arOffersSlider = CYouWanna::getSliderForOffer($arItem['OFFERS']);
        if (0 !== count($arOffersSlider)) {
            foreach ($arOffersSlider as $color => $arOfferSlider) {
                if ($arOfferSlider['SKU_HEX']) {
                    $arItem['COLORS']['HEX'][$color] = $arOfferSlider['SKU_HEX'];
                }
            }
        }

    }
    unset($arItem);
}