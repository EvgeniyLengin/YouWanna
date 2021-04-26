<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);
?>
<?foreach($arResult["ITEMS"] as $arItem):
	if ($arItem['PROPERTIES']['LAYOUT']['VALUE_XML_ID'] == 'block' && $arItem['PROPERTIES']['SLIDER']['VALUE'] == '') continue;
	include  "blocks/" . $arItem['PROPERTIES']['LAYOUT']['VALUE_XML_ID'] . '.php';
endforeach?>