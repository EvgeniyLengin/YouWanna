<?php
/**
 * Детальная страница адреса магазинов
 *
 * @global CMain $APPLICATION
 */
require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php';

if (LANGUAGE_ID === 'ru') {
    $APPLICATION->AddChainItem(
        'Каталог',
        '/catalog/'
    );
} else {
    $APPLICATION->AddChainItem(
        'Catalog',
        '/' . LANGUAGE_ID . '/catalog/'
    );
}

$assets->addJs(SITE_TEMPLATE_PATH . '/js/jquery.fancybox.min.js');

?><?if ( $_REQUEST['AJAX_CALL'] == 'Y' && $_REQUEST['action'] == 'add2favorite' ) {
    // +++++++++++++++++++++++++++++++ add2favorite +++++++++++++++++++++++++++++++ //
    $ELEMENT_ID = IntVal($_REQUEST['element_id']);

    $APPLICATION->RestartBuffer();

    if(count($_POST['element_id']) > 0)
    {
        $arrItemID = $_POST['element_id'];

        if(empty($arrItemID))
            die("param itemID not found");

        if(!$USER->IsAuthorized()){
            $arElements = unserialize($APPLICATION->get_cookie('favorites'));

            if(!in_array($arrItemID, $arElements)) {
                $arElements[] = $arrItemID;
            } else {
                foreach ( $arElements as $key => $element) {

                    if (intval($arrItemID) === intval($element)){
                        unset($arElements[$key]);
                    }
                }
            }

            $APPLICATION->set_cookie("favorites",serialize($arElements));
        }
        else{
            $idUser = $USER->GetID();
            $rsUser = CUser::GetByID($idUser);
            $arUser = $rsUser->Fetch();
            $arElements = $arUser['UF_FAVORITES'];

                if(!in_array($arrItemID, $arElements)) {
                    $arElements[] = $arrItemID;
                } else {
                    foreach ( $arElements as $key => $element) {

                        if (intval($arrItemID) === intval($element)){
                            unset($arElements[$key]);
                        }
                    }
                }

            $USER->Update($idUser, Array("UF_FAVORITES"=>$arElements));
        }
    };

    return false;
    }?>

    <?

    if (true){?>
        <div class="share-section">
            <script src="//yastatic.net/es5-shims/0.0.2/es5-shims.min.js"></script>
            <script src="//yastatic.net/share2/share.js"></script>
            <a class="share-switch" href="#" onclick="toggleShare()" title="Поделиться"><span class="hide-mobile"><?= CYouWanna::multiTranslate('Поделиться', LANGUAGE_ID); ?></span></a>
            <div class="ya-share2 share-wrapper" data-services="vkontakte,facebook,twitter,whatsapp,telegram"></div>
            <?/* $APPLICATION->IncludeComponent(
                'bitrix:main.share',
                'youwanna.product.detail.share',
                Array(
                    'COMPOSITE_FRAME_MODE' => 'A',
                    'COMPOSITE_FRAME_TYPE' => 'AUTO',
                    'HANDLERS'             => array('vk', 'facebook', 'twitter'),
                    'HIDE'                 => 'Y',
                    'PAGE_TITLE'           => $arResult['NAME'],
                    'PAGE_URL'             => $APPLICATION->GetCurUri(),
                )
            ); */?>
        </div>
    <?}?>

    <?$APPLICATION->IncludeComponent(
	"bitrix:catalog.element",
	"detail.product",
	Array(
		"ACTION_VARIABLE" => "action",
		"ADD_DETAIL_TO_SLIDER" => "N",
		"ADD_ELEMENT_CHAIN" => "N",
		"ADD_PICT_PROP" => "-",
		"ADD_PROPERTIES_TO_BASKET" => "Y",
		"ADD_SECTIONS_CHAIN" => "Y",
		"ADD_TO_BASKET_ACTION" => array('BUY','ADD'),
		"ADD_TO_BASKET_ACTION_PRIMARY" => array('BUY','ADD'),
		"BACKGROUND_IMAGE" => "-",
		"BASKET_URL" => "/personal/make/",
		"BRAND_USE" => "N",
		"BROWSER_TITLE" => "-",
		"CACHE_GROUPS" => "Y",
		"CACHE_TIME" => 3600,
		"CACHE_TYPE" => "A",
		"CHECK_SECTION_ID_VARIABLE" => "N",
		"COMPATIBLE_MODE" => "Y",
		"CONVERT_CURRENCY" => "N",
		"DETAIL_PICTURE_MODE" => array('POPUP','MAGNIFIER'),
		"DETAIL_URL" => "",
		"DISABLE_INIT_JS_IN_COMPONENT" => "N",
		"DISPLAY_COMPARE" => "N",
		"DISPLAY_NAME" => "Y",
		"DISPLAY_PREVIEW_TEXT_MODE" => "E",
		"ELEMENT_CODE" => $_REQUEST['ELEMENT_CODE'],
		"ELEMENT_ID" => "",
		"FROM_PRODUCT_LIST" => $_REQUEST['fromProductList'],
		"GIFTS_DETAIL_BLOCK_TITLE" => "Выберите один из подарков",
		"GIFTS_DETAIL_HIDE_BLOCK_TITLE" => "N",
		"GIFTS_DETAIL_PAGE_ELEMENT_COUNT" => "4",
		"GIFTS_DETAIL_TEXT_LABEL_GIFT" => "Подарок",
		"GIFTS_MAIN_PRODUCT_DETAIL_BLOCK_TITLE" => "Выберите один из товаров, чтобы получить подарок",
		"GIFTS_MAIN_PRODUCT_DETAIL_HIDE_BLOCK_TITLE" => "N",
		"GIFTS_MAIN_PRODUCT_DETAIL_PAGE_ELEMENT_COUNT" => "4",
		"GIFTS_MESS_BTN_BUY" => "Выбрать",
		"GIFTS_SHOW_DISCOUNT_PERCENT" => "Y",
		"GIFTS_SHOW_IMAGE" => "Y",
		"GIFTS_SHOW_NAME" => "Y",
		"GIFTS_SHOW_OLD_PRICE" => "Y",
		"HIDE_NOT_AVAILABLE_OFFERS" => "N",
		"IBLOCK_ID" => CYouWanna::getIblockIdByCode(CYouWanna::IBLOCK_CODE_CATALOG),
		"IBLOCK_TYPE" => CYouWanna::IBLOCK_TYPE_CATALOG,
		"IMAGE_RESOLUTION" => "16by9",
		"IS_AJAX" => $_REQUEST['ajax'],
		"LABEL_PROP" => array(),
		"LANGUAGE_ID" => LANGUAGE_ID,
		"LINK_ELEMENTS_URL" => "link.php?PARENT_ELEMENT_ID=#ELEMENT_ID#",
		"LINK_IBLOCK_ID" => "",
		"LINK_IBLOCK_TYPE" => "",
		"LINK_PROPERTY_SID" => "",
		"MAIN_BLOCK_OFFERS_PROPERTY_CODE" => array('SIZE','COLOR'),
		"MAIN_BLOCK_PROPERTY_CODE" => array(),
		"MESSAGE_404" => "",
		"MESS_BTN_ADD_TO_BASKET" => "В корзину",
		"MESS_BTN_BUY" => "Купить",
		"MESS_BTN_SUBSCRIBE" => "Подписаться",
		"MESS_COMMENTS_TAB" => "Комментарии",
		"MESS_DESCRIPTION_TAB" => "Описание",
		"MESS_NOT_AVAILABLE" => "Нет в наличии",
		"MESS_PRICE_RANGES_TITLE" => "Цены",
		"MESS_PROPERTIES_TAB" => "Характеристики",
		"META_DESCRIPTION" => "-",
		"META_KEYWORDS" => "-",
		"OFFERS_CART_PROPERTIES" => array('SIZE','COLOR'),
		"OFFERS_FIELD_CODE" => array('',''),
		"OFFERS_LIMIT" => "0",
		"OFFERS_PROPERTY_CODE" => array('SIZE','COLOR',''),
		"OFFERS_SORT_FIELD" => "CATALOG_AVAILABLE",
		"OFFERS_SORT_FIELD2" => "SORT",
		"OFFERS_SORT_ORDER" => "DESC",
		"OFFERS_SORT_ORDER2" => "ASC",
		"OFFER_ADD_PICT_PROP" => "-",
		"OFFER_TREE_PROPS" => array('SIZE','COLOR',''),
		"PARTIAL_PRODUCT_PROPERTIES" => "N",
		"PRICE_CODE" => array('BASE'),
		"PRICE_VAT_INCLUDE" => "Y",
		"PRICE_VAT_SHOW_VALUE" => "N",
		"PRODUCT_ID_VARIABLE" => "id",
		"PRODUCT_INFO_BLOCK_ORDER" => "sku,props",
		"PRODUCT_PAY_BLOCK_ORDER" => "rating,price,priceRanges,quantityLimit,quantity,buttons",
		"PRODUCT_PROPERTIES" => array(),
		"PRODUCT_PROPS_VARIABLE" => "prop",
		"PRODUCT_QUANTITY_VARIABLE" => "quantity",
		"PRODUCT_SUBSCRIPTION" => "Y",
		"PROPERTY_CODE" => array('CML2_ARTICLE',''),
		"SECTION_CODE" => "",
		"SECTION_ID" => "",
		"SECTION_ID_VARIABLE" => "SECTION_ID",
		"SECTION_URL" => "",
		"SEF_MODE" => "N",
		"SET_BROWSER_TITLE" => "Y",
		"SET_CANONICAL_URL" => "Y",
		"SET_LAST_MODIFIED" => "N",
		"SET_META_DESCRIPTION" => "Y",
		"SET_META_KEYWORDS" => "Y",
		"SET_STATUS_404" => "Y",
		"SET_TITLE" => "Y",
		"SET_VIEWED_IN_COMPONENT" => "N",
		"SHOW_404" => "Y",
		"SHOW_CLOSE_POPUP" => "N",
		"SHOW_DEACTIVATED" => "N",
		"SHOW_DISCOUNT_PERCENT" => "N",
		"SHOW_MAX_QUANTITY" => "N",
		"SHOW_OLD_PRICE" => "N",
		"SHOW_PRICE_COUNT" => "1",
		"SHOW_SLIDER" => "N",
		"STRICT_SECTION_CHECK" => "N",
		"TEMPLATE_THEME" => "blue",
		"USE_COMMENTS" => "N",
		"USE_ELEMENT_COUNTER" => "Y",
		"USE_ENHANCED_ECOMMERCE" => "N",
		"USE_GIFTS_DETAIL" => "Y",
		"USE_GIFTS_MAIN_PR_SECTION_LIST" => "Y",
		"USE_MAIN_ELEMENT_SECTION" => "N",
		"USE_PRICE_COUNT" => "N",
		"USE_PRODUCT_QUANTITY" => "N",
		"USE_RATIO_IN_RANGES" => "N",
		"USE_VOTE_RATING" => "N"
	)
);?>

    <div class="catalog-detail-tabs" data-component="tabs">


        <div class="catalog-detail-tabs__item">
            <a href="#recommend" rel="recommend" class="catalog-detail-tabs__link js-slick-refresh">Рекомендуем</a>
        </div>

        <div class="catalog-detail-tabs__item">
            <a href="#viewed" rel="viewed" class="catalog-detail-tabs__link js-slick-refresh">Вы смотрели</a>
        </div>

    </div>

