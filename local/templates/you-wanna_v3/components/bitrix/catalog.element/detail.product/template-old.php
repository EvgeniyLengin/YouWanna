<?php
/**
 * Шаблон компонента
 *
 * @var CMain $APPLICATION
 * @var array $arParams
 * @var array $arResult
 */
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Localization\Loc;

//use Bitrix\Main\Page\Asset;

Loc::loadMessages(__FILE__);
$this->setFrameMode(true);

if ($arParams['IS_AJAX'] === 'Y') {
    ob_start();
}

# если просмотр со списка товаров, то очищаем буфер
if ($arParams['FROM_PRODUCT_LIST'] === 'Y') {
    $APPLICATION->RestartBuffer();
}

$arFirstOffer = reset($arResult['NEW_OFFERS'])['ITEMS'];
$firstOffer = reset($arFirstOffer);
$discountPrice = reset($firstOffer['PRICES'])['DISCOUNT_VALUE'];
$price = reset($firstOffer['PRICES'])['VALUE'];

$isDiscount = $discountPrice < $price;

//$assets = Asset::getInstance();

$arResult['NAME'] = CYouWanna::multiTranslate($arResult['NAME'], $arParams['LANGUAGE_ID']);
$arResult['SECTION']['NAME'] = CYouWanna::multiTranslate($arResult['SECTION']['NAME'], $arParams['LANGUAGE_ID']);

