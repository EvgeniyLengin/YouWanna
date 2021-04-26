<?
/**
 * Загрузка компонента Корзины
 */

if (!isset($WITHOUT_BX_HEADER)) {
    define('NO_KEEP_STATISTIC', true);
    define('STOP_STATISTICS', true);
    define('NO_AGENT_STATISTIC', true);
    define('NO_AGENT_CHECK', true);
    define('NOT_CHECK_PERMISSIONS', true);
    define('PERFMON_STOP', true);
    define('LANGUAGE_ID', $_REQUEST['lang']);

    require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';
}

CModule::IncludeModule('currency');
CJSCore::Init('currency');

$APPLICATION->IncludeComponent(
    "bitrix:sale.basket.basket.line",
    "template",
    Array(
        "COMPONENT_TEMPLATE" => "template",
        "HIDE_ON_BASKET_PAGES" => "Y",
        "MAX_IMAGE_SIZE" => "70",
        "PATH_TO_AUTHORIZE" => "",
        "PATH_TO_BASKET" => SITE_DIR."personal/basket/",
        "PATH_TO_ORDER" => SITE_DIR."personal/basket/",
        "PATH_TO_PERSONAL" => SITE_DIR."personal/",
        "PATH_TO_PROFILE" => SITE_DIR."personal/",
        "PATH_TO_REGISTER" => SITE_DIR."login/",
        "POSITION_FIXED" => "N",
        "SHOW_AUTHOR" => "N",
        "SHOW_DELAY" => "N",
        "SHOW_EMPTY_VALUES" => "Y",
        "SHOW_IMAGE" => "Y",
        "SHOW_NOTAVAIL" => "N",
        "SHOW_NUM_PRODUCTS" => "Y",
        "SHOW_PERSONAL_LINK" => "Y",
        "SHOW_PRICE" => "Y",
        "SHOW_PRODUCTS" => "Y",
        "SHOW_REGISTRATION" => "N",
        "SHOW_SUMMARY" => "Y",
        "SHOW_TOTAL_PRICE" => "Y"
    )
);

// $APPLICATION->IncludeComponent(
//     'bitrix:sale.basket.basket',
//     'main.basket_fast',
//     array(
//         'STATIC_BASKET'                   => $STATIC_BASKET,
//         "AJAX_MODE" => "N",
//         "AJAX_OPTION_JUMP" => "N",
//         "AJAX_OPTION_STYLE" => "Y",
//         "AJAX_OPTION_HISTORY" => "N",
//         'ACTION_VARIABLE'                 => 'action',
//         'ADDITIONAL_PICT_PROP_3'          => '-',
//         'ADDITIONAL_PICT_PROP_4'          => '-',
//         'AUTO_CALCULATION'                => 'Y',
//         'BASKET_IMAGES_SCALING'           => 'no_scale',
//         'COLUMNS_LIST_EXT'                => array(
//             0 => 'PREVIEW_PICTURE',
//             1 => 'DELETE',
//             // 2 => 'DELAY',
//             //3 => 'TYPE',
//             // 4 => 'SUM',
//         ),
//         'COLUMNS_LIST_MOBILE'             => array(
//             0 => 'PREVIEW_PICTURE',
//             1 => 'DELETE',
//             // 2 => 'DELAY',
//             // 3 => 'SUM',
//         ),
//         'COMPATIBLE_MODE'                 => 'Y',
//         'CORRECT_RATIO'                   => 'Y',
//         'DEFERRED_REFRESH'                => 'N',
//         'DISCOUNT_PERCENT_POSITION'       => 'top-right',
//         'DISPLAY_MODE'                    => 'extended',
//         'GIFTS_BLOCK_TITLE'               => 'Выберите один из подарков',
//         'GIFTS_CONVERT_CURRENCY'          => 'N',
//         'GIFTS_HIDE_BLOCK_TITLE'          => 'N',
//         'GIFTS_HIDE_NOT_AVAILABLE'        => 'N',
//         'GIFTS_MESS_BTN_BUY'              => 'Выбрать',
//         'GIFTS_MESS_BTN_DETAIL'           => 'Подробнее',
//         'GIFTS_PAGE_ELEMENT_COUNT'        => '4',
//         'GIFTS_PLACE'                     => 'BOTTOM',
//         'GIFTS_PRODUCT_PROPS_VARIABLE'    => 'prop',
//         'GIFTS_PRODUCT_QUANTITY_VARIABLE' => 'quantity',
//         'GIFTS_SHOW_DISCOUNT_PERCENT'     => 'Y',
//         'GIFTS_SHOW_IMAGE'                => 'Y',
//         'GIFTS_SHOW_NAME'                 => 'Y',
//         'GIFTS_SHOW_OLD_PRICE'            => 'N',
//         'GIFTS_TEXT_LABEL_GIFT'           => 'Подарок',
//         'HIDE_COUPON'                     => $STATIC_BASKET ? 'N' : 'Y',
//         'LABEL_PROP'                      => array(),
//         'LABEL_PROP_MOBILE'               => '',
//         'LABEL_PROP_POSITION'             => '',
//         'OFFERS_PROPS'                    => array(
//             'COLOR',
//             'SIZE',
//         ),
//         'PATH_TO_ORDER'                   => '/personal/make/',
//         'PRICE_DISPLAY_MODE'              => 'Y',
//         'PRICE_VAT_SHOW_VALUE'            => 'N',
//         'PRODUCT_BLOCKS_ORDER'            => 'props,sku,columns',
//         'QUANTITY_FLOAT'                  => 'N',
//         'SET_TITLE'                       => 'N',
//         'SHOW_DISCOUNT_PERCENT'           => 'N',
//         'SHOW_FILTER'                     => 'N',
//         'SHOW_RESTORE'                    => 'Y',
//         'TEMPLATE_THEME'                  => 'blue',
//         'TOTAL_BLOCK_DISPLAY'             => array(
//             0 => 'bottom',
//         ),
//         'USE_DYNAMIC_SCROLL'              => 'Y',
//         'USE_ENHANCED_ECOMMERCE'          => 'N',
//         'USE_GIFTS'                       => 'N',
//         'USE_PREPAYMENT'                  => 'N',
//         'USE_PRICE_ANIMATION'             => 'N',
//         'COMPONENT_TEMPLATE'              => '.default',
//         'LANGUAGE_ID'                     => LANGUAGE_ID
//     ),
//     false
// );
