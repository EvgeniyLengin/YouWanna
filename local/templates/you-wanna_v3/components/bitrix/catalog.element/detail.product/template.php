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


//
// echo "<pre>";
// print_r($arResult['NEW_OFFERS']);
// echo "</pre>";



use Bitrix\Main\Localization\Loc;

//use Bitrix\Main\Page\Asset;

Loc::loadMessages(__FILE__);
$this->setFrameMode(true);

if ($arParams['IS_AJAX'] === 'Y') {
    ob_start();
}



$clearBask = $_GET['clear'];
$productIdForBask = $_GET['prodid'];
    if ($clearBask == "true") {

        $res = CSaleBasket::GetList(array(), array(
                                      'FUSER_ID' => CSaleBasket::GetBasketUserID(),
                                      'LID' => SITE_ID,
                                      'ORDER_ID' => 'null',
                                      'DELAY' => 'N',
                                      'CAN_BUY' => 'Y'));
        while ($row = $res->fetch()) {
           CSaleBasket::Delete($row['ID']);
        }


        CModule::IncludeModule("sale");
        CModule::IncludeModule("catalog");
        Add2BasketByProductID($productIdForBask);


}


# если просмотр со списка товаров, то очищаем буфер
if ($arParams['FROM_PRODUCT_LIST'] === 'Y') {
    $APPLICATION->RestartBuffer();
}

$arFirstOffer = reset($arResult['NEW_OFFERS'])['ITEMS'];

$propz =CIBlockElement::GetByID($arResult['ID'])->GetNextElement()->GetProperties();
$propCheck = $propz['COMING_SON']['~VALUE'];



$firstOffer = reset($arFirstOffer);
$discountPrice = reset($firstOffer['PRICES'])['DISCOUNT_VALUE'];
$price = reset($firstOffer['PRICES'])['VALUE'];

$isDiscount = $discountPrice < $price;

//$assets = Asset::getInstance();

$arResult['NAME'] = CYouWanna::multiTranslate($arResult['NAME'], $arParams['LANGUAGE_ID']);
$arResult['SECTION']['NAME'] = CYouWanna::multiTranslate($arResult['SECTION']['NAME'], $arParams['LANGUAGE_ID']);

?>
<div class="custom-modal-cooming-soon">
    <div class="forexit">
        <div class="exit-button">
            <div class="exit-form">

            </div>
        </div>

<?$APPLICATION->IncludeComponent("bitrix:form","",Array(
        "AJAX_MODE" => "Y",
        "SEF_MODE" => "Y",
        "WEB_FORM_ID" => "3",
        "RESULT_ID" => $_REQUEST["RESULT_ID"],
        "START_PAGE" => "new",
        "SHOW_LIST_PAGE" => "Y",
        "SHOW_EDIT_PAGE" => "Y",
        "SHOW_VIEW_PAGE" => "Y",
        "SUCCESS_URL" => "/success.php",
        "SHOW_ANSWER_VALUE" => "Y",
        "SHOW_ADDITIONAL" => "Y",
        "SHOW_STATUS" => "Y",
        "EDIT_ADDITIONAL" => "Y",
        "EDIT_STATUS" => "Y",
        "NOT_SHOW_FILTER" => Array(),
        "NOT_SHOW_TABLE" => Array(),
        "CHAIN_ITEM_TEXT" => "",
        "CHAIN_ITEM_LINK" => "",
        "IGNORE_CUSTOM_TEMPLATE" => "Y",
        "USE_EXTENDED_ERRORS" => "Y",
        "CACHE_TYPE" => "A",
        "CACHE_TIME" => "3600",
        "AJAX_OPTION_JUMP" => "N",
        "AJAX_OPTION_STYLE" => "Y",
        "AJAX_OPTION_HISTORY" => "N",
        "SEF_FOLDER" => "",
        "SEF_URL_TEMPLATES" => Array(

        ),
        "VARIABLE_ALIASES" => Array(
            "new" => Array(),
            "list" => Array(),
            "edit" => Array(),
            "view" => Array(),
        )
    )
);?>
    </div>
