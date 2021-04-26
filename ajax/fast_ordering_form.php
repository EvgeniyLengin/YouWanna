<?php
/**
 * Загрузка и обновление формы "Быстрый заказ"
 */
define('NO_KEEP_STATISTIC', true);
define('STOP_STATISTICS', true);
define('NO_AGENT_STATISTIC', true);
define('NO_AGENT_CHECK', true);
define('NOT_CHECK_PERMISSIONS', true);
define('PERFMON_STOP', true);
define('LANGUAGE_ID', $_REQUEST['LANGUAGE_ID']);

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

global $APPLICATION;
$APPLICATION->IncludeComponent(
    'vis.center:form.ordering',
    '',
    array(
        'IS_AJAX'             => $_REQUEST['IS_AJAX'],
        'EVENT_TYPE'          => 'FORM_ORDERING',
        'LANGUAGE_ID'         => LANGUAGE_ID
    )
);
