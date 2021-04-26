<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<a class="yw-trendblock__item fullwidth" href="<?=$arItem['PROPERTIES']['LINK']['VALUE']?>">
	<div class="yw-trendblock__fototext">
		<div class="img" style="background-image: url(<?=CFile::GetPath($arItem['PROPERTIES']['SINGLEPIC']['VALUE'])?>)"></div>
		<div class="text">
			<div class="title"><?=CYouWanna::multiTranslateAlt($arItem['PROPERTIES']['SINGLEPIC_TITLE']['VALUE'], $arItem['PROPERTIES']['SINGLEPIC_TITLE_EN']['VALUE'], LANGUAGE_ID, false, true)?></div>
			<div class="desc"><?=CYouWanna::multiTranslateAlt($arItem['PROPERTIES']['SINGLEPIC_DESC']['~VALUE']['TEXT'], $arItem['PROPERTIES']['SINGLEPIC_DESC_EN']['~VALUE']['TEXT'], LANGUAGE_ID, false, true)?></div>
		</div>
	</div>
</a>