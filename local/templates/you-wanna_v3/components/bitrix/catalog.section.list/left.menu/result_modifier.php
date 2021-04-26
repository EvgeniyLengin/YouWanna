<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(in_array($arParams['ACTIVE_SECTION'], ['NEW_YEAR', 'NEW_PRODUCT', 'COMING_SON', 'SALE','NEW_YEAR_PRODUCT'])) {
    if($arParams['ACTIVE_SECTION'] === 'SALE') {
        $сache = Bitrix\Main\Data\Cache::createInstance();
        $cacheTime = 3600;
        $cacheId = CYouWanna::IBLOCK_ID_CATALOG;
        $cacheDir = 'saleElementsId';

        if ($сache->initCache($cacheTime, $cacheId, $cacheDir)) {
            $ar_res = $сache->getVars();
        } elseif ($сache->startDataCache()) {
            $ar_res = array();

            if (CModule::IncludeModule("catalog")) {
                $IBLOCK_ID = CYouWanna::IBLOCK_ID_CATALOG;
                $arInfo = CCatalogSKU::GetInfoByProductIBlock($IBLOCK_ID);
                $ar_res = array();
                if (is_array($arInfo)) {
                    $rsOffers = CIBlockElement::GetList(array(),
                        array(
                            'IBLOCK_ID' => $arInfo['IBLOCK_ID'],
                            'ACTIVE'    => 'Y'
                        )
                    );
                    while ($arOffer = $rsOffers->GetNext()) {
                        $disc = CCatalogDiscount::GetDiscount(
                            $arOffer['ID'],
                            4
                        );
                        if (!empty($disc)) {
                            $elementId = CCatalogSku::GetProductInfo(
                                $arOffer['ID']
                            );
                            if (!in_array($elementId['ID'], $ar_res)) {
                                $ar_res[] = $elementId['ID'];
                            }
                        }
                    }
                }
            }

            if (empty($ar_res)) {
                $cache->abortDataCache();
            }

            $сache->endDataCache($ar_res);
        }

        $arFilter = array(
            'ID' => $ar_res
        );
    }
    else {
        $arFilter = [
            'PROPERTY_'.$arParams['ACTIVE_SECTION'].'_VALUE' => 'Да'
        ];
    }

    $products = CIBlockElement::GetList([], $arFilter, false, [], $arSelect);
}

if($products) {
    $arResult["SECTIONS"] = [];

    while($product = $products->GetNext()) {
        $section = CIBlockSection::GetByID($product['IBLOCK_SECTION_ID'])->GetNext();
        if(!in_array($section, $arResult["SECTIONS"])) $arResult["SECTIONS"][] = $section;
    }
}
?>
