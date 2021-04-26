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

$this->setFrameMode(true);

?>

<? if (0 !== count($arResult['ITEMS'])) { ?>
    <div class="yw-trendlist__wrap">
    <div class="ajaxAutoLoadActive yw-trendlist__list <?= $nCatalog != 'Y' ? 'row ' : 'catalog-detail-page product-slider js-product-slider' ?>">

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
            }
            ?>
            <div class="col-3">
                <div class="element-list-item">
                <div class="element-inner js-element-favorite"
                     data-detail="<?php echo $item['DETAIL_PAGE_URL'] ?>"
                     data-element-id="<?php echo $item['ID']?>"
                >
                    <div class="single-item">
                        <a href="<?php echo $item['DETAIL_PAGE_URL'] ?>" title="<?php echo $item['NAME'] ?>">
                            <div class="element__image <?php if ( $item['PROPERTIES']['HOVER_PHOTO']['VALUE'] != 0 && !$isRecom ) { echo 'element__image--has-hover'; } ?>">
                                <?$photo = CFile::ResizeImageGet($item['DETAIL_PICTURE']['ID'], array("width"=>440, "height"=>550), BX_RESIZE_IMAGE_EXACT);?>
                                <img class="element__image--default" src="<?php echo $photo['src']; ?>" alt="">
                                <?//=tagPicture($photo['src'], 'element__image--default');?>

                                <?php if ( $item['PROPERTIES']['HOVER_PHOTO']['VALUE'] != 0 && !$isRecom ) : ?>
                                    <?$hover_photo = CFile::ResizeImageGet($item['PROPERTIES']['HOVER_PHOTO']['VALUE'], array("width"=>440, "height"=>550), BX_RESIZE_IMAGE_EXACT);?>
                                    <img class="element__image--hover" src="<?php echo $hover_photo['src']; ?>" alt="">
                                    <?//=tagPicture($hover_photo['src'], 'element__image--hover');?>
                                <?php endif; ?>
                            </div>
                        </a>

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
                    <div class="colors offer-change">
                        <? foreach ($item['COLORS']['HEX'] as $key => $color) {?>
                            <span class="element-colors"
                                  style="background-color: <?=  $color?>">
                            </span>
                        <? } ?>
                    </div>
                </div>
                </div>
            </div>
        <? } ?>

    </div>
    <div class="clearfix"></div>    
    </div>

<? } ?>
