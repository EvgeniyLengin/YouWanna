<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

global $APPLICATION;
global $isIndexPage;
global $isTrend;
global $countSlides;
global $noDevMode;
global $isDevMode; ?>

<? if (!$isTrend) { ?>

    </div><!-- /content -->
    </div><!-- /page-wrapper -->
    <div class="scroll-top js-scroll-top">
        <div class="scroll-top__inner">
            <img src="<?= SITE_TEMPLATE_PATH ?>/img/arrow-up.svg" alt="">
        </div>
    </div>
<div class="footer">
    <? } else { ?>

    <div class="section fp-section main-footer" id="#page<?= $countSlides ?>">
        <div class="container">
            <div class="row">
                <div class="col-md-8 push-center subscribe-wrapper">
                    <div class="subscribe-heading">
                        <?= Loc::getMessage('FT_ACTIONS_TEXT') ?>
                    </div>
                    <div class="subscribe-meta">
                        <?= Loc::getMessage('FT_PROMISES') ?>
                    </div>
                    <? $APPLICATION->IncludeComponent(
                        "bitrix:sender.subscribe",
                        "subscribe.main.page",
                        array(
                            "COMPONENT_TEMPLATE"      => ".default",
                            "USE_PERSONALIZATION"     => "N",
                            "CONFIRMATION"            => "N",
                            "HIDE_MAILINGS"           => "N",
                            "SHOW_HIDDEN"             => "N",
                            "USER_CONSENT"            => "N",
                            "USER_CONSENT_ID"         => "0",
                            "USER_CONSENT_IS_CHECKED" => "Y",
                            "USER_CONSENT_IS_LOADED"  => "N",
                            "AJAX_MODE"               => "Y",
                            "AJAX_OPTION_JUMP"        => "N",
                            "AJAX_OPTION_STYLE"       => "Y",
                            "AJAX_OPTION_HISTORY"     => "N",
                            "AJAX_OPTION_ADDITIONAL"  => "",
                            "CACHE_TYPE"              => "A",
                            "CACHE_TIME"              => "3600",
                            "SET_TITLE"               => "N"
                        ),
                        false
                    ); ?>
                    <div class="subscribe-agree">
                        <?= Loc::getMessage('FT_CONF_TEXT') ?>
                    </div>
                </div>
            </div>
        </div>

        <? } ?>

        <div class="footer__navigation">
            <div class="container">
                <div class="row">
                    <div class="col-xl-8 col-lg-8 mx-auto <?= !$isIndexPage ?: 'push-bottom' ?> navigation">
                        <div class="row">
                            <div class="col col-4 menu left">
                                <? $APPLICATION->IncludeComponent(
                                    'bitrix:menu',
                                    'bottom',
                                    array(
                                        'TITLE'                 => Loc::getMessage('FT_BUYER'),
                                        'ROOT_MENU_TYPE'        => 'buyer',
                                        'MAX_LEVEL'             => '1',
                                        'CHILD_MENU_TYPE'       => '',
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
                            </div>
                            <div class="col-4 menu center">
                                <? $APPLICATION->IncludeComponent(
                                    'bitrix:menu',
                                    'bottom',
                                    array(
                                        'TITLE'                 => Loc::getMessage('FT_COMPANY'),
                                        'ROOT_MENU_TYPE'        => 'company',
                                        'MAX_LEVEL'             => '1',
                                        'CHILD_MENU_TYPE'       => '',
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
                            </div>
                            <div class="col-4 menu right">
                                <? $APPLICATION->IncludeComponent(
                                    'bitrix:menu',
                                    'bottom',
                                    array(
                                        'TITLE'                 => Loc::getMessage('FT_PAYMENT'),
                                        'ROOT_MENU_TYPE'        => 'payment',
                                        'MAX_LEVEL'             => '1',
                                        'CHILD_MENU_TYPE'       => '',
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
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <? if ($isIndexPage && $isTrend) { ?>

    </div>
    </div>
    <? $countSlides++ ?>
</div><!-- /content -->


    </div><!-- /page-wrapper -->


<? } ?>

<div class="footer__copyright <?= !$isIndexPage ?: 'footer__copyright--fixed' ?>">
    <div class="container">
        <div class="row">
            <div class="col-lg-2 col-md-2 col-sm-3 col-4 shops-link">
                <a href="/shops/moskva/">
                    <?= Loc::getMessage('FT_SHOPS') ?>
                </a>
            </div>
            <div class="col-lg-8 col-md-7 col-sm-6 col-4 center">
                <div class="row">
                    <div class="col-xl-4 col-lg-4 col-md-6 hide-mobile"><span class="est">© 2015—<? echo date('Y'); ?> YOU WANNA</span></div>
                    <div class="col-xl-4 col-lg-6 col-md-6 col-12">
                        <div class="social-links">
                            <a href="https://www.facebook.com/youwannamoscow/" target="_blank" class="facebook-link"></a>
                            <a href="https://www.instagram.com/youwanna_official/" target="_blank" class="instagram-link"></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-sm-3 col-4 contacts-link">
                <a href="tel:+79268877007" class="show-mobile ">
                    <?= Loc::getMessage('FT_CALL') ?>
                </a>
                <a href="/about/contacts/" class="hide-mobile">
                    <?= Loc::getMessage('FT_CONTACTS') ?>
                </a>
            </div>
        </div>
    </div>
</div>
<? if (!$isIndexPage && !$isTrend) { ?>
    </div>
<? } ?>

<? $APPLICATION->IncludeFile(
    SITE_TEMPLATE_PATH . '/include/modals.php',
    array(),
    array(
        'MODE' => 'text',
        'NAME' => 'Модальные окна'
    )
) ?>


<? if ($isIndexPage) {
    $assets->addJs(SITE_TEMPLATE_PATH . '/js/mainpage.js');
}
?>

<? if ($noDevMode) { ?>

<!-- calltouch -->
<script async type="text/javascript">
(function(w,d,n,c){w.CalltouchDataObject=n;w[n]=function(){w[n]["callbacks"].push(arguments)};if(!w[n]["callbacks"]){w[n]["callbacks"]=[]}w[n]["loaded"]=false;if(typeof c!=="object"){c=[c]}w[n]["counters"]=c;for(var i=0;i<c.length;i+=1){p(c[i])}function p(cId){var a=d.getElementsByTagName("script")[0],s=d.createElement("script"),i=function(){a.parentNode.insertBefore(s,a)};s.type="text/javascript";s.async=true;s.src="https://mod.calltouch.ru/init.js?id="+cId;if(w.opera=="[object Opera]"){d.addEventListener("DOMContentLoaded",i,false)}else{i()}}})(window,document,"ct","cczl1vws");
</script>

<!-- <script async type="text/javascript">
    jQuery(document).on('mousedown touchstart', '#bx_subscribe_btn_sljzMT', function() { window.ct('goal','sub') });
    jQuery(document).on('mousedown touchstart', 'a[href="/instashop/"]', function() { window.ct('goal','inst') });
    jQuery(document).on('mousedown touchstart', 'a[href="/about/contacts/"]', function() { window.ct('goal','contacts') });
    jQuery(document).on('mousedown touchstart', '.header button.callback-modal-button', function() { window.ct('goal','call') });
    jQuery(document).on('mousedown touchstart', 'a[href="/personal/basket/"]', function() { window.ct('goal','basket') });
    jQuery(document).on('mousedown touchstart', 'a[href="/personal/order/"]', function() { window.ct('goal','lk') });
</script> -->

<script async type="text/javascript" >
jQuery(document).on('click', 'form input[type="submit"]', function() {
	var m = jQuery(this).closest('form');
    var fio = m.find('input[placeholder*="имя"]').val();
    var phone = m.find('input[placeholder*="телефон"]').val();
    var comment = m.find('textarea[placeholder*="Сообщение"]').val();
    var ct_site_id = '37720';
    var sub = 'Заказать звонок c ' + location.hostname;
    var ct_data = {
        fio: fio,
        phoneNumber: phone,
        subject: sub,
        comment: comment,
        sessionId: window.call_value
    };
	console.log(ct_data);
    if (!!fio && !!phone){
        jQuery.ajax({
            url: 'https://api-node14.calltouch.ru/calls-service/RestAPI/requests/'+ct_site_id+'/register/',
            dataType: 'json', type: 'POST', data: ct_data, async: false
        });
    }
});
</script>

<script async type="text/javascript" >
jQuery(document).on('click', '#ORDER_FORM #ORDER_CONFIRM_BUTTON', function() {
	var m = jQuery(this).closest('#ORDER_FORM');
    var fio = m.find('#FIO').val();
    var phone = m.find('#PHONE').val();
    var mail = m.find('#EMAIL').val();
    var city = m.find('#LOCATION').val();
    var adres = m.find('#ADDRESS').val();
    var comment = m.find('#ORDER_DESCRIPTION').val();
    var ct_site_id = '37720';
    var sub = 'Оформление заказа c ' + location.hostname;
    var ct_data = {
        fio: fio,
        phoneNumber: phone,
        subject: sub,
        comment: comment,
        sessionId: window.call_value
    };
	console.log(ct_data);
    if (!!fio && !!phone && !!mail && !!city && !!adres){
        jQuery.ajax({
            url: 'https://api-node14.calltouch.ru/calls-service/RestAPI/requests/'+ct_site_id+'/register/',
            dataType: 'json', type: 'POST', data: ct_data, async: false
        });
    }
});
</script>
<!-- calltouch -->

<!-- Yandex.Metrika counter -->
<!-- <script async type="text/javascript" > (function(m,e,t,r,i,k,a){m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)}; m[i].l=1*new Date();k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)}) (window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym"); ym(45473325, "init", { clickmap:true, trackLinks:true, accurateTrackBounce:true, webvisor:true, trackHash:true, ecommerce:"dataLayer" });
 </script> <noscript><div><img src="https://mc.yandex.ru/watch/45473325" style="position:absolute; left:-9999px;" alt="" /></div></noscript> -->
 <!-- /Yandex.Metrika counter -->


    <!-- <script src="https://www.googletagmanager.com/gtag/js?id=UA-107276732-1"></script> -->
    <!-- <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-WB9HGWS"height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <script async>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','GTM-WB9HGWS');</script> -->
    <!-- <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments)
        };
        gtag('js', new Date());

        gtag('config', 'UA-107276732-1');
    </script> -->

<? }


 ?>








 <?
   /* Эта функция будет проверять, является ли посетитель роботом поисковой системы */
   $bots = array(
      'rambler', 'googlebot', 'aport', 'yahoo', 'msnbot', 'turtle', 'mail.ru', 'omsktele',
      'yetibot', 'picsearch', 'sape.bot', 'sape_context', 'gigabot', 'snapbot', 'alexa.com',
      'megadownload.net', 'askpeter.info', 'igde.ru', 'ask.com', 'qwartabot', 'yanga.co.uk',
      'scoutjet', 'similarpages', 'oozbot', 'shrinktheweb.com', 'aboutusbot', 'followsite.com',
      'dataparksearch', 'google-sitemaps', 'appEngine-google', 'feedfetcher-google',
      'liveinternet.ru', 'xml-sitemaps.com', 'agama', 'metadatalabs.com', 'h1.hrn.ru',
      'googlealert.com', 'seo-rus.com', 'yaDirectBot', 'yandeG', 'yandex',
      'yandexSomething', 'Copyscape.com', 'AdsBot-Google', 'domaintools.com',
      'Nigma.ru', 'bing.com', 'dotnetdotcom', 'Chrome-Lighthouse'
   );

   $check = true;
   foreach ($bots as $bot)
      if (stripos($_SERVER['HTTP_USER_AGENT'], $bot) !== false) {
            $check = false;
      }



   if($check) { ?>
       <script defer type="text/javascript" src="<?= SITE_TEMPLATE_PATH ?>/metrickandgooglescreepts.js"></script>
<script src="//code-ya.jivosite.com/widget/6PhYzLIIJi" async></script>
 <? } ?>



</body>
</html>