</div>

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
            <div class="col-lg-8">
                <?
                    foreach ((array)$arResult['SLIDER_BIG'] as $color => $arColor) :
                    $cPhotos = count($arColor);
                ?>
                    <div class="product-gallery product-gallery--deactivated" data-color="<?= $color ?>">
                        <div class="product-gallery__grid">
                            <? foreach ((array)$arColor as $key => $picture) : ?>
                                <div class="product-gallery__item js-zoom-item" data-img-big="<?= $arResult['SLIDER_BIG'][$color][$key] ?>">
                                    <img src="<?= $arResult['SLIDER_BIG'][$color][$key] ?>" alt="<?= $arResult['NAME'] ?>">
                                </div>
                            <? endforeach; ?>
                        </div>
                        <div class="product-gallery__slider js-product-gallery-slider">
                            <div class="swiper-container">
                                <div class="swiper-wrapper">
                                    <? foreach ((array)$arColor as $key => $picture) : ?>
                                        <div class="swiper-slide">
                                            <img class="swiper-lazy" data-src="<?= $arResult['SLIDER_BIG'][$color][$key] ?>" alt="<?= $arResult['NAME'] ?>">
                                            <div class="swiper-lazy-preloader swiper-lazy-preloader-black"></div>
                                        </div>
                                    <? endforeach; ?>
                                </div>
                                <div class="swiper-pagination"></div>
                            </div>
                        </div>
                    </div>
                <? endforeach; ?>
            </div>

            <div class="col-lg-4">

                <div class="product-info">
                    <h2 id="current-product-name">
                        <?= $arResult['NAME'] ?>
                        <span id="current-product-article"><?= Loc::getMessage('CT_BCE_CATALOG_PRODUCT_ARTICLE'); ?>:
                        <?= $arResult['PROPERTIES']['CML2_ARTICLE']['VALUE'] ?></span>
                    </h2>

                    <a class="element-like add2favorite  js-add-to-favorite" rel="nofollow" href="#favorite"></a>

                    <div class="row">

                        <div id="detail-price-<?= $arResult['ID'] ?>" class="col-12">
                            <div class="detail-element-list-price row">
                                <?  if ($isDiscount) : ?>
                                    <span class="price discount-price js-discount-price col col-3 align-right">
                                        <?= number_format((double)$price, 0, '', ' ') ?> <span class="rub">&#8381;</span>
                                    </span>
                                    <span class="price js-price col col-5 align-left">
                                        <?= number_format((double)$discountPrice, 0, '', ' ') ?> <span class="rub">&#8381;</span>
                                    </span>
                                <? else : ?>
                                    <span class="price col col-12">
                                        <?= number_format((double)$price, 0, '', ' ') ?> &#8381;
                                    </span>
                                <? endif; ?>

                                <? if ($arResult['PROPERTIES']['NEW_PRODUCT']['VALUE_XML_ID'] === 'Y') : ?>
                                    <span class="detail-price-label-new"><?= Loc::getMessage('CT_BCS_CATALOG_NEW'); ?></span>
                                <? endif; ?>

                                <? if ($isDiscount) : ?>
                                    <span class="discount-badge"><?= Loc::getMessage('CT_BCS_CATALOG_DISCOUNT'); ?></span>
                                <? endif; ?>
                            </div>
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
                                        echo '<div class="props__icon"><a href=#box-composition  rel =#box-composition class=collapse-toggle></a></div>';
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

