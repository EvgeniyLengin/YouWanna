<?  if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}
use Bitrix\Main\Page\Asset;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

CModule::IncludeModule('currency');
CJSCore::Init(array('ajax', 'currency'));

global $APPLICATION;
global $USER;
global $isIndexPage;
global $isAdmin;
global $isDevMode;
global $isDevSite;
global $isProdSite;
global $isTrend;


$curDir = str_replace('/' . LANGUAGE_ID . '/', '/', $APPLICATION->GetCurDir());

$isDevSite = (0 === strpos($_SERVER['HTTP_HOST'], 'test.'));
$isProdSite = ($_SERVER['SERVER_NAME'] === 'you-wanna.ru');
$isAdmin = $USER->IsAdmin();
$noAdmin = !$USER->IsAdmin();
$isDevMode = $isAdmin || $isDevSite;
$noDevMode = $noAdmin && $isProdSite;

$isIndexPage = '/index.php' === str_replace('/' . LANGUAGE_ID . '/', '/', $APPLICATION->GetCurPage(true));
$isTrend = substr_count($curDir, '/trend-') > 0;

?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <link rel="shortcut icon" type="image/x-icon" href="/favicon.ico">
    <meta name="cmsmagazine" content="80615d77906aa7ce8bba6546fa7268b7" />
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
<?php



CModule::IncludeModule("iblock");

$urlParams = $APPLICATION->GetCurPageParam();


$query_str = parse_url($urlParams, PHP_URL_QUERY);
parse_str($query_str, $query_params);

$element_code = $query_params['ELEMENT_CODE'];

