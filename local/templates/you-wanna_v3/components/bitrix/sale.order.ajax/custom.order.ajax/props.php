<?php
/**
 * @var array  $arResult
 * @var array  $arParams
 * @var string $templateFolder
 * @var CMain  $APPLICATION
 * @var CUser  $USER
 */
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}
/** @noinspection PhpIncludeInspection */
include $_SERVER['DOCUMENT_ROOT'] . $templateFolder . '/props_format.php';
?>

<div class="form__section cls-order-box cls-order-show-box" id="sale_order_props" data-step=1>

    <div class="form__section-title">
        <div class="row">
            <div class="col-sm-4">
                <?= GetMessage('SOA_TEMPL_PROP_INFO') ?>
              
            </div>
            <div class="col-sm-6">
                <?
                    global $USER;

                    if (!$USER->IsAuthorized()) :
                         echo GetMessage('SOA_TEMPL_PROP_PERSONAL_ENTER');
                    endif;
                ?>
            </div>
        </div>

    </div>


    <?php
    PrintPropsForm(
        (array)$arResult['ORDER_PROP']['USER_PROPS_N'],
        $arParams['TEMPLATE_LOCATION'],
        null,
        null,
        $arResult['ERROR']
    );
    PrintPropsForm(
        (array)$arResult['ORDER_PROP']['USER_PROPS_Y'],
        $arParams['TEMPLATE_LOCATION'],
        null,
        null,
        $arResult['ERROR']
    );
    ?>
</div>