?>
    <div class="catalog-detail-page js-element-favorite"
         data-element-id="<?= $arResult['ID'] ?>">
        <? if (!empty($arResult['SECTION']['NAME'])) { ?>
            <div class="catalog-detail-page__back">
                <div class="button-back-section">
                    <a href="<?= $arResult['SECTION']['SECTION_PAGE_URL'] ?>"
                       title="<?= $arResult['SECTION']['NAME'] ?>">
                        <?= CYouWanna::multiTranslate($arResult['SECTION']['NAME'], $arParams['LANGUAGE_ID']) ?>
                    </a>
                </div>
            </div>
        <? } ?>
        <? if ($_REQUEST['fromProductList'] !== 'Y') { ?>
            <? if (!empty($arResult['LEFT_ELEMENT'])) { ?>
<!--                <div class="button-more-detail-product prev">-->
<!--                    <a href="--><?//= $arResult['LEFT_ELEMENT']['URL'] ?><!--"-->
<!--                       title="--><?//= Loc::getMessage('CATALOG_PRODUCT_PREV') ?><!-- - &quot;--><?//= CYouWanna::multiTranslate($arResult['LEFT_ELEMENT']['NAME'],
//                           $arParams['LANGUAGE_ID']) ?><!--&quot;">-->
<!--                    </a>-->
<!--                </div>-->
            <? } ?>
            <? if (!empty($arResult['RIGHT_ELEMENT'])) { ?>
<!--                <div class="button-more-detail-product next">-->
<!--                    <a href="--><?//= $arResult['RIGHT_ELEMENT']['URL'] ?><!--"-->
<!--                       title="--><?//= Loc::getMessage('CATALOG_PRODUCT_NEXT') ?><!-- - &quot;--><?//= CYouWanna::multiTranslate($arResult['RIGHT_ELEMENT']['NAME'],
//                           $arParams['LANGUAGE_ID']) ?><!--&quot;">-->
<!--                    </a>-->
<!--                </div>-->
            <? } ?>
        <? } ?>
        <div class="row">
            <div class="col-md-8">
                <? // $frame = $this->createFrame()->begin(''); ?>
                <? // if ($isDiscount) { ?>
                <? // } ?>
                <? // $frame->end(); ?>


                <?
                    /*foreach ((array)$arResult['SLIDER_BIG'] as $color => $arColor) :
                    $cPhotos = count($arColor);
                ?>
                    <div class="product-gallery" data-color="<?= $color ?>">
                        <div class="product-gallery__grid">
                            <div class="product-gallery__full js-full-image" >
                                <div class="product-gallery__clear-photo js-clear-photo-container">
                                    <svg class="icon icon-close"><use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprites/svg/symbols.svg#icon-close"></use></svg>
                                </div>
                            </div>
                            <? foreach ((array)$arColor as $key => $picture) : ?>
                                <div class="product-gallery__item js-zoom-item" data-img-big="<?= $arResult['SLIDER_BIG'][$color][$key] ?>">
                                    <img src="<?= $arResult['SLIDER_BIG'][$color][$key] ?>" alt="<?= $arResult['NAME'] ?>">
                                </div>
                            <? endforeach; ?>

                        </div>
                    </div>
                <? endforeach; */?>

                <? foreach ((array)$arResult['SLIDER_BIG'] as $color => $arColor) :
                    $cPhotos = count($arColor); ?>

                    <div class="<? if ($cPhotos > 1) { ?>owl-carousel<? } ?> detail-product-slider deactivated"
                         data-color="<?= $color ?>"
                         data-text-prev=" "
                         data-text-next=" ">
                        <? foreach ((array)$arColor as $key => $picture) { ?>
                            <div class="overlay-container overlay-visible js-image-zoom"
                                 data-img-big="<?= $arResult['SLIDER_BIG'][$color][$key] ?>">
                                <img src="<?= $arResult['SLIDER_BIG'][$color][$key] ?>"
                                     alt="<?= $arResult['NAME'] ?>">
                            </div>
                        <? } ?>
                    </div>

                <? endforeach; ?>
            </div>
            <div class="col-md-4">

                <div class="product-info">
                    <h2 id="current-product-name">
                        <?= $arResult['NAME'] ?>
                        <span id="current-product-article"><?= Loc::getMessage('CT_BCE_CATALOG_PRODUCT_ARTICLE'); ?>:
                        <?= $arResult['PROPERTIES']['CML2_ARTICLE']['VALUE'] ?></span>
                    </h2>

                    <a class="element-like add2favorite" rel="nofollow" href="#favorite"></a>

                    <div class="row">

                        <div id="detail-price-<?= $arResult['ID'] ?>" class="col-12">
                            <? //$frame = $this->createFrame('detail-price-'.$arResult['ID'], false)->begin(''); ?>
                            <div class="detail-element-list-price row">
                                <?  if ($isDiscount) { ?>
                                    <span class="price discount-price js-discount-price col col-3 align-right">
                                        <?= number_format((double)$price, 0, '', ' ') ?> <span class="rub">&#8381;</span>
                                    </span>
                                    <span class="price js-price col col-5 align-left">
                                        <?= number_format((double)$discountPrice, 0, '', ' ') ?> <span class="rub">&#8381;</span>
                                    </span>
                                <?  } else { ?>
                                    <span class="price col col-12">
                                        <?= number_format((double)$price, 0, '', ' ') ?> &#8381;
                                    </span>
                                <? } ?>
                                <? if ($arResult['PROPERTIES']['NEW_PRODUCT']['VALUE_XML_ID'] === 'Y') { ?>
                                    <span class="detail-price-label-new"><?= Loc::getMessage('CT_BCS_CATALOG_NEW'); ?></span>
                                <? } ?>
                                <? if ($isDiscount) { ?>
                                    <span class="discount-badge"><?= Loc::getMessage('CT_BCS_CATALOG_DISCOUNT'); ?></span>
                                <? } ?>

                            </div>
                            <? // $frame->end(); ?>
                        </div>

                        <div class="detail-content col col-12">



                            <? if(isset($arResult['DETAIL_TEXT'])) : ?>
                                <div class="product-info__description">
                                    <?
                                        echo '<h4>'. Loc::getMessage('DESCRIPTION'). '</h4>';
                                        echo '<p>'. $arResult["DETAIL_TEXT"] .'</p>';
                                    ?>
                                </div>
                            <? endif; ?>

                            <div class="product-info__props props" data-component="collapse">

                                <?
                                    if ( isset( $arResult['PROPERTIES']['COMPOSITION']['VALUE'] ) && !empty( $arResult['PROPERTIES']['COMPOSITION']['VALUE'] ) ) :
                                        echo '<div class="props__item">';
                                        echo '<a href="#box-composition" class="props__title collapse-toggle">Состав</a>';
                                        echo '<div id="box-composition" class="props__content collapse-box hide">' . htmlspecialcharsBack($arResult['PROPERTIES']['COMPOSITION']['VALUE']['TEXT']) . '</div>';
                                        echo '<div class="props__icon"></div>';
                                        echo '</div>';
                                    endif;
                                ?>

                                <?
                                    if ( isset( $arResult['PROPERTIES']['MEASUREMENTS']['VALUE'] ) && !empty( $arResult['PROPERTIES']['MEASUREMENTS']['VALUE'] ) ) :
                                        echo '<div class="props__item">';
                                        echo '<a href="#box-measurement" class="props__title collapse-toggle">Обмеры</a>';
                                        echo '<div id="box-measurement" class="props__content collapse-box hide">' . htmlspecialcharsBack($arResult['PROPERTIES']['MEASUREMENTS']['VALUE']['TEXT']) . '</div>';
                                        echo '<div class="props__icon"><a href="#box-measurement" class="collapse-toggle"></a></div>';
                                        echo '</div>';
                                    endif;
                                ?>

                                <?
                                    if ( isset( $arResult['PROPERTIES']['PRODUCT_CARE']['VALUE'] ) && !empty( $arResult['PROPERTIES']['PRODUCT_CARE']['VALUE'] ) ) :
                                        echo '<div class="props__item">';
                                        echo '<a href="#box-care" class="props__title collapse-toggle">Уход за изделием</a>';
                                        echo '<div id="box-care" class="props__content collapse-box hide">' . htmlspecialcharsBack($arResult['PROPERTIES']['PRODUCT_CARE']['VALUE']['TEXT']) . '</div>';
                                        echo '<div class="props__icon"><a href="#box-care" class="collapse-toggle"></a></div>';
                                        echo '</div>';
                                    endif;
                                ?>
                            </div>





                    </div>

                    <? /*php if ($arResult['SHOW_CHARACTIRISTICS']) : ?>
                    <div class="properties-block col-12">
                        <h3><?= Loc::getMessage('CT_BCE_CATALOG_PRODUCT_CHARACTERSISTICS'); ?></h3>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <tbody>
                                    <?php foreach ((array)$arResult['PROPERTIES'] as $characteristic) : ?>
                                        <?php if (null !== $characteristic['VALUE']
                                            && '' !== trim($characteristic['VALUE'])
                                            && in_array(
                                                $characteristic['CODE'],
                                                $arParams['CHARACTERISTIC_FOR_SHOW'],
                                                true
                                            )
                                        ) : ?>
                                            <tr>
                                                <td><?= Loc::getMessage($characteristic['NAME']); ?></td>
                                                <td>
                                                    <?php if ($characteristic['CODE'] === 'TSVET') : ?>
                                                        <?= CYouWanna::translate(
                                                            $characteristic['VALUE'],
                                                            LANGUAGE_ID
                                                        ); ?>
                                                    <?php else : ?>
                                                        <?= $characteristic['VALUE'] ?>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php endif; */ ?>

                    <div class="colors col col-12">
                        <? $firstProduct = ''; ?>
                        <form role="form"
                              class="clearfix margin-full-clear padding-full-clear"
                              name="form_product"
                              id="form_<?= $arResult['ID'] ?>"
                              action=""
                              method="post">
                            <input type="hidden" name="product_id" value="<?= $arResult['ID'] ?>">
                            <div class="form-group">
                                <ul class="b-sku-panel-parent">
                                    <label class="b-sku-active-color"></label>
                                    <? foreach ((array)$arResult['NEW_OFFERS'] as $color => $sizes) { ?>
                                        <li class="b-sku-panel-parent-item<?= $firstProduct ? '' : ' active' ?>">
                                            <span class="b-sku-panel-parent-item-name" <?
                                            if ($arResult['COLORS']['HEX'][$color]) {
                                            ?>style="background: <?= $arResult['COLORS']['HEX'][$color] ?>;" <?
                                                  } else {
                                                  ?>style="background: url(<?= $arResult['COLORS']['IMAGES'][$color]['SRC'] ?>)
                                                    no-repeat; background-size: cover;"<?
                                            } ?>
                                                  title="<?= CYouWanna::multiTranslate($sizes['NAME'],
                                                      $arParams['LANGUAGE_ID']) ?>">
                                            </span>
                                            <ul class="b-sku-absolute-panel">

                                                <label class="b-sku-size-heading"><?= Loc::getMessage('CT_BCE_CATALOG_PRODUCT_SIZE') ?></label>
                                                <? foreach ($sizes['ITEMS'] as $offer) { ?>
                                                    <? if ($firstProduct === '' && $offer['CAN_BUY']) {
                                                        $firstProduct = $offer['ID'];
                                                    }
                                                    $price = reset($offer['PRICES'])['VALUE'];
                                                    $discountPrice = '';
                                                    if (reset($offer['PRICES'])['DISCOUNT_VALUE'] < $price) {
                                                        $discountPrice = reset($offer['PRICES'])['DISCOUNT_VALUE'];
                                                    } ?>
                                                    <li class="b-sku-panel-item">
                                                        <label class="b-sku-panel-item-label <?= !$offer['CAN_BUY'] ?
                                                            'disabled' : '' ?> <?= $firstProduct === $offer['ID'] ? 'checked' : '' ?>"
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
                                                                <?= $firstProduct === $offer['ID'] ? 'checked' : '' ?>
                                                                <?= !$offer['CAN_BUY'] ? 'disabled' : '' ?>
                                                            />
                                                            <span class="b-sku-panel-item-name">
                                                                <?= $offer['PROPERTIES']['SIZE']['VALUE'] ?>
                                                            </span>
                                                        </label>
                                                    </li>

                                                <? } ?>
                                                <? /*?>
                                            <li class="b-sku-panel-item size-table">
                                                <label class="b-sku-panel-item-label">
                                                    <span class="b-sku-panel-item-name">
                                                            <a href="#">Таблица размеров</a>
                                                        </span>
                                                </label>
                                            </li>
                                            <?*/ ?>
                                            </ul>
                                        </li>
                                    <? } ?>
                                </ul>
                            </div>
                            <? if ($arResult['CATALOG_AVAILABLE'] === 'Y') { ?>
                                <div class="form-group">
                                    <a type="button"
                                       value="<?/* if ($arResult['IN_BASKET'] === 'Y') { ?><?= Loc::getMessage('CT_BCE_CATALOG_ADD_TO_BASKET_OK'); ?><? } else { */?><?// } ?>"
                                       class="add-cart"
                                       data-text-add="<?= Loc::getMessage('CT_BCE_CATALOG_ADD_TO_BASKET_OK'); ?>"
                                        <?= !$firstProduct ? ' disabled="disabled"' : '' ?>
                                       data-product-id="<?= $firstProduct ?>"><?= Loc::getMessage('CT_BCE_CATALOG_BUY'); ?></a>
                                </div>

                                <? if ($_REQUEST['fromProductList'] !== 'Y') { ?>
                                    <div class="form-group">
                                        <input type="button"
                                               value="<?= Loc::getMessage('QUICK_CHECKOUT'); ?>"
                                               class="js-load-form-order" />
                                    </div>
                                <? } ?>
                            <? } ?>
                        </form>
                    </div>

                </div>
                </div>

            </div>
        </div>

        <script>
            $(document).ready(function () {
                var $firstElement = $('input.b-sku-panel-item-input:checked');
                changePriceForDetailPage($firstElement);
                var dataColor = $firstElement.attr('data-color');
                var activeColor = $('.active .b-sku-panel-parent-item-name').prop('title');
                $('.b-sku-active-color').html(activeColor);
                $('.detail-product-slider').removeClass('active').addClass('deactivated');
                $('.detail-product-slider[data-color=' + dataColor + ']').removeClass('deactivated').addClass('active');

                zoomProductItem();

                $('.add-cart').unbind('click').click(function (e) {
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
                                $element.addClass('in-cart').val($element.attr('data-text-add'));
                                $('#addToBasket').modal();
                            }
                        });
                    } else {
                        window.location.href = '<?= $arParams['BASKET_URL'] ?>';
                    }
                });
                $('input[type=radio]').unbind('change').change(function () {
                    var button = $('.add-cart');
                    button.attr('data-product-id', $(this).val()).removeClass('in-cart');
                    button.val('<?= Loc::getMessage('CT_BCE_CATALOG_BUY'); ?>');

                    changePriceForDetailPage($(this));
                    $('.b-sku-panel-item-label').removeClass('checked');
                    $(this).parent().addClass('checked');

                    var dataColor = $(this).attr('data-color');
                    $('.detail-product-slider').removeClass('active').addClass('deactivated');
                    $('.detail-product-slider[data-color=' + dataColor + ']').removeClass('deactivated').addClass('active');
                });

                initDetailOwl();
                initDetailSkuTrigger();
                updateFavoriteLikeProducts();
                if ($('window').outerWidth() > 1024) {
                    var systemBtnTopVal = $('.overlay-container').offset().top - 10;
                    $('.button-back-section, .share-section').css({'top': systemBtnTopVal});
                }

                initZoomDetailImage();

                /*// Bind a click event to a Cloud Zoom instance.
                $('#cloud-zoom').bind('click',function(){
                    // On click, get the Cloud Zoom object,
                    var cloudZoom = $(this).data('cloud-zoom');
                    // Close the zoom window (from 2.1 rev 1211291557)
                    cloudZoom.closeZoom();
                    // and pass Cloud Zoom's image list to Fancy Box.
                    $.fancybox.open(cloudZoom.getGalleryList());
                    return false;
                });*/
                $(window).resize(function(){
                    if ($(window).width() < 769) {
                        destroyDetailOwl()
                        initDetailOwl();
                    } else {
                        destroyDetailOwl()
                        initDetailOwl();
                    }
                })
            });
        </script>

    </div>
<?php # если просмотр со списка товаров, то далее выводить ничего не нужно
if ($arParams['FROM_PRODUCT_LIST'] === 'Y') {
    die();
} ?>

<? if ($arParams['IS_AJAX'] === 'Y') { ?>
    <? $templateData['AJAX_HTML'] = ob_get_contents();
    ob_end_clean();
} ?>
