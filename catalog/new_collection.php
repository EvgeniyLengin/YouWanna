<?php
/**
 * Страница общего списка товаров
 *
 * @global CMain $APPLICATION
 */
require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php';

$APPLICATION->SetTitle("NEW COLLECTION");

?><? global $arrFilter ?>
<?
$arrFilter = array(
    'PROPERTY_NEW_PRODUCT_VALUE' => 'Да'
)
?>
<?if(true):?>
    <?if(!isset($_COOKIE["catalog_view"])) setcookie("catalog_view", 'grid', time()+(3600 * 24 * 7));?>


    <div class="yw-catalogwrap">
        <div class="row">
            <div class="col-xl-10 ml-auto yw-catalogpanel">
                <?php
                if ($_SERVER['REQUEST_URI'] != '/catalog/') {
                    ?>
                    <div class="catalog-button">
                        <a href="/catalog"> Посмотреть все товары </a>
                    </div>
                    <?php
                }

                 ?>
            <div class="wrapper-sort-c">     
                <div class="yw-catalogsort">
                    <div class="yw-catalogsort__hint"><?= CYouWanna::multiTranslate('Сортировать по:', LANGUAGE_ID) ?></div>
                    <?
                    $sort_val = CYouWanna::multiTranslate('Выбрать', LANGUAGE_ID);
                    $sort_val = ($_REQUEST['sort'] === 'price_asc') ? CYouWanna::multiTranslate('Возрастанию цены', LANGUAGE_ID)  : $sort_val;
                    $sort_val = ($_REQUEST['sort'] === 'price_desc') ? CYouWanna::multiTranslate('Уменьшению цены', LANGUAGE_ID)  : $sort_val;
                     ?>
                    <div class="yw-catalogsort__box">
                        <div class="yw-catalogsort__value"><?=$sort_val ?></div>
                        <ul class="yw-catalogsort__list">
                            <li class="yw-catalogsort__item <?= ($_REQUEST['sort'] === 'price_asc' || !isset($_REQUEST['sort'])) ? 'active' : '' ?>">
                                <a href="<?=$APPLICATION->GetCurPage(false)?>?sort=price_asc&n=Y"><?= CYouWanna::multiTranslate('Возрастанию цены', LANGUAGE_ID) ?></a>
                            </li>
                            <li class="yw-catalogsort__item <?= $_REQUEST['sort'] === 'price_desc' ? 'active' : '' ?>">
                                <a href="<?=$APPLICATION->GetCurPage(false)?>?sort=price_desc&n=Y"><?= CYouWanna::multiTranslate('Уменьшению цены', LANGUAGE_ID) ?></a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="yw-catalogview">
                    <ul class="yw-catalogview__list row">
                        <li class="yw-catalogview__item<?=$_COOKIE["catalog_view"] == 'biggrid' ? ' active' : ''?>" data-action="catalog_view" data-value="biggrid"><img src="<?=SITE_TEMPLATE_PATH?>/img/icon-biggrid.svg" alt="<?= CYouWanna::multiTranslate('2 в ряд', LANGUAGE_ID) ?>"></li>
                        <li class="yw-catalogview__item<?=$_COOKIE["catalog_view"] == 'grid' ? ' active' : ''?>" data-action="catalog_view" data-value="grid"><img src="<?=SITE_TEMPLATE_PATH?>/img/icon-grid.svg" alt="<?= CYouWanna::multiTranslate('4 в ряд', LANGUAGE_ID) ?>"></li>
                    </ul>
                </div>
            </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xl-2 d-none d-xl-block yw-catalog__left">
                <h1><?php $APPLICATION->ShowTitle(false); ?></h1>
                <?
	            $cLeftCats = $APPLICATION->IncludeComponent(
	                'bitrix:catalog.section.list',
	                'left.menu',
	                Array(
	                    'IBLOCK_TYPE'        => CYouWanna::IBLOCK_TYPE_CATALOG,
	                    'IBLOCK_ID'          => CYouWanna::getIblockIdByCode(CYouWanna::IBLOCK_CODE_CATALOG),
	                    'SECTION_CODE'       => "",
	                    'SECTION_ID'         => '',
	                    'SECTION_URL'        => '',
	                    'COUNT_ELEMENTS'     => 'N',
	                    'ADD_SECTIONS_CHAIN' => 'N',
	                    'CACHE_TYPE'         => 'N',
	                    'CACHE_TIME'         => 36000000,
	                    'CACHE_NOTES'        => '',
	                    'CACHE_GROUPS'       => 'N',
	                    'ACTIVE_SECTION'     => 'NEW_PRODUCT',
	                    'LANGUAGE_ID'        => LANGUAGE_ID
	                )
	            );
	            ?>
            </div>
            <div class="col-xl-10 yw-catalog__right">
                <?
                    if ($_REQUEST['sort'] === 'price_asc') {
                        $sortField = 'PROPERTY_MINIMUM_PRICE';
                        $sortOrder = 'ASC';
                    } elseif ($_REQUEST['sort'] === 'price_desc') {
                        $sortField = 'PROPERTY_MINIMUM_PRICE';
                        $sortOrder = 'DESC';
                    } else {
                        $sortField = 'SORT';
                        $sortOrder = 'ASC';
                    }
                ?>
                <?$APPLICATION->IncludeComponent(
                    "bitrix:catalog.section",
                    "section.products_new",
                    Array(
                        "ACTION_VARIABLE" => "action",
                        "ADD_PICT_PROP" => "-",
                        "ADD_PROPERTIES_TO_BASKET" => "Y",
                        "ADD_SECTIONS_CHAIN" => "N",
                        "ADD_TO_BASKET_ACTION" => "ADD",
                        "AJAX_MODE" => "N",
                        "AJAX_OPTION_ADDITIONAL" => "",
                        "AJAX_OPTION_HISTORY" => "N",
                        "AJAX_OPTION_JUMP" => "N",
                        "AJAX_OPTION_STYLE" => "Y",
                        "BACKGROUND_IMAGE" => "-",
                        "BASKET_URL" => "/personal/basket.php",
                        "BROWSER_TITLE" => "-",
                        "CACHE_FILTER" => "Y",
                        "CACHE_GROUPS" => "Y",
                        "CACHE_TIME" => "3600",
                        "CACHE_TYPE" => "N",
                        "COMPATIBLE_MODE" => "Y",
                        "COMPONENT_TEMPLATE" => "section.products_new",
                        "COMPOSITE_FRAME_MODE" => "A",
                        "COMPOSITE_FRAME_TYPE" => "AUTO",
                        "CONVERT_CURRENCY" => "N",
                        "CUSTOM_FILTER" => "",
                        "DETAIL_URL" => "",
                        "DISABLE_INIT_JS_IN_COMPONENT" => "N",
                        "DISPLAY_BOTTOM_PAGER" => "Y",
                        "DISPLAY_COMPARE" => "N",
                        "DISPLAY_TOP_PAGER" => "N",
                        "ELEMENT_SORT_FIELD" => $sortField,
                        "ELEMENT_SORT_FIELD2" => "id",
                        "ELEMENT_SORT_ORDER" => $sortOrder,
                        "ELEMENT_SORT_ORDER2" => "desc",
                        "ENLARGE_PRODUCT" => "STRICT",
                        "FILE_404" => "",
                        "FILTER_NAME" => "arrFilter",
                        "HIDE_NOT_AVAILABLE" => "Y",
                        "HIDE_NOT_AVAILABLE_OFFERS" => "Y",
                        "IBLOCK_ID" => CYouWanna::getIblockIdByCode(CYouWanna::IBLOCK_CODE_CATALOG),
                        "IBLOCK_TYPE" => "YouWanna",
                        "INCLUDE_SUBSECTIONS" => "Y",
                        "IS_AJAX" => $_REQUEST["ajax_page"],
                        "LABEL_PROP" => "",
                        "LANGUAGE_ID" => LANGUAGE_ID,
                        "LAZY_LOAD" => "N",
                        "LINE_ELEMENT_COUNT" => "2",
                        "LOAD_ON_SCROLL" => "N",
                        "MESSAGE_404" => "",
                        "MESS_BTN_ADD_TO_BASKET" => "В корзину",
                        "MESS_BTN_BUY" => "Купить",
                        "MESS_BTN_DETAIL" => "Подробнее",
                        "MESS_BTN_SUBSCRIBE" => "Подписаться",
                        "MESS_NOT_AVAILABLE" => "Нет в наличии",
                        "META_DESCRIPTION" => "-",
                        "META_KEYWORDS" => "-",
                        "OFFERS_CART_PROPERTIES" => array(0=>"SIZE",1=>"COLOR",2=>"CML2_ARTICLE",3=>"MORE_PHOTO"),
                        "OFFERS_FIELD_CODE" => array(0=>"PREVIEW_PICTURE",1=>"",),
                        "OFFERS_LIMIT" => "0",
                        "OFFERS_PROPERTY_CODE" => array(0=>"SIZE",1=>"COLOR",2=>"",),
                        "OFFERS_SORT_FIELD" => "CATALOG_AVAILABLE",
                        "OFFERS_SORT_FIELD2" => "SORT",
                        "OFFERS_SORT_ORDER" => "DESC",
                        "OFFERS_SORT_ORDER2" => "ASC",
                        "PAGER_BASE_LINK_ENABLE" => "N",
                        "PAGER_DESC_NUMBERING" => "N",
                        "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
                        "PAGER_SHOW_ALL" => "N",
                        "PAGER_SHOW_ALWAYS" => "N",
                        "PAGER_TEMPLATE" => "ajax-scroll",
                        "PAGER_TITLE" => "Товары",
                        "PAGE_ELEMENT_COUNT" => "16",
                        "PARTIAL_PRODUCT_PROPERTIES" => "N",
                        "PRICE_CODE" => array(0=>"BASE",),
                        "PRICE_VAT_INCLUDE" => "Y",
                        "PRODUCT_BLOCKS_ORDER" => "price,props,sku,quantityLimit,quantity,buttons,compare",
                        "PRODUCT_DISPLAY_MODE" => "N",
                        "PRODUCT_ID_VARIABLE" => "id",
                        "PRODUCT_PROPERTIES" => array(),
                        "PRODUCT_PROPS_VARIABLE" => "prop",
                        "PRODUCT_QUANTITY_VARIABLE" => "quantity",
                        "PRODUCT_ROW_VARIANTS" => "[{'VARIANT':'2','BIG_DATA':false},{'VARIANT':'2','BIG_DATA':false},{'VARIANT':'2','BIG_DATA':false},{'VARIANT':'2','BIG_DATA':false},{'VARIANT':'2','BIG_DATA':false},{'VARIANT':'2','BIG_DATA':false}]",
                        "PRODUCT_SUBSCRIPTION" => "Y",
                        "PROPERTY_CODE" => array(0=>"",1=>"MORE_PHOTO",2=>"",3=>'',4=>'HOR_PHOTO',5=>'HOR_PHOTO_HOVER'),
                        "PROPERTY_CODE_MOBILE" => "",
                        "RCM_PROD_ID" => "",
                        "RCM_TYPE" => "personal",
                        "SECTION_CODE" => $_REQUEST["SECTION_CODE"],
                        "SECTION_ID" => "",
                        "SECTION_ID_VARIABLE" => "SECTION_ID",
                        "SECTION_URL" => "",
                        "SECTION_USER_FIELDS" => array(0=>"",1=>"",),
                        "SEF_MODE" => "N",
                        "SET_BROWSER_TITLE" => "Y",
                        "SET_LAST_MODIFIED" => "N",
                        "SET_META_DESCRIPTION" => "Y",
                        "SET_META_KEYWORDS" => "Y",
                        "SET_STATUS_404" => "Y",
                        "SET_TITLE" => "Y",
                        "SHOW_404" => "Y",
                        "SHOW_ALL_WO_SECTION" => "Y",
                        "SHOW_CLOSE_POPUP" => "N",
                        "SHOW_DISCOUNT_PERCENT" => "N",
                        "SHOW_FROM_SECTION" => "N",
                        "SHOW_MAX_QUANTITY" => "N",
                        "SHOW_OLD_PRICE" => "N",
                        "SHOW_PRICE_COUNT" => "1",
                        "SHOW_SLIDER" => "Y",
                        "SLIDER_INTERVAL" => "3000",
                        "SLIDER_PROGRESS" => "N",
                        "TEMPLATE_THEME" => "blue",
                        "USE_ENHANCED_ECOMMERCE" => "N",
                        "USE_MAIN_ELEMENT_SECTION" => "N",
                        "USE_PRICE_COUNT" => "N",
                        "USE_PRODUCT_QUANTITY" => "N"
                    )
                );?>
            </div>
        </div>
    </div>

