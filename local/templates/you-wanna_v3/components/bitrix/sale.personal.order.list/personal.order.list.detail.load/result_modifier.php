<?if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}
global $USER;
$arFilter = Array(
    "USER_ID" => $USER->GetID()
);

$db_sales = CSaleOrder::GetList(array("DATE_INSERT" => "ASC"), $arFilter, array('nPageSize' => 10));
$db_sales->NavStart();

$arResult['navCountPage'] = $db_sales->NavPageSize;
$arResult['navPage'] = $db_sales->NavPageNomer;
$arResult['navCountItem'] = $db_sales->NavRecordCount;
$arResult['navSize'] = $db_sales->NavPageSize;
$arResult['navItemsLeft'] = $arResult['navCountItem'] - ($arResult['navPage'] * $arResult['navSize']);
$arResult['navNum'] = $db_sales->NavNum;
if ($arResult['navItemsLeft'] < $arResult['navSize']) {
    $arResult['navSize'] = $arResult['navItemsLeft'];
}
?>