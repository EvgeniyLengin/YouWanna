<?php
/**
 * @var array  $arParams
 * @var array  $arResult
 * @var string $templateFolder
 * @var CMain  $APPLICATION
 */
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Localization\Loc;

/*$bDefaultColumns = $arResult['GRID']['DEFAULT_COLUMNS'];
$colspan = $bDefaultColumns ? count($arResult['GRID']['HEADERS']) : count($arResult['GRID']['HEADERS']) - 1;
$bPropsColumn = false;
$bUseDiscount = false;
$bPriceType = false;
$bShowNameWithPicture = $bDefaultColumns ? true : false; // flat to show name and picture column in one column
?>

    <div class="order-summory-c fixed-block">
        <div class="order-info">
            <fieldset>
                <legend>
                    <?= Loc::getMessage('SALE_PRODUCTS_SUMMARY'); ?>
                    <span class="legend-text">
                        <a href="/personal/cart/">
                            Изменить состав заказа
                        </a>
                    </span>
                </legend>
            </fieldset>

            <?php $pIDS = array();
            $IDS = array();
            $arElements = array();

            foreach ($arResult['BASKET_ITEMS'] as $arItem) {
                $pIDS[] = $arItem['PRODUCT_ID'];
            }


            $rsProducts = CIBlockElement::GetList(
                array(),
                array('=ID' => $pIDS),
                false,
                false,
                array('ID', 'IBLOCK_ID', 'PROPERTY_CML2_LINK')
            );


            while ($arProduct = $rsProducts->Fetch()) {
                $IDS[$arProduct['ID']] = $arProduct['PROPERTY_CML2_LINK_VALUE'] ?: $arProduct['ID'];
            }


            $rsElements = CIBlockElement::GetList(
                array(),
                array('=ID' => $IDS),
                false,
                false,
                array('ID', 'NAME', 'PROPERTY_GW_NAIMENOVANIE_ANGL')
            );

            while ($arElement = $rsElements->Fetch()) {
                $arElements[$arElement['ID']] = $arElement['NAME'];
                if (LANGUAGE_ID === 'en') {
                    $arElements[$arElement['ID']] =
                        $arElement['PROPERTY_GW_NAIMENOVANIE_ANGL_VALUE'] ?: $arElement['NAME'];
                }
            } */
