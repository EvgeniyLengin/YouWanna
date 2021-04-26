<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<a class="yw-trendblock__item" href="<?=$arItem['PROPERTIES']['LINK']['VALUE']?>">
	<div class="yw-trendblock__fototext">
		<div class="img" style="background-image: url(<?=CFile::GetPath($arItem['PROPERTIES']['PIC1']['VALUE'])?>)"></div>
		<div class="text">
			<div class="title"><?=CYouWanna::multiTranslateAlt($arItem['PROPERTIES']['PIC1_TITLE']['VALUE'], $arItem['PROPERTIES']['PIC1_TITLE_EN']['VALUE'], LANGUAGE_ID)?></div>
			<div class="desc"><?=CYouWanna::multiTranslateAlt($arItem['PROPERTIES']['PIC1_DESC']['~VALUE']['TEXT'], $arItem['PROPERTIES']['PIC1_DESC_EN']['~VALUE']['TEXT'], LANGUAGE_ID)?></div>
		</div>
	</div>
</a>
<a class="yw-trendblock__item" href="<?=$arItem['PROPERTIES']['LINK']['VALUE']?>">
	<div class="yw-trendblock__foto">
		<div class="img" style="background-image: url(<?=CFile::GetPath($arItem['PROPERTIES']['PIC2']['VALUE'])?>)"></div>
	</div>
</a>