<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

# для подключения языковых lang файлов в шаблоне
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

$arComponentParameters = array(
    'GROUPS'     => array(),
    'PARAMETERS' => array(
        'EVENT_TYPE'          => array(
            'PARENT'  => 'BASE',
            'NAME'    => Loc::getMessage('EVENT_TYPE'),
            'TYPE'    => 'STRING',
            'DEFAULT' => '',
        ),
    ),
);
?>