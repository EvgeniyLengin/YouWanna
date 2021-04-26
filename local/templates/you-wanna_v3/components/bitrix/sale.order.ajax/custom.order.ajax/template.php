<?php
/**
 * Шаблон компонента
 *
 * @var array $arParams
 * @var array $arResult
 * @var CMain $APPLICATION
 * @var CUser $USER
 * @var string $templateFolder
 */
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Page\Asset;

//$this->addExternalJs(SITE_TEMPLATE_PATH . '/assets/jquery.autocomplete.min.js');

Loc::loadLanguageFile(__FILE__);

if (($arResult['USER_VALS']['CONFIRM_ORDER'] === 'Y' || $arResult['NEED_REDIRECT'] === 'Y')
    && strlen($arResult['REDIRECT_URL']) > 0
    && ($arParams['ALLOW_AUTO_REGISTER'] === 'Y' || $USER->IsAuthorized())

) {
    $APPLICATION->RestartBuffer(); ?>
    <script type="text/javascript">
		window.top.location.href = '<?=CUtil::JSEscape($arResult['REDIRECT_URL'])?>';
    </script>
    <?php die();
}
//$asset = Asset::getInstance();
//$asset->addString('<script src="//api-maps.yandex.ru/2.1/?lang=ru_RU" type="text/javascript"></script>', true);

CJSCore::Init(array('fx', 'popup', 'window', 'ajax'));
?>


