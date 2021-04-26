<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="yw-trend yw-trendmain">
	<div class="yw-trendmain__left">
		<h1><?= CYouWanna::multiTranslateAlt($arItem['PROPERTIES']['MAINTITLE']['VALUE'], $arItem['PROPERTIES']['MAINTITLE_EN']['VALUE'], LANGUAGE_ID, false, true) ?></h1>
		<div class="yw-trendmain__text"><?= CYouWanna::multiTranslateAlt($arItem['PROPERTIES']['MAINDESC']['~VALUE']['TEXT'], $arItem['PROPERTIES']['MAINDESC_EN']['~VALUE']['TEXT'], LANGUAGE_ID, false, true) ?></div>
	</div>
	<div class="yw-trendmain__center">
		<div class="img" style="background-image: url(<?=CFile::GetPath($arItem['PROPERTIES']['MAINPIC1']['VALUE'])?>)"></div>
	</div>
	<div class="yw-trendmain__right">
		<div class="img" style="background-image: url(<?=CFile::GetPath($arItem['PROPERTIES']['MAINPIC2']['VALUE'])?>)"></div>
	</div>
</div>