<?php
/**
 * @var CMain $APPLICATION
 * @var array $arParams
 * @var array $arResult
 * @var array $templateData
 */

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

# Вывод языковых фраз
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

if ($arParams['SEARCH'] !== '' && $templateData['COUNT_ELEMENTS'] === 0) { ?>
    <p><?= Loc::getMessage('CT_BCS_TPL_ELEMENT_SEARCH_NONE'); ?></p>
<? }

// Ajax подгрузка товаров вместо пагинации
if ($templateData['AJAX_HTML']) {
    $APPLICATION->RestartBuffer();
    echo $templateData['AJAX_HTML'];
    die();
} ?>