<div id="recommend" class="open">
    <?
    global $recFilter;
    if ($recFilter) {
        $APPLICATION->IncludeComponent(
            'bitrix:catalog.section',
            'section.products',
            [
                'IBLOCK_TYPE'                     => CYouWanna::IBLOCK_TYPE_CATALOG,
                'IBLOCK_ID'                       => CYouWanna::getIblockIdByCode(CYouWanna::IBLOCK_CODE_CATALOG),
                'ACTION_VARIABLE'                 => 'action',
                'ADD_PICT_PROP'                   => '-',
                'ADD_PROPERTIES_TO_BASKET'        => 'Y',
                'ADD_SECTIONS_CHAIN'              => 'N',
                'ADD_TO_BASKET_ACTION'            => 'ADD',
                'AJAX_MODE'                       => 'N',
                'AJAX_OPTION_ADDITIONAL'          => '',
                'AJAX_OPTION_HISTORY'             => 'N',
                'AJAX_OPTION_JUMP'                => 'N',
                'AJAX_OPTION_STYLE'               => 'Y',
                'BACKGROUND_IMAGE'                => '-',
                'BASKET_URL'                      => '/personal/basket.php',
                'BROWSER_TITLE'                   => '-',
                'CACHE_FILTER'                    => 'Y',
                'CACHE_GROUPS'                    => 'Y',
                'CACHE_TIME'                      => 3600,
                'CACHE_TYPE'                      => 'N',
                'COMPATIBLE_MODE'                 => 'Y',
                'CONVERT_CURRENCY'                => 'N',
                'CUSTOM_FILTER'                   => 'Y',
                'DETAIL_URL'                      => '',
                'DISABLE_INIT_JS_IN_COMPONENT'    => 'N',
                'DISPLAY_BOTTOM_PAGER'            => 'N',
                'DISPLAY_COMPARE'                 => 'N',
                'DISPLAY_TOP_PAGER'               => 'N',
                'ELEMENT_SORT_FIELD'              => 'RAND',
                'ELEMENT_SORT_ORDER'              => 'ASC',
                'ELEMENT_SORT_FIELD2'             => 'SORT',
                'ELEMENT_SORT_ORDER2'             => 'ASC',
                'ENLARGE_PRODUCT'                 => 'STRICT',
                'FILTER_NAME'                     => 'recFilter',
                'HIDE_NOT_AVAILABLE'              => 'N',
                'HIDE_NOT_AVAILABLE_OFFERS'       => 'N',
                'INCLUDE_SUBSECTIONS'             => 'Y',
                'LABEL_PROP'                      => array(),
                'LAZY_LOAD'                       => 'N',
                'LINE_ELEMENT_COUNT'              => '2',
                'LOAD_ON_SCROLL'                  => 'N',
                'MESSAGE_404'                     => '',
                'MESS_BTN_ADD_TO_BASKET'          => 'В корзину',
                'MESS_BTN_BUY'                    => 'Купить',
                'MESS_BTN_DETAIL'                 => 'Подробнее',
                'MESS_BTN_SUBSCRIBE'              => 'Подписаться',
                'MESS_NOT_AVAILABLE'              => 'Нет в наличии',
                'META_DESCRIPTION'                => '-',
                'META_KEYWORDS'                   => '-',
                'OFFERS_CART_PROPERTIES'          => array('SIZE', 'COLOR', 'CML2_ARTICLE'),
                'OFFERS_FIELD_CODE'               => array('PREVIEW_PICTURE', ''),
                'OFFERS_LIMIT'                    => '0',
                'OFFERS_PROPERTY_CODE'            => array('SIZE', 'COLOR', ''),
                'OFFERS_SORT_FIELD'               => 'CATALOG_AVAILABLE',
                'OFFERS_SORT_ORDER'               => 'DESC',
                'OFFERS_SORT_FIELD2'              => 'SORT',
                'OFFERS_SORT_ORDER2'              => 'ASC',
                'PAGER_BASE_LINK_ENABLE'          => 'N',
                'PAGER_DESC_NUMBERING'            => 'N',
                'PAGER_DESC_NUMBERING_CACHE_TIME' => '36000',
                'PAGER_SHOW_ALL'                  => 'N',
                'PAGER_SHOW_ALWAYS'               => 'N',
                'PAGER_TEMPLATE'                  => '.default',
                'PAGER_TITLE'                     => 'Товары',
                'PAGE_ELEMENT_COUNT'              => 4,
                'PARTIAL_PRODUCT_PROPERTIES'      => 'N',
                'PRICE_CODE'                      => array('BASE'),
                'PRICE_VAT_INCLUDE'               => 'Y',
                'PRODUCT_BLOCKS_ORDER'            => 'price,props,sku,quantityLimit,quantity,buttons,compare',
                'PRODUCT_DISPLAY_MODE'            => 'N',
                'PRODUCT_ID_VARIABLE'             => 'id',
                'PRODUCT_PROPERTIES'              => array(),
                'PRODUCT_PROPS_VARIABLE'          => 'prop',
                'PRODUCT_QUANTITY_VARIABLE'       => 'quantity',
                'PRODUCT_ROW_VARIANTS'            => "[{'VARIANT':'2','BIG_DATA':false},{'VARIANT':'2','BIG_DATA':false},{'VARIANT':'2','BIG_DATA':false},{'VARIANT':'2','BIG_DATA':false},{'VARIANT':'2','BIG_DATA':false},{'VARIANT':'2','BIG_DATA':false}]",
                'PRODUCT_SUBSCRIPTION'            => 'Y',
                'PROPERTY_CODE'                   => array('MORE_PHOTO', ''),
                'PROPERTY_CODE_MOBILE'            => array(),
                'RCM_PROD_ID'                     => '',
                'RCM_TYPE'                        => 'personal',
                'SECTION_CODE'                    => $_REQUEST['SECTION_CODE'],
                'SECTION_ID'                      => '',
                'SECTION_URL'                     => '',
                'SECTION_USER_FIELDS'             => array('', ''),
                'SEF_MODE'                        => 'N',
                'SET_BROWSER_TITLE'               => 'N',
                'SET_LAST_MODIFIED'               => 'N',
                'SET_META_DESCRIPTION'            => 'N',
                'SET_META_KEYWORDS'               => 'N',
                'SET_STATUS_404'                  => 'N',
                'SET_TITLE'                       => 'N',
                'SHOW_404'                        => 'N',
                'SHOW_ALL_WO_SECTION'             => 'N',
                'SHOW_CLOSE_POPUP'                => 'N',
                'SHOW_DISCOUNT_PERCENT'           => 'N',
                'SHOW_FROM_SECTION'               => 'N',
                'SHOW_MAX_QUANTITY'               => 'N',
                'SHOW_OLD_PRICE'                  => 'N',
                'SHOW_PRICE_COUNT'                => '1',
                'SHOW_SLIDER'                     => 'Y',
                'SLIDER_INTERVAL'                 => '3000',
                'SLIDER_PROGRESS'                 => 'N',
                'USE_ENHANCED_ECOMMERCE'          => 'N',
                'USE_MAIN_ELEMENT_SECTION'        => 'N',
                'USE_PRICE_COUNT'                 => 'N',
                'USE_PRODUCT_QUANTITY'            => 'N',
                'LANGUAGE_ID'                     => LANGUAGE_ID,
                'NO_CATALOG'                      => 'Y',
                'IS_RECOMMENDED'                  => 'Y'
            ]
        );
    } ?>
