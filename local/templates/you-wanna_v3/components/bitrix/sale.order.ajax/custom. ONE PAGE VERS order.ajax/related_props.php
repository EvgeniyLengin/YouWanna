<?php
/**
 * @var array  $arParams
 * @var array  $arResult
 * @var string $templateFolder
 */
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}
/** @noinspection PhpIncludeInspection */
include $_SERVER['DOCUMENT_ROOT'] . $templateFolder . '/props_format.php';

$style = (is_array($arResult['ORDER_PROP']['RELATED']) && count($arResult['ORDER_PROP']['RELATED'])) ? '' : 'display:none';
?><div class="bx_section"
     style="<?= $style ?>">
    <?php PrintPropsForm($arResult['ORDER_PROP']['RELATED'], $arParams['TEMPLATE_LOCATION'],
        $arParams['HIDE_DELIVERY_PROPERTIES'], $arParams['HOME_ICON'], $arResult['ERROR']) ?>
</div>