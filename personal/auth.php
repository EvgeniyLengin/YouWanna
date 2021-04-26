<?php
/**
 * Страница регистрации
 *
 * Created by PhpStorm.
 *
 * Date: 03.10.2016
 * Time: 20:14
 *
 * @var CMain $APPLICATION
 */

if (isset($_REQUEST['lang'])) {
    define('LANGUAGE_ID', $_REQUEST['lang']);
}

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php'; ?>
<div id="callback-modal-wrapper">
    <? $APPLICATION->IncludeComponent(
        "bitrix:system.auth.form",
        "youwanna.auth",
        Array(
            "SHOW_ERRORS"              => "Y"
        ),
        false
    ); ?>
</div>