</div>

<div id="viewed" class="hide">
    <?
    $RecentlyViewed = json_decode($_COOKIE["RecentlyViewed"],true);


    if ($RecentlyViewed) {
        global $rvFilter;
        $rvFilter = array("ID" => $RecentlyViewed);

        $APPLICATION->IncludeComponent(
            'bitrix:catalog.section',
            'section.products',
            [
                'IBLOCK_TYPE'                     => CYouWanna::IBLOCK_TYPE_CATALOG,
                'IBLOCK_ID'                       => CYouWanna::getIblockIdByCode(CYouWanna::IBLOCK_CODE_CATALOG),
                'ACTION_VARIABLE'                 => 'action',
                'ADD_PICT_PROP'                   => '-',
                'ADD_PROPERTIES_TO_BASKET'        => 'Y',
                'ADD_SECTIONS_CHAIN'              => 'N',
                'ADD_TO_BASKET_ACTION'            => 'ADD',
                'AJAX_MODE'                       => 'N',
                'AJAX_OPTION_ADDITIONAL'          => '',
                'AJAX_OPTION_HISTORY'             => 'N',
                'AJAX_OPTION_JUMP'                => 'N',
                'AJAX_OPTION_STYLE'               => 'Y',
                'BACKGROUND_IMAGE'                => '-',
                'BASKET_URL'                      => '/personal/basket.php',
                'BROWSER_TITLE'                   => '-',
                'CACHE_FILTER'                    => 'Y',
                'CACHE_GROUPS'                    => 'Y',
                'CACHE_TIME'                      => 3600,
                'CACHE_TYPE'                      => 'N',
                'COMPATIBLE_MODE'                 => 'Y',
                'CONVERT_CURRENCY'                => 'N',
                'CUSTOM_FILTER'                   => 'Y',
                'DETAIL_URL'                      => '',
                'DISABLE_INIT_JS_IN_COMPONENT'    => 'N',
                'DISPLAY_BOTTOM_PAGER'            => 'N',
                'DISPLAY_COMPARE'                 => 'N',
                'DISPLAY_TOP_PAGER'               => 'N',
                'ELEMENT_SORT_FIELD'              => 'RAND',
                'ELEMENT_SORT_ORDER'              => 'ASC',
                'ELEMENT_SORT_FIELD2'             => 'SORT',
                'ELEMENT_SORT_ORDER2'             => 'ASC',
                'ENLARGE_PRODUCT'                 => 'STRICT',
                'FILTER_NAME'                     => 'rvFilter',
                'USE_FILTER'					  => 'Y',
                'HIDE_NOT_AVAILABLE'              => 'N',
                'HIDE_NOT_AVAILABLE_OFFERS'       => 'N',
                'INCLUDE_SUBSECTIONS'             => 'Y',
                'LABEL_PROP'                      => array(),
                'LAZY_LOAD'                       => 'N',
                'LINE_ELEMENT_COUNT'              => '2',
                'LOAD_ON_SCROLL'                  => 'N',
                'MESSAGE_404'                     => '',
                'MESS_BTN_ADD_TO_BASKET'          => 'В корзину',
                'MESS_BTN_BUY'                    => 'Купить',
                'MESS_BTN_DETAIL'                 => 'Подробнее',
                'MESS_BTN_SUBSCRIBE'              => 'Подписаться',
                'MESS_NOT_AVAILABLE'              => 'Нет в наличии',
                'META_DESCRIPTION'                => '-',
                'META_KEYWORDS'                   => '-',
                'OFFERS_CART_PROPERTIES'          => array('SIZE', 'COLOR', 'CML2_ARTICLE'),
                'OFFERS_FIELD_CODE'               => array('PREVIEW_PICTURE', ''),
                'OFFERS_LIMIT'                    => '0',
                'OFFERS_PROPERTY_CODE'            => array('SIZE', 'COLOR', ''),
                'OFFERS_SORT_FIELD'               => 'CATALOG_AVAILABLE',
                'OFFERS_SORT_ORDER'               => 'DESC',
                'OFFERS_SORT_FIELD2'              => 'SORT',
                'OFFERS_SORT_ORDER2'              => 'ASC',
                'PAGER_BASE_LINK_ENABLE'          => 'N',
                'PAGER_DESC_NUMBERING'            => 'N',
                'PAGER_DESC_NUMBERING_CACHE_TIME' => '36000',
                'PAGER_SHOW_ALL'                  => 'N',
                'PAGER_SHOW_ALWAYS'               => 'N',
                'PAGER_TEMPLATE'                  => '.default',
                'PAGER_TITLE'                     => 'Товары',
                'PAGE_ELEMENT_COUNT'              => 9,
                'PARTIAL_PRODUCT_PROPERTIES'      => 'N',
                'PRICE_CODE'                      => array('BASE'),
                'PRICE_VAT_INCLUDE'               => 'Y',
                'PRODUCT_BLOCKS_ORDER'            => 'price,props,sku,quantityLimit,quantity,buttons,compare',
                'PRODUCT_DISPLAY_MODE'            => 'N',
                'PRODUCT_ID_VARIABLE'             => 'id',
                'PRODUCT_PROPERTIES'              => array(),
                'PRODUCT_PROPS_VARIABLE'          => 'prop',
                'PRODUCT_QUANTITY_VARIABLE'       => 'quantity',
                'PRODUCT_ROW_VARIANTS'            => "[{'VARIANT':'2','BIG_DATA':false},{'VARIANT':'2','BIG_DATA':false},{'VARIANT':'2','BIG_DATA':false},{'VARIANT':'2','BIG_DATA':false},{'VARIANT':'2','BIG_DATA':false},{'VARIANT':'2','BIG_DATA':false}]",
                'PRODUCT_SUBSCRIPTION'            => 'Y',
                'PROPERTY_CODE'                   => array('MORE_PHOTO', ''),
                'PROPERTY_CODE_MOBILE'            => array(),
                'RCM_PROD_ID'                     => '',
                'RCM_TYPE'                        => 'personal',
                'SECTION_ID'                      => '',
                'SECTION_URL'                     => '',
                'SECTION_USER_FIELDS'             => array('', ''),
                'SEF_MODE'                        => 'N',
                'SET_BROWSER_TITLE'               => 'N',
                'SET_LAST_MODIFIED'               => 'N',
                'SET_META_DESCRIPTION'            => 'N',
                'SET_META_KEYWORDS'               => 'N',
                'SET_STATUS_404'                  => 'N',
                'SET_TITLE'                       => 'N',
                'SHOW_404'                        => 'N',
                'SHOW_ALL_WO_SECTION'             => 'N',
                'SHOW_CLOSE_POPUP'                => 'N',
                'SHOW_DISCOUNT_PERCENT'           => 'N',
                'SHOW_FROM_SECTION'               => 'N',
                'SHOW_MAX_QUANTITY'               => 'N',
                'SHOW_OLD_PRICE'                  => 'N',
                'SHOW_PRICE_COUNT'                => '1',
                'SHOW_SLIDER'                     => 'Y',
                'SLIDER_INTERVAL'                 => '3000',
                'SLIDER_PROGRESS'                 => 'N',
                'USE_ENHANCED_ECOMMERCE'          => 'N',
                'USE_MAIN_ELEMENT_SECTION'        => 'N',
                'USE_PRICE_COUNT'                 => 'N',
                'USE_PRODUCT_QUANTITY'            => 'N',
                'LANGUAGE_ID'                     => LANGUAGE_ID,
                'NO_CATALOG'                      => 'Y',
                'IS_RECOMMENDED'                  => 'N',
                'IS_RV'                 		  => 'Y',
                'SHOW_ALL_WO_SECTION'             => 'Y'
            ]
        );
    } ?>
</div>


<?
	require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php';
?>
