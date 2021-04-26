<?
/**
 * Шаблон компонента
 *
 * @global CMain $APPLICATION
 * @var array    $arParams
 * @var array    $arResult
 */
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

# Вывод языковых фраз
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);
$this->setFrameMode(false);

// Подгрузка информации о постраничной навигации
$navCountPage = $arResult['NAV_RESULT']->NavPageCount;
$navPage = $arResult['NAV_RESULT']->PAGEN;
$navCountItem = $arResult['NAV_RESULT']->NavRecordCount;
$navSize = $arResult['NAV_RESULT']->SIZEN;
$navItemsLeft = $navCountItem - ($navPage * $navSize);
$navNum = $arResult['NAV_RESULT']->NavNum;
if ($navItemsLeft < $navSize) {
    $navSize = $navItemsLeft;
}
$nCatalog = $arParams['NO_CATALOG'];
$isRecom = $arParams['IS_RECOMMENDED'];
$isRV = $arParams['IS_RV'];
$firstProduct = '';
$i = 1;
$hor = '';


// echo "<pre>";
// print_r($arParams);
// echo "</pre>";
// die();

?>
<?if($_REQUEST['ajax']=='Y') $APPLICATION->RestartBuffer();?>
<? if (0 !== count($arResult['ITEMS'])) { ?>
    <?
    if($_COOKIE['catalog_view'] == 'undefined'):
        setcookie("catalog_view", 'biggrid', time()+(3600 * 24 * 7));
        $view = 'biggrid';
    endif;?>
    <div id="js-ajax-nav-section-catalog"
         class="ajaxAutoLoadActive <?= $nCatalog != 'Y' ? 'row ' : 'catalog-detail-page product-slider js-product-slider '?><?='yw-cataloglist yw-cataloglist--' . $view ?>"
         data-last-page="<?= $navCountPage ?>" data-nav-num="<?= $navNum ?>" data-ajax="list">

        <?



        foreach ((array)$arResult['ITEMS'] as $key => $item) { ?>
            <?
            $hor = $item['PROPERTIES']['HOR_PHOTO']['VALUE'] != 0 ? ' yw-catalogitem--hor' : '';

            $item['NAME'] = CYouWanna::multiTranslate($item['NAME'], $arParams['LANGUAGE_ID']);

            $firstOffer = reset($item['OFFERS']);

            $isDiscount = false;
            foreach($item['OFFERS'] as $offer){

                $discountPrice = reset($offer['PRICES'])['DISCOUNT_VALUE'];
                $price = reset($offer['PRICES'])['VALUE'];
                $isDiscount = $discountPrice < $price;
                if($firstProduct === '' && $offer['CAN_BUY']) $firstProduct = $offer['ID'];
                if($firstProduct === '' && $offer['CAN_BUY']) $firstColor = $offer['ID'];
                if($firstProduct === $offer['ID'] && $offer['CAN_BUY']):
                ?>
                <?
                endif;
                if ($isDiscount) {
                    continue;
                }

            }
            ?>
            <div class="<?= $nCatalog == 'Y' ? '' : 'yw-catalogitem' . $hor ?>" data-ajax="item">
                <?
                    /*$morePhArr = $item['DISPLAY_PROPERTIES']['MORE_PHOTO']['FILE_VALUE'];*/
                ?>
                <div class="element-list-item">
                <div class="element-inner js-element-favorite"
                     data-detail="<?php echo $item['DETAIL_PAGE_URL'] ?>"
                     data-element-id="<?php echo $item['ID']?>"
                >
                    <div class="single-item">
                      <div class="single-item-inner">
                      <?
                      if(true){
                        ?>
                        <a href="<?php echo $item['DETAIL_PAGE_URL'] ?>" title="<?php echo $item['NAME'] ?>">
                        <?
                        $k2 = 0;


                        foreach((array)$item['NEW_OFFERS'] as $color=>$valx){

                            // this is necessary for a good preview, although it looks like a magic hook
                            $valx = $item['SLIDER'][$color];
                            if($k2 == 0){
                                $valx[0] = CFile::ResizeImageGet($item['DETAIL_PICTURE']['ID'] ? $item['DETAIL_PICTURE']['ID'] : $item['PREVIEW_PICTURE']['ID'], array("width"=>740, "height"=>1000), BX_RESIZE_IMAGE_EXACT)['src'];
                                if(empty($item['PROPERTIES']['HOVER_PHOTO']['VALUE'])){
                                    $valx[1] = CFile::ResizeImageGet($item['DETAIL_PICTURE']['ID'] ? $item['DETAIL_PICTURE']['ID'] : $item['PREVIEW_PICTURE']['ID'], array("width"=>740, "height"=>1000), BX_RESIZE_IMAGE_EXACT)['src'];
                                }
                                else{
                                $valx[1] = CFile::ResizeImageGet($item['PROPERTIES']['HOVER_PHOTO']['VALUE'], array("width"=>740, "height"=>1000), BX_RESIZE_IMAGE_EXACT)['src'];
                                }
                            }
                                else{
                                if((count($valx)>3)){
                                $valx[0] = $valx[2];
                                $valx[1] = $valx[3];
                                }
                                if(empty($valx[1])){
                                    if(!empty($valx[0])){
                                        $valx[1] = $valx[0];
                                    }
                                    else{
                                        $photo = CFile::ResizeImageGet($item['DETAIL_PICTURE']['ID'] ? $item['DETAIL_PICTURE']['ID'] : $item['PREVIEW_PICTURE']['ID'], array("width"=>740, "height"=>1000), BX_RESIZE_IMAGE_EXACT);
                                        if($photo){
                                            $valx[0] = $photo;
                                            $valx[1] = $photo;
                                        }

                                    }
                                }
                            }
                            $photoUrlWebpDesctop = str_replace(".jpg",".webp",$valx[0]);
                            $checkWEBP = file_exists(SITE_DIR."/home/bitrix/www/".$photoUrlWebpDesctop);
                            if($checkWEBP){

                            } else {
                                $photoUrlWebpDesctop = "";
                            }
                            //Ужимаем фото для мобилки
                            $photoUrlpMobile = CFile::ResizeImageGet($item['DETAIL_PICTURE']['ID'] ? $item['DETAIL_PICTURE']['ID'] : $item['PREVIEW_PICTURE']['ID'], array("width"=>319, "height"=>500), BX_RESIZE_IMAGE_EXACT);
                            $currentFotoForMobile = $photoUrlpMobile['src'];
                            //Делаем маленький вебп для мобилки
                            $photoUrlWenpMobile = str_replace(".jpg",".webp",$currentFotoForMobile);




                            ?>
                            <div data-color='<?=$color?>' class="element__image <?php if( $valx[0]) { echo 'element__image--has-hover'; } if($k2!=0){echo ' hide ';}?>">
                                <picture class="element__image--default">
                                    <source srcset="" data_desctop="<?= $photoUrlWebpDesctop ?>" data_mobile="<?= $photoUrlWenpMobile ?>" type="image/webp">
                                    <img src="/zagr.jpeg" data_desctop="<?= $valx[0] ?>" data_mobile="<?= $currentFotoForMobile  ?>" >
                               </picture>

                                <?php if ( $valx[1]) : ?>
                                    <?php
                                    $photoUrlWebpDesctop = str_replace(".jpg",".webp",$valx[1]);
                                    $checkWEBP = file_exists(SITE_DIR."/home/bitrix/www/".$photoUrlWebpDesctop);
                                    if($checkWEBP){

                                    } else {
                                        $photoUrlWebpDesctop = "";
                                    }
                                    //Ужимаем фото для мобилки
                                    $photoUrlpMobile = CFile::ResizeImageGet($item['DETAIL_PICTURE']['ID'] ? $item['DETAIL_PICTURE']['ID'] : $item['PREVIEW_PICTURE']['ID'], array("width"=>319, "height"=>500), BX_RESIZE_IMAGE_EXACT);
                                    $currentFotoForMobile = $photoUrlpMobile['src'];
                                    //Делаем маленький вебп для мобилки
                                    $photoUrlWenpMobile = str_replace(".jpg",".webp",$currentFotoForMobile);


                                     ?>
                                     <picture class="element__image--hover" style="width:100%;">
                                         <source srcset="" data_desctop="<?= $photoUrlWebpDesctop ?>" data_mobile="<?= $photoUrlWenpMobile ?>" type="image/webp">
                                         <img src=" " data_desctop="<?= $valx[1] ?>" data_mobile="<?= $currentFotoForMobile  ?>" >
                                    </picture>
                                <?php endif;
                                $k2++;
                                ?>
                            </div>
                            <?
                            }?>
                         <?if($hor):?>
                        <div class="element__image element__image__hor <?php if ( $item['PROPERTIES']['HOR_PHOTO_HOVER']['VALUE'] != 0 && !$isRecom ) { echo 'element__image--has-hover'; } ?>">
                            <?$photo_hor = CFile::ResizeImageGet($item['PROPERTIES']['HOR_PHOTO']['VALUE'], array("width"=>1500, "height"=>1000), BX_RESIZE_IMAGE_EXACT);
                            //Делаем вебп под десктоп
                            $photoUrlWebpDesctop = str_replace(".jpg",".webp",$photo_hor['src']);
                            $checkWEBP = file_exists(SITE_DIR."/home/bitrix/www/".$photoUrlWebpDesctop);
                            if($checkWEBP){

                            } else {
                                $photoUrlWebpDesctop = "";
                            }
                            //Ужимаем фото для мобилки
                            $photoUrlpMobile = CFile::ResizeImageGet($item['PROPERTIES']['HOR_PHOTO']['VALUE'], array("width"=>319, "height"=>500), BX_RESIZE_IMAGE_EXACT);
                            $currentFotoForMobile = $photoUrlpMobile['src'];
                            //Делаем маленький вебп для мобилки
                            $photoUrlWenpMobile = str_replace(".jpg",".webp",$currentFotoForMobile);


                            ?>

                            <picture class="element__image--default">
                                <source srcset="" data_desctop="<?= $photoUrlWebpDesctop ?>" data_mobile="<?= $photoUrlWenpMobile ?>" type="image/webp">
                                <img src="/zagr.jpeg" data_desctop="<?= $photo_hor['src'] ?>" data_mobile="<?= $currentFotoForMobile  ?>" >
                           </picture>


                            <?php if ( $item['PROPERTIES']['HOR_PHOTO_HOVER']['VALUE'] != 0 && !$isRecom ) : ?>
                                <?$hover_photo_hor = CFile::ResizeImageGet($item['PROPERTIES']['HOR_PHOTO_HOVER']['VALUE'], array("width"=>1500, "height"=>1000), BX_RESIZE_IMAGE_EXACT);
                                $photoUrlWebpDesctopHOVER = str_replace(".jpg",".webp",$hover_photo_hor['src']);
                                $checkWEBP = file_exists(SITE_DIR."/home/bitrix/www/".$photoUrlWebpDesctopHOVER);
                                if($checkWEBP){

                                } else {
                                    $photoUrlWebpDesctopHOVER = "";
                                }
                                //Ужимаем фото для мобилки
                                $photoUrlpMobileHOVER = CFile::ResizeImageGet($item['PROPERTIES']['HOR_PHOTO_HOVER']['VALUE'], array("width"=>319, "height"=>500), BX_RESIZE_IMAGE_EXACT);
                                $currentFotoForMobileHOVER = $photoUrlpMobileHOVER['src'];
                                //Делаем маленький вебп для мобилки
                                $photoUrlWenpMobileHOVER = str_replace(".jpg",".webp",$currentFotoForMobileHOVER);



                                ?>
                                <picture class="element__image--hover" style="width:100%;">
                                    <source srcset="" data_desctop="<?= $photoUrlWebpDesctopHOVER ?>" data_mobile="<?= $photoUrlWenpMobileHOVER ?>" type="image/webp">
                                    <img src="" data_desctop="<?= $hover_photo_hor['src'] ?>" data_mobile="<?= $currentFotoForMobileHOVER  ?>" >
                               </picture>

                            <?endif?>
                        </div>
                            <?endif;
                            ?>
                            </a>
                            <?

                    }else{
                      ?>
                        <a href="<?php echo $item['DETAIL_PAGE_URL'] ?>" title="<?php echo $item['NAME'] ?>">
                            <div class="element__image <?php if ( $item['PROPERTIES']['HOVER_PHOTO']['VALUE'] != 0 && !$isRecom ) { echo 'element__image--has-hover'; } ?>">
                                <?$photo = CFile::ResizeImageGet($item['DETAIL_PICTURE']['ID'] ? $item['DETAIL_PICTURE']['ID'] : $item['PREVIEW_PICTURE']['ID'], array("width"=>740, "height"=>1000), BX_RESIZE_IMAGE_EXACT);?>
                                <img class="element__image--default" src="<?php echo $photo['src']; ?>" alt="">

                                <?php if ( $item['PROPERTIES']['HOVER_PHOTO']['VALUE'] != 0 && !$isRecom ) : ?>
                                    <?$hover_photo = CFile::ResizeImageGet($item['PROPERTIES']['HOVER_PHOTO']['VALUE'], array("width"=>740, "height"=>1000), BX_RESIZE_IMAGE_EXACT);?>
                                    <img class="element__image--hover" src="<?php echo $hover_photo['src']; ?>" alt="">
                                <?php endif; ?>
                            </div>
                            <?if($hor):?>
                            <div class="element__image element__image__hor <?php if ( $item['PROPERTIES']['HOR_PHOTO_HOVER']['VALUE'] != 0 && !$isRecom ) { echo 'element__image--has-hover'; } ?>">
                                <?$photo_hor = CFile::ResizeImageGet($item['PROPERTIES']['HOR_PHOTO']['VALUE'], array("width"=>1500, "height"=>1000), BX_RESIZE_IMAGE_EXACT);?>
                                <img class="element__image--default" src="<?php echo $photo_hor['src']; ?>" alt="">


                                <?php if ( $item['PROPERTIES']['HOR_PHOTO_HOVER']['VALUE'] != 0 && !$isRecom ) : ?>
                                    <?$hover_photo_hor = CFile::ResizeImageGet($item['PROPERTIES']['HOR_PHOTO_HOVER']['VALUE'], array("width"=>1500, "height"=>1000), BX_RESIZE_IMAGE_EXACT);?>
                                    <img class="element__image--hover" src="<?php echo $hover_photo_hor['src']; ?>" alt="">
                                <?endif?>
                            </div>
                            <?endif?>
                        </a>
                                <?}?>
                        <form role="form" class="yw-catalogitem-form" name="form_product" id="form_<?= $arResult['ID'] ?>" method="post">
                            <input type="hidden" name="product_id" value="<?= $arResult['ID'] ?>">
                            <div class="form-group">
                                <ul class="b-sku-panel-tabs">
                                  <?$k = false;
                                  foreach ((array)$item['NEW_OFFERS'] as $color => $sizes):?>
                                    <li class="b-sku-panel-tab<?= $k ? '' : ' active' ?>" <?
                                        if ($item['COLORS']['HEX'][$color]) {
                                        ?>style="background: <?= $item['COLORS']['HEX'][$color] ?>;" <?
                                              } else {
                                              ?>style="background: url(<?= $item['COLORS']['IMAGES'][$color]['SRC'] ?>)
                                                no-repeat; background-size: cover;"<?
                                        } ?>
                                             data-color="<?=$color;?>"
                                              title="<?= CYouWanna::multiTranslate($sizes['NAME'],
                                                  $arParams['LANGUAGE_ID']) ?>">
                                      </li>
                                  <?$k = true;
                                endforeach?>
                                </ul>
                                <ul class="b-sku-panel-parent">

                                    <? $k = false;
                                    foreach ((array)$item['NEW_OFFERS'] as $color => $sizes):?>
                                        <li class="b-sku-panel-parent-item<?= $k ? '' : ' active' ?>" data-title="<?= CYouWanna::multiTranslate($sizes['NAME'], $arParams['LANGUAGE_ID']) ?>">
                                            <ul class="b-sku-absolute-panel b-sku-size-panel">
                                                <? foreach ($sizes['ITEMS'] as $offer) { ?>
                                                    <? $active = $firstProduct === $offer['ID'] ? true : false;
                                                    ?>
                                                    <!-- <?=$firstProduct?> || <?=$offer['ID']?> || <?=$offer['CAN_BUY']?> -->

                                                    <?
                                                    $price = reset($offer['PRICES'])['VALUE'];
                                                    $discountPrice = '';
                                                    if (reset($offer['PRICES'])['DISCOUNT_VALUE'] < $price) {
                                                        $discountPrice = reset($offer['PRICES'])['DISCOUNT_VALUE'];
                                                    } ?>
                                                    <li class="b-sku-panel-item" data-size-sort="<?=$offer['PROPERTIES']['SIZE']['VALUE_SORT']?>">
                                                        <label class="b-sku-panel-item-label <?= !$offer['CAN_BUY'] ?
                                                            'disabled' : '' ?> <?= $active ? 'checked' : '' ?>"
                                                               for="p<?= $offer['ID'] ?>">
                                                            <input id="p<?= $offer['ID'] ?>"
                                                                   class="b-sku-panel-item-input"
                                                                   type="radio"
                                                                   name="offer"
                                                                   data-color="<?= $color ?>"
                                                                   data-discount-price="<?= $discountPrice
                                                                       ? number_format((double)$discountPrice, 0, '',
                                                                           ' ') . ' <span class=\'rub\'>&#8381;</span>' : '' ?>"
                                                                   data-price="<?= number_format((double)$price, 0, '',
                                                                       ' ') ?> <span class='rub'>&#8381;</span>"
                                                                   value="<?= $offer['ID'] ?>"
                                                                <?= $active ? 'checked' : '' ?>
                                                                <?= !$offer['CAN_BUY'] ? 'disabled' : '' ?>
                                                            />
                                                            <span class="b-sku-panel-item-name">
                                                                <?= $offer['PROPERTIES']['SIZE']['VALUE'] ?>
                                                            </span>
                                                        </label>
                                                    </li>

                                                <? } ?>
                                            </ul>
                                        </li>
                                    <? $k = true;
                                  endforeach?>
                                </ul>
                            </div>
                            <? if ($item['CATALOG_AVAILABLE'] === 'Y') { ?>

                            <? } ?>
                        </form>
                      </div>
                    </div>
                    <?
                    /**
                     * Лейблы - скидка, добавить в избранное
                     */
                    ?>
                    <div class="element-list-item-label">

                        <? $frame = $this->createFrame()->begin('');
                            if ($isDiscount) { ?>
                                <div class="element-list-item-label__item">SALE</div>
                            <? } ?>
                            <? if ( $item['PROPERTIES']['COMING_SON']['VALUE_XML_ID'] === 'Y' ) {?>
                                <div class="element-list-item-label__item">NEW</div>
                            <? } ?>
                        <? $frame->end(); ?>
                        <button class="element-list-item-label__like js-add-to-favorite"></button>
                    </div>

                    <div class="element-list-item-title">
                        <a href="<?= $item['DETAIL_PAGE_URL'] ?>" title="<?= $item['NAME'] ?>"><?= $item['NAME'] ?></a>
                    </div>

                    <div class="element-list-item-price">


                            <div class="element-list-item-price__item border-style" style=" border-bottom: 1px solid black; cursor: pointer;"  data-element-id="<?php echo $item['ID']?>">
                                <a href="<?php echo $item['DETAIL_PAGE_URL'] ?>" style="text-decoration:none; color:black;"><span>ОФОРМИТЬ ПРЕДЗАКАЗ</span></a>
                            </div>

                    </div>
                </div>
                </div>
            </div>
        <? $firstProduct = '';
      } ?>
    </div>
