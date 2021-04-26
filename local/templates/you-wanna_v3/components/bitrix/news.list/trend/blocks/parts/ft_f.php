<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
$photo = CFile::ResizeImageGet($arItem['PROPERTIES']['PIC1']['VALUE'], array("width"=>680, "height"=>800), BX_RESIZE_IMAGE_EXACT);
$photo2 = CFile::ResizeImageGet($arItem['PROPERTIES']['PIC2']['VALUE'], array("width"=>680, "height"=>800), BX_RESIZE_IMAGE_EXACT);
?>
<a class="yw-trendblock__item" href="<?=$arItem['PROPERTIES']['LINK']['VALUE']?>">
	<div class="yw-trendblock__fototext">
		<div class="img" style="background-image: url(<?=$photo['src']?>)"></div>
		<div class="text">
			<div class="title"><?=CYouWanna::multiTranslateAlt($arItem['PROPERTIES']['PIC1_TITLE']['VALUE'], $arItem['PROPERTIES']['PIC1_TITLE_EN']['VALUE'], LANGUAGE_ID, false, true)?></div>
			<div class="desc"><?=CYouWanna::multiTranslateAlt($arItem['PROPERTIES']['PIC1_DESC']['~VALUE']['TEXT'], $arItem['PROPERTIES']['PIC1_DESC_EN']['~VALUE']['TEXT'], LANGUAGE_ID, false, true)?></div>
		</div>
	</div>
</a>
<a class="yw-trendblock__item" href="<?=$arItem['PROPERTIES']['LINK']['VALUE']?>">
	<div class="yw-trendblock__foto">
		<div class="img" style="background-image: url(<?=$photo2['src']?>)"></div>
	</div>
</a>