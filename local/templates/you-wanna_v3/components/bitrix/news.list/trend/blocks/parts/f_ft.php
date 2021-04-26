<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
$photo = CFile::ResizeImageGet($arItem['PROPERTIES']['PIC1']['VALUE'], array("width"=>680, "height"=>800), BX_RESIZE_IMAGE_EXACT);
$photo2 = CFile::ResizeImageGet($arItem['PROPERTIES']['PIC2']['VALUE'], array("width"=>680, "height"=>800), BX_RESIZE_IMAGE_EXACT);
?>
<a class="yw-trendblock__item" href="<?=$arItem['PROPERTIES']['LINK']['VALUE']?>">
	<div class="yw-trendblock__foto">
		<div class="img" style="background-image: url(<?=$photo['src']?>)"></div>
	</div>
</a>
<a class="yw-trendblock__item" href="<?=$arItem['PROPERTIES']['LINK']['VALUE']?>">
	<div class="yw-trendblock__fototext">
		<div class="img" style="background-image: url(<?=$photo2['src']?>)"></div>
		<div class="text">
			<div class="title"><?=$arItem['PROPERTIES']['PIC2_TITLE']['VALUE']?></div>
			<div class="desc"><?$arItem['PROPERTIES']['PIC2_DESC']['~VALUE']['TEXT']?></div>
		</div>
	</div>
</a>