<? } ?>

<?if ($arParams['DISPLAY_BOTTOM_PAGER']):?>
    <?= $arResult['NAV_STRING']; ?>
<?endif?>

<?if($_REQUEST['ajax']=='Y'):?>
    <script>
        $(function () {
            initDetailCatalogTrigger();
        });
    </script>
    <?die();
endif?>
<script type="text/javascript">
    $(document).ready(function(){
       $(document).on('click', 'a.add-to-cart-section-button', function() {
            var element = $(this);
            var productId = element.attr('data-product-id');
            $.ajax({
                url: '<?=$APPLICATION->GetCurPage()?>?action=ADD2BASKET&id='+ productId,
                success: function() {
                    updateSmallBasketQuantity();
                    element.addClass('in-cart');
                }
            });
        });
        updateFavoriteLikeProducts();

        $(document).on('click', '.add-cart', function (e) {
            e.preventDefault();
            if (!$(this).hasClass('in-cart')) {
                var product = $(this).attr('data-product-id');
                var $element = $(this);
                $.ajax({
                    url: '<?=$APPLICATION->GetCurPage()?>?action=BUY&ajax_basket=Y&id=' + product,
                    <?//Функция находится в шаблоне gulliver.wear.sale.basket.small
                    // компонента sale.basket.basket.small ?>
                    success: function () {
                        updateSmallBasketQuantity();
                        $element.addClass('in-cart').html($element.attr('data-text-add'));
                        setTimeout(function(){$element.html($element.attr('data-text-red'))}, 2000)
                        $('#addToBasket').modal();
                        $('#fast-basket').addClass('open');
                        // BX.Sale.BasketComponent.sendRequest('refreshAjax', {fullRecalculation: 'Y'});
                        BX.onCustomEvent('OnBasketChange');
                        <?php
                                            if($_SESSION['logged_in_user_id'] != "") { ?>
                                                $.ajax({
                                                    url: '/',
                                                    type: "POST",
                                                    data: {
                                                        iduser: "<?php echo $_SESSION['logged_in_user_id']; ?>",
                                                        emailuser: "<?php echo $_SESSION['SESS_AUTH']['EMAIL']; ?>",
                                                    },

                                                    success: function (data) {

                                                    }
                                                });
                                            <?php }  ?>

                    }
                });
            } else {
                window.location.href = '<?= $arParams['BASKET_URL'] ?>';
            }
        });

        $(document).on('change', 'input[type=radio]', function () {
            var product = $(this).closest('.yw-catalogitem');
            var button = product.find('.add-cart');
            var dataColor = $(this).attr('data-color');
            var parent = $(this).parent();
            var price = $(this).data('price') ? $(this).data('price') : false;
            var discount = $(this).data('discount-price') ? $(this).data('discount-price') : false;
            var siblings = parent.parent().siblings().find('label');
            button.attr('data-product-id', $(this).val()).removeClass('in-cart');
            button.html('<?= CYouWanna::multiTranslate('Добавить в корзину', LANGUAGE_ID) ?>');

            if(price) product.find('.element-list-item-price__item._new').html(price);
            if(discount) product.find('.element-list-item-price__item._old').html(discount);

            parent.addClass('checked');
            siblings.removeClass('checked');
        });

        $(document).on('click', '.b-sku-panel-tabs li', function() {
          $(this).addClass('active').siblings().removeClass('active');
          $(this).parent().next().find('.b-sku-panel-parent-item:eq(' + $(this).index() + ')').addClass('active').siblings().removeClass('active');
          $(this).parent().next().find('.b-sku-panel-parent-item:eq(' + $(this).index() + ')').find('label').first().trigger('click');
        })

        updateFavoriteLikeProducts();

        if ($('window').outerWidth() > 1024) {
            var systemBtnTopVal = $('.overlay-container').offset().top - 10;
            $('.button-back-section, .share-section').css({'top': systemBtnTopVal});
        }
    });
