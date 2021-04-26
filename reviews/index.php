<? require($_SERVER['DOCUMENT_ROOT'].'/bitrix/header.php'); ?>

<div id="form-review"></div>

<?
$reviewsFilter = array (
    "ACTIVE" => "Y",

);
?>
<? $APPLICATION->IncludeComponent(
    'bitrix:news.list',
    'youwanna.reviews.page',
    array(
        'IBLOCK_TYPE'               => "YouWanna",
        'IBLOCK_ID'                 => 5,
        'NEWS_COUNT'                => 300,
        'SORT_BY1'                  => 'SORT',
        'SORT_ORDER1'               => 'ASC',
        'FIELD_CODE'                => array(),
        'PROPERTY_CODE'             => array('*'),
        'SET_TITLE'                 => 'N',
        'SET_BROWSER_TITLE'         => 'N',
        'SET_META_KEYWORDS'         => 'N',
        'SET_META_DESCRIPTION'      => 'N',
        'SET_LAST_MODIFIED'         => 'N',
        'INCLUDE_IBLOCK_INTO_CHAIN' => 'N',
        'ADD_SECTIONS_CHAIN'        => 'N',
        'CACHE_TYPE'                => 'N',
        'CACHE_TIME'                => PHP_INT_MAX,
        'CACHE_FILTER'              => 'Y',
        'CACHE_GROUPS'              => 'N',
        "CHECK_DATES"               => 'Y',
        'FILTER_NAME'               => 'reviewsFilter'
    )
); ?>

<? require($_SERVER['DOCUMENT_ROOT'].'/bitrix/footer.php');