<?//if ($USER->IsAdmin()){?>
	<div class="props__item">
		<a href="#box-stock" class="props__title collapse-toggle">Наличие в магазинах</a>
		<div id="box-stock" class="props__content collapse-box hide">
			<? $bFirstSKU = true;?>
                        <? foreach ((array)$arResult['NEW_OFFERS'] as $color => $sizes) { ?>
			<div id="<?=$color?>-stores" class="stock-table"<?=($bFirstSKU ? '' : ' style="display:none;"')?>>
				<div class="stock-line">
					<?foreach($sizes['ITEMS'] as $size => $arSize){?>
					<div class="stock-th"><?=$arSize['PROPERTIES']['SIZE']['VALUE']?></div>
					<?}?>
				</div>
<?

foreach($arResult['RA_STORES'] as $arStore){?>

<?php
        if ($arStore['NAME'] == "ТЦ «ХОРОШО»" ) continue;
            ?>
            <div class="stock-line tc-line">
                <div class="stock-td"><?=$arStore['NAME']?></div>
                <div class="stock-td"><a href="tel:<?=$arStore['PHONE']?>" class="ra-phone"><?=$arStore['PHONE']?></a></div>
            </div>


            <div class="stock-line stock-check-line">
                <?foreach($sizes['ITEMS'] as $size => $arSize){?>
                    <?
                    $strStoresQtys = $arSize['PROPERTIES']['STORES_QTY']['~VALUE'];
                    $arStoresQtys = $strStoresQtys ? unserialize($strStoresQtys) : array();
                    ?>
                <div class="stock-td<?if($arStoresQtys[$arStore['ID']]){?> in-stock<?}?>"></div>
                <?}?>
            </div>

    <?


?>

<? } ?>
			</div>
				<?$bFirstSKU = false;?>
			<?}?>
		</div>
		<div class="props__icon"><a href="#box-stock" class="collapse-toggle"></a></div>
	</div>
<?//}?>

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
                                            <ul class="b-sku-absolute-panel b-sku-size-panel">

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
                                                    <li class="b-sku-panel-item" data-size-sort="<?=$offer['PROPERTIES']['SIZE']['VALUE_SORT']?>">
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
                                    <?php
                                        if ($propCheck == "Да") { ?>

                                            <input type="button"
                                                   value="Оформить предзаказ"
                                                   class="loadform-js-predz" />

                                    <?php     } else { ?>
                                            <a type="button"
                                               value="<?/* if ($arResult['IN_BASKET'] === 'Y') { ?><?= Loc::getMessage('CT_BCE_CATALOG_ADD_TO_BASKET_OK'); ?><? } else { */?><?// } ?>"
                                               class="add-cart"
                                               data-text-add="<?= Loc::getMessage('CT_BCE_CATALOG_ADD_TO_BASKET_OK'); ?>"
                                                <?= !$firstProduct ? ' disabled="disabled"' : '' ?>
                                               data-product-id="<?= $firstProduct ?>"><?= Loc::getMessage('CT_BCE_CATALOG_BUY'); ?></a>

                                    <?php    }

                                    ?>

                                </div>

                                <? if ($_REQUEST['fromProductList'] !== 'Y') { ?>
                                    <div class="form-group">
                                        <?php if ($propCheck == "Да") {

                                        } else {?>

                                        <input type="button"
                                               value="<?= Loc::getMessage('QUICK_CHECKOUT'); ?>"
                                               class="js-load-form-order" />
                                           <?php } ?>
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

                // $('.detail-product-slider').removeClass('active').addClass('deactivated');
                // $('.detail-product-slider[data-color=' + dataColor + ']').removeClass('deactivated').addClass('active');

                changeActiveGallery(dataColor);

                zoomDetailImage();
                productGallerySwiper();

                $(window).resize(function() {
                    zoomDetailImage();
                    //productGallerySwiper();
                });

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

                $('input[type=radio]').unbind('change').change(function () {
                    var button = $('.add-cart');
                    var dataColor = $(this).attr('data-color');

                    button.attr('data-product-id', $(this).val()).removeClass('in-cart');
                    button.val('<?= Loc::getMessage('CT_BCE_CATALOG_BUY'); ?>');

                    changePriceForDetailPage($(this));
                    $('.b-sku-panel-item-label').removeClass('checked');
                    $(this).parent().addClass('checked');

                    // $('.detail-product-slider').removeClass('active').addClass('deactivated');
                    // $('.detail-product-slider[data-color=' + dataColor + ']').removeClass('deactivated').addClass('active');

                    changeActiveGallery(dataColor);
                });

                initDetailOwl();
                initDetailSkuTrigger();
                updateFavoriteLikeProducts();

                if ($('window').outerWidth() > 1024) {
                    var systemBtnTopVal = $('.overlay-container').offset().top - 10;
                    $('.button-back-section, .share-section').css({'top': systemBtnTopVal});
                }

                $(window).resize(function(){
                    if ($(window).width() < 769) {
                        destroyDetailOwl();
                        initDetailOwl();
                    } else {
                        destroyDetailOwl();
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
