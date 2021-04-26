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
$this->setFrameMode(true);

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
$autoload = $arParams['PAGE_ELEMENT_COUNT'] >  count($arResult['ITEMS']) ? 'ajaxAutoLoadActive  ' : '';
?>

<? if (0 !== count($arResult['ITEMS'])) { ?>
    <div id="js-ajax-nav-section-catalog"
         class="<?=$autoload?> <?= $nCatalog != 'Y' ? 'row ' : 'catalog-detail-page product-slider js-product-slider' ?>"
         data-last-page="<?= $navCountPage ?>" data-nav-num="<?= $navNum ?>">

        <? if ($arParams['IS_AJAX'] === 'Y') { // Если идет ajax запрос ?>
            <? ob_start(); ?>
        <? } ?>

        <? foreach ((array)$arResult['ITEMS'] as $key => $item) { ?>
            <?
            $item['NAME'] = CYouWanna::multiTranslate($item['NAME'], $arParams['LANGUAGE_ID']);
            $firstOffer = reset($item['OFFERS']);
            $isDiscount = false;
            foreach($item['OFFERS'] as $offer){
                $discountPrice = reset($offer['PRICES'])['DISCOUNT_VALUE'];
                $price = reset($offer['PRICES'])['VALUE'];
                $isDiscount = $discountPrice < $price;
                if ($isDiscount) {
                    continue;
                }

                if($item['PREVIEW_PICTURE']['SRC'] == "") {
                    $item['PREVIEW_PICTURE'] = $item['DETAIL_PICTURE'];
                }
            }
            ?>
            <div class="<?= $nCatalog == 'Y' ? '' : 'col-6' ?>">
                <?
                    /*$morePhArr = $item['DISPLAY_PROPERTIES']['MORE_PHOTO']['FILE_VALUE'];*/
                ?>
                <div class="element-list-item">
                <div class="element-inner js-element-favorite"
                     data-detail="<?php echo $item['DETAIL_PAGE_URL'] ?>"
                     data-element-id="<?php echo $item['ID']?>"
                >
                    <div class="single-item">
                        <a href="<?php echo $item['DETAIL_PAGE_URL'] ?>" title="<?php echo $item['NAME'] ?>">
                            <div class="element__image <?php if ( $item['PROPERTIES']['HOVER_PHOTO']['VALUE'] != 0 && !$isRecom ) { echo 'element__image--has-hover'; } ?>">
                                <img class="element__image--default" src="<?php echo $item['PREVIEW_PICTURE']['SRC']; ?>" alt="">

                                <?php if ( $item['PROPERTIES']['HOVER_PHOTO']['VALUE'] != 0 && !$isRecom ) : ?>
                                    <img class="element__image--hover" src="<?php echo CFile::GetPath($item['PROPERTIES']['HOVER_PHOTO']['VALUE']); ?>" alt="">
                                <?php endif; ?>
                            </div>
                        </a>

                        <?/* foreach ($morePhArr as $slidePict) { ?>
                        <a href="<?= $item['DETAIL_PAGE_URL'] ?>"
                           title="<?= $item['NAME'] ?>">
                            <div class="element-image" style="background-image: url(<?= $slidePict['SRC'] ?>)">


                            </div>
                        </a>
                        <? } */?>
                    </div>



                    <?/*?><a data-product-id="<?= reset($item['OFFERS'])['ID']?>"
                       data-item-id="<?= $item['DETAIL_PAGE_URL']?>"
                       class="add-to-cart-section-button"
                       href="<?= $item['DETAIL_PAGE_URL'] ?>">
                        <span class="list__link cart"></span>
                        <span class="text">
                            <?= Loc::getMessage('CATALOG_ADD') ?>
                        </span><?
                    </a>*/?>

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
                            <? if ( $item['PROPERTIES']['NEW_PRODUCT']['VALUE_XML_ID'] === 'Y' ) {?>
                                <div class="element-list-item-label__item">NEW</div>
                            <? } ?>
                        <? $frame->end(); ?>
                        <button class="element-list-item-label__like js-add-to-favorite"></button>
                    </div>

                    <div class="element-list-item-title">
                        <a href="<?= $item['DETAIL_PAGE_URL'] ?>" title="<?= $item['NAME'] ?>"><?= $item['NAME'] ?></a>
                    </div>

                    <div class="element-list-item-price">
                        <? if ($isDiscount) : ?>
                            <div class="element-list-item-price__item _old">
                                <?php echo number_format((double)$price, 0, '', ' '); ?> &#8381;
                            </div>
                            <div class="element-list-item-price__item _diff">
                                (<?php echo number_format((double)($discountPrice - $price), 0, '', ' '); ?>)
                            </div>
                            <div class="element-list-item-price__item _new">
                                <?php echo number_format((double)$discountPrice, 0, '', ' '); ?> &#8381;
                            </div>
                        <? else : ?>
                            <?php echo number_format((double)$price, 0, '', ' '); ?> &#8381;
                        <? endif; ?>
                    </div>


                    <?
                    /* $frame = $this->createFrame('list-price-' . $item['ID'], true)->begin();*/
                    /**
                     * Цвета с возможностью переключение фотографии товара
                     */
                    ?>
                    <div class="colors offer-change">
                        <? foreach ($item['COLORS']['HEX'] as $key => $color) {
                            $preview = $item['COLORS'][$key]['DATA']['PREVIEW_PICTURE'][0];
                            $bg = $item['COLORS'][$key]['DATA']['PREVIEW_PICTURE'][1];
                        ?>
                            <span data-offer-id="<?//= $offer['ID']?>"
                                  title="<?/*= $offer['CAN_BUY']
                                      ? Loc::getMessage('CT_BCS_TPL_MESS_PRODUCT_AVAILABLE')
                                      : Loc::getMessage('CT_BCS_TPL_MESS_PRODUCT_NOT_AVAILABLE') */?>"
                                  class="element-colors"
                                  style="background-color: <?=  $color?>"
                                  data-offer-name="<?= $item['COLORS'][$key]['DATA']['NAME']; ?>"
                                  data-offer-discount-value="<?= $item['COLORS'][$key]['DATA']['DISCOUNT_VALUE']; ?>"
                                  data-offer-value="<?= $item['COLORS'][$key]['DATA']['VALUE_VAT']; ?>"
                                  data-offer-discount-percent="<?= $item['COLORS'][$key]['DATA']['DISCOUNT_DIFF_PERCENT']; ?>"
                                  data-offer-img="<?= $preview ?>"
                                  data-offer-img2="<?= $bg ?>"
                                  >
                            </span>
                        <? } ?>
                    </div>





                    <?/* $frame->beginStub(); ?>
                        <div class="element-list-price row">
                            <span class="price col-12">
                                &#8381;
                            </span>
                        </div>
                    <? $frame->end(); */?>

                    <a class="button inverted outline element-list-preview-button"
                        href="javascript:void(0);"
                        data-show-detail-catalog-item="Y"
                        id="quick-view-<?= $item['ID'] ?>"
                        data-item-id="<?= $item['ID'] ?>"
                        data-item-url="<?= $arParams['LANGUAGE_ID'] !== 'ru' ? '/' . $arParams['LANGUAGE_ID'] : '' ?><?= $item['DETAIL_PAGE_URL'] ?>?ajax=Y<?if(isset($arParams['DETAIL_PAGE_LOCATING']) && $arParams['DETAIL_PAGE_LOCATING'] === 'Y'){?>&similar=Y<?}?>">
                        <?= Loc::getMessage('CT_BCS_CATALOG_QUICK_VIEW') ?>
                    </a>

                </div>
                </div>
            </div>
        <? } ?>

        <? if ($arParams['DISPLAY_BOTTOM_PAGER']) { ?>
            <? if ($navCountPage > $navPage) { ?>
                <div class="js-ajax-load-items-button ajax-load-items-button"
                     data-ss-colspan="4">
                    <a href="javascript:void(0);"
                       class="button secondary"
                       title="<?= Loc::getMessage('CT_BCS_CATALOG_SHOW_MORE') ?> <?= $navSize
                       ?> <?= Loc::getMessage('CT_BCS_CATALOG_SHOW_MORE_2') ?> <?= $navItemsLeft ?>"
                       onclick="return ajaxLoadCatalogSectionElements(this);"
                       data-ajax-url="<?= $arParams['IS_FAVORITE_PRODUCTS'] ?: '' ?>"
                       data-favorites-list='<?= json_encode($arParams['FAVORITES_LIST']) ?>'>
                        <?= Loc::getMessage('CT_BCS_CATALOG_SHOW_MORE') ?> <?= $navSize ?>
                        <?= Loc::getMessage('CT_BCS_CATALOG_SHOW_MORE_2') ?> <?= $navItemsLeft ?>
                    </a>
                    <?/*?><a href="?SHOWALL_<?= $navNum ? $navNum : 2 ?>=1"
                       class="all__catalog btn btn-default"
                       title="<?= Loc::getMessage('CT_BCS_CATALOG_SHOW_ALL') ?>">
                        <?= Loc::getMessage('CT_BCS_CATALOG_SHOW_ALL') ?>
                    </a><?*/?>
                </div>
            <? } ?>
        <? } ?>

        <? if ($arParams['IS_AJAX'] === 'Y') { // Если идет ajax запрос ?>
            <script>
                $(function () {
                    initListOwl();
                    initDetailCatalogTrigger();
                });
            </script>
            <div></div>
            <? $templateData['AJAX_HTML'] = ob_get_contents();
            ob_end_clean(); ?>
        <? } ?>
    </div>
    <div class="clearfix"></div>
    <? if ($arParams['DISPLAY_BOTTOM_PAGER']) { ?>
        <div style="display: none;">
            <?= $arResult['NAV_STRING']; ?>
        </div>
    <? } ?>

    <div id="detail-item-popup" class="modal-box hide" data-component="modal" data-item-id="">
        <div class="modal detail-item-inner">
            <span class="close"></span>
            <div class="modal-body"></div>
            <a id="prev-product-item"
                    class="b-nav-buttons__btn b-nav-buttons__btn_type-prev"
                    data-item-id=""></a>
            <a id="next-product-item"
                    class="b-nav-buttons__btn b-nav-buttons__btn_type-next"
                    data-item-id=""></a>
        </div>
    </div>
    <script>
        /*$('#detail-item-popup').on('open.modal', function()
        {
            $('#detail-item-popup').addClass('open');
        });*/
    </script>

<? } ?>
<script type="text/javascript">

/**$(document).ready(function () {
		var $win = $(window);
		$win.scroll(function() {
			var $marker = $('.js-ajax-load-items-button');
			if (typeof($marker[0]) != "undefined" && $marker[0] !== null) {
				if ($win.scrollTop() + $win.height() >= $marker.offset().top) {
					$marker.find('a').click();
				}
			}
		});
});**/

    $(document).ready(function(){
        /*$('.element-colors').unbind('click').click(function(){
            $('.element-colors').removeClass('active');
            $(this).addClass('active').parents('.element-list-item').find('.add-to-cart-section-button')
                .attr('data-product-id', $(this).attr('data-offer-id'));
        });*/

        $('a.add-to-cart-section-button').unbind('click').click(function() {
            var element = $(this);
            var productId = element.attr('data-product-id');
            $.ajax({
                url: '<?=$APPLICATION->GetCurPage()?>?action=ADD2BASKET&id='+ productId,
                success: function() {
                    updateSmallBasketQuantity();
                    element.addClass('in-cart');
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
        });
        updateFavoriteLikeProducts();
    });
</script>
