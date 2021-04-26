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
    <?php $APPLICATION->IncludeComponent(
        'bitrix:main.register',
        '',
        array(
            'USER_PROPERTY_NAME'     => '',
            'SEF_MODE'               => 'N',
            'SHOW_FIELDS'            => array('PERSONAL_PHONE'),
            'REQUIRED_FIELDS'        => array('PERSONAL_PHONE'),
            'AUTH'                   => 'Y',
            'USE_BACKURL'            => 'N',
            'SUCCESS_PAGE'           => '',
            'SET_TITLE'              => 'Y',
            'USER_PROPERTY'          => array(),
            'SEF_FOLDER'             => '/',
            'VARIABLE_ALIASES'       => array(),
            'LANGUAGE_ID'            => LANGUAGE_ID,
            'PERSONAL_URL'           => '/personal/profile/',
            'AUTH_URL'               => '/personal/auth/',
            "AJAX_MODE"              => "Y",
            "AJAX_OPTION_ADDITIONAL" => "",
            "AJAX_OPTION_HISTORY"    => "N",
            "AJAX_OPTION_JUMP"       => "N",
            "AJAX_OPTION_STYLE"      => "Y",
        )
    ); ?>
</div>