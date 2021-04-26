<?php
declare(strict_types=1);
/**
 * Страница поиска
 *
 * Created by PhpStorm.
 *
 * Date: 03.10.2016
 * Time: 20:16
 *
 * @var CMain $APPLICATION
 */
if (isset($_REQUEST['lang'])) {
    define('LANGUAGE_ID', $_REQUEST['lang']);
}
require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php'; ?>

<?
$q = '';

if ($_REQUEST['q']) {
    $q = trim(htmlspecialchars(strip_tags($_REQUEST['q'])));
} ?>
    <h1 class="page-title text-center">
        <?= $APPLICATION->GetProperty('title'); ?>
    </h1>

    <div class="search-page">
        <form action=""
              method="get">
            <div class="row">
                <div class="col-lg-4 mx-auto">
                    <label for="q"
                           class="control-label"
                           style="display: none;"></label>
                    <input class="form-control"
                           style="margin: 8px 0;"
                           type="text"
                           name="q"
                           id="q"
                           value="<?= $q ?>"
                           size="40"
                           placeholder=""/>
                </div>
            </div>
        </form>
    </div>

<?php if ($_REQUEST['ajax_search'] === 'Y') { // Если идёт ajax запрос от поиска
    $APPLICATION->RestartBuffer();
    $template = 'catalog.search.header';
    if ($_REQUEST['q']) {
        $q = trim(htmlspecialchars(strip_tags($_REQUEST['q'])));
    }
} else {
    $template = 'section.products';
}

