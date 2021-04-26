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
<?if($_REQUEST['ajax']=='Y') $APPLICATION->RestartBuffer();?>
<div class="yw-trendblocks" data-ajax="list">
<?foreach($arResult["ITEMS"] as $arItem):?>
	<div class="yw-trendblocks__one" data-ajax="item">
	<?include  "blocks/" . $arItem['PROPERTIES']['LAYOUT']['VALUE_XML_ID'] . '.php';?>
	</div>
<?endforeach?>
	
</div>
<?if ($arParams['DISPLAY_BOTTOM_PAGER']):?>
    <?= $arResult['NAV_STRING']; ?>
<?endif?>
<?if($_REQUEST['ajax']=='Y') die();?>