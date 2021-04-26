<?
/**
 * Шаблон компонента
 *
 * @global CMain $APPLICATION
 * @var array    $arParams
 * @var array    $arResult
 */
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

$this->setFrameMode(true);

// Подгрузка информации о постраничной навигации
?>

<? if (0 !== count($arResult['ITEMS'])) { ?>
<div class="yw-trendslider">
	<div class="yw-trendslider__list">
	    <?foreach ($arResult['ITEMS'] as $arSlide):?>
	    <div class="yw-trendslider__wrapper">
	        <div class="yw-trendslider__item">
	            <?if($arSlide['PROPERTIES']['DESC']['VALUE']['TEXT']):?><div class="yw-trendslider__text"><?= CYouWanna::multiTranslateAlt($arSlide['PROPERTIES']['DESC']['VALUE']['TEXT'], $arSlide['PROPERTIES']['DESC_EN']['VALUE']['TEXT'], LANGUAGE_ID) ?></div><?endif?>
	            <div class="yw-trendslider__img img" style="background-image: url(<?=$arSlide['DETAIL_PICTURE']['SRC']?>)"></div>
	        </div>
	    </div>
	    <?endforeach?>
	</div>
</div>
<? } ?>