</script>
<?if(true){
    ?>
    <script>
    windowOuterWidth = window.outerWidth;
    console.log(windowOuterWidth);
    if (windowOuterWidth*1 > 750 ) {

        $( "picture" ).each(function( index ) {
            let desctopimageWEBP = $(this).find('source').attr('data_desctop');
            if (desctopimageWEBP != "") {

                    $(this).find('source').attr('srcset', desctopimageWEBP);
            } else {

            }
            let desctopimageJPG = $(this).find('img').attr('data_desctop');
            $(this).find('img').attr('src', desctopimageJPG);


        });
    } else {


        $( "picture" ).each(function( index ) {
            let mobileimageWEBP = $(this).find('source').attr('data_mobile');
            if (mobileimageWEBP != "") {
                $(this).find('source').attr('srcset', mobileimageWEBP);
            } else {

            }


            let mobileimageJPG = $(this).find('img').attr('data_mobile');
            $(this).find('img').attr('src', mobileimageJPG);

        });
    }
    $(document).ready(function(){
        $( '.yw-catalogitem' ).each(function( index ) {
            var product = $(this);
            if(!product.hasClass("yw-catalogitem--hor")){
                var dataColor = product.find('.yw-catalogitem-form .b-sku-panel-tabs .b-sku-panel-tab.active').attr("data-color");
                $( product.find('.single-item-inner .element__image') ).each(function( index2 ) {
                        if(dataColor == $( this ).attr("data-color")){
                            $( this ).removeClass('hide');
                        }
                        else{
                            $( this ).addClass('hide');
                        }
                    });
            }
        });
        $(document).on('change', 'input[type=radio]', function () {

                var product = $(this).closest('.yw-catalogitem');
                if(!product.hasClass("yw-catalogitem--hor")){
                    var dataColor = $(this).attr('data-color');
                    var parent = $(this).parent();
                    console.log(dataColor);
                    $( product.find('.single-item-inner .element__image') ).each(function( index ) {
                        if(dataColor == $( this ).attr("data-color")){
                            $( this ).removeClass('hide');
                        }
                        else{
                            $( this ).addClass('hide');
                        }
                    });
                }
            });
        });
        $('body').on('DOMNodeInserted', '.yw-catalogitem', function () {
            var product = $(this);
            if(!product.hasClass("yw-catalogitem--hor")){
                var dataColor = product.find('.yw-catalogitem-form .b-sku-panel-tabs .b-sku-panel-tab.active').attr("data-color");
                $( product.find('.single-item-inner .element__image') ).each(function( index2 ) {
                        if(dataColor == $( this ).attr("data-color")){
                            $( this ).removeClass('hide');
                        }
                        else{
                            $( this ).addClass('hide');
                        }
                    });
            }
        });
    </script>
    <?
}?>