if ($q !== '') {

    global $searchFilter;
    $search = '%' . $q . '%';
    $searchFilter[] = [
        'LOGIC' => 'OR',
        [
            'NAME' => '%' . $search . '%'
        ],
        [
            'PROPERTY_NAME_EN' => '%' . $search . '%'
        ],
        [
            'PROPERTY_CML2_ARTICLE' => '%' . $search . '%'
        ]
    ];

    $APPLICATION->IncludeComponent(
        'bitrix:catalog.section',
        $template,
        [
            'IS_SEARCH_PAGE'                  => 'Y',
            'NO_SMART_FILTER'                 => 'Y',
            'IS_AJAX'                         => $_REQUEST['ajax_page'],
            'TITLE_SECTION_FOR_GTM'           => 'Поиск',
            'LANGUAGE_ID'                     => LANGUAGE_ID,
            'SEARCH'                          => $q,
            'TEMPLATE_THEME'                  => 'blue',
            'PRODUCT_DISPLAY_MODE'            => 'N',
            'ADD_PICT_PROP'                   => 'MORE_PHOTO',
            'LABEL_PROP'                      => 'NEW_BOOK',
            'OFFER_ADD_PICT_PROP'             => 'FILE',
            'OFFER_TREE_PROPS'                => '',
            'PRODUCT_SUBSCRIPTION'            => 'N',
            'SHOW_DISCOUNT_PERCENT'           => 'N',
            'SHOW_OLD_PRICE'                  => 'N',
            'SHOW_CLOSE_POPUP'                => 'Y',
            'AJAX_MODE'                       => 'N',
            'SEF_MODE'                        => 'N',
            'IBLOCK_TYPE'                     => 'catalog',
            'IBLOCK_ID'                       => '3',
            'SECTION_ID'                      => '',
            'SECTION_CODE'                    => '',
            'SECTION_USER_FIELDS'             => [],
            'ELEMENT_SORT_FIELD'              => 'CATALOG_AVAILABLE',
            'ELEMENT_SORT_ORDER'              => 'DESC',
            'ELEMENT_SORT_FIELD2'             => 'SORT',
            'ELEMENT_SORT_ORDER2'             => 'ASC',
            'FILTER_NAME'                     => 'searchFilter',
            'INCLUDE_SUBSECTIONS'             => 'Y',
            'SHOW_ALL_WO_SECTION'             => 'Y',
            'SECTION_URL'                     => '/catalog/#SECTION_CODE#/',
            'DETAIL_URL'                      => '',
            'BASKET_URL'                      => '/personal/basket.php',
            'ACTION_VARIABLE'                 => 'action',
            'PRODUCT_ID_VARIABLE'             => 'id',
            'PRODUCT_QUANTITY_VARIABLE'       => 'quantity',
            'ADD_PROPERTIES_TO_BASKET'        => 'Y',
            'PRODUCT_PROPS_VARIABLE'          => 'prop',
            'PARTIAL_PRODUCT_PROPERTIES'      => 'N',
            'SECTION_ID_VARIABLE'             => 'SECTION_ID',
            'ADD_SECTIONS_CHAIN'              => 'N',
            'DISPLAY_COMPARE'                 => 'N',
            'SET_TITLE'                       => 'N',
            'SET_BROWSER_TITLE'               => 'N',
            'BROWSER_TITLE'                   => '-',
            'SET_META_KEYWORDS'               => 'N',
            'META_KEYWORDS'                   => '',
            'SET_META_DESCRIPTION'            => 'N',
            'META_DESCRIPTION'                => '',
            'SET_LAST_MODIFIED'               => 'N',
            'USE_MAIN_ELEMENT_SECTION'        => 'Y',
            'SET_STATUS_404'                  => 'N',
            'PAGE_ELEMENT_COUNT'              => 64,
            'LINE_ELEMENT_COUNT'              => 1,
            'PROPERTY_CODE'                   => ['MORE_PHOTO'],
            'OFFERS_CART_PROPERTIES'          => ['SIZE', 'COLOR', 'CML2_ARTICLE', 'MORE_PHOTO'],
            'OFFERS_FIELD_CODE'               => ['PREVIEW_PICTURE'],
            'OFFERS_PROPERTY_CODE'            => ['SIZE', 'COLOR', ''],
            'OFFERS_LIMIT'                    => 1,
            'BACKGROUND_IMAGE'                => '-',
            'PRICE_CODE'                      => ['BASE'],
            'USE_PRICE_COUNT'                 => 'Y',
            'SHOW_PRICE_COUNT'                => 1,
            'PRICE_VAT_INCLUDE'               => 'Y',
            'PRODUCT_PROPERTIES'              => [],
            'USE_PRODUCT_QUANTITY'            => 'Y',
            'CACHE_TYPE'                      => 'Y',
            'CACHE_TIME'                      => 86400,
            'CACHE_FILTER'                    => 'Y',
            'CACHE_GROUPS'                    => 'Y',
            'DISPLAY_BOTTOM_PAGER'            => 'Y',
            'PAGER_SHOW_ALWAYS'               => 'N',
            'PAGER_TEMPLATE'                  => 'pagenavigation',
            'HIDE_NOT_AVAILABLE'              => 'N',
            'AJAX_OPTION_JUMP'                => 'Y',
            'AJAX_OPTION_STYLE'               => 'Y',
            'AJAX_OPTION_HISTORY'             => 'Y',
            'CONVERT_CURRENCY'                => 'Y',
            'CURRENCY_ID'                     => 'RUB',
            'ADD_TO_BASKET_ACTION'            => 'ADD',
            'PAGER_BASE_LINK_ENABLE'          => 'Y',
            'SHOW_404'                        => 'Y',
            'DISABLE_INIT_JS_IN_COMPONENT'    => 'N',
            'PAGER_BASE_LINK'                 => '',
            'PAGER_PARAMS_NAME'               => 'arrPager',
            'COMPONENT_TEMPLATE'              => 'catalog.products',
            'AJAX_OPTION_ADDITIONAL'          => '',
            'DISPLAY_TOP_PAGER'               => 'N',
            'PAGER_DESC_NUMBERING'            => 'N',
            'PAGER_DESC_NUMBERING_CACHE_TIME' => 0,
            'PAGER_SHOW_ALL'                  => 'N',
            'FILE_404'                        => ''
        ]
    );
} ?>



<?php if ($_REQUEST['ajax_search'] === 'Y') { // Если идёт ajax запрос от поиска
    die;
} ?>

<?php require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php';