<?else:?>
<h1 class="page-title"><?php $APPLICATION->ShowTitle(false); ?></h1>

    <div class="row">
		<div class="col-xl-2 col--left-menu">
            <?
            $cLeftCats = $APPLICATION->IncludeComponent(
                'bitrix:catalog.section.list',
                'left.menu',
                Array(
                    'IBLOCK_TYPE'        => CYouWanna::IBLOCK_TYPE_CATALOG,
                    'IBLOCK_ID'          => CYouWanna::getIblockIdByCode(CYouWanna::IBLOCK_CODE_CATALOG),
                    'SECTION_CODE'       => "",
                    'SECTION_ID'         => '',
                    'SECTION_URL'        => '',
                    'COUNT_ELEMENTS'     => 'N',
                    'ADD_SECTIONS_CHAIN' => 'N',
                    'CACHE_TYPE'         => 'N',
                    'CACHE_TIME'         => 36000000,
                    'CACHE_NOTES'        => '',
                    'CACHE_GROUPS'       => 'N',
                    'ACTIVE_SECTION'     => 'NEW_PRODUCT',
                    'LANGUAGE_ID'        => LANGUAGE_ID
                )
            );
            ?>
        </div>
        <div class="col-xl-10 col-lg-12 <?= !$cLeftCats ?: 'with-left-menu' ?>">

            <div class="col-6 sort-price">
                <div class="catalog-list-sort flex-container flex-row flex-item">
                    <div class="bx-filter-parameters-box-title">
                    <span class="bx-filter-parameters-box-hint">
                        <?= CYouWanna::multiTranslate('Сортировать по:', LANGUAGE_ID) ?>
                    </span>
                    </div>
                    <div class="bx-filter-block">
                        <div class="flex-container flex-row">
                            <div class="multiselect-checkbox-block">
                                <div class="filter-select-box">
                                <span class="filter-select-text">
                                    <?= CYouWanna::multiTranslate('Выбрать', LANGUAGE_ID) ?>
                                </span>
                                    <i data-role="prop_angle" class="fa fa-angle-down"></i>
                                </div>
                                <div class="checkbox-block" style="display: none;">
                                    <div class="checkbox js-sort-click <?= ($_REQUEST['sort'] === 'price_asc' || !isset($_REQUEST['sort'])) ? 'active' : '' ?>"
                                         data-value="price_asc">
                                        <?= CYouWanna::multiTranslate('Возрастанию цены', LANGUAGE_ID) ?>
                                    </div>
                                    <div class="checkbox js-sort-click <?= $_REQUEST['sort'] === 'price_desc' ? 'active' : '' ?>"
                                         data-value="price_desc">
                                        <?= CYouWanna::multiTranslate('Уменьшению цены', LANGUAGE_ID) ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

    <? if ($_REQUEST['sort'] === 'price_asc') {
            $sortField = 'PROPERTY_MINIMUM_PRICE';
            $sortOrder = 'ASC';
        } elseif ($_REQUEST['sort'] === 'price_desc') {
            $sortField = 'PROPERTY_MINIMUM_PRICE';
            $sortOrder = 'DESC';
        } else {
            $sortField = 'sort';
            $sortOrder = 'ASC';
        }
      ?>

            <? $APPLICATION->IncludeComponent(
	"bitrix:catalog.section",
	"section.products",
	array(
		"IS_AJAX" => $_REQUEST["ajax_page"],
		"IBLOCK_TYPE" => "YouWanna",
		"IBLOCK_ID" => CYouWanna::getIblockIdByCode(CYouWanna::IBLOCK_CODE_CATALOG),
		"ACTION_VARIABLE" => "action",
		"ADD_PICT_PROP" => "-",
		"ADD_PROPERTIES_TO_BASKET" => "Y",
		"ADD_SECTIONS_CHAIN" => "N",
		"ADD_TO_BASKET_ACTION" => "ADD",
		"AJAX_MODE" => "N",
		"AJAX_OPTION_ADDITIONAL" => "",
		"AJAX_OPTION_HISTORY" => "N",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "Y",
		"BACKGROUND_IMAGE" => "-",
		"BASKET_URL" => "/personal/basket.php",
		"BROWSER_TITLE" => "-",
		"CACHE_FILTER" => "Y",
		"CACHE_GROUPS" => "Y",
		"CACHE_TIME" => "3600",
		"CACHE_TYPE" => "N",
		"COMPATIBLE_MODE" => "Y",
		"CONVERT_CURRENCY" => "N",
		"CUSTOM_FILTER" => "",
		"DETAIL_URL" => "",
		"DISABLE_INIT_JS_IN_COMPONENT" => "N",
		"DISPLAY_BOTTOM_PAGER" => "Y",
		"DISPLAY_COMPARE" => "N",
		"DISPLAY_TOP_PAGER" => "N",
		"ELEMENT_SORT_FIELD" => $sortField,
		"ELEMENT_SORT_FIELD2" => "id",
		"ELEMENT_SORT_ORDER" => $sortOrder,
		"ELEMENT_SORT_ORDER2" => "desc",
		"ENLARGE_PRODUCT" => "STRICT",
		"FILTER_NAME" => "arrFilter",
		"HIDE_NOT_AVAILABLE" => "N",
		"HIDE_NOT_AVAILABLE_OFFERS" => "N",
		"INCLUDE_SUBSECTIONS" => "Y",
		"LABEL_PROP" => "",
		"LAZY_LOAD" => "N",
		"LINE_ELEMENT_COUNT" => "2",
		"LOAD_ON_SCROLL" => "N",
		"MESSAGE_404" => "",
		"MESS_BTN_ADD_TO_BASKET" => "В корзину",
		"MESS_BTN_BUY" => "Купить",
		"MESS_BTN_DETAIL" => "Подробнее",
		"MESS_BTN_SUBSCRIBE" => "Подписаться",
		"MESS_NOT_AVAILABLE" => "Нет в наличии",
		"META_DESCRIPTION" => "-",
		"META_KEYWORDS" => "-",
		"OFFERS_CART_PROPERTIES" => array(
			0 => "SIZE",
			1 => "COLOR",
			2 => "CML2_ARTICLE",
		),
		"OFFERS_FIELD_CODE" => array(
			0 => "PREVIEW_PICTURE",
			1 => "",
		),
		"OFFERS_LIMIT" => "0",
		"OFFERS_PROPERTY_CODE" => array(
			0 => "SIZE",
			1 => "COLOR",
			2 => "",
		),
		"OFFERS_SORT_FIELD" => "CATALOG_AVAILABLE",
		"OFFERS_SORT_ORDER" => "DESC",
		"OFFERS_SORT_FIELD2" => "SORT",
		"OFFERS_SORT_ORDER2" => "ASC",
		"PAGER_BASE_LINK_ENABLE" => "N",
		"PAGER_DESC_NUMBERING" => "N",
		"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
		"PAGER_SHOW_ALL" => "N",
		"PAGER_SHOW_ALWAYS" => "N",
		"PAGER_TEMPLATE" => ".default",
		"PAGER_TITLE" => "Товары",
		"PAGE_ELEMENT_COUNT" => "16",
		"PARTIAL_PRODUCT_PROPERTIES" => "N",
		"PRICE_CODE" => array(
			0 => "BASE",
		),
		"PRICE_VAT_INCLUDE" => "Y",
		"PRODUCT_BLOCKS_ORDER" => "price,props,sku,quantityLimit,quantity,buttons,compare",
		"PRODUCT_DISPLAY_MODE" => "N",
		"PRODUCT_ID_VARIABLE" => "id",
		"PRODUCT_PROPERTIES" => array(
		),
		"PRODUCT_PROPS_VARIABLE" => "prop",
		"PRODUCT_QUANTITY_VARIABLE" => "quantity",
		"PRODUCT_ROW_VARIANTS" => "[{'VARIANT':'2','BIG_DATA':false},{'VARIANT':'2','BIG_DATA':false},{'VARIANT':'2','BIG_DATA':false},{'VARIANT':'2','BIG_DATA':false},{'VARIANT':'2','BIG_DATA':false},{'VARIANT':'2','BIG_DATA':false}]",
		"PRODUCT_SUBSCRIPTION" => "Y",
		"PROPERTY_CODE" => array(
			0 => "",
			1 => "MORE_PHOTO",
			2 => "",
		),
		"PROPERTY_CODE_MOBILE" => "",
		"RCM_PROD_ID" => "",
		"RCM_TYPE" => "personal",
		"SECTION_CODE" => "",
		"SECTION_ID" => "",
		"SECTION_ID_VARIABLE" => "SECTION_ID",
		"SECTION_URL" => "",
		"SECTION_USER_FIELDS" => array(
			0 => "",
			1 => "",
		),
		"SEF_MODE" => "N",
		"SET_BROWSER_TITLE" => "N",
		"SET_LAST_MODIFIED" => "N",
		"SET_META_DESCRIPTION" => "N",
		"SET_META_KEYWORDS" => "N",
		"SET_STATUS_404" => "N",
		"SET_TITLE" => "N",
		"SHOW_404" => "N",
		"SHOW_ALL_WO_SECTION" => "Y",
		"SHOW_CLOSE_POPUP" => "N",
		"SHOW_DISCOUNT_PERCENT" => "N",
		"SHOW_FROM_SECTION" => "N",
		"SHOW_MAX_QUANTITY" => "N",
		"SHOW_OLD_PRICE" => "N",
		"SHOW_PRICE_COUNT" => "1",
		"SHOW_SLIDER" => "Y",
		"SLIDER_INTERVAL" => "3000",
		"SLIDER_PROGRESS" => "N",
		"TEMPLATE_THEME" => "blue",
		"USE_ENHANCED_ECOMMERCE" => "N",
		"USE_MAIN_ELEMENT_SECTION" => "N",
		"USE_PRICE_COUNT" => "N",
		"USE_PRODUCT_QUANTITY" => "N",
		"LANGUAGE_ID" => LANGUAGE_ID,
		"COMPONENT_TEMPLATE" => "section.products",
		"COMPOSITE_FRAME_MODE" => "A",
		"COMPOSITE_FRAME_TYPE" => "AUTO"
	),
	false
); ?>

        </div>

    </div>
<?endif?>
<?php require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php';
