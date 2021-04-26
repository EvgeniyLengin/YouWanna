<?

require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php');
global $USER;
$APPLICATION->SetTitle('YOU WANNA');
$APPLICATION->SetPageProperty("description", "Сеть магазинов модной одежды в Москве");?>
<!--<?=clearDoublePostsInsta();?>-->


<?php

 function isBot() {
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
  foreach ($bots as $bot)
     if (stripos($_SERVER['HTTP_USER_AGENT'], $bot) !== false) {
        return true;
     }
  return false;
}

  if (!isBot()) {
      // Проверка на активность элемента (выводить или нет)
      $arFilterC = array(
      'ID' => '98',
      );

      $isCheck = "";
       $CheckActive = CIBlockElement::GetProperty(17, 9751, "sort", "asc", $arFilterC);
       while($test = $CheckActive->Fetch()){

       $isCheck = $test["VALUE_ENUM"];
        }
        // Конец проверки    (усл)
        // Если check - Y - подключаем и собираем видео
        if ($isCheck == "Y") {
            if(CModule::IncludeModule("iblock")){


                          $desctopUrlMass = [];
                           $db_props = CIBlockElement::GetProperty(17, 9751, "sort", "asc", array());
                             /*Перечисляем все его свойства*/
                            while($ar_props = $db_props->Fetch()){


                                 /*Выводим все параметры данного свойства*/
                               $PAHTVIDEO =  CFile::GetPath($ar_props['VALUE']);
                               $CODE = $ar_props['CODE'];

                               $desctopUrlMass[$CODE][] = $PAHTVIDEO;


                             }

                         }


?>

<div class="wrapper-for-video-in-home" style="position:relative;">

        <div class="wrapper-for-video-block">

            <!-- <div class="color-senkt">

             </div> -->
           <!-- <div class="wrapper-animate-fv">
               <span class="elem _elem1">YOU</span>
               <span class="elem _elem2">WANNA</span>
               <span class="elem _elem3">Moscow</span>
               <span class="elem _elem4">For you © 2015—2021</span>
           </div> -->
        </div>
        <video  style="width: 100%; position:absolute; z-index:2; display: block; height:auto;;" id="myVideo"   muted playsinline >
            <source src="" id="mp4Source" type="video/mp4">
            Your browser does not support the video tag.
        </video>

        <video  style="width: 100%; position:absolute; z-index:1;display: block; height:auto;%;" id="myVideo2"   muted playsinline >
            <source src="" id="mp4Source2" type="video/mp4">
            Your browser does not support the video tag.
        </video>

</div>


<script attr_c="" type="text/javascript">
    var tmp_video = '<?= json_encode($desctopUrlMass); ?>';
    let VideoMass = JSON.parse(tmp_video);

    const screenWidth = window.outerWidth;

    let currentvideomass = "";
    if (screenWidth >= 1800) {
        currentvideomass = VideoMass.VIDEODESCTOP;
    } else if (screenWidth <= 1800 && screenWidth > 1400 ) {
        currentvideomass = VideoMass.VIDEODESCTOP;

    }  else if (screenWidth <= 1400 && screenWidth > 1024 ) {
        currentvideomass = VideoMass.VIDEO1400;
    } else if (screenWidth <= 1024 && screenWidth > 720 ) {
        currentvideomass = VideoMass.VIDEO1024;
    } else if (screenWidth <= 720 && screenWidth > 480 ) {
        currentvideomass = VideoMass.VIDEO720;
    } else {
        currentvideomass = VideoMass.VIDEO420;
    }
    let masscount = currentvideomass.length;
    masscount = masscount - 1;




    $('#myVideo').attr('src', currentvideomass[0]);
    $('#myVideo2').attr('src', currentvideomass[1]);


    var player1=document.getElementById('myVideo');
    var player2=document.getElementById('myVideo2');

    player1.load();
    //ЗАПУСК ПЕРВОГО ВИДЕО ВЫНЕСЕН В script.js (чтобы адекватно работала анимация);
    // player1.play();
    // let h = $('#myVideo').height() ;
    // let pluseheight = Number(screenWidth)/100*6;
    // console.log(h);
    // h = Number(h)+pluseheight;
    // $('.wrapper-for-video-in-home').css('height', h);


    setTimeout(function(){
        player2.load();
    },2000)

    let count = 1;





    jQuery(document).ready(function($)
   {
       $( window ).resize(function() {
           resize_info();
       });
   });

   function resize_info()
   {
       (function($) {
           let h = $('#myVideo').height() ;
           $('.wrapper-for-video-in-home').css('height', h);
       })(jQuery);
   }


        player1.addEventListener('ended', myHandlerended1);

 function myHandlerended1(e)
 {
            $('#myVideo2').css('z-index', "5")
             player2.play();


             if(count > masscount) {
                 count = 0;
             } else {
                 count++;
             }

             $('#myVideo').attr('src', currentvideomass[count]);
             player1.load();
 }






        player2.addEventListener('ended', myHandlerended2);

function myHandlerended2(e)
{
            $('#myVideo2').css('z-index', "1")
            player1.play();


                 if(count > masscount) {
                     count = 0;
                 } else {
                     count++;
                 }


            $('#myVideo2').attr('src', currentvideomass[count]);
            player2.load();
}


</script>
<?php
}
      }

