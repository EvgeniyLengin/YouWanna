<?php
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

$navCount = $arResult['NAV_RESULT']->NavRecordCount;

?>

<?php if (0 !== count($arResult['ITEMS'])) : ?>

    <div class="search-popup container">

        <? $targetBlank =  $arParams['STORES_LIST'] ? ' target="_blank"' : ''; ?>

        <div class="owl-carousel search-result-carousel" id="header-search-carousel">
            <? foreach ((array)$arResult['ITEMS'] as $key => $item) {
            $item['NAME'] = CYouWanna::multiTranslate($item['NAME'], $arParams['LANGUAGE_ID']);
            $firstOffer = reset($item['OFFERS']);
            $discountPrice = reset($firstOffer['PRICES'])['DISCOUNT_VALUE'];
            $price = reset($firstOffer['PRICES'])['VALUE'];
            $isDiscount = $discountPrice < $price;
            ?>
            <div class="element-search-item">

                <a href="<?= $item['DETAIL_PAGE_URL'] ?>"
                   title="<?= $item['NAME'] ?>">
                    <div class="image-wrapper">
                        <img src="<?= $item['PREVIEW_PICTURE']['SRC'] ?>"
                             alt="<?= $item['NAME'] ?>"
                             class="element-list-image">
                     </div>

                    <? $frame = $this->createFrame()->begin('');
                        if ($isDiscount) { ?>
                            <div class="sale-icon">SALE</div>
                            <?/*= $item['SALE_ICON'] */?>
                        <? }
                        if ( $item['PROPERTIES']['NEW_PRODUCT']['VALUE_XML_ID'] === 'Y' ) {?>
                            <div class="new-icon">NEW</div>
                        <?}
                    $frame->end(); ?>
                            <?= $item['NAME'] ?>
                        </a>
                    <? $frame = $this->createFrame('list-price-' . $item['ID'], false)->begin(); ?>
                        <div class="colors">
                            <?/* foreach ($item['OFFERS'] as $offer) { ?>
                                <span data-offer-id="<?= $offer['ID']?>"
                                      title="<?= $offer['CAN_BUY']
                                          ? Loc::getMessage('CT_BCS_TPL_MESS_PRODUCT_AVAILABLE')
                                          : Loc::getMessage('CT_BCS_TPL_MESS_PRODUCT_NOT_AVAILABLE') ?>"
                                      class="element-colors<?= $offer['CAN_BUY'] ? ' active' : ''?>"
                                      style="background-color: <?=  $offer['PROPERTIES']['COLOR']['VALUE_XML_ID']?>">
                                </span>
                            <? } */?>
                            <? foreach ($item['COLORS']['HEX'] as $color) { ?>
                                <span class="element-colors"
                                      style="background-color: <?= $color ?>">
                                </span>
                            <? } ?>
                        </div>
                        <div class="element-list-price row">
                            <? if ($isDiscount) { ?>
                                <span class="price discount-price col-6 align-right">
                                    <?= number_format((double)$price, 0, '', ' ') ?> &#8381;
                                </span>
                                <span class="price col-6 align-left">
                                    <?= number_format((double)$discountPrice, 0, '', ' ') ?> &#8381;
                                </span>
                            <? } else { ?>
                                <span class="price col-12">
                                    <?= number_format((double)$price, 0, '', ' ') ?> &#8381;
                                </span>
                            <? } ?>
                        </div>
                    <? $frame->beginStub(); ?>
                        <div class="element-list-price row">
                            <span class="price col-12">
                                &#8381;
                            </span>
                        </div>
                    <? $frame->end(); ?>
            </div>
        <? } ?>
        </div>
        <div class="clearfix"></div>
        <? if (!$arParams['STORES_LIST']) { ?>
            <div class="all-elements">
                <div class="found-elements">
                    <?/*= Loc::getMessage('CT_BCS_CATALOG_FOUND_1') . ': ' . count($arResult['ITEMS']) */?>
                    <a class="button secondary outline"
                       href="<?= $arParams['LANGUAGE_ID'] !== 'ru' ? '/' . $arParams['LANGUAGE_ID'] : '' ?>/catalog/search/?q=<?= $_REQUEST['q'] ?>"
                       title="<?= Loc::getMessage('CT_BCS_CATALOG_ALL'); ?>">
                        <?= Loc::getMessage('CT_BCS_CATALOG_ALL'); ?>
                    </a>
                </div>
            </div>
        <? } ?>
<?php else : ?>
    <? if ($arParams['STORES_LIST'] !== 'Y') { ?>
        <div class="empty-elements container"><?= Loc::getMessage('CT_BCS_CATALOG_EMPTY'); ?></div>
    <? } ?>
<?php endif;