?>

    <div class="order-items row">
        <table class="col col-6">
            <? /*php foreach ((array)$arResult['GRID']['ROWS'] as $k => $arData) : ?>
                        <?php
	                    $arItem = isset($arData['columns'][$arColumn['id']]) ? $arData['columns'] : $arData['data'];
	                    //$price = CButtonBlue::getOfferPrices($arData['data']['PRODUCT_ID']);
	                    $price['MIN_PRICE'] = $arItem['PRICE_FORMATED'];
	                    $maxPrice = max(intval($price['MAX_PRICE']), $arItem['FULL_PRICE']);
	                    $arResult['PRICE_WITHOUT_DISCOUNT'] += $maxPrice * $arItem['QUANTITY'];
	                    $price['MAX_PRICE'] = CCurrencyLang::CurrencyFormat($maxPrice, $arItem['CURRENCY'], true);
	                    $price['IS_DISCOUNT'] = false;
	                    $price['PERCENT'] = '';
	                    $price['DIFF_PRICE'] = '';
	                    if ($maxPrice > $arItem['PRICE'] && !$arItem['IS_GIFT']) {
		                    $price['IS_DISCOUNT'] = true;
		                    $percent = 100.0 * ($maxPrice - $arItem['PRICE']) / $maxPrice;
		                    if ($percent > 0 && $percent < 1) {
			                    $percent = 1;
		                    }
		                    $decimals = $percent - floor($percent);
		                    if (0.4 < $decimals && $decimals < 0.6) {
			                    $percent = floor($percent) + 0.5;
		                    }
		                    else {
			                    $percent = floor($percent);
		                    }
		                    $price['PERCENT'] = $percent.'%';
		                    $price['DIFF_PRICE'] = CCurrencyLang::CurrencyFormat($maxPrice - $arItem['PRICE'], $arItem['CURRENCY'], true);
	                    }
                        ?>

                        <tr class="item-info">
                            <td class="name">
                                <?= trim(str_replace('()', '', $arData['data']['NAME'])) ?>
                                <? if (!empty($arData['data']['SHOW_PROPS']) && !$arItem['IS_GIFT']) { ?>
                                    <ul class="props">
                                        <? foreach ($arData['data']['SHOW_PROPS'] as $arProp) { ?>
                                            <li><?= $arProp['NAME'] ?>: <?= $arProp['VALUE'] ?></li>
                                        <? } ?>
                                    </ul>
                                <? } ?>
                            </td>
                            <td class="price one-product">
                                <?= $price['MIN_PRICE'] ?>
	                            <?php if ($price['IS_DISCOUNT']) : ?>
	                                <div class="old-price"><?= $price['MAX_PRICE'] ?></div>
                                <?php endif; ?>
                            </td>
                            <td class="qty">
                                <span>
                                    <?= $arData['data']['QUANTITY'] ?>
                                </span>
                            </td>
                            <td class="discount">
                                <?= $price['PERCENT'] ?>
	                            <div class="labels">
	                                <?if (!empty($arItem['LABELS'])) {?>
			                            <?foreach($arItem['LABELS'] as $label) {?>
				                            <div><span class="label outline"><?=$label?></span></div>
			                            <?}?>
	                                <?}?>
	                            </div>
                            </td>
                            <td class="price sum-product">
                                <?= $arData['data']['SUM'] ?>
                            </td>
                        </tr>
                    <?php endforeach; */ ?>


            <? /*?><tr class="info">
                        <td><?= Loc::getMessage('SOA_TEMPL_SUM_SUMMARY') ?></td>
                        <td class="price one-product"></td>
                        <td class="qty"></td>
                        <td class="discount"></td>
                        <td class="price"
                            id="summary-products-price">
                            <?= $arResult['ORDER_PRICE_FORMATED'] ?>
                        </td>
                    </tr>

                    <?php if ((float)$arResult['DISCOUNT_SUM'] > 0) : ?>
                        <tr class="info">
                            <td><?= Loc::getMessage('SOA_TEMPL_SUM_DISCOUNT') ?><?php
                                if (strlen($arResult['DISCOUNT_PERCENT_FORMATED']) > 0) :
                                    ?>(<?= $arResult['DISCOUNT_PERCENT_FORMATED']; ?>)<?php
                                endif; ?>:
                            </td>
                            <td class="price one-product"></td>
                            <td class="qty"></td>
                            <td class="discount"></td>
                            <td class="price"> <?= $arResult['DISCOUNT_SUM_FORMATTED'] ?></td>
                        </tr>
                    <?php endif; ?><? */ ?>

            <?php global $hideDelivery; ?>
            <? /*?><tr class="info summary-delivery-price-row <?= $hideDelivery ? 'hidden' : '' ?>">
                        <td> <?= Loc::getMessage('SOA_TEMPL_SUM_DELIVERY') ?></td>
                        <td class="price one-product"></td>
                        <td class="qty"></td>
                        <td class="discount"></td>
                        <td class="price"
                            id="summary-delivery-price">
                            <?php # продумать  как сделать лучше
                            global $selectedPriceSum;
                            if ($arResult['DELIVERY_PRICE_FORMATED'] === '0 руб.' && $selectedPriceSum !== null) : ?>
                                <?=$selectedPriceSum ?> руб.
                            <?php else : ?>
                                <?= $arResult['DELIVERY_PRICE_FORMATED'] ?>
                            <?php endif; ?>
                        </td>
                    </tr><?*/ ?>
            <?
            foreach ($arResult['DELIVERY'] as $deliveryId => $delivery) { ?>
                <? if ($delivery['CHECKED'] === 'Y') {
                    $deliveryPriceNoForm = $delivery['PRICE'];
                }
            }
            if ($arResult['NORUS']) {
                $totalPrice = CurrencyFormat(($deliveryPriceNoForm + $arResult['ORDER_TOTAL_PRICE']), 'RUB');
            } else {
                $totalPrice = $arResult['ORDER_TOTAL_PRICE_FORMATED'];
            }
            ?>
            <tr class="total">
                <td><?= Loc::getMessage('SOA_TEMPL_SUM_IT') ?></td>
                <td class="price one-product"></td>
                <td class="qty"></td>
                <td class="discount"></td>
                <td class="price"
                    id="total_sum">
                    <?= $totalPrice ?>
                </td>
            </tr>
        </table>
        <div class="text-center aux-margin-top-1 aux-margin-bottom-1 col col-6 first">
            <a href="javascript:void(0);"
               onclick="checkOrderForm();"
               id="ORDER_CONFIRM_BUTTON"
               class="button secondary upper width-100">
                <?= GetMessage('SOA_TEMPL_BUTTON') ?>
            </a>
        </div>
        <div class="form-item">
            <? if (!$USER->IsAuthorized()) { ?>
                <p class="privacy-modal-text"><?= Loc::getMessage('AGREEMENT_PRIVACY') ?></p>
            <? } ?>
        </div>
    </div>
<? /*?></div>
    </div><?*/ ?>
<?php /*
$APPLICATION->AddChainItem(Loc::getMessage('SOA_TEMPL_MAKE'));
$APPLICATION->SetPageProperty('title', Loc::getMessage('SOA_TEMPL_MAKE')); */
