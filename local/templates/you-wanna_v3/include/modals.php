<?php
/**
 * Created by PhpStorm.
 * Date: 06.03.2018
 * Time: 12:37
 */
?>

<!--noindex-->
<?/* Форма заказа обратного звонка */?>
<div id="callback-modal" class="modal-box hide">
    <div class="callback-modal-wind col col-xl-4 col-lg-5 col-md-6 col-sm-12">
        <span class="close"></span>
        <div class="callback-modal-body">

        </div>
    </div>
</div>
<!--/noindex-->


<!--noindex-->
<div id="js-popup-cart-block" class="modal-box hide">
    <div class="popup-cart-block col col-xl-4 col-lg-5 col-md-6 col-sm-12">
        <span class="close"></span>
        <span class="title"><?= LANGUAGE_ID !== 'ru' ? 'Basket' : 'Корзина' ?></span>
        <div class="js-content-basket"></div>
    </div>
</div>
<!--/noindex-->

<?php
$arFilter = Array("IBLOCK_ID"=> 15, "ID"=> 9053);


$res = CIBlockElement::GetList(Array(), $arFilter);

if ($ob = $res->GetNextElement()){


$arFields = $ob->GetFields(); // Получаем стандартные поля обьекта
$arProps = $ob->GetProperties(); // Получаем кастомные свойства обьекта
}


   /*Вместо IBLOCK_ID, впишите id инфоблока, вместо ELEMENT_ID впишите id элемента*/



       /*Выводим все параметры данного свойства*/





?>

<?php




 // if(isset($_COOKIE['podpis'])) {
     // $cookiePrime = "yes";

 // } else {
     // 86400
     // $domain = ".you-wanna.ru";
     // setcookie('podpis', 'data', time()+14400, '/', $domain);

     // $cookiePrime = "no";
     ?>
     <!-- <script>
        setTimeout(function(){
            $('.fast-check-p').removeClass('undisplay');
            // $('html').addClass('fix-html');
            $('.black-fon').addClass('active-over');
        },5000)
        </script> -->

 <?php
 // }


  ?>
<div class="black-fon">

</div>
<div class="success-podpis undisplay">
    <div class="content-area-success">
        <div class="close-this-modal">
            <div class="in-close-button">

            </div>

        </div>
        <h5>Спасибо!</h5>
        <span>Благодарим за подписку</span>
        <span class="block-ok">Начать покупки</span>
    </div>
</div>
 <!-- fast-check-p  rip -->

<!--noindex-->
<div id="js-fast-ordering-form" class="modal-box hide" data-component="modal">
    <div class="modal popup-fast-ordering-form">
        <div class="close"></div>
        <div class="modal-header"><?= LANGUAGE_ID !== 'ru' ? 'Checkout' : 'Оформить заказ' ?></div>
        <div class="modal-body"></div>
    </div>
</div>

<?
if('Y' != CSite::InDir('/personal/')) { ?>
    <div id="fast-basket" class="modal-box hide">
        <div class="fast-basket-modal col col-xl-4 col-lg-5 col-md-6 col-sm-12">
            <span class="close"></span>
            <div id="basket-root" class="fast-basket-modal-body">
                <?$APPLICATION->IncludeFile(SITE_DIR . '/ajax/get_fast-basket.php');?>
            </div>
        </div>
    </div>
<?}
?>


<div id="video-popup" class="modal-box hide" data-component="modal" data-item-id="">
    <div class="modal video-inner">
        <span class="close"></span>
        <div class="modal-body"></div>
    </div>
</div>

<div id="product-added" class="modal-box hide" data-component="modal" data-item-id="" data-loaded="true">
    <div class="product-added-inner col col-xl-4 col-lg-5 col-md-6 col-sm-12">
        <span class="close"></span>
        <div class="modal-body"></div>
    </div>
</div>
<!--/noindex-->

<!--noindex-->
<div id="js-delivery-free" class="modal-box hide" data-component="modal">
    <div class="modal popup-delivery-free">
        <div class="close"></div>
        <div class="modal-body"></div>
    </div>
</div>
<!--/noindex-->

<!--noindex-->
<?
global $isAdmin;
if ($isAdmin) include('goout.php');;
?>
<!--/noindex-->
