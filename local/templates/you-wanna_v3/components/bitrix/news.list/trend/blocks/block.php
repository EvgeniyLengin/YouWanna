<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="yw-trendblock">
	<div class="yw-trendblock__wrap<?if($arItem['PROPERTIES']['LAYOUT_FOTO']['VALUE_XML_ID'] != 's' && $arItem['PROPERTIES']['LAYOUT_FOTO']['VALUE_XML_ID'] != 'st') {?> js-trendslide<?}?>">
		<?include  "parts/" . $arItem['PROPERTIES']['LAYOUT_FOTO']['VALUE_XML_ID'] . '.php';?>
	</div>
</div>