?>






<?$APPLICATION->IncludeComponent(
    "bitrix:news.list",
    "youwanna.slider",
    Array(
        "ACTIVE_DATE_FORMAT" => "d.m.Y",
        "ADD_SECTIONS_CHAIN" => "N",
        "AJAX_MODE" => "N",
        "AJAX_OPTION_ADDITIONAL" => "",
        "AJAX_OPTION_HISTORY" => "N",
        "AJAX_OPTION_JUMP" => "N",
        "AJAX_OPTION_STYLE" => "Y",
        "CACHE_FILTER" => "N",
        "CACHE_GROUPS" => "N",
        "CACHE_TIME" => "3600",
        "CACHE_TYPE" => "N",
        "CHECK_DATES" => "Y",
        "DETAIL_URL" => "",
        "DISPLAY_BOTTOM_PAGER" => "Y",
        "DISPLAY_TOP_PAGER" => "N",
        "FIELD_CODE" => array("", ""),
        "FILTER_NAME" => "",
        "HIDE_LINK_WHEN_NO_DETAIL" => "N",
        "IBLOCK_ID" => "7",
        "IBLOCK_TYPE" => "YouWanna",
        "INCLUDE_IBLOCK_INTO_CHAIN" => "N",
        "INCLUDE_SUBSECTIONS" => "Y",
        "LANGUAGE_ID" => LANGUAGE_ID,
        "MESSAGE_404" => "",
        "NEWS_COUNT" => "50",
        "PAGER_BASE_LINK_ENABLE" => "N",
        "PAGER_DESC_NUMBERING" => "N",
        "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
        "PAGER_SHOW_ALL" => "N",
        "PAGER_SHOW_ALWAYS" => "N",
        "PAGER_TEMPLATE" => ".default",
        "PAGER_TITLE" => "Новости",
        "PARENT_SECTION" => "",
        "PARENT_SECTION_CODE" => "",
        "PREVIEW_TRUNCATE_LEN" => "",
        "PROPERTY_CODE" => array("VIDEO_MOB", "VIDEO", "LINK_NAME", "LINK", "", ""),
        "SET_BROWSER_TITLE" => "N",
        "SET_LAST_MODIFIED" => "N",
        "SET_META_DESCRIPTION" => "N",
        "SET_META_KEYWORDS" => "N",
        "SET_STATUS_404" => "N",
        "SET_TITLE" => "N",
        "SHOW_404" => "N",
        "SORT_BY1" => "SORT",
        "SORT_BY2" => "SORT",
        "SORT_ORDER1" => "ASC",
        "SORT_ORDER2" => "ASC",
        "STRICT_SECTION_CHECK" => "N"
    )
);?>
<?$APPLICATION->IncludeComponent(
    "bitrix:main.include",
    "",
    Array(
        "AREA_FILE_SHOW" => "file",
        "AREA_FILE_SUFFIX" => "inc",
        "COMPOSITE_FRAME_MODE" => "N",
        "COMPOSITE_FRAME_TYPE" => "AUTO",
        "EDIT_TEMPLATE" => "",
        "PATH" => "/local/includes/subscribe-form.php"
    )
);?>
<?$APPLICATION->IncludeComponent(
    "bitrix:news.list",
    "trend",
    array(
        "ACTIVE_DATE_FORMAT" => "d.m.Y",
        "ADD_SECTIONS_CHAIN" => "N",
        "AJAX_MODE" => "N",
        "AJAX_OPTION_ADDITIONAL" => "",
        "AJAX_OPTION_HISTORY" => "N",
        "AJAX_OPTION_JUMP" => "N",
        "AJAX_OPTION_STYLE" => "Y",
        "CACHE_FILTER" => "N",
        "CACHE_GROUPS" => "Y",
        "CACHE_TIME" => "36000000",
        "CACHE_TYPE" => "Y",
        "CHECK_DATES" => "Y",
        "COMPOSITE_FRAME_MODE" => "A",
        "COMPOSITE_FRAME_TYPE" => "AUTO",
        "DETAIL_URL" => "",
        "DISPLAY_BOTTOM_PAGER" => "Y",
        "DISPLAY_DATE" => "Y",
        "DISPLAY_NAME" => "Y",
        "DISPLAY_PICTURE" => "Y",
        "DISPLAY_PREVIEW_TEXT" => "Y",
        "DISPLAY_TOP_PAGER" => "N",
        "FIELD_CODE" => array(
            0 => "",
            1 => "",
        ),
        "FILTER_NAME" => "",
        "HIDE_LINK_WHEN_NO_DETAIL" => "N",
        "IBLOCK_ID" => "12",
        "IBLOCK_TYPE" => "trend",
        "INCLUDE_IBLOCK_INTO_CHAIN" => "Y",
        "INCLUDE_SUBSECTIONS" => "Y",
        "MESSAGE_404" => "",
        "NEWS_COUNT" => "5",
        "PAGER_BASE_LINK_ENABLE" => "N",
        "PAGER_DESC_NUMBERING" => "N",
        "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
        "PAGER_SHOW_ALL" => "N",
        "PAGER_SHOW_ALWAYS" => "N",
        "PAGER_TEMPLATE" => "ajax-scroll",
        "PAGER_TITLE" => "Новости",
        "PARENT_SECTION" => "",
        "PARENT_SECTION_CODE" => "",
        "PREVIEW_TRUNCATE_LEN" => "",
        "PROPERTY_CODE" => array(
            0 => "",
            1 => "MAINTITLE",
            2 => "MAINTITLE_EN",
            3 => "LAYOUT_FOTO",
            4 => "TITLE",
            5 => "TITLE_EN",
            6 => "MAINDESC",
            7 => "MAINDESC_EN",
            8 => "DESC",
            9 => "LINK_MORE",
            10 => "LINK",
            11 => "LAYOUT",
            12 => "PIC1_TITLE",
            13 => "PIC1_TITLE_EN",
            14 => "PIC1_DESC",
            15 => "PIC1_DESC_EN",
            16 => "PIC2_TITLE",
            17 => "PIC2_TITLE_EN",
            18 => "PIC2_DESC",
            19 => "PIC2_DESC_EN",
            20 => "SINGLEPIC_TITLE",
            21 => "SINGLEPIC_TITLE_EN",
            22 => "SINGLEPIC_DESC",
            23 => "SINGLEPIC_DESC_EN",
            24 => "",
        ),
        "SET_BROWSER_TITLE" => "N",
        "SET_LAST_MODIFIED" => "N",
        "SET_META_DESCRIPTION" => "N",
        "SET_META_KEYWORDS" => "N",
        "SET_STATUS_404" => "N",
        "SET_TITLE" => "N",
        "SHOW_404" => "N",
        "SORT_BY1" => "SORT",
        "SORT_BY2" => "ID",
        "SORT_ORDER1" => "ASC",
        "SORT_ORDER2" => "ASC",
        "STRICT_SECTION_CHECK" => "N",
        "COMPONENT_TEMPLATE" => "trend"
    ),
    false
);?>
<?
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php');