<div class="row">


    <? if (!isset($_REQUEST['ORDER_ID'])) { ?>

        <div class="col-lg-6 col-md-12">
            <div class="head-steps">
                <span class="head-step head-step-1 on">
                    <span class="head-step__value">1</span>
                    <span class="head-step__desc">доставка</span>
                </span>
                <span class="head-step head-step-2">
                    <span class="head-step__value">2</span>
                    <span class="head-step__desc">подтверждение</span>
                </span>
                <span class="head-step head-step-3">
                    <span class="head-step__value">3</span>
                    <span class="head-step__desc">оплата</span>
                </span>
            </div>
        <? } else { ?>

            <div class="col col-12 first">

            <? } ?>

            <div class="form__section cls-order-box" id="sale_order_props" data-step=2>
                <? $APPLICATION->IncludeFile(
                    SITE_DIR . 'ajax/get_basket.php',
                    [
                        'WITHOUT_BX_HEADER' => 'Y',
                        'STATIC_BASKET'     => 'Y',
                    ]
                ); ?>
            </div>


                <a name="order_form"></a>

                <div class="order-make-c root-of-fixed-block" id="order_form_div">
                    <NOSCRIPT>
                        <div class="errortext"><?= GetMessage('SOA_NO_JS') ?></div>
                    </NOSCRIPT>

                    <?php if (!function_exists('getColumnName')) {
                        /**
                         * @param $arHeader
                         *
                         * @return mixed|string
                         */
                        function getColumnName($arHeader)
                        {
                            return (strlen($arHeader['name']) > 0) ? $arHeader['name'] : GetMessage('SALE_' . $arHeader['id']);
                        }
                    } ?>


                    <?php if (!function_exists('cmpBySort')) {
                        /**
                         * @param $array1
                         * @param $array2
                         *
                         * @return int
                         */
                        function cmpBySort($array1, $array2)
                        {
                            if (!isset($array1['SORT'], $array2['SORT'])) {
                                return -1;
                            }

                            if ($array1['SORT'] > $array2['SORT']) {
                                return 1;
                            }

                            if ($array1['SORT'] < $array2['SORT']) {
                                return -1;
                            }

                            if ($array1['SORT'] === $array2['SORT']) {
                                return 0;
                            }

                            return 0;
                        }
                    } ?>

                    <?php
                    if ($arParams['ALLOW_AUTO_REGISTER'] === 'N' && !$USER->IsAuthorized()) {
                        if (!empty($arResult['OK_MESSAGE'])) {
                            foreach ((array)$arResult['OK_MESSAGE'] as $v) {
                                ShowNote($v);
                            }
                        }

                        /** @noinspection PhpIncludeInspection */
                        include $_SERVER['DOCUMENT_ROOT'] . $templateFolder . '/auth.php';
                    } else {
                    if ($arResult['NEED_REDIRECT'] === 'Y' || $arResult['USER_VALS']['CONFIRM_ORDER'] === 'Y') {
                        if (trim($arResult['REDIRECT_URL']) === '') {
                            /** @noinspection PhpIncludeInspection */
                            include $_SERVER['DOCUMENT_ROOT'] . $templateFolder . '/confirm.php';
                        }
                    } else {
                    ?>
                    <script type="text/javascript">

                        <?php  if(CSaleLocation::isLocationProEnabled()):?>

                        <?php
                        // spike: for children of cities we place this prompt
                        $city = \Bitrix\Sale\Location\TypeTable::getList(array(
                            'filter' => array('=CODE' => 'CITY'),
                            'select' => array('ID')
                        ))->fetch();
                        ?>

                        BX.saleOrderAjax.init(<?=CUtil::PhpToJSObject(array(
                            'source' => $this->__component->getPath() . '/get.php',
                            'cityTypeId' => (int)$city['ID'],
                            'messages' => array(
                                'otherLocation' => '--- ' . GetMessage('SOA_OTHER_LOCATION'),
                                'moreInfoLocation' => '--- ' . GetMessage('SOA_NOT_SELECTED_ALT'),
                                // spike: for children of cities we place this prompt
                                'notFoundPrompt' => '<div class="-bx-popup-special-prompt">' . GetMessage('SOA_LOCATION_NOT_FOUND') . '.<br />' . GetMessage('SOA_LOCATION_NOT_FOUND_PROMPT',
                                        array(
                                            '#ANCHOR#' => '<a href="javascript:void(0)" class="-bx-popup-set-mode-add-loc">',
                                            '#ANCHOR_END#' => '</a>'
                                        )) . '</div>'
                            )
                        ))?>);
                        <?endif?>
                        var BXFormPosting = false;

                        function submitForm(val) {
                            var orderForm;
                            if (true === BXFormPosting) {
                                return true;
                            }

                            BXFormPosting = true;

                            if ('Y' != val) {
                                BX('confirmorder').value = 'N';
                            }
                            <?/* if ($_REQUEST['sms_error'] === 'Y') { ?>
                                BX('confirmorder').value = 'N';
                            <? } */?>

                            orderForm = BX('ORDER_FORM');
                            //BX.showWait();
                            $('#ORDER_FORM').addClass('disabled');

                            <?if(CSaleLocation::isLocationProEnabled()):?>
                            BX.saleOrderAjax.cleanUp();
                            <?endif?>

                            BX.ajax.submit(orderForm, ajaxResult);

                            return true;
                        }

                        function ajaxResult(res) {
                            var json;
                            var orderForm = BX('ORDER_FORM');

                            try {
                                // if json came, it obviously a successfull order submit
                                json = JSON.parse(res);
                                //BX.closeWait();
                                $('#ORDER_FORM').removeClass('disabled');
                                if (json.error) {

                                    BXFormPosting = false;
                                    return;
                                } else if (json.redirect) {
                                    window.top.location.href = json.redirect;
                                }

                                //$(window).scroll(); //need for reposition fixed block
                            } catch (e) {
                                // json parse failed, so it is a simple chunk of html

                                BXFormPosting = false;
                                BX('order_form_content').innerHTML = res;

                                <?if(CSaleLocation::isLocationProEnabled()):?>
                                BX.saleOrderAjax.initDeferredControl();
                                <?php endif;?>
                            }

                            //BX.closeWait();
                            $('#ORDER_FORM').removeClass('disabled');
                            BX.onCustomEvent(orderForm, 'onAjaxSuccess');

                            /*reloadFormPhoneMask();*/
                            reloadDatataFromSaleOrder();
                        }

                        function SetContact(profileId) {
                            BX("profile_change").value = "Y";
                            submitForm();
                        }
                    </script>
                <?php if ($_POST['is_ajax_post'] !== 'Y')
                {
                ?>
                    <form action="<?= $APPLICATION->GetCurPage(); ?>" method="POST" name="ORDER_FORM" id="ORDER_FORM"
                          enctype="multipart/form-data" class="form">

                        <div id="order_form_content">

                            <?php
                            } else {
                                $APPLICATION->RestartBuffer();
                            }
                            ?>
                            <?= bitrix_sessid_post() ?>
                            <div class="left-order-block">
                                <? if ((int)$_REQUEST['PERMANENT_MODE_STEPS'] === 1) : ?>
                                    <input type="hidden" name="PERMANENT_MODE_STEPS" value="1"/>
                                <? endif; ?>

                                <? if (!empty($arResult['ERROR'])) : ?>
                                    <script type="text/javascript">
                                        top.BX.scrollToNode(top.BX('ORDER_FORM'));
                                    </script>
                                <? endif; ?>

                                <?php
                                /** @noinspection PhpIncludeInspection */
                                include $_SERVER['DOCUMENT_ROOT'] . $templateFolder . '/person_type.php';
                                /** @noinspection PhpIncludeInspection */
                                include $_SERVER['DOCUMENT_ROOT'] . $templateFolder . '/props.php';

                                if ($arParams['DELIVERY_TO_PAYSYSTEM'] === 'p2d') {
                                    /** @noinspection PhpIncludeInspection */
                                    include $_SERVER['DOCUMENT_ROOT'] . $templateFolder . '/paysystem.php';
                                    /** @noinspection PhpIncludeInspection */
                                    include $_SERVER['DOCUMENT_ROOT'] . $templateFolder . '/delivery.php';
                                    include $_SERVER['DOCUMENT_ROOT'] . $templateFolder . '/verification.php';
                                    /** @noinspection PhpIncludeInspection */
                                    include $_SERVER['DOCUMENT_ROOT'] . $templateFolder . '/related_props.php';
                                } else {
                                    /** @noinspection PhpIncludeInspection */
                                    include $_SERVER['DOCUMENT_ROOT'] . $templateFolder . '/delivery.php';

                                    /** @noinspection PhpIncludeInspection */
                                    include $_SERVER['DOCUMENT_ROOT'] . $templateFolder . '/related_props.php';
                                    include $_SERVER['DOCUMENT_ROOT'] . $templateFolder . '/verification.php';
                                    /** @noinspection PhpIncludeInspection */
                                    include $_SERVER['DOCUMENT_ROOT'] . $templateFolder . '/paysystem.php';
                                }
                                ?>
                            </div>

                            <?php if (strlen($arResult['PREPAY_ADIT_FIELDS']) > 0) {
                                echo $arResult['PREPAY_ADIT_FIELDS'];
                            } ?>

                            <?php if ($_POST['is_ajax_post'] !== 'Y')
                            {
                            ?>
                        </div>
                        <input type="hidden" name="confirmorder" id="confirmorder" value="Y">
                        <?/* if (!$USER->IsAuthorized()) { ?>
                            <input type="hidden" name="sms_error" id="sms_error" value="Y">
                        <? } */
                        ?>
                        <input type="hidden" name="profile_change" id="profile_change" value="N">
                        <input type="hidden" name="is_ajax_post" id="is_ajax_post" value="Y">
                        <input type="hidden" name="json" value="Y">

                        <?php if ($arParams['DELIVERY_NO_AJAX'] === 'N'): ?>
                            <div style="display:none;">
                                <?php $APPLICATION->IncludeComponent(
                                    'bitrix:sale.ajax.delivery.calculator',
                                    '',
                                    array(),
                                    null,
                                    array('HIDE_ICONS' => 'Y')
                                ); ?>
                            </div>
                        <?php endif; ?>
                        <?
                        }
                        else {
                            ?>
                            <script type="text/javascript">
                                top.BX('confirmorder').value = 'Y';
                                top.BX('profile_change').value = 'N';
                            </script>
                            <?php
                            die();
                        }
                        }
                        }
                        ?>

                    </form>

                    <?php if (CSaleLocation::isLocationProEnabled()): ?>
                        <div style="display: none">
                            <?php // we need to have all styles for sale.location.selector.steps, but RestartBuffer() cuts
                            // off document head with styles in it?>
                            <?php $APPLICATION->IncludeComponent(
                                'bitrix:sale.location.selector.steps',
                                '.default',
                                array(),
                                false
                            ); ?>
                            <?php $APPLICATION->IncludeComponent(
                                'bitrix:sale.location.selector.search',
                                '.default',
                                array(),
                                false
                            ); ?>
                        </div>
                    <?php endif ?>

                    <script>
                        var orderMESSAGES = {
                            DELIVERY_PRICE: '<?=GetMessage('SOA_TEMPL_JS_DELIVERY_PRICE')?>',
                            TOTAL_SUM_WITH_DELIVERY: '<?=GetMessage('SOA_TEMPL_JS_TOTAL_SUM_WITH_DELIVERY')?>',
                            CURRENCY: '<?=GetMessage('SOA_TEMPL_JS_CURRENCY')?>',
                            LOADING_POINTS: '<?=GetMessage('SOA_TEMPL_JS_LOADING_POINTS')?>',
                            LOADING_CITIES: '<?=GetMessage('SOA_TEMPL_JS_LOADING_CITIES')?>',
                            PHONE_FIELD_REQUIRED: '<?=GetMessage('SOA_TEMPL_JS_PHONE_FIELD_REQUIRED')?>',
                            LANGUAGE_ID: '<?=LANGUAGE_ID?>'
                        };
                    </script>
                </div>
                <div class="fixed-block-stopper"></div>
        </div>

        <div class="col-lg-3 col-md-12">
        </div>
            <? if (!isset($_REQUEST['ORDER_ID'])) { ?>
            <div class="col-lg-3 col-md-12">
               <?global $USER; if ($USER->IsAdmin()) {?>
                   <?$APPLICATION->IncludeComponent(
                                      "bitrix:sale.basket.basket.line",
                                      "template",
                                      Array(
                                         "PATH_TO_BASKET" => "/personal/cart/",
                                         "PATH_TO_PERSONAL" => "/personal/",
                                         "SHOW_PERSONAL_LINK" => "Y"
                                      )
                                   );?>
               <?}?>
                <div class="basket-checkout-container" data-entity="basket-checkout-aligner">
                    <div class="yw-basket-heading">Итого</div>
                    <div class="yw-basket-checkout">
                        <?php
                        if (CModule::IncludeModule("sale"))
                        {
                           $arBasketItems = array();
                           $dbBasketItems = CSaleBasket::GetList(
                                          array("NAME" => "ASC","ID" => "ASC"),
                                          array("FUSER_ID" => CSaleBasket::GetBasketUserID(), "LID" => SITE_ID, "ORDER_ID" => "NULL"),
                                          false,
                                          false,
                                          array("ID","MODULE","PRODUCT_ID","QUANTITY","CAN_BUY","PRICE"));
                           while ($arItems=$dbBasketItems->Fetch())
                           {
                              $arItems=CSaleBasket::GetByID($arItems["ID"]);
                              $arBasketItems[]=$arItems;
                              $cart_num+=$arItems['QUANTITY'];
                              $cart_sum+=$arItems['PRICE']*$arItems['QUANTITY'];
                           }
                           if (empty($cart_num))
                              $cart_num="0";
                           if (empty($cart_sum))
                              $cart_sum="0";

                           ?>


                           <div class="basket-checkout-section">
                               <div class="basket-checkout-section-inner">
                                   <div class="basket-checkout-block basket-checkout-block-items-count">
                                       <div class="yw-basket-checkout-row basket-checkout-block-items-count-inner">
                                           <div class="yw-basket-checkout-title">Количество товаров:</div>
                                           <div class="yw-basket-checkout-value basket-checkout-block-items-count-value" data-entity="basket-items-count"><?=$cart_num?></div>
                                       </div>
                                   </div>
                               </div>
                           </div>

                           <div class="basket-checkout-section">
                               <div class="basket-checkout-section-inner">
                                   <div class="basket-checkout-block basket-checkout-block-total-price">
                                       <div class="basket-checkout-block-total-price-inner">
                                           <div class="yw-basket-checkout-row basket-coupon-block-total-price">
                                               <div class="yw-basket-checkout-title">Всего</div>
                                               <div class="yw-basket-checkout-value basket-coupon-block-total-price-current" data-entity="basket-total-price">
                                                <? $totalPrice = $arResult['ORDER_TOTAL_PRICE_FORMATED']; ?>
                                                   <?=$totalPrice?>
                                               </div>
                                           </div>
                                       </div>
                                   </div>
                               </div>
                           </div>
                           <?
                        }
                        ?>

                    </div>

                    <div class="basket-checkout-block basket-checkout-block-btn">
                        <a href="#" class="button secondary upper basket-btn-checkout cls-next-step-btn">Далее</a>
                        <a href="javascript:void(0);"
                        onclick="checkOrderForm();"
                        id="ORDER_CONFIRM_BUTTON"
                        class="button secondary hiden upper basket-btn-checkout">
                        Оплатить
                    </a>
                </div>
            </div><? }?>


    </div>
