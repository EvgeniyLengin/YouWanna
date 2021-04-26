<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="yw-trendinsta">
	<div class="yw-trendinsta__title">Instashop</div>

	<?$APPLICATION->IncludeComponent(
		"webfly:show.instagram_posts", 
		"youwanna.instashop.trend", 
		array(
			"CACHE_TIME" => "600",
			"CACHE_TYPE" => "N",
			"CARDS_IN_ROW" => "3",
			"FILTER_TAG" => array(
			),
			"NAV_TEMPLATE" => "grid",
			"ON_PAGE" => "3",
			"ORDER" => "DESC",
			"ORDER_BY" => "DATE",
			"PIC_HEIGHT" => "500",
			"PIC_WIDTH" => "500",
			"USE_NAV" => "Y",
			"COMPONENT_TEMPLATE" => "youwanna.instashop.trend",
			"SHOW_BIO" => "N",
			"SHOW_USER_NAME" => "N",
			"IS_AJAX" => 'N',
			"LANGUAGE_ID" => LANGUAGE_ID,
			"COMPOSITE_FRAME_MODE" => "A",
			"COMPOSITE_FRAME_TYPE" => "AUTO"
		),
		false
	); ?>

	<div class="yw-trendinsta__linkwrap"><a href="/instashop/" class="yw-trendinsta__link yw-btn"><?=CYouWanna::multiTranslate('Смотреть все', LANGUAGE_ID, false, true)?></a></div>
</div>