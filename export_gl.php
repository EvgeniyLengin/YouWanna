<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("экспорт");
$yvalue = 3;
$title_site = 'YOU WANNA - Trending Clothes for Women';
$data_update = date('Y-m-dTH:i:00');
$content = '<?xml version="1.0"?>
<feed xmlns="http://www.w3.org/2005/Atom" xmlns:g="http://base.google.com/ns/1.0">
<title>'.$title_site.'</title>
<link rel="self" href="https://you-wanna.ru"/>
<updated>'.$data_update.'</updated> ';


$arSelect = Array("ID", "NAME","PREVIEW_PICTURE","DETAIL_PICTURE",);
$arFilter = Array("IBLOCK_ID"=>IntVal($yvalue), "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y");
$res = CIBlockElement::GetList(Array(), $arFilter, false, Array("nPageSize"=>50000), $arSelect);

while($ob = $res->GetNextElement())
{
 $arFields = $ob->GetFields();

$pictureId = $arFields['PREVIEW_PICTURE'];
if($pictureId == "") {
    continue;
}
//  echo "<pre>";
// print_r($arFields);
// echo "</pre>";
$ParrentOrderId = $arFields['ID'];
$urlImage = CFile::GetPath($pictureId);



$arOffersFilter =array(
"IBLOCK_ID"=>$yvalue, // ID торгового каталога
);
$arOffersOrder = array("sort"=>"asc","id"=>"desc");
$arSelectFields = array("CATALOG_GROUP_1","DETAIL_PAGE_URL","DETAIL_PICTURE","NAME");
$arSelectProps = array(
    "CATALOG_GROUP_1",
    "DETAIL_PAGE_URL",
    "DETAIL_PICTURE",
    "COLOR",
    "SIZE",
    "NAME",


);
$productID = $ParrentOrderId;
$arOffersLimit = 0;
$arOffersPrices = array();
$arVat = false;


$arOffers = CIBlockPriceTools::GetOffersArray(
    $arOffersFilter,
    array($productID), // ID товара из основного каталога
    $arOffersOrder,
    $arSelectFields,
    $arSelectProps,
    $arOffersLimit,
    $arPrices,
    $arVat

);
//
// echo "<pre>";
//     print_r($arOffers);
// echo "</pre>";

$countChildElements = count($arOffers);
if ($countChildElements == 0) {
    continue;
}


foreach ($arOffers as $key => $value) {
$content .='
<entry>
<g:id>'.$value['ID'].'</g:id>
<g:title>'.$value['NAME'].'</g:title>
<g:description>Одежда для современной городской женщины. Собраны все актуальные сезонные тренды. Коллекции создаются с учетом тенденций fashion-индустрии.</g:description>
<g:link>https://you-wanna.ru'.$value['DETAIL_PAGE_URL'].'</g:link>
<g:image_link>https://you-wanna.ru'.$urlImage.'</g:image_link>
<g:condition>new</g:condition>
<g:availability>in stock</g:availability>
<g:price>'.$value['CATALOG_PRICE_1'].' RUB</g:price>
<g:mpn>YOU WANNA</g:mpn>
<g:color>'.$value['DISPLAY_PROPERTIES']['COLOR']['DISPLAY_VALUE'].'</g:color>
<g:size>'.$value['PROPERTIES']['SIZE']['VALUE'].'</g:size>
</entry>';

}



// dump($arOffers);




}

// ВЫХОД ИЗ ПЕРЕБОРА


$content .= '</feed>';


$file = $_SERVER["DOCUMENT_ROOT"].'/formerchant.xml';
// print_r($file);
$putcontent = file_put_contents($file, $content);

if ($putcontent) {
    echo "Фид обновлен";
} else {
    echo "Произошла какая-то ошибка. Пожалуйста, обратитесь к разработчику";
}

?>
<? require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