</div>

<script>
    (function () {
        var step = 1;
        var maxStep = 4;
        var clsBox = '.cls-order-box'; // Класс блоков шагов
        var clsShowBox = 'cls-order-show-box'; // Класс блоков шагов
        var clsBtn = '.cls-next-step-btn'; // Класс кнопки "Далее"
        var clsError = 'error';

        function getBoxByStep(step) {
            return $(clsBox +'[data-step="'+ step +'"]');
        }

        function showOnlyCurrentStepBox(step) {
                    $(clsBox).removeClass(clsShowBox);
                    getBoxByStep(step).addClass(clsShowBox);
                }

        function getField($box) {
            return function (selector) {
                return $box.find(selector);
            }
        }

        function clearError($field) {
            if (!$field.length) {
                return;
            }

            if ($field.attr('name').toUpperCase() == 'ORDER_PROP_8') {
                $field = $field.closest('.form-item-inner');
            }

            $field.removeClass(clsError);
        }

        function showError($field) {
            if (!$field.length) {
                return;
            }

            if ($field.attr('name').toUpperCase() == 'ORDER_PROP_8') {
                $field = $field.closest('.form-item-inner');
            }

            $field.addClass(clsError);
        }

        // Return form fields by step
        function getFieldsByStep(step) {
            var $box = getBoxByStep(step);
            var fieldBy = getField($box);

            switch (step) {
                case 1:
                    return [
                        fieldBy('input[name="ORDER_PROP_8"]'),
                        // fieldBy('input[name="ORDER_PROP_5"]'),
                        fieldBy('input[name="ORDER_PROP_3"]'),
                        fieldBy('input[name="ORDER_PROP_4"]'),
                        fieldBy('input[name="ORDER_PROP_2"]'),
                        fieldBy('input[name="DELIVERY_ID"]:checked'),
                    ];
                    break;
                default:
                    return [];
                    break;
            }
        }

        // Validate form fields by step
        function validate() {
            var valid = true;
            var fields = getFieldsByStep(step);
            var isFirst = true;

            fields.forEach(function($field) {
                if (!$field.length) {
                    return;
                }

                if (!$field.val()) {
                    console.log($field, 'invalid');
                    valid = false;

                    if (isFirst) {
                        $field.focus();
                        isFirst = false;
                    }

                    showError($field);
                } else {
                    clearError($field);
                }
            });

            return valid;
        }

        function moveNextStep(e) {
                    e.preventDefault();

                    if (!validate()) {
                        return;
                    }

                    step++;

                    if (step === 1) {
                        $('.head-step-1').addClass('on');
                    }
                    if (step === 2) {
                        $('.head-step-2').addClass('on');
                    }
                    if (step === 3) {

                        $('.head-step-3').addClass('on');
                        $('.button.secondary').hide();
                        $('.button.secondary.hiden').addClass('on');
                    }

                    if (step >= maxStep) {
                        $('#ORDER_FORM').submit();
                        return;
                    }

                    showOnlyCurrentStepBox(step);
                }

        $(document).off('click', clsBtn, moveNextStep)
                    .on('click', clsBtn, moveNextStep);
    }());
</script>
<style>
    #order_form_div .form-item-inner {
        width: 100%;
    }
    .cls-order-box, .button.secondary.hiden {
        display: none;
    }
    .button.secondary.hiden.on {
        display: block !important;
    }
    .cls-order-show-box {
        display: block;
    }
</style>
