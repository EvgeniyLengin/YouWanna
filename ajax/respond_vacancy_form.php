<?
/**
 * Загрузка формы отправки Вакансии
 */
define('NO_KEEP_STATISTIC', true);
define('STOP_STATISTICS', true);
define('NO_AGENT_STATISTIC', true);
define('NO_AGENT_CHECK', true);
define('NOT_CHECK_PERMISSIONS', true);
define('PERFMON_STOP', true);
define('LANGUAGE_ID', $_REQUEST['lang']);

/*echo stristr($_REQUEST['vacancyId'], '?', true);*/

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';
?>
<script type="text/javascript" src="/bitrix/js/main/core/core_ajax.js"></script>
<div id="respond-vacancy-wrapper">
    <? $APPLICATION->IncludeComponent(
        'bitrix:form.result.new',
        'vacancy.form',
        [
            'CACHE_TIME'             => 3600,
            'CACHE_TYPE'             => 'A',
            'CHAIN_ITEM_LINK'        => '',
            'CHAIN_ITEM_TEXT'        => '',
            'EDIT_URL'               => '',
            'IGNORE_CUSTOM_TEMPLATE' => 'N',
            'LIST_URL'               => '',
            'SEF_MODE'               => 'N',
            'SUCCESS_URL'            => '',
            'USE_EXTENDED_ERRORS'    => 'N',
            'VARIABLE_ALIASES'       => [
                'RESULT_ID'   => 'RESULT_ID',
                'WEB_FORM_ID' => 'WEB_FORM_ID'
            ],
            'WEB_FORM_ID'            => 1,
            'AJAX_MODE'              => 'Y',
            'AJAX_OPTION_ADDITIONAL' => 'undefined',
            'AJAX_OPTION_HISTORY'    => 'N',
            'AJAX_OPTION_JUMP'       => 'N',
            'AJAX_OPTION_STYLE'      => 'Y',
            'VACANCY_NAME'           => urldecode($_REQUEST['vacancy_name']),
            'LANGUAGE_ID'            => LANGUAGE_ID
        ]
    ); ?>
</div>
<?/*$APPLICATION->IncludeComponent(
    "vis.center:main.feedback",
    "",
    Array(
        "COMPONENT_TEMPLATE" => ".default",
        "EMAIL_TO" => "andrei.yezhov@gmail.com",
        "EVENT_NAME" => "MAIN_FEEDBACK_FORM",
        "EVENT_MESSAGE_ID" => array("62"),
        "USER_EVENT_MESSAGE_ID" => array("64"),
        "USER_EVENT_MESSAGE_TRUE" => "Y",
        "THEME_TITLE" => "Заказать звонок",
        "MULTIPLE_LIST_TITLE" => "",
        "MULTIPLE_LIST" => "",
        "OK_TEXT" => "Спасибо, ваш запрос принят.",
        "DISPLAY_PHONE" => "Y",
        "REQUIRED_FIELDS" => array("NAME", "PHONE"),
        "USE_CAPTCHA" => "Y",
        "AJAX_OPTION_JUMP" => "N",
        "AJAX_OPTION_STYLE" => "Y",
        "AJAX_OPTION_HISTORY" => "N",
        "AJAX_OPTION_ADDITIONAL" => "undefined",
        "AJAX_MODE" => "Y",
        "SEND_FILE" => "N",
        "SEND_FILE_TITLE" => "",
        "FORM_TITLE" => $_REQUEST['vacancy_name'],
        "SUBMIT_TEXT" => "Отправить",
        "ELEMENT_NAME" => ""
    )
);*/?>