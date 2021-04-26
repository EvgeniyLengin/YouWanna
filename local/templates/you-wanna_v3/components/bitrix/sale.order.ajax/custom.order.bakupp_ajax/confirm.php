<?php
/**
 * @var array $arResult
 * @var array $arParams
 * @var CMain $APPLICATION
 */
use Bitrix\Main\SystemException;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Localization\Loc;

if ($arResult['ORDER']['ID']) {
    // Подписка пользователя на рассылку после Оформления заказа
    CYouWanna::subscribeUserFromOrder($arResult['ORDER']['ID']);
}

?>

<style>
    .breadcrumbs-c {
        display: none;
    }
    .content-c > .wrap {
        margin: initial;
        max-width: initial;
        padding: 0;
    }
</style>
<div class="order-confirm-page">
    <div class="wrap">
        <div class="row">
            <div class="inner">
                <?php if (!empty($arResult['ORDER'])) { ?>

                    <?php
                    $APPLICATION->AddChainItem(Loc::getMessage('SOA_TEMPL_ORDER_COMPLETE'));
                    $APPLICATION->SetPageProperty('title', Loc::getMessage('SOA_TEMPL_ORDER_COMPLETE'));
                    ?>
                    <div class="order-done">
                        <img src="<?=$templateFolder?>/images/order-done.png" alt="" />
                    </div>
                    <h3 class="success-page-title">
                        <?= Loc::getMessage('SOA_TEMPL_ORDER_COMPLETE_1') ?>
                        <br/>№<?= $arResult['ACCOUNT_NUMBER'] ?>
                        <?= Loc::getMessage('SOA_TEMPL_ORDER_COMPLETE_2') ?>
                    </h3>

                    <div>
                        <table class="sale_order_full_table">
                            <tr>
                                <td class="desc-success"><?= Loc::GetMessage('SOA_TEMPL_ORDER_COMPLETE_NOTIFY_TEXT') ?></td>
                            </tr>
                            <tr>
                                <td class="ps_logo">
                                    <div class="paysystem_name">
                                        <? if($arResult['ORDER']['PAY_SYSTEM_ID'] == 2) { ?><? //= Loc::getMessage('SOA_TEMPL_ORDER_COMPLETE_PAY_2_CASH') ?> <? } else { ?>
                                        <?= Loc::getMessage('SOA_TEMPL_ORDER_COMPLETE_PAY_2') ?>
                                        <? } ?>
                                    </div>
                                </td>
                            </tr>
                            <?php if (strlen($arResult['PAY_SYSTEM']['ACTION_FILE']) > 0) : ?>
                                <tr>
                                    <td>
                                        <?php if ($arResult['PAY_SYSTEM']['NEW_WINDOW'] === 'Y') : ?>

                                            <? if ($arResult['ORDER']['PAY_SYSTEM_ID'] === '17') { ?>
                                            <script language="JavaScript">
                                                window.autopayTimout = 5;

                                                function checkAutoPay() {
                                                    console.log(window.autopayTimout);
                                                    if (window.autopayTimout > 0) {
                                                        window.autopayTimout--;
                                                        $('.js-autopay-timer').html(window.autopayTimout);
                                                        if (window.autopayTimout === 0) {
                                                            location.assign($('.js-order-pay-button').attr('href'));
                                                        }
                                                        else {
                                                            setTimeout(checkAutoPay, 1000);
                                                        }
                                                    }
                                                    else {
                                                        $('.js-autopay-timer').parent().hide();
                                                    }
                                                }

                                                $(function () {
                                                    if ($('.js-order-pay-button').length > 0) {
                                                        setTimeout(checkAutoPay, 1000);
                                                    }
                                                });

                                                function stopAutoPayTimer() {
                                                    console.log('stop');
                                                    window.autopayTimout = -1;
                                                    $('.js-autopay-timer-container').hide();
                                                }

                                                $(document).ready(function () {
                                                    $('.js-order-pay-button').on('click', function () {
                                                        stopAutoPayTimer();
                                                    });
                                                });
                                            </script>
                                            <div class="js-autopay-timer-container" style="margin-bottom: 10px">
                                                <?= Loc::getMessage('ORDER_CONFIRM_TIMER_1'); ?>
                                                <span class="js-autopay-timer">5</span>
                                                <?= Loc::getMessage('ORDER_CONFIRM_TIMER_2'); ?>
                                                (<a href="javascript:void(0);"
                                                    onclick="stopAutoPayTimer();"><?= Loc::getMessage('ORDER_CONFIRM_TIMER_STOP'); ?></a>)
                                            </div>
                                        <? } ?>

                                        <?= Loc::getMessage('SOA_TEMPL_PAY_LINK',
                                            array('#LINK#' => $arParams['PATH_TO_PAYMENT'] . '?ORDER_ID=' . urlencode(urlencode($arResult['ORDER']['ACCOUNT_NUMBER'])))) ?>

                                        <? if ($arResult['ORDER']['PAY_SYSTEM_ID'] === '17') { ?>
                                            <div style="margin-top: 10px">
                                                <?= Loc::getMessage('ORDER_CONFIRM_PAY_DAYS') ?>
                                            </div>
                                        <? } ?>

                                            <?php
                                            if (CSalePdf::isPdfAvailable() && CSalePaySystemsHelper::isPSActionAffordPdf($arResult['PAY_SYSTEM']['ACTION_FILE'])) {
                                                ?><br/>
                                                <?= Loc::getMessage('SOA_TEMPL_PAY_PDF',
                                                    array('#LINK#' => $arParams['PATH_TO_PAYMENT'] . '?ORDER_ID=' . urlencode(urlencode($arResult['ORDER']['ACCOUNT_NUMBER'])) . '&pdf=1&DOWNLOAD=Y')) ?>
                                                <?php
                                            }
                                            ?>
                                        <?php else : ?>
                                            <?php if (strlen($arResult['PAY_SYSTEM']['PATH_TO_ACTION']) > 0) : ?>
                                                <?php
                                                try {
                                                    /** @noinspection PhpIncludeInspection */
                                                    include $arResult['PAY_SYSTEM']['PATH_TO_ACTION'];
                                                } catch (SystemException $e) {
                                                    /** @noinspection PhpDeprecationInspection */
                                                    if ((int)$e->getCode() === CSalePaySystemAction::GET_PARAM_VALUE) {
                                                        $message = Loc::getMessage('SOA_TEMPL_ORDER_PS_ERROR');
                                                    } else {
                                                        $message = $e->getMessage();
                                                    }

                                                    echo '<span style="color:red;">' . $message . '</span>';
                                                }
                                                ?>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </table>
                    </div>
                <?php } else { ?>

                    <b><?= Loc::getMessage('SOA_TEMPL_ERROR_ORDER') ?></b><br/><br/>

                    <table class="sale_order_full_table">
                        <tr>
                            <td>
                                <?= Loc::getMessage('SOA_TEMPL_ERROR_ORDER_LOST', array('#ORDER_ID#' => $arResult['ACCOUNT_NUMBER'])) ?>
                                <?= Loc::getMessage('SOA_TEMPL_ERROR_ORDER_LOST1') ?>
                            </td>
                        </tr>
                    </table>
                <?php } ?>
            </div>
        </div>
    </div>
