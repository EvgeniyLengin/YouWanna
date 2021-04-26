<?php
/**
 * Вывод способов доставки
 *
 * @var array  $arParams
 * @var array  $arResult
 * @var string $templateFolder
 */
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Localization\Loc;

//$deliveryService = \Bitrix\Sale\Delivery\Services\Manager::getActiveList();
?><?
if (CModule::IncludeModule("sale")) {
    $arLocs = CSaleLocation::GetByID($arResult['USER_VALS']['DELIVERY_LOCATION'], LANGUAGE_ID);
    if (intval($arLocs['COUNTRY_ID']) === 1) {
        $noRus = false;
        $deliveryDiscount = '';
    } else {
        $noRus = true;
        $deliveryDiscount = 'discount';
    }
}
?>
<input type="hidden" name="BUYER_STORE" id="BUYER_STORE" value="<?= $arResult['BUYER_STORE']; ?>"/>

<?php if (!empty($arResult['DELIVERY'])) { ?>
    <div class="form__section">
        <div class="form__section-title"><?= Loc::getMessage('SOA_TEMPL_DELIVERY'); ?></div>

        <?php foreach ($arResult['DELIVERY'] as $deliveryId => $delivery) : ?>
            <? if ($delivery['CHECKED'] === 'Y') {
                $deliveryPrice = $delivery['PRICE_FORMATED'];

                if ($noRus) {
                    $deliveryPriceNoForm = $delivery['PRICE'];
                } else {
                    $deliveryPriceNoForm = $delivery['PRICE'];
                }
            } ?>
            <? if (!($deliveryId !== 0 && (int)$deliveryId <= 0)) : ?>
                <? $clickHandler = "onClick = \"BX('ID_DELIVERY_ID_" . $delivery['ID'] . "').checked=true;submitForm();\""; ?>

                    <div class="form__item">
                        <label class="radio">
                            <input class="radio__input"
                                   type="radio"
                                   id="ID_DELIVERY_ID_<?= $delivery['ID']; ?>"
                                   name="<?= htmlspecialcharsbx($delivery['FIELD_NAME']); ?>"
                                   value="<?= $delivery['ID'] ?>"<?= $delivery['CHECKED'] === 'Y' ? ' checked' : ''; ?>
                                   onclick="submitForm();"/>
                            <span class="radio__text">
                                <?= CYouWanna::multiTranslate($delivery['NAME'], LANGUAGE_ID) ?>
                            </span>
                            <span class="radio__note">
                                <span>
                                    <span class="delivery-info-header"><?= Loc::getMessage('ORDER_CONFIRM_PERIOD') ?></span>
                                    <?= trim($delivery['PERIOD_TEXT']) ?>
                                </span>
                                <span>
                                    <span class="delivery-info-header"><?= Loc::getMessage('ORDER_CONFIRM_PRICE') ?></span>
                                    <?= trim($delivery['PRICE_FORMATED']) ?>
                                </span>
                            </span>
                        </label>
                    </div>

            <? endif; ?>
        <? endforeach; ?>
    </div>


    <div class="form__section">
        <?= Loc::getMessage('DELIVERY_PAYMENT') ?><? echo $deliveryPrice ?><br>
        <? if("0000073738" == $arLocs['CODE']) echo Loc::getMessage('DELIVERY_MOSCOW_FEATURE');?>
    </div>
    <input type="hidden" value="<? echo $deliveryPriceNoForm ?>" name="ORDER_PROP_14" id="DELIVERY_COST">
<? } else { ?>
    <fieldset><?= Loc::getMessage('NO_SDEK') ?></fieldset>
<? } ?>