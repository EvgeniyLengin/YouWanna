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
    <div class="personal-right-block-wrapper">
        <div class="personal-right-block">
            <div class="container-fluid sale-order-detail">
                <?/*?><div class="sale-order-detail-title-container">
                    <h1 class="sale-order-detail-title-element">
                        <?= Loc::getMessage('SPOD_LIST_MY_ORDER', array(
                            '#ACCOUNT_NUMBER#'    => htmlspecialcharsbx($arResult["ACCOUNT_NUMBER"]),
                            '#DATE_ORDER_CREATE#' => $arResult["DATE_INSERT_FORMATED"]
                        )) ?>
                    </h1>
                </div><?*/ ?>
                <?
                if ($arParams['GUEST_MODE'] !== 'Y') {
                    ?>
                    <a class="sale-order-detail-back-to-list-link-up"
                       href="<?= htmlspecialcharsbx($arResult["URL_TO_LIST"]) ?>">
                        &larr; <?= Loc::getMessage('SPOD_RETURN_LIST_ORDERS') ?>
                    </a>
                    <?
                }
                ?>
                <div class="col-md-12 col-sm-12 col-xs-12 sale-order-detail-general">
                    <div class="row">
                        <div class="col-md-12 cols-sm-12 col-xs-12 sale-order-detail-general-head">
                        <span class="sale-order-detail-general-item">
                            <?= Loc::getMessage('SPOD_SUB_ORDER_TITLE', array(
                            "#ACCOUNT_NUMBER#"    => htmlspecialcharsbx($arResult["ACCOUNT_NUMBER"]),
                            "#DATE_ORDER_CREATE#" => $arResult["DATE_INSERT_FORMATED"]
                        )) ?>
                        <?/*= count($arResult['BASKET']); ?>
                        <?
                        $count = count($arResult['BASKET']) % 10;
                        if ($count == '1') {
                            echo Loc::getMessage('SPOD_TPL_GOOD');
                        } elseif ($count >= '2' && $count <= '4') {
                            echo Loc::getMessage('SPOD_TPL_TWO_GOODS');
                        } else {
                            echo Loc::getMessage('SPOD_TPL_GOODS');
                        }*/
                        ?>
                        <?= Loc::getMessage('SPOD_TPL_SUMOF') ?>
                        <?= $arResult["PRICE_FORMATED"] ?>
					</span>
                        </div>
                    </div>

                    <div class="row sale-order-detail-about-order">

                        <div class="col-md-12 col-sm-12 col-xs-12 sale-order-detail-about-order-container">
                            <div class="row">
                                <div class="col-md-12 col-sm-12 col-xs-12 sale-order-detail-about-order-inner-container">
                                    <h3 class="sale-order-detail-about-order-title-element">
                                        <?= Loc::getMessage('SPOD_LIST_ORDER_INFO') ?>
                                    </h3>
                                    <div class="row">
                                        <div class="col-md-4 col-sm-6 sale-order-detail-about-order-inner-container-name">
                                            <div class="sale-order-detail-about-order-inner-container-name-title">
                                                <?
                                                $userName = $arResult["USER"]["NAME"] . " " . $arResult["USER"]["SECOND_NAME"] . " " . $arResult["USER"]["LAST_NAME"];
                                                if (strlen($userName) || strlen($arResult['FIO'])) {
                                                    echo Loc::getMessage('SPOD_LIST_FIO');
                                                } else {
                                                    echo Loc::getMessage('SPOD_LOGIN');
                                                }
                                                ?>
                                            </div>
                                            <div class="sale-order-detail-about-order-inner-container-name-detail">
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
                                            <?/*?><a class="sale-order-detail-about-order-inner-container-name-read-less">
                                                <?= Loc::getMessage('SPOD_LIST_LESS') ?>
                                            </a>
                                            <a class="sale-order-detail-about-order-inner-container-name-read-more">
                                                <?= Loc::getMessage('SPOD_LIST_MORE') ?>
                                            </a><?*/ ?>
                                        </div>

                                        <div class="col-md-4 col-sm-6 sale-order-detail-about-order-inner-container-status">
                                            <div class="sale-order-detail-about-order-inner-container-status-title">
                                                <?= Loc::getMessage('SPOD_LIST_CURRENT_STATUS', array(
                                                    '#DATE_ORDER_CREATE#' => $arResult["DATE_INSERT_FORMATED"]
                                                )) ?>
                                            </div>
                                            <div class="sale-order-detail-about-order-inner-container-status-detail">
                                                <?
                                                if ($arResult['CANCELED'] !== 'Y') {
                                                    echo htmlspecialcharsbx($arResult["STATUS"]["NAME"]);
                                                } else {
                                                    echo Loc::getMessage('SPOD_ORDER_CANCELED');
                                                }
                                                ?>
                                            </div>
                                        </div>

                                        <div class="col-md-4 col-sm-6 sale-order-detail-about-order-inner-container-price">
                                            <div class="sale-order-detail-about-order-inner-container-price-title">
                                                <?= Loc::getMessage('SPOD_ORDER_PRICE') ?>:
                                            </div>
                                            <div class="sale-order-detail-about-order-inner-container-price-detail">
                                                <?= $arResult["PRICE_FORMATED"] ?>
                                            </div>
                                        </div>
                                        <?
                                        /*if ($arParams['GUEST_MODE'] !== 'Y') {
                                            ?>
                                            <div class="col-md-2 col-sm-6 sale-order-detail-about-order-inner-container-repeat">
                                                <a href="<?= $arResult["URL_TO_COPY"] ?>"
                                                   class="sale-order-detail-about-order-inner-container-repeat-button">
                                                    <?= Loc::getMessage('SPOD_ORDER_REPEAT') ?>
                                                </a>
                                                <?
                                                if ($arResult["CAN_CANCEL"] === "Y") {
                                                    ?>
                                                    <a href="<?= $arResult["URL_TO_CANCEL"] ?>"
                                                       class="sale-order-detail-about-order-inner-container-repeat-cancel">
                                                        <?= Loc::getMessage('SPOD_ORDER_CANCEL') ?>
                                                    </a>
                                                    <?
                                                }
                                                ?>
                                            </div>
                                            <?
                                        }*/
                                        ?>
                                    </div>
                                    <?/*?><div class="col-md-12 col-sm-12 col-xs-12 sale-order-detail-about-order-inner-container-details">
                                        <h4 class="sale-order-detail-about-order-inner-container-details-title">
                                            <?= Loc::getMessage('SPOD_USER_INFORMATION') ?>
                                        </h4>
                                        <ul class="sale-order-detail-about-order-inner-container-details-list">
                                            <?
                                            if (strlen($arResult["USER"]["LOGIN"]) && !in_array("LOGIN",
                                                    $arParams['HIDE_USER_INFO'])) {
                                                ?>
                                                <li class="sale-order-detail-about-order-inner-container-list-item">
                                                    <?= Loc::getMessage('SPOD_LOGIN') ?>:
                                                    <div class="sale-order-detail-about-order-inner-container-list-item-element">
                                                        <?= htmlspecialcharsbx($arResult["USER"]["LOGIN"]) ?>
                                                    </div>
                                                </li>
                                                <?
                                            }
                                            if (strlen($arResult["USER"]["EMAIL"]) && !in_array("EMAIL",
                                                    $arParams['HIDE_USER_INFO'])) {
                                                ?>
                                                <li class="sale-order-detail-about-order-inner-container-list-item">
                                                    <?= Loc::getMessage('SPOD_EMAIL') ?>:
                                                    <a class="sale-order-detail-about-order-inner-container-list-item-link"
                                                       href="mailto:<?= htmlspecialcharsbx($arResult["USER"]["EMAIL"]) ?>"><?= htmlspecialcharsbx($arResult["USER"]["EMAIL"]) ?></a>
                                                </li>
                                                <?
                                            }
                                            if (strlen($arResult["USER"]["PERSON_TYPE_NAME"]) && !in_array("PERSON_TYPE_NAME",
                                                    $arParams['HIDE_USER_INFO'])) {
                                                ?>
                                                <li class="sale-order-detail-about-order-inner-container-list-item">
                                                    <?= Loc::getMessage('SPOD_PERSON_TYPE_NAME') ?>:
                                                    <div class="sale-order-detail-about-order-inner-container-list-item-element">
                                                        <?= htmlspecialcharsbx($arResult["USER"]["PERSON_TYPE_NAME"]) ?>
                                                    </div>
                                                </li>
                                                <?
                                            }
                                            if (isset($arResult["ORDER_PROPS"])) {
                                                foreach ($arResult["ORDER_PROPS"] as $property) {
                                                    ?>
                                                    <li class="sale-order-detail-about-order-inner-container-list-item">
                                                        <?= htmlspecialcharsbx($property['NAME']) ?>:
                                                        <div class="sale-order-detail-about-order-inner-container-list-item-element">
                                                            <?
                                                            if ($property["TYPE"] == "Y/N") {
                                                                echo Loc::getMessage('SPOD_' . ($property["VALUE"] == "Y" ? 'YES' : 'NO'));
                                                            } else {
                                                                if ($property['MULTIPLE'] == 'Y'
                                                                    && $property['TYPE'] !== 'FILE'
                                                                    && $property['TYPE'] !== 'LOCATION') {
                                                                    $propertyList = unserialize($property["VALUE"]);
                                                                    foreach ($propertyList as $propertyElement) {
                                                                        echo $propertyElement . '</br>';
                                                                    }
                                                                } elseif ($property['TYPE'] == 'FILE') {
                                                                    echo $property["VALUE"];
                                                                } else {
                                                                    echo htmlspecialcharsbx($property["VALUE"]);
                                                                }
                                                            }
                                                            ?>
                                                        </div>
                                                    </li>
                                                    <?
                                                }
                                            }
                                            ?>
                                        </ul>
                                        <?
                                        if (strlen($arResult["USER_DESCRIPTION"])) {
                                            ?>
                                            <h4 class="sale-order-detail-about-order-inner-container-details-title sale-order-detail-about-order-inner-container-comments">
                                                <?= Loc::getMessage('SPOD_ORDER_DESC') ?>
                                            </h4>
                                            <div class="col-xs-12 sale-order-detail-about-order-inner-container-list-item-element">
                                                <?= nl2br(htmlspecialcharsbx($arResult["USER_DESCRIPTION"])) ?>
                                            </div>
                                            <?
                                        }
                                        ?>
                                    </div><?*/ ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 col-sm-12 col-xs-12 sale-order-detail-about-order-inner-container">
                                    <h3 class="sale-order-detail-about-order-title-element">
                                        <?= Loc::getMessage('SPOD_ORDER_PAYMENT') ?>
                                    </h3>
                                    <div class="row">
                                        <div class="col-md-4 col-sm-6 sale-order-detail-about-order-inner-container-name">
                                            <div class="sale-order-detail-about-order-inner-container-name-title">
                                                <?= Loc::getMessage('SPOD_LOGIN') ?>
                                            </div>
                                            <div class="sale-order-detail-about-order-inner-container-name-detail">
                                                <?= htmlspecialcharsbx($arResult["USER"]["LOGIN"]) ?>
                                            </div>
                                        </div>
                                        <?
                                        if (strlen($arResult["USER"]["EMAIL"]) && !in_array("EMAIL",
                                                $arParams['HIDE_USER_INFO'])) {
                                            ?>
                                            <div class="col-md-4 col-sm-6 sale-order-detail-about-order-inner-container-status">
                                                <div class="sale-order-detail-about-order-inner-container-status-title">
                                                    <?= Loc::getMessage('SPOD_EMAIL') ?>:
                                                </div>
                                                <div class="sale-order-detail-about-order-inner-container-status-detail">

                                                    <?= htmlspecialcharsbx($arResult["USER"]["EMAIL"]) ?>

                                                </div>
                                            </div>
                                            <?
                                        } ?>

                                        <?
                                        if (isset($arResult["ORDER_PROPS"])) {
                                            foreach ($arResult["ORDER_PROPS"] as $property) {
                                                if ($property["TYPE"] == "Y/N") {
                                                    /*echo Loc::getMessage('SPOD_' . ($property["VALUE"] == "Y" ? 'YES' : 'NO'));*/
                                                } else {
                                                    if ($property['CODE'] === 'LOCATION') { ?>
                                                        <div class="col-md-4 col-sm-6 sale-order-detail-about-order-inner-container-status">
                                                            <div class="sale-order-detail-about-order-inner-container-status-title">
                                                                <?= $property["NAME"] ?>:
                                                            </div>
                                                            <div class="sale-order-detail-about-order-inner-container-status-detail">

                                                                <?= htmlspecialcharsbx($property["VALUE"]) ?>

                                                            </div>
                                                        </div>
                                                    <? } elseif ($property['CODE'] === 'ADDRESS') { ?>
                                                        <div class="col-md-4 col-sm-6 sale-order-detail-about-order-inner-container-status">
                                                            <div class="sale-order-detail-about-order-inner-container-status-title">
                                                                <?= $property["NAME"] ?>:
                                                            </div>
                                                            <div class="sale-order-detail-about-order-inner-container-status-detail">

                                                                <?= htmlspecialcharsbx($property["VALUE"]) ?>

                                                            </div>
                                                        </div>
                                                        <?
                                                    }
                                                }
                                            } ?>
                                            <?/*?><div class="col-md-4 col-sm-6 sale-order-detail-about-order-inner-container-status">
                                                <div class="sale-order-detail-about-order-inner-container-status-title">
                                                    <?= Loc::getMessage('CONTACT_NAME') ?>:
                                                </div>
                                                <div class="sale-order-detail-about-order-inner-container-status-detail">

                                                    <?= htmlspecialcharsbx($arResult['CONTACT_NAME']) ?>

                                                </div>
                                            </div><?*/?>
                                            <?
                                        } ?>
                                        <?
                                        /*if ($arParams['GUEST_MODE'] !== 'Y') {
                                            ?>
                                            <div class="col-md-2 col-sm-6 sale-order-detail-about-order-inner-container-repeat">
                                                <a href="<?= $arResult["URL_TO_COPY"] ?>"
                                                   class="sale-order-detail-about-order-inner-container-repeat-button">
                                                    <?= Loc::getMessage('SPOD_ORDER_REPEAT') ?>
                                                </a>
                                                <?
                                                if ($arResult["CAN_CANCEL"] === "Y") {
                                                    ?>
                                                    <a href="<?= $arResult["URL_TO_CANCEL"] ?>"
                                                       class="sale-order-detail-about-order-inner-container-repeat-cancel">
                                                        <?= Loc::getMessage('SPOD_ORDER_CANCEL') ?>
                                                    </a>
                                                    <?
                                                }
                                                ?>
                                            </div>
                                            <?
                                        }*/
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row sale-order-detail-payment-options">

                        <div class="col-md-12 col-sm-12 col-xs-12 sale-order-detail-payment-options-container">

                            <div class="row">
                                <div class="col-md-12 col-sm-12 col-xs-12 sale-order-detail-payment-options-inner-container">
                                    <div class="row">
                                        <div class="col-md-12 col-sm-12 col-xs-12 sale-order-detail-payment-options-methods-container">
                                            <h3 class="sale-order-detail-about-order-title-element">
                                                <?= Loc::getMessage('PAY_SYSTEM_NAME') ?>
                                            </h3>
                                            <?
                                            foreach ($arResult['PAYMENT'] as $payment) {
                                                ?>
                                                <div class="col-md-12 col-sm-12 col-xs-12 sale-order-detail-payment-options-methods">
                                                    <div class="row sale-order-detail-payment-options-methods-information-block">

                                                        <div class="col-md-12 col-sm-12 col-xs-12 sale-order-detail-payment-options-methods-info">
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
                                                                            <?/*?><a href="#"
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

                    <?
                    if (count($arResult['SHIPMENT'])) {
                        ?>
                        <div class="row sale-order-detail-payment-options">
                            <div class="col-md-12 col-sm-12 col-xs-12 sale-order-detail-payment-options-container">
                                <div class="row">
                                    <div class="col-md-12 col-sm-12 col-xs-12 sale-order-detail-payment-options-inner-container">
                                        <h3 class="sale-order-detail-payment-options-title-element">
                                            <?= Loc::getMessage('SPOD_ORDER_SHIPMENT') ?>
                                        </h3>
                                        <?
                                        foreach ($arResult['SHIPMENT'] as $shipment) {
                                            ?>
                                            <div class="row">
                                                <div class="col-md-12 col-md-12 col-sm-12 sale-order-detail-payment-options-shipment-container">
                                                    <div class="row">
                                                        <div class="col-md-12 col-md-12 col-sm-12 sale-order-detail-payment-options-shipment">
                                                            <?
                                                            if (!strlen($shipment['PRICE_DELIVERY_FORMATED'])) {
                                                                $shipment['PRICE_DELIVERY_FORMATED'] = 0;
                                                            } ?>
                                                            <div class="col-md-4 col-sm-6 sale-order-detail-about-order-inner-container-price">
                                                                <div class="sale-order-detail-about-order-inner-container-price-title">
                                                                    <?= Loc::getMessage('SPOD_DELIVARY_PRICE') ?>
                                                                </div>
                                                                <div class="sale-order-detail-about-order-inner-container-price-detail">
                                                                    <?= $shipment['PRICE_DELIVERY_FORMATED'] ?>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4 col-sm-6 sale-order-detail-about-order-inner-container-price">
                                                                <div class="sale-order-detail-about-order-inner-container-price-title">
                                                                    <?= Loc::getMessage('SPOD_ORDER_SHIPMENT_STATUS') ?>
                                                                </div>
                                                                <div class="sale-order-detail-about-order-inner-container-price-detail">
                                                                    <?= htmlspecialcharsbx($shipment['STATUS_NAME']) ?>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4 col-sm-6 sale-order-detail-about-order-inner-container-price">
                                                                <div class="sale-order-detail-about-order-inner-container-price-title">
                                                                    <?= Loc::getMessage('SPOD_ORDER_DELIVERY') ?>
                                                                </div>
                                                                <div class="sale-order-detail-about-order-inner-container-price-detail">
                                                                    <?= htmlspecialcharsbx($shipment["DELIVERY_NAME"]) ?>
                                                                </div>
                                                            </div>
                                                            <!--row-->
                                                            <div class="col-md-9 col-md-offset-3 col-sm-12 sale-order-detail-payment-options-shipment-composition-map">
                                                                <?
                                                                $store = $arResult['DELIVERY']['STORE_LIST'][$shipment['STORE_ID']];
                                                                if (isset($store)) {
                                                                    ?>
                                                                    <div class="row">
                                                                        <div class="col-md-12 col-sm-12 sale-order-detail-map-container">
                                                                            <div class="row">
                                                                                <h4 class="sale-order-detail-payment-options-shipment-composition-map-title">
                                                                                    <?= Loc::getMessage('SPOD_SHIPMENT_STORE') ?>
                                                                                </h4>
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
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <?
                                                                    if (strlen($store['ADDRESS'])) {
                                                                        ?>
                                                                        <div class="row">
                                                                            <div class="col-md-12 col-sm-12 sale-order-detail-payment-options-shipment-map-address">
                                                                                <div class="row">
																<span class="col-md-2 sale-order-detail-payment-options-shipment-map-address-title">
																	<?= Loc::getMessage('SPOD_STORE_ADDRESS') ?>:</span>
                                                                                    <span class="col-md-10 sale-order-detail-payment-options-shipment-map-address-element">
																	<?= htmlspecialcharsbx($store['ADDRESS']) ?></span>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <?
                                                                    }
                                                                }
                                                                ?>

                                                            </div>
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
                    }
                    ?>


                    <div class="row sale-order-detail-payment-options-order-content">

                        <div class="col-md-12 col-sm-12 col-xs-12 sale-order-detail-payment-options-order-content-container">
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

                                                        <?if ($itemProps['CODE'] === 'COLOR'){?>

                                                            <span class="element-colors"
                                                                  style="background-color: <?= $arResult['COLORS_HEX'][$itemProps['SKU_VALUE']['XML_ID']] ?>">
                                                            </span>

                                                        <?} else {?>

                                                            <span class="sale-order-detail-order-item-color-type"><?= htmlspecialcharsbx($itemProps['VALUE']) ?></span>

                                                        <?}?>
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

                </div><!--sale-order-detail-general-->
                <?
                if ($arParams['GUEST_MODE'] !== 'Y' && $arResult['LOCK_CHANGE_PAYSYSTEM'] !== 'Y') {
                    ?>
                    <a class="sale-order-detail-back-to-list-link-down"
                       href="<?= $arResult["URL_TO_LIST"] ?>">&larr; <?= Loc::getMessage('SPOD_RETURN_LIST_ORDERS') ?></a>
                    <?
                }
                ?>
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

