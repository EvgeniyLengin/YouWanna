<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="yw-trenddetail yw-trenddetail">
	<div class="yw-trenddetail__left">		
		<div class="img" style="background-image: url(<?=$arItem['DETAIL_PICTURE']['SRC']?>)"></div>
	</div>
	<div class="yw-trenddetail__right">
		<h1><?= CYouWanna::multiTranslateAlt($arItem['PROPERTIES']['TITLE']['VALUE'], $arItem['PROPERTIES']['TITLE_EN']['VALUE'], LANGUAGE_ID) ?></h1>
		<div class="yw-trendmain__text"><?= CYouWanna::multiTranslateAlt($arItem['PROPERTIES']['DESC']['~VALUE']['TEXT'], $arItem['PROPERTIES']['DESC_EN']['~VALUE']['TEXT'], LANGUAGE_ID) ?></div>
	</div>
</div>