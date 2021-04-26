<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}
/** @var CBitrixComponentTemplate $this */
/** @var array $arParams */
/** @var array $arResult */

if ($arResult['USER_VALS']['CONFIRM_ORDER'] === 'Y') {
    if (CModule::IncludeModule("sale")) {
        $db_props = CSaleOrderPropsValue::GetOrderProps($arResult['ORDER']['ID']);
        while ($arProps = $db_props->Fetch()) {
            if ($arProps['CODE'] === 'ADDRESS') {
                // адрес
                $address = $arProps['VALUE'];
                // вывод результата
                $addressID = $arProps['ID'];
            } elseif ($arProps['CODE'] === 'LOCATION') {
                // город
                $addressCity = CSaleLocation::GetByID($arProps['VALUE'], LANGUAGE_ID);
                $city = $addressCity["CITY_NAME"];
            }
        };

        if(trim($address) !== '' && trim($city) !== ''){
            $addressForMetro = $city.', '.$address;
            // получение координат адреса
            $coord = CYouWanna::getCoordNameByAddress($addressForMetro);
            // получение ближайшего метро по координатам
            $metro = CYouWanna::getMetroNameByCoord($coord);

            if (trim($metro) !== '' && !stristr($addressForMetro, $metro)) {
                CSaleOrderPropsValue::Update($addressID, array(
                        "VALUE" => $address . ' (' . $metro . ')'
                    )
                );
            }
        }
    };
};
if (CModule::IncludeModule("sale")) {
    $arLocs = CSaleLocation::GetByID($arResult['USER_VALS']['DELIVERY_LOCATION'], LANGUAGE_ID);
    if (intval($arLocs['COUNTRY_ID']) === 104) {
        $arResult['NORUS'] = false;
    } else {
        $arResult['NORUS'] = true;
    }
}