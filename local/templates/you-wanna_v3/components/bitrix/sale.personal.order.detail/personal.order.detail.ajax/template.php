<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Localization\Loc,
    Bitrix\Main\Page\Asset;

if ($arParams['GUEST_MODE'] !== 'Y') {
    Asset::getInstance()->addJs("/bitrix/components/bitrix/sale.order.payment.change/templates/.default/script.js");
    Asset::getInstance()->addCss("/bitrix/components/bitrix/sale.order.payment.change/templates/.default/style.css");
}
$this->addExternalCss("/bitrix/css/main/bootstrap.css");

CJSCore::Init(array('clipboard', 'fx'));

$APPLICATION->SetTitle("");
foreach ($arResult['ORDER_PROPS'] as $key => $arProp) {
    if ($arProp['CODE'] === 'DELIVERY_COST' ){
        $deliveryCost = CurrencyFormat($arProp['VALUE'], "RUB");;
    }
}
if (!empty($arResult['ERRORS']['FATAL'])) {
    foreach ($arResult['ERRORS']['FATAL'] as $error) {
        ShowError($error);
    }

    $component = $this->__component;

    if ($arParams['AUTH_FORM_IN_TEMPLATE'] && isset($arResult['ERRORS']['FATAL'][$component::E_NOT_AUTHORIZED])) {
        $APPLICATION->AuthForm('', false, false, 'N', false);
    }
} else {
    if (!empty($arResult['ERRORS']['NONFATAL'])) {
        foreach ($arResult['ERRORS']['NONFATAL'] as $error) {
            ShowError($error);
        }
    }
    ?>

    <div class="order-content">
        <div class="order-content__title hidden">
            <?= Loc::getMessage('SPOD_SUB_ORDER_TITLE', array(
                "#ACCOUNT_NUMBER#"    => htmlspecialcharsbx($arResult["ACCOUNT_NUMBER"]),
                "#DATE_ORDER_CREATE#" => $arResult["DATE_INSERT_FORMATED"]
            )) ?>
            <?= Loc::getMessage('SPOD_TPL_SUMOF') // на сумму ?>
            <?= $arResult["PRICE_FORMATED"] // 000 руб. ?>
        </div>

        <? // Покупатель ?>
        <div class="order-content__section">
            <div class="order-content__section-title">
                <?= Loc::getMessage('SPOD_LIST_ORDER_INFO') ?>
            </div>
            <div class="order-content__item">
                <div class="row">

                    <? // ФИО ?>
                    <div class="col-md-4">
                        <div class="order-content__column">
                            <div class="order-content__subtitle">
                                <?
                                $userName = $arResult["USER"]["NAME"] . " " . $arResult["USER"]["SECOND_NAME"] . " " . $arResult["USER"]["LAST_NAME"];

                                if (strlen($userName) || strlen($arResult['FIO'])) :
                                    echo Loc::getMessage('SPOD_LIST_FIO');
                                else :
                                    echo Loc::getMessage('SPOD_LOGIN');
                                endif;
                                ?>
                            </div>
                            <div class="order-content__value">
                                <?
                                if (strlen($userName)) {
                                    echo htmlspecialcharsbx($userName);
                                } elseif (strlen($arResult['FIO'])) {
                                    echo htmlspecialcharsbx($arResult['FIO']);
                                } else {
                                    echo htmlspecialcharsbx($arResult["USER"]['LOGIN']);
                                }
                                ?>
                            </div>
                        </div>
                    </div>

                    <? // статус ?>
                    <div class="col-md-4">
                        <div class="order-content__column">
                            <div class="order-content__subtitle">
                                <?= Loc::getMessage('SPOD_LIST_CURRENT_STATUS', array(
                                    '#DATE_ORDER_CREATE#' => $arResult["DATE_INSERT_FORMATED"]
                                )) ?>
                            </div>
                            <div class="order-content__value lowercase">
                                <?
                                if ($arResult['CANCELED'] !== 'Y') :
                                    echo htmlspecialcharsbx($arResult["STATUS"]["NAME"]);
                                else :
                                    echo Loc::getMessage('SPOD_ORDER_CANCELED');
                                endif;
                                ?>
                            </div>
                        </div>
                    </div>

                    <? // сумма ?>
                    <div class="col-md-4">
                        <div class="order-content__column">
                            <div class="order-content__subtitle">
                                <?= Loc::getMessage('SPOD_ORDER_PRICE') ?>
                            </div>
                            <div class="order-content__value lowercase">
                                <?= $arResult["PRICE_FORMATED"] ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    <div class="order-content__section">
        <div class="order-content__section-title">
            <?= Loc::getMessage('SPOD_ORDER_PAYMENT') ?>
        </div>
        <div class="order-content__item">
            <div class="row">

                <? // логин ?>
                <div class="col-md-4">
                    <div class="order-content__column">
                        <div class="order-content__subtitle">
                            <?= Loc::getMessage('SPOD_LOGIN') ?>
                        </div>
                        <div class="order-content__value">
                            <?= htmlspecialcharsbx($arResult["USER"]["LOGIN"]) ?>
                        </div>
                    </div>
                </div>

                <? // эл. почта ?>
                <? if (strlen($arResult["USER"]["EMAIL"]) && !in_array("EMAIL", $arParams['HIDE_USER_INFO'])) : ?>
                    <div class="col-md-4">
                        <div class="order-content__column">
                            <div class="order-content__subtitle">
                                <?= Loc::getMessage('SPOD_EMAIL') ?>
                            </div>
                            <div class="order-content__value">
                                <?= htmlspecialcharsbx($arResult["USER"]["EMAIL"]) ?>
                            </div>
                        </div>
                    </div>
                <? endif; ?>
            </div>
            <div class="row">
                <? if (isset($arResult["ORDER_PROPS"])) : ?>
                    <? foreach ($arResult["ORDER_PROPS"] as $property) : ?>
                        <? if ($property["TYPE"] == "Y/N") : ?>
                        <?
                            //echo Loc::getMessage('SPOD_' . ($property["VALUE"] == "Y" ? 'YES' : 'NO'));
                            else :
                        ?>
                            <? // город ?>
                            <? if ($property['CODE'] === 'LOCATION') : ?>
                                <div class="col-md-4">
                                    <div class="order-content__column">
                                        <div class="order-content__subtitle">
                                            <?= $property["NAME"] ?>
                                        </div>
                                        <div class="order-content__value">
                                            <?= htmlspecialcharsbx($property["VALUE"]) ?>
                                        </div>
                                    </div>
                                </div>

                            <? elseif ($property['CODE'] === 'ADDRESS') : ?>
                                <div class="col-md-6">
                                    <div class="order-content__column">
                                        <div class="order-content__subtitle">
                                            <?= $property["NAME"] ?>
                                        </div>
                                        <div class="order-content__value">
                                            <?= htmlspecialcharsbx($property["VALUE"]) ?>
                                        </div>
                                    </div>
                                </div>
                            <? endif; ?>
                        <? endif; ?>
                    <? endforeach; ?>
                <? endif; ?>
            </div>
        </div>
    </div>

    <div class="order-content__section">
        <div class="order-content__section-title">
            <?= Loc::getMessage('PAY_SYSTEM_NAME') ?>
        </div>
        <div class="sale-order-detail-payment-options">



                <div class="">
                    <div class="sale-order-detail-payment-options-inner-container">
                        <div class="">
                            <div class="sale-order-detail-payment-options-methods-container">
                                <?
                                foreach ($arResult['PAYMENT'] as $payment) {
                                    ?>
                                    <div class="sale-order-detail-payment-options-methods">
                                        <div class="sale-order-detail-payment-options-methods-information-block">

                                            <div class="sale-order-detail-payment-options-methods-info">
                                                <div class="sale-order-detail-payment-options-methods-info-title">
                                                    <div class="sale-order-detail-methods-title">
                                                        <?
                                                        $paymentData[$payment['ACCOUNT_NUMBER']] = array(
                                                            "payment"         => $payment['ACCOUNT_NUMBER'],
                                                            "order"           => $arResult['ACCOUNT_NUMBER'],
                                                            "allow_inner"     => $arParams['ALLOW_INNER'],
                                                            "only_inner_full" => $arParams['ONLY_INNER_FULL'],
                                                            "refresh_prices"  => $arParams['REFRESH_PRICES'],
                                                            "path_to_payment" => $arParams['PATH_TO_PAYMENT']
                                                        );
                                                        /*$paymentSubTitle = Loc::getMessage('SPOD_TPL_BILL') . " " . Loc::getMessage('SPOD_NUM_SIGN') . $payment['ACCOUNT_NUMBER'];
                                                        if (isset($payment['DATE_BILL'])) {
                                                            $paymentSubTitle .= " " . Loc::getMessage('SPOD_FROM') . " " . $payment['DATE_BILL']->format($arParams['ACTIVE_DATE_FORMAT']);
                                                        }
                                                        $paymentSubTitle .= ",";*/ ?>
                                                        <div class="col-md-4 col-sm-6 sale-order-detail-about-order-inner-container-status">

                                                            <div class="sale-order-detail-about-order-inner-container-status-detail">
                                                                <div class="order-content__subtitle">Способ оплаты</div>
                                                                <?
                                                                

                                                                /*?><a href="#"

                                                                               id="<?= $payment['ACCOUNT_NUMBER'] ?>"
                                                                               class="sale-order-detail-payment-options-methods-info-change-link"><?*/?>
                                                                <?= $payment['PAY_SYSTEM_NAME'] ?>
                                                                <?/*?></a><?*/?>
                                                            </div>
                                                        </div>
                                                        <?
                                                        if ($payment['PAID'] === 'Y') {
                                                            ?>
                                                            <div class="paid"><?= Loc::getMessage('SPOD_PAYMENT_PAID') ?></div>
                                                            <?
                                                        } elseif ($arResult['IS_ALLOW_PAY'] == 'N') {
                                                            ?>
                                                            <div class="payment_not_available"><?= Loc::getMessage('SPOD_TPL_RESTRICTED_PAID') ?></div>
                                                            <?
                                                        } else {
                                                            ?>
                                                            <div class="col-md-4 col-sm-6 sale-order-detail-about-order-inner-container-status">

                                                                <span class="unpaid"><?= Loc::getMessage('SPOD_PAYMENT_UNPAID') ?></span>
                                                            </div>
                                                            <?
                                                        }
                                                        ?>
                                                        <?
                                                        if ($payment['PAY_SYSTEM']["IS_CASH"] !== "Y") {
                                                            ?>
                                                            <div class="col-md-4 col-sm-12 col-xs-12 sale-order-detail-payment-options-methods-button-container">
                                                                <?
                                                                if ($payment['PAY_SYSTEM']['PSA_NEW_WINDOW'] === 'Y' && $arResult["IS_ALLOW_PAY"] !== "N") {
                                                                    ?>
                                                                    <a class="btn-theme sale-order-detail-payment-options-methods-button-element-new-window"
                                                                       target="_blank"
                                                                       href="<?= htmlspecialcharsbx($payment['PAY_SYSTEM']['PSA_ACTION_FILE']) ?>">
                                                                        <?= Loc::getMessage('SPOD_ORDER_PAY') ?>
                                                                    </a>
                                                                    <?
                                                                } else {
                                                                    if ($payment["PAID"] === "Y" || $arResult["CANCELED"] === "Y" || $arResult["IS_ALLOW_PAY"] === "N") {
                                                                        ?>
                                                                        <a class="btn-theme sale-order-detail-payment-options-methods-button-element inactive-button"><?= Loc::getMessage('SPOD_ORDER_PAY') ?></a>
                                                                        <?
                                                                    } else {
                                                                        ?>
                                                                        <a class="btn-theme sale-order-detail-payment-options-methods-button-element active-button button secondary upper outline"><?= Loc::getMessage('SPOD_ORDER_PAY') ?></a>
                                                                        <?
                                                                    }
                                                                }
                                                                ?>
                                                            </div>
                                                            <?
                                                        }
                                                        ?>
                                                    </div>
                                                </div>
                                                <?
                                                if (!empty($payment['CHECK_DATA'])) {
                                                    $listCheckLinks = "";
                                                    foreach ($payment['CHECK_DATA'] as $checkInfo) {
                                                        $title = Loc::getMessage('SPOD_CHECK_NUM',
                                                                array('#CHECK_NUMBER#' => $checkInfo['ID'])) . " - " . htmlspecialcharsbx($checkInfo['TYPE_NAME']);
                                                        if (strlen($checkInfo['LINK']) > 0) {
                                                            $link = $checkInfo['LINK'];
                                                            $listCheckLinks .= "<div><a href='$link' target='_blank'>$title</a></div>";
                                                        }
                                                    }
                                                    if (strlen($listCheckLinks) > 0) {
                                                        ?>
                                                        <div class="sale-order-detail-payment-options-methods-info-total-check">
                                                            <div class="sale-order-detail-sum-check-left"><?= Loc::getMessage('SPOD_CHECK_TITLE') ?>
                                                                :
                                                            </div>
                                                            <div class="sale-order-detail-sum-check-left">
                                                                <?= $listCheckLinks ?>
                                                            </div>
                                                        </div>
                                                        <?
                                                    }
                                                }
                                                if (
                                                    $payment['PAID'] !== 'Y'
                                                    && $arResult['CANCELED'] !== 'Y'
                                                    && $arParams['GUEST_MODE'] !== 'Y'
                                                    && $arResult['LOCK_CHANGE_PAYSYSTEM'] !== 'Y'
                                                ) {
                                                    ?>

                                                    <?
                                                }
                                                ?>
                                                <?
                                                if ($arResult['IS_ALLOW_PAY'] === 'N' && $payment['PAID'] !== 'Y') {
                                                    ?>
                                                    <div class="sale-order-detail-status-restricted-message-block">
                                                        <span class="sale-order-detail-status-restricted-message"><?= Loc::getMessage('SOPD_TPL_RESTRICTED_PAID_MESSAGE') ?></span>
                                                    </div>
                                                    <?
                                                }
                                                ?>

                                            </div>

                                            <div class="sale-order-detail-payment-inner-row-template col-md-offset-3 col-sm-offset-5 col-md-5 col-sm-10 col-xs-12">
                                                <a class="sale-order-list-cancel-payment">
                                                    <i class="fa fa-long-arrow-left"></i> <?= Loc::getMessage('SPOD_CANCEL_PAYMENT') ?>
                                                </a>
                                            </div>

                                        </div>
                                        <?
                                        if ($payment["PAID"] !== "Y"
                                            && $payment['PAY_SYSTEM']["IS_CASH"] !== "Y"
                                            && $payment['PAY_SYSTEM']['PSA_NEW_WINDOW'] !== 'Y'
                                            && $arResult['CANCELED'] !== 'Y'
                                            && $arResult["IS_ALLOW_PAY"] !== "N") {
                                            ?>
                                            <div class="row sale-order-detail-payment-options-methods-template col-md-12 col-sm-12 col-xs-12">
														<span class="sale-paysystem-close active-button">
															<span class="sale-paysystem-close-item sale-order-payment-cancel"></span>
                                                            <!--sale-paysystem-close-item-->
														</span><!--sale-paysystem-close-->
                                                <?= $payment['BUFFERED_OUTPUT'] ?>
                                                <!--<a class="sale-order-payment-cancel">-->
                                                <?//= Loc::getMessage('SPOD_CANCEL_PAY')
                                                ?><!--</a>-->
                                            </div>
                                            <?
                                        }
                                        ?>
                                    </div>
                                    <?
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>

        </div>
    </div>

    <? // Способ оплаты
    /* ?>
    <div class="order-content__section">
        <div class="order-content__section-title">
            <?= Loc::getMessage('PAY_SYSTEM_NAME') ?>
        </div>

        <? foreach ($arResult['PAYMENT'] as $payment) : ?>
            <div class="order-content__item">
                <div class="row">
                    <? // оплата ?>
                    <?
                    $paymentData[$payment['ACCOUNT_NUMBER']] = array(
                        "payment"         => $payment['ACCOUNT_NUMBER'],
                        "order"           => $arResult['ACCOUNT_NUMBER'],
                        "allow_inner"     => $arParams['ALLOW_INNER'],
                        "only_inner_full" => $arParams['ONLY_INNER_FULL'],
                        "refresh_prices"  => $arParams['REFRESH_PRICES'],
                        "path_to_payment" => $arParams['PATH_TO_PAYMENT']
                    );
                    ?>
                    <div class="col-md-4">
                        <div class="order-content__column">
                            <div class="order-content__subtitle">
                                <?= Loc::getMessage('PAYMENT_SYSTEM_TITLE') ?>
                            </div>
                            <div class="order-content__value">
                                <?= $payment['PAY_SYSTEM_NAME'] ?>
                            </div>
                        </div>
                    </div>

                    <? // статус оплаты ?>
                    <div class="col-md-4">
                        <div class="order-content__column">
                            <div class="order-content__subtitle">
                                <?= Loc::getMessage('PAYMENT_INFO_TITLE') ?>
                            </div>
                            <div class="order-content__value">
                                <?
                                    if ($payment['PAID'] === 'Y') :
                                        echo Loc::getMessage('SPOD_PAYMENT_PAID');
                                    elseif ($arResult['IS_ALLOW_PAY'] == 'N') :
                                        echo Loc::getMessage('SPOD_TPL_RESTRICTED_PAID');
                                    else :
                                        echo Loc::getMessage('SPOD_PAYMENT_UNPAID');
                                    endif;
                                ?>
                            </div>
                        </div>
                    </div>

                    <? // какая-то магия ?>
                    <? if ($payment['PAY_SYSTEM']["IS_CASH"] !== "Y") : ?>
                        <div class="col-md-4">
                            <div class="order-content__column">
                                <? if ($payment['PAY_SYSTEM']['PSA_NEW_WINDOW'] === 'Y' && $arResult["IS_ALLOW_PAY"] !== "N") : ?>
                                    <a class="btn-theme sale-order-detail-payment-options-methods-button-element-new-window"
                                       target="_blank"
                                       href="<?= htmlspecialcharsbx($payment['PAY_SYSTEM']['PSA_ACTION_FILE']) ?>"
                                    >
                                        <?= Loc::getMessage('SPOD_ORDER_PAY') ?>
                                    </a>
                                <? else :
                                    if ($payment["PAID"] === "Y" || $arResult["CANCELED"] === "Y" || $arResult["IS_ALLOW_PAY"] === "N") :
                                        ?>
                                         <a class="btn-theme sale-order-detail-payment-options-methods-button-element inactive-button"><?= Loc::getMessage('SPOD_ORDER_PAY') ?></a>
                                         <?
                                    else :
                                        ?>
                                            <a class="btn-theme sale-order-detail-payment-options-methods-button-element active-button button secondary upper outline"><?= Loc::getMessage('SPOD_ORDER_PAY') ?></a>
                                        <?
                                    endif; ?>
                                <? endif; ?>
                            </div>
                        </div>
                    <? endif; ?>


                    <?
                    if (!empty($payment['CHECK_DATA'])) {
                        $listCheckLinks = "";
                        foreach ($payment['CHECK_DATA'] as $checkInfo) {
                            $title = Loc::getMessage('SPOD_CHECK_NUM',
                                    array('#CHECK_NUMBER#' => $checkInfo['ID'])) . " - " . htmlspecialcharsbx($checkInfo['TYPE_NAME']);
                            if (strlen($checkInfo['LINK']) > 0) {
                                $link = $checkInfo['LINK'];
                                $listCheckLinks .= "<div><a href='$link' target='_blank'>$title</a></div>";
                            }
                        }
                        if (strlen($listCheckLinks) > 0) {
                            ?>
                            <div class="sale-order-detail-payment-options-methods-info-total-check">
                                <div class="sale-order-detail-sum-check-left"><?= Loc::getMessage('SPOD_CHECK_TITLE') ?>
                                    :
                                </div>
                                <div class="sale-order-detail-sum-check-left">
                                    <?= $listCheckLinks ?>
                                </div>
                            </div>
                            <?
                        }
                    }
                    if (
                        $payment['PAID'] !== 'Y'
                        && $arResult['CANCELED'] !== 'Y'
                        && $arParams['GUEST_MODE'] !== 'Y'
                        && $arResult['LOCK_CHANGE_PAYSYSTEM'] !== 'Y'
                    ) {
                        ?>

                        <?
                    }
                    ?>

                    <? if ($arResult['IS_ALLOW_PAY'] === 'N' && $payment['PAID'] !== 'Y') : ?>
                        <div class="col-md-12">
                            <div class="order-content__column">
                                <div class="alert alert--warning">
                                    <?= Loc::getMessage('SOPD_TPL_RESTRICTED_PAID_MESSAGE') ?>
                                </div>
                            </div>
                        </div>
                    <? endif; ?>

                    <div class="sale-order-detail-payment-inner-row-template col-md-offset-3 col-sm-offset-5 col-md-5 col-sm-10 col-xs-12">
                        <a class="sale-order-list-cancel-payment">
                            <i class="fa fa-long-arrow-left"></i> <?= Loc::getMessage('SPOD_CANCEL_PAYMENT') ?>
                        </a>
                    </div>

                </div>
                <?
                    if ($payment["PAID"] !== "Y"
                        && $payment['PAY_SYSTEM']["IS_CASH"] !== "Y"
                        && $payment['PAY_SYSTEM']['PSA_NEW_WINDOW'] !== 'Y'
                        && $arResult['CANCELED'] !== 'Y'
                        && $arResult["IS_ALLOW_PAY"] !== "N") {
                ?>
                    <div class="row sale-order-detail-payment-options-methods-template col col-12">
                        <span class="sale-paysystem-close active-button">
                            <span class="sale-paysystem-close-item sale-order-payment-cancel"></span>
                            <!--sale-paysystem-close-item-->
                        </span><!--sale-paysystem-close-->
                        <?= $payment['BUFFERED_OUTPUT'] ?>
                        <!--<a class="sale-order-payment-cancel">-->
                        <?//= Loc::getMessage('SPOD_CANCEL_PAY')
                        ?><!--</a>-->
                    </div>
                <? } ?>
            </div>
        <? endforeach; ?>
    </div><? */ ?>

    <? // Доставка ?>
    <? if (count($arResult['SHIPMENT'])) : ?>
        <div class="order-content__section">
            <div class="order-content__section-title">
                <?= Loc::getMessage('SPOD_ORDER_SHIPMENT') ?>
            </div>
            <div class="order-content__item">

                <? foreach ($arResult['SHIPMENT'] as $shipment) : ?>
                    <?
                        if (!strlen($shipment['PRICE_DELIVERY_FORMATED'])) :
                            $shipment['PRICE_DELIVERY_FORMATED'] = 0;
                        endif;
                    ?>

                    <div class="row">
                        <? // стоимость ?>
                        <div class="col-md-4">
                            <div class="order-content__column">
                                <div class="order-content__subtitle">
                                    <?= Loc::getMessage('SPOD_DELIVARY_PRICE') ?>
                                </div>
                                <div class="order-content__value">
                                    <?= $deliveryCost ?>
                                </div>
                            </div>
                        </div>

                        <? // служба ?>
                        <div class="col-md-4">
                            <div class="order-content__column">
                                <div class="order-content__subtitle">
                                    <?= Loc::getMessage('SPOD_ORDER_DELIVERY') ?>
                                </div>
                                <div class="order-content__value">
                                    <?= htmlspecialcharsbx($shipment["DELIVERY_NAME"]) ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?
                        $store = $arResult['DELIVERY']['STORE_LIST'][$shipment['STORE_ID']];

                        if (isset($store)) :
                    ?>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="order-content__column">
                                    <div class="order-content__subtitle">
                                        <?= Loc::getMessage('SPOD_SHIPMENT_STORE') ?>
                                    </div>
                                    <div class="order-content__map">
                                        <?
                                        $APPLICATION->IncludeComponent(
                                            "bitrix:map.yandex.view",
                                            "",
                                            Array(
                                                "INIT_MAP_TYPE" => "COORDINATES",
                                                "MAP_DATA"      => serialize(
                                                    array(
                                                        'yandex_lon' => $store['GPS_S'],
                                                        'yandex_lat' => $store['GPS_N'],
                                                        'PLACEMARKS' => array(
                                                            array(
                                                                "LON"  => $store['GPS_S'],
                                                                "LAT"  => $store['GPS_N'],
                                                                "TEXT" => htmlspecialcharsbx($store['TITLE'])
                                                            )
                                                        )
                                                    )
                                                ),
                                                "MAP_WIDTH"     => "100%",
                                                "MAP_HEIGHT"    => "300",
                                                "CONTROLS"      => array(
                                                    "ZOOM",
                                                    "SMALLZOOM",
                                                    "SCALELINE"
                                                ),
                                                "OPTIONS"       => array(
                                                    "ENABLE_DRAGGING",
                                                    "ENABLE_SCROLL_ZOOM",
                                                    "ENABLE_DBLCLICK_ZOOM"
                                                ),
                                                "MAP_ID"        => ""
                                            )
                                        );
                                        ?>


                                        <? if (strlen($store['ADDRESS'])) : ?>

                                            <?= Loc::getMessage('SPOD_STORE_ADDRESS') ?>:
                                            <?= htmlspecialcharsbx($store['ADDRESS']) ?>

                                        <? endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <? endif; ?>
                <? endforeach; ?>
            </div>

        </div>
    <? endif; ?>


                <div class="row sale-order-detail-payment-options-order-content">

                    <div class="col col-12 sale-order-detail-payment-options-order-content-container">
                        <div class="row">
                            <?
                            foreach ($arResult['BASKET'] as $basketItem) {
                                ?>
                                <div class="sale-order-detail-order-item-tr sale-order-detail-order-basket-info sale-order-detail-order-item-tr-first col col-12">
                                    <div class="row">
                                        <div class="sale-order-detail-order-item-img-block col col-2">
                                            <a href="<?= $basketItem['DETAIL_PAGE_URL'] ?>">
                                                <?
                                                if (strlen($basketItem['PICTURE']['SRC'])) {
                                                    $imageSrc = $basketItem['PICTURE']['SRC'];
                                                } else {
                                                    $imageSrc = $this->GetFolder() . '/images/no_photo.png';
                                                }
                                                ?>
                                                <div class="sale-order-detail-order-item-imgcontainer"
                                                     style="background-image: url(<?= $imageSrc ?>);
                                                             background-image: -webkit-image-set(url(<?= $imageSrc ?>) 1x,
                                                             url(<?= $imageSrc ?>) 2x)">
                                                </div>
                                            </a>
                                        </div>
                                        <div class="sale-order-detail-order-item-title col col-4">
                                            <a href="<?= $basketItem['DETAIL_PAGE_URL'] ?>">
                                                <?= htmlspecialcharsbx($basketItem['NAME']) ?>
                                            </a>
                                        </div>
                                        <?
                                        if (isset($basketItem['PROPS']) && is_array($basketItem['PROPS'])) {
                                            foreach ($basketItem['PROPS'] as $itemProps) {
                                                ?>
                                                <div class="sale-order-detail-order-item col <?
                                                if ($itemProps['CODE'] === 'SIZE') { ?>col-2<? } else { ?>col-1<?
                                                } ?>">
                                                    <span class="sale-order-detail-order-item-color-name"><?= htmlspecialcharsbx($itemProps['NAME']) ?></span>

                                                    <?
                                                    if ($itemProps['CODE'] === 'COLOR') { ?>

                                                        <span class="element-colors"
                                                              style="background-color: <?= $arResult['COLORS_HEX'][$itemProps['SKU_VALUE']['XML_ID']] ?>">
                                                            </span>

                                                    <? } else { ?>

                                                        <span class="sale-order-detail-order-item-color-type"><?= htmlspecialcharsbx($itemProps['VALUE']) ?></span>

                                                        <?
                                                    } ?>
                                                </div>
                                                <?
                                            }
                                        }
                                        ?>
                                        <?/*?><div class="sale-order-detail-order-item-td sale-order-detail-order-item-properties bx-text-right">
                                                                <div class="sale-order-detail-order-item-td-title col-xs-7 col-sm-5 visible-xs visible-sm">
                                                                    <?= Loc::getMessage('SPOD_PRICE') ?>
                                                                </div>
                                                                <div class="sale-order-detail-order-item-td-text">
                                                                    <strong class="bx-price"><?= $basketItem['BASE_PRICE_FORMATED'] ?></strong>
                                                                </div>
                                                            </div><?*/ ?>
                                        <?/*
                                                            if (strlen($basketItem["DISCOUNT_PRICE_PERCENT_FORMATED"])) {
                                                                ?>
                                                                <div class="sale-order-detail-order-item-td sale-order-detail-order-item-properties bx-text-right">
                                                                    <div class="sale-order-detail-order-item-td-title col-xs-7 col-sm-5 visible-xs visible-sm">
                                                                        <?= Loc::getMessage('SPOD_DISCOUNT') ?>
                                                                    </div>
                                                                    <div class="sale-order-detail-order-item-td-text">
                                                                        <strong class="bx-price"><?= $basketItem['DISCOUNT_PRICE_PERCENT_FORMATED'] ?></strong>
                                                                    </div>
                                                                </div>
                                                                <?
                                                            } elseif (strlen($arResult["SHOW_DISCOUNT_TAB"])) {
                                                                ?>
                                                                <div class="sale-order-detail-order-item-td sale-order-detail-order-item-properties bx-text-right">
                                                                    <div class="sale-order-detail-order-item-td-title col-xs-7 col-sm-5 visible-xs visible-sm">
                                                                        <?= Loc::getMessage('SPOD_DISCOUNT') ?>
                                                                    </div>
                                                                    <div class="sale-order-detail-order-item-td-text">
                                                                        <strong class="bx-price"></strong>
                                                                    </div>
                                                                </div>
                                                                <?
                                                            }*/
                                        ?>
                                        <div class="sale-order-detail-order-item col col-1">
                                            <span class="sale-order-detail-order-item-color-name"><?= Loc::getMessage('SPOD_QUANTITY') ?></span>
                                            <div class="sale-order-detail-order-item-td-text">
														<span><?= $basketItem['QUANTITY'] ?>&nbsp;
                                                            <?
                                                            if (strlen($basketItem['MEASURE_NAME'])) {
                                                                echo htmlspecialcharsbx($basketItem['MEASURE_NAME']);
                                                            } else {
                                                                echo Loc::getMessage('SPOD_DEFAULT_MEASURE');
                                                            }
                                                            ?></span>
                                            </div>
                                        </div>
                                        <div class="col col-2">
                                            <div class="sale-order-detail-order-item-td-text element-summ">
                                                <strong class="bx-price all"><?= $basketItem['FORMATED_SUM'] ?></strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?
                            }
                            ?>
                        </div>
                    </div>
                </div>




    </div>
    <?
    $javascriptParams = array(
        "url"            => CUtil::JSEscape($this->__component->GetPath() . '/ajax.php'),
        "templateFolder" => CUtil::JSEscape($templateFolder),
        "paymentList"    => $paymentData
    );
    $javascriptParams = CUtil::PhpToJSObject($javascriptParams);
    ?>
    <script>
        BX.Sale.PersonalOrderComponent.PersonalOrderDetail.init(<?=$javascriptParams?>);
    </script>
    <?
}
?>
