<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

$sliderOffers = [];
$sliderOffer = [];

foreach ($arResult['BASKET'] as $basketItem) {

    $arSelect = Array("ID", "NAME", "DATE_ACTIVE_FROM", "PROPERTY_CML2_ARTICLE");
    $arFilter = Array(
        "IBLOCK_ID"   => CYouWanna::getIblockIdByCode(CYouWanna::IBLOCK_CODE_CATALOG),
        "ID"          => $basketItem['PARENT']['ID'],
        "ACTIVE_DATE" => "Y",
        "ACTIVE"      => "Y"
    );
    $res = CIBlockElement::GetList(Array(), $arFilter, false, Array("nPageSize" => 50), $arSelect);
    while ($ob = $res->GetNextElement()) {
        $arFields = $ob->GetFields();
        $basketItem['PARENT_ARTICLE'] = $arFields['PROPERTY_CML2_ARTICLE_VALUE'];
    }


    foreach ($basketItem['PROPS'] as $itemProp) {
        if ($itemProp['CODE'] === 'COLOR') {
            $sliderOffer = [];
            $sliderOffer['PROPERTIES']['COLOR'] = $itemProp;
            $sliderOffer['PROPERTIES']['COLOR']['VALUE'] = $itemProp['SKU_VALUE']['XML_ID'];
            $sliderOffer['PROPERTIES']['COLOR']['USER_TYPE_SETTINGS']['TABLE_NAME'] = 'colorreference';
            $sliderOffers[] = $sliderOffer;
        }
    }
}

$arOffersSlider = CYouWanna::getSliderForOffer($sliderOffers);

if (0 !== count($arOffersSlider)) {
    foreach ($arOffersSlider as $color => $arOfferSlider) {
        if ($arOfferSlider['SKU_HEX']) {
            $arResult['COLORS_HEX'][$color] = $arOfferSlider['SKU_HEX'];
        }
    }
}


/*$rsUser = CUser::GetByID($arResult['USER']['ID']);
$arUser = $rsUser->Fetch();

$rsContact = CUserFieldEnum::GetList(array(), array(
    "ID" => $arUser['UF_CONTACT_NAME'],
));
if ($arContact = $rsContact->GetNext()) {
    $arResult['CONTACT_NAME'] = $arContact["VALUE"];
}
*/

?>