if ($element_code != "") {

$id = CIBlockFindTools::GetElementID($element_id, $element_code, $section_id, $section_code, $arFilter);


$prop = CIBlockElement::GetByID($id)->GetNextElement()->GetProperties();
// print "<pre>"; print_r($prop); print "</pre>";

$tmp_morephoto = $prop['MORE_PHOTO']['VALUE']['0'];

// if ($USER->IsAdmin()) {
//     echo "<pre>";
//     print_r($prop['MORE_PHOTO']);
//     echo "</pre>";
//     $URL = CFile::GetPath($tmp_morephoto);
//     echo "<img src='$URL'>";
// }


$URL = CFile::GetPath($tmp_morephoto);

// print "<pre>"; print_r($_SERVER); print "</pre>";
}

 ?>
    <? $APPLICATION->ShowMeta('title') ?>
    <title><? $APPLICATION->ShowTitle() ?></title>
    <?php if ($URL) { ?>

        <meta property="og:image" content="<?= $URL ?>"/>

    <?php  } ?>


    <!-- Global site tag (gtag.js) - Google Analytics -->
	<!-- <script async src="https://www.googletagmanager.com/gtag/js?id=UA-160660172-1"></script>
	<script>
		window.dataLayer = window.dataLayer || [];
		function gtag(){dataLayer.push(arguments);}
		gtag('js', new Date());

		gtag('config', 'UA-160660172-1');
	</script> -->

    <!-- Google Tag Manager -->
    <!-- <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','GTM-WB9HGWS');</script> -->
    <!-- End Google Tag Manager -->

    <?
    $assets = Asset::getInstance();

    $assets->addCss(SITE_TEMPLATE_PATH . '/css/libs.min.css');
    $assets->addCss(SITE_TEMPLATE_PATH . '/css/fonts.css');
    $assets->addCss(SITE_TEMPLATE_PATH . '/css/jquery.fancybox.css');
    $assets->addCss(SITE_TEMPLATE_PATH . '/css/sale.basket.basket_new.css');
    // $_REQUEST['n'] !== "Y" && ($APPLICATION->GetCurPage(false) == '/personal/basket/' || $APPLICATION->GetCurPage(false) == '/en/personal/basket/' || $APPLICATION->GetCurPage(false) == '/personal/make/' ) ? $assets->addCss(SITE_TEMPLATE_PATH . '/css/sale.basket.basket_new.css') : $assets->addCss(SITE_TEMPLATE_PATH . '/css/sale.basket.basket.css');
    $assets->addCss(SITE_TEMPLATE_PATH . '/js/slick/slick.css');
    $assets->addCss(SITE_TEMPLATE_PATH . '/css/custom.css');
    $assets->addCss(SITE_TEMPLATE_PATH . '/css/landing.css');  // ВЫКИНУТЬ в файл лендинга
    $assets->addCss(SITE_TEMPLATE_PATH . '/css/media.css'); //должен подключаться последним


    $assets->addJs(SITE_TEMPLATE_PATH . '/js/bundle.min.js');
    // $assets->addJs(SITE_TEMPLATE_PATH . '/js/ya.translate.js');
    $assets->addJs(SITE_TEMPLATE_PATH . '/js/jquery.sticky.js');
    $assets->addJs(SITE_TEMPLATE_PATH . '/js/jquery.zoom.js');

    $assets->addJs(SITE_TEMPLATE_PATH . '/js/jquery.maskedinput.js');
    // $assets->addJs(SITE_TEMPLATE_PATH . '/js/goals.js');
    $assets->addJs(SITE_TEMPLATE_PATH . '/js/cookie.min.js');
    $assets->addJs(SITE_TEMPLATE_PATH . '/js/slick/slick.min.js');

    if ($_REQUEST['test']==1) $assets->addJs(SITE_TEMPLATE_PATH . '/js/script1.js');
    else $assets->addJs(SITE_TEMPLATE_PATH . '/js/script.js');
    ?>

    <? $APPLICATION->ShowHead(); ?>

    <? if (!$isDevMode) {
        $APPLICATION->IncludeFile(SITE_DIR . '/include/counters.php', array(), array('MODE' => 'text', 'NAME' => 'Счетчики'));
    } ?>



	<!-- Facebook Pixel Code -->
	<!-- <script async >
		!function(f,b,e,v,n,t,s)
		{if(f.fbq)return;n=f.fbq=function(){n.callMethod?n.callMethod.apply(n,arguments):n.queue.push(arguments)};
		if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
		n.queue=[];t=b.createElement(e);t.async=!0;
		t.src=v;s=b.getElementsByTagName(e)[0];
		s.parentNode.insertBefore(t,s)}(window,document,'script',
		'https://connect.facebook.net/en_US/fbevents.js');
		fbq('init', '843622079372397');
		fbq('track', 'PageView');
	</script>
	<noscript>
		<img height="1" width="1" src="https://www.facebook.com/tr?id=843622079372397&ev=PageView&noscript=1"/>
	</noscript> -->
	<!-- End Facebook Pixel Code -->

    <!-- <script async  >
      (function() {
        var ta = document.createElement('script'); ta.type = 'text/javascript'; ta.async = true;
        ta.src = 'https://analytics.tiktok.com/i18n/pixel/sdk.js?sdkid=BRUQFBKKNCGQHT5KLFKG';
        var s = document.getElementsByTagName('script')[0];
        s.parentNode.insertBefore(ta, s);
      })();
    </script> -->
    <!-- Yandex.Metrika counter -->
    <!-- <script async type="text/javascript" > (function(m,e,t,r,i,k,a){m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)}; m[i].l=1*new Date();k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)}) (window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym"); ym(45473325, "init", { clickmap:true, trackLinks:true, accurateTrackBounce:true, webvisor:true, trackHash:true, ecommerce:"dataLayer" });
     </script> <noscript><div><img src="https://mc.yandex.ru/watch/45473325" style="position:absolute; left:-9999px;" alt="" /></div></noscript> -->
     <!-- /Yandex.Metrika counter -->



</head>
<body>
    <!-- Google Tag Manager (noscript) -->
    <!-- <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-WB9HGWS"height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript> -->
    <!-- End Google Tag Manager (noscript) -->
<div class="page-main-container">
    <div id="panel"><?
    $APPLICATION->ShowPanel();
     ?></div>
<?php


// Получаем поля блока.
CModule::IncludeModule("iblock");
$res = CIBlockElement::GetByID(10741);
if($ar_res = $res->GetNext())
     $isActiveRow = $ar_res['ACTIVE'];
     $textRow = $ar_res['DETAIL_TEXT'];




 ?>
    <div class="header <?= !$isIndexPage ? '' : 'mainpage' ?>">

        <div id="js-sticker" class="header-top<?= !$isIndexPage ? '' : ' mainpage' ?>">
            <?php
            
            if($isActiveRow == "Y") { ?>
                <div class="going-panel">
                    <span class="going-panel__text"> <?= $textRow ?> </span>
                </div>
            <?php } else {

            } ?>



            <div class="header-top-container row">
                <div class="top__search col col-4 hide-sm">

                    <div id="js-header-title-search"
                         class="header-title-search">
                        <form id="js-header-search-form"
                              class="searchform"
                              action="/catalog/search/">

                            <div class="search-submit">
                                <button name="s"
                                        class="go-to-search"
                                        type="submit"
                                        value="">
                                </button>
                            </div>
                            <div class="search-input">
                                <input class="form-control js-header-title-search-open"
                                       id="title-search-input"
                                       type="text"
                                       name="q"
                                       value=""
                                       autocomplete="off"
                                       placeholder="<?= Loc::getMessage('HD_SEARCH') ?>"/>
                            </div>
                        </form>
                    </div>

                    <div class="language-change-block">
                        <? if (LANGUAGE_ID === 'ru') { ?>
                            <a class="language-change" href="https://you-wanna.com/">English</a>
                        <? } else if (LANGUAGE_ID === 'en') { ?>
                            <a class="language-change" href="<?= $curDir ?>?set_lang=ru">Русский</a>
                        <? } ?>
                    </div>

                    <?
                    // \Bitrix\Main\Page\Frame::getInstance()->startDynamicWithID("cookie_city");
                    /*$value = '';
                    $cityCookie = '';
                    $cityIp = '';
                    if ($value = CYouWannaCity::getCityName()) {
                        $cityCookie = $value;
                    } elseif ($value = GeoLocation::getCityByIpAndSetCookie()) {
                        $cityIp = $value['city'];
                    }
                    // Компонент выбора города
                    $APPLICATION->IncludeComponent(
                        'vis.center:choose.city',
                        'header.choose.city',
                        array(
                            'IBLOCK_TYPE'         => CYouWanna::IBLOCK_TYPE_YOUWANNA,
                            'IBLOCK_ID'           => CYouWanna::getIblockIdByCode(CYouWanna::IBLOCK_STORE_CODE),
                            'SECTION_ID'          => '',
                            'SECTION_CODE'        => '',
                            'COUNT_ELEMENTS'      => 'N',
                            'TOP_DEPTH'           => 3,
                            'SECTION_FIELDS'      => array(''),
                            'SECTION_USER_FIELDS' => array('UF_*'),
                            'SECTION_URL'         => '',
                            'CACHE_TYPE'          => 'A',
                            'CACHE_TIME'          => PHP_INT_MAX,
                            'CACHE_NOTES'         => '',
                            'CACHE_GROUPS'        => 'N',
                            'ADD_SECTIONS_CHAIN'  => 'N',
                            'CITY_COOKIE'         => $cityCookie,
                            'CITY_FROM_IP'        => $cityIp,
                            'CHECK_CITY_COOKIE'   => CYouWannaCity::getCheck(),
                            'IP_CLIENTS'          => $_SERVER['REMOTE_ADDR'],
                            'AJAX_ACTION_URL'     => '/ajax/choose_header_city.php',
                            'LANG_ID'             => LANGUAGE_ID
                        )
                    );*/
                    // \Bitrix\Main\Page\Frame::getInstance()->finishDynamicWithID("cookie_city", "");
                    ?>

                </div>

                <div class="col col-4 show-on-small menu-toggle">
                    <a href="#" data-component="toggleme" data-target="#top-catalog-menu"></a>
                </div>
                <div class="top__logo col-6 col-sm-4 " style=''>
                    <?

                   //  $APPLICATION->IncludeComponent(
                   //      'bitrix:main.include',
                   //      '.default',
                   //      array(
                   //          'AREA_FILE_SHOW'     => 'file',
                   //          'COMPONENT_TEMPLATE' => '.default',
                   //          'PATH'               => '/include/logo.php'
                   //      ),
                   //      false
                   // );

                ?>
                <a href="https://you-wanna.ru/">
                <svg xmlns="http://www.w3.org/2000/svg" xml:space="preserve" width="2362px" height="401px" version="1.1" style="shape-rendering:geometricPrecision; text-rendering:geometricPrecision; image-rendering:optimizeQuality; fill-rule:evenodd; clip-rule:evenodd"
                viewBox="0 0 2360.96 400.7"
                 xmlns:xlink="http://www.w3.org/1999/xlink"
                 xmlns:xodm="http://www.corel.com/coreldraw/odm/2003">

                 <g id="Слой_x0020_1">
                  <metadata id="CorelCorpID_0Corel-Layer"/>
                  <g id="_2185907846256">
                   <g>
                    <path class="fil0" d="M498.23 106.68c0,-24.22 -5.28,-53.34 -30.8,-77.27 -26.41,-24.51 -59.56,-29.41 -86.56,-29.41 -26.99,0 -60.14,4.9 -86.55,29.41 -25.52,23.93 -30.8,53.05 -30.8,77.27 0,24.22 5.28,53.33 30.8,77.26 26.41,24.51 59.56,29.41 86.55,29.41 27,0 60.15,-4.9 86.56,-29.41 25.52,-23.93 30.8,-53.04 30.8,-77.26zm-117.36 -79.58c22.6,0 43.14,7.21 56.34,19.89 11.44,10.96 22.3,30.86 22.3,59.69 0,28.83 -10.86,48.72 -22.3,59.68 -13.2,12.68 -33.74,19.89 -56.34,19.89 -22.58,0 -43.12,-7.21 -56.33,-19.89 -11.44,-10.96 -22.29,-30.85 -22.29,-59.68 0,-28.83 10.85,-48.73 22.29,-59.69 13.21,-12.68 33.75,-19.89 56.33,-19.89z"/>
                    <path class="fil0" d="M777.2 5.77l-39.03 0 0 119.36c0,11.82 0,31.13 -13.2,44.4 -7.92,8.07 -22.3,14.41 -46.06,14.41 -12.91,0 -33.45,-1.73 -46.07,-14.41 -12.61,-12.69 -13.2,-29.12 -13.2,-44.4l0 -119.36 -39.03 0 0 122.53c0,20.18 4.93,43.03 22.89,61.41 17.25,17.65 43.13,22.49 75.41,22.49 31.68,0 58.38,-5.48 75.4,-22.49 17.89,-17.88 22.89,-41.52 22.89,-61.41l0 -122.53z"/>
                    <polygon class="fil0" points="1095.57,46.51 1146.61,207.59 1188.87,207.59 1254.3,5.77 1210.58,5.77 1167.15,166.56 1116.7,5.77 1074.44,5.77 1023.4,166.56 979.97,5.77 936.25,5.77 1001.39,207.59 1043.64,207.59 "/>
                    <path class="fil0" d="M1348.05 156.26l100.34 0 23.77 51.33 41.96 0 -93.3 -201.82 -44.02 0 -93.59 201.82 41.96 0 22.88 -51.33zm10.86 -25.94l39.61 -91.4 40.19 91.4 -79.8 0z"/>
                    <polygon class="fil0" points="1620.55,55.16 1736.42,207.59 1780.15,207.59 1780.15,5.77 1741.12,5.77 1741.12,158.2 1625.55,5.77 1581.53,5.77 1581.53,207.59 1620.55,207.59 "/>
                    <polygon class="fil0" points="1902.99,55.16 2018.86,207.59 2062.59,207.59 2062.59,5.77 2023.56,5.77 2023.56,158.2 1907.99,5.77 1863.96,5.77 1863.96,207.59 1902.99,207.59 "/>
                    <path class="fil0" d="M2194.89 156.26l100.35 0 23.76 51.33 41.96 0 -93.3 -201.82 -44.02 0 -93.59 201.82 41.96 0 22.88 -51.33zm10.86 -25.94l39.61 -91.4 40.19 91.4 -79.8 0z"/>
                    <polygon class="fil0" points="111.21,98.32 48.13,5.77 0,5.77 88.9,124.84 88.9,207.59 130.58,207.59 130.58,124.84 219.77,5.77 173.41,5.77 "/>
                   </g>
                   <path class="fil0" d="M730.34 399.71l0 -1.99c-3.14,0 -3.97,-1.65 -3.8,-3.97l0 -65.41 0.16 -0.33 35.27 70.04 35.27 -70.04 0.5 0.33 0 65.41c0,2.32 -0.83,3.97 -4.64,3.97l0 1.99 23.19 0 0 -1.99c-3.81,0 -4.64,-1.65 -4.64,-3.97l0 -75.18c0,-2.32 0.83,-3.98 4.64,-3.98l0 -1.98 -29.64 0 0 1.98c2.98,0 4.14,1 4.14,1.99 0,0.66 -0.5,1.33 -0.67,1.99l-27.98 52.99 -26.99 -52.99c-0.33,-0.5 -0.67,-1.16 -0.67,-1.82 0,-1.16 1,-2.16 3.81,-2.16l0 -1.98 -27.98 0 0 1.98c3.81,0 4.64,1.66 4.64,3.98l0 75.18c0,2.32 -0.83,3.97 -4.31,3.97l0 1.99 19.7 0zm201.35 -88.1c-28.64,0 -49.84,15.9 -49.84,44.55 0,28.65 21.2,44.54 49.84,44.54 28.65,0 49.85,-15.89 49.85,-44.54 0,-28.65 -21.2,-44.55 -49.85,-44.55zm0 11.26c22.19,0 35.28,12.59 35.28,33.29 0,20.7 -13.09,33.28 -35.28,33.28 -22.19,0 -35.27,-12.58 -35.27,-33.28 0,-20.7 13.08,-33.29 35.27,-33.29zm110.94 68.56l1.66 0.99c1.16,-1.32 2.15,-1.98 3.47,-1.98 4.97,0 15.24,10.26 39.08,10.26 28.15,0 43.06,-13.58 43.06,-27.49 0,-42.72 -70.54,-12.58 -70.54,-35.1 0,-8.94 11.26,-15.57 29.14,-15.57 16.06,0 27.32,6.63 27.32,9.77 0,0.67 0,1.33 -0.16,2.16l1.82 1.15 9.11 -19.04 -1.99 -0.83c-0.99,1.16 -1.99,2.16 -4.31,2.16 -4.3,0 -12.91,-6.3 -31.79,-6.3 -24.18,0 -42.72,10.93 -42.72,26.66 0,40.91 70.54,9.14 70.54,34.94 0,9.61 -8.94,16.56 -29.81,16.56 -16.23,0 -33.28,-7.94 -33.28,-14.07 0,-0.66 0.16,-1.33 0.5,-2.15l-2.16 -1.16 -8.94 19.04zm233.31 -20.86l-2.15 1.32c0.33,0.5 0.82,1.49 0.82,2.32 0,4.47 -14.24,15.23 -29.97,15.23 -20.37,0 -40.07,-13.74 -40.07,-33.78 0,-22.85 19.21,-32.79 37.09,-32.79 17.56,0 31.8,8.45 31.8,12.09 0,1 -0.5,1.66 -0.83,2.16l2.15 0.99 10.27 -17.72 -1.99 -1.32c-0.66,0.82 -0.83,1.49 -2.48,1.49 -1.83,0 -5.63,-2.32 -12.09,-4.47 -6.46,-2.16 -15.24,-4.48 -26,-4.48 -31.63,0 -51.5,20.04 -51.5,42.4 0,26.66 21.2,46.69 52.16,46.69 27.66,0 38.09,-13.91 40.41,-13.91 0.99,0 1.82,0.33 2.48,1.16l2.15 -1.49 -12.25 -15.89zm124.02 -58.96c-28.65,0 -49.85,15.9 -49.85,44.55 0,28.65 21.2,44.54 49.85,44.54 28.64,0 49.84,-15.89 49.84,-44.54 0,-28.65 -21.2,-44.55 -49.84,-44.55zm0 11.26c22.19,0 35.27,12.59 35.27,33.29 0,20.7 -13.08,33.28 -35.27,33.28 -22.19,0 -35.27,-12.58 -35.27,-33.28 0,-20.7 13.08,-33.29 35.27,-33.29zm157.96 76.84l0 -1.99c-2.15,0 -3.31,-0.66 -3.31,-1.65 0,-1.49 0.5,-2.65 0.99,-3.98l22.19 -61.93 0.33 0 22.19 60.28c0.66,1.82 1.83,4.14 1.83,5.63 0,0.99 -0.5,1.65 -3.32,1.65l0 1.99 21.86 0 0 -1.99c-2.48,0 -3.31,-0.33 -3.31,-1.49 0,-1.65 0.83,-2.98 1.82,-5.79l26 -70.22c1.65,-4.3 2.65,-5.63 5.46,-5.63l0 -1.98 -20.86 0 0 1.98c2.15,0 3.64,1 3.64,2.32 0,1.33 -0.49,2.16 -0.99,3.65l-22.03 59.61 -0.33 0 -21.52 -58.79c-0.67,-1.82 -1.49,-3.47 -1.49,-4.63 0,-1.66 0.99,-2.16 2.98,-2.16l0 -1.98 -23.02 0 0 1.98c1.66,0 2.48,0.5 2.48,1.99 0,0.66 -0.16,1.49 -0.49,2.49l-22.19 63.09 -0.33 0 -22.36 -59.95c-0.83,-2.15 -1.82,-3.81 -1.82,-5.13 0,-1.49 0.83,-2.49 3.64,-2.49l0 -1.98 -21.69 0 0 1.98c2.98,0 4.3,1.83 5.63,5.63l26.49 70.55c0.5,1.49 1.33,3.64 1.33,5.13 0,1.33 -0.33,1.82 -3.31,1.82l0 1.99 23.51 0z"/>
                  </g>
                 </g>
                </svg>
                </a>

                </div>
                <div class="top__menu col col-4">
                    <ul class="list">
                        <li class="list__item hide-mobile">
                            <button class="list__link call callback-modal-button"></button>
                        </li>
                        <li class="list__item hide-mobile">
                            <a href="/catalog/favorite_prods/" class="list__link like js-link-like">
                                <span class="js-link-like-quantity"></span>
                            </a>
                            <!--noindex-->
                            <div class="empty-block"><?= Loc::getMessage('FAVORITES_EMPTY') ?></div>
                            <!--/noindex-->
                        </li>
                        <li class="list__item">
                            <a href="/personal/basket/" class="list__link cart">
                                <span class="js-link-cart-quantity"></span>
                            </a>
                            <!--noindex-->
                            <div class="empty-block"><?= Loc::getMessage('CART_EMPTY') ?></div>
                            <!--/noindex-->
                        </li>
                        <li class="list__item hide-mobile">
                            <a href="/personal/order/" class="list__link profile" data-user-auth="<?= $USER->IsAuthorized()?>"></a>
                        </li>
                    </ul>
                </div>

                <div id="top-catalog-menu" class="main-menu push-center hide-sm">

                    <div class="header-search">

                        <form action="/catalog/search/">
                            <div class="search-submit-mobile">
                                <button name="mob"
                                        class="go-to-search-mobile"
                                        type="submit"
                                        value="">
                                </button>
                            </div>
                            <input type="text" name="q" value="" autocomplete="off" class="form-control form-control--rounded" placeholder="Поиск по каталогу">
                        </form>
                    </div>
                    <? $APPLICATION->IncludeComponent(
                        'bitrix:menu',
                        'main_v2',
                        array(
                            'ROOT_MENU_TYPE'        => 'main_new',
                            'MAX_LEVEL'             => '2',
                            'CHILD_MENU_TYPE'       => 'new-left',
                            'USE_EXT'               => 'N',
                            'DELAY'                 => 'N',
                            'ALLOW_MULTI_SELECT'    => 'N',
                            'MENU_CACHE_TYPE'       => 'N',
                            'MENU_CACHE_TIME'       => '3600',
                            'MENU_CACHE_USE_GROUPS' => 'Y',
                            'MENU_CACHE_GET_VARS'   => '',
                            'LANGUAGE_ID'           => LANGUAGE_ID
                        )
                    ); ?>
                    <div class="language-change-block hidden">
                        <? if (LANGUAGE_ID === 'ru') { ?>
                            <a class="language-change" href="https://you-wanna.com/">English</a>
                        <? } else if (LANGUAGE_ID === 'en') { ?>
                            <a class="language-change" href="<?= $curDir ?>?set_lang=ru">Русский</a>
                        <? } ?>
                    </div>
                </div>
            </div>
            <div class="header-search-result-container row">
                <div id="js-header-search-result"
                     class="header-search-result col col-8 push-center subscribe-wrapper"></div>
            </div>
        </div>
    </div>

    <? $APPLICATION->IncludeComponent(
        'vis.center:banner.page',
        '',
        array(
            'IBLOCK_TYPE'     => CYouWanna::IBLOCK_TYPE_YOUWANNA,
            'IBLOCK_ID'       => CYouWanna::getIblockIdByCode(CYouWanna::IBLOCK_BANNERS_ON_PAGE),
            'CACHE_TYPE'      => 'A',
            'CACHE_TIME'      => 36000000,
            'FILTER_PROPERTY' => 'SHOW_ON_PAGE',
            'CUR_PAGE'        => $APPLICATION->GetCurDir(),
        )
    );

    ?>
    <?php if (true) {
//         echo "<pre>";
// print_r($USER);
//         echo "</pre>";
//         die('s');
        $customUserId = $_SESSION['logged_in_user_id'];
        // echo "<pre>";
        // print_r($customUserId);
        // echo "</pre>";
        // die('zzzz');
        $countSal = 0;
        $arFilter = Array(
          "USER_ID" => $customUserId, // Это ID пользователя
        );

         CModule::IncludeModule("sale");

      $db_sales = CSaleOrder::GetList(array(), $arFilter);

      while ($ar_sales = $db_sales->Fetch())
      {   $countSal = count($ar_sales);
          // print_r($countSal);
          // die('sss');
          break;

      }
         ?>

    <input type="hidden" class="useridCustom" name="" value="<?= $customUserId ?>">
    <input type="hidden" class="countOffers" name="" value="<?= $countSal ?>">
    <?




 }




 ?>
<input type="hidden" class="cuRpage" name="" value="<?=$APPLICATION->GetCurPage()?>">
    <div class="page-wrapper">
        <div class="push-center <?= ($isIndexPage || $isTrend) ? 'full-page' : 'container container--content' ?>">
