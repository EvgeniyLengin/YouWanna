<?
/**
 * @var array $arResult
 * @var array $arParams
 */
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

# для подключения языковых lang файлов в шаблоне
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Security\Random;

Loc::loadMessages(__FILE__);

$arResult = [];

global $USER;

if (CModule::IncludeModule('sale')) {
    $arResult['DISCOUNT_SUM'] = 0;
    $arResult['TOTAL_SUM'] = 0;
    $arResult['ITEMS'] = [];

    $arBasketItems = [];
    $dbBasketItems = CSaleBasket::GetList(
        [
            'NAME' => 'ASC',
            'ID'   => 'ASC'
        ],
        [
            'FUSER_ID' => CSaleBasket::GetBasketUserID(),
            'LID'      => SITE_ID,
            'ORDER_ID' => 'NULL',
            'CAN_BUY'  => 'Y'
        ],
        false,
        false,
        [/*
            'ID',
            'PRODUCT_ID',
            'NAME',
            'DETAIL_PAGE_URL',
            'QUANTITY',
            'CAN_BUY',
            'PRICE',
            'MEASURE_NAME'
        */]
    );
    while ($arBasketItem = $dbBasketItems->Fetch()) {
        $arBasketItems[] = $arBasketItem;
    }

    $arOrder = [
        'SITE_ID'      => SITE_ID,
        'USER_ID'      => $USER->GetID(),
        'BASKET_ITEMS' => $arBasketItems
    ];
    $arOptions = [];
    $arErrors = [];
    // Обновляем цены в корзине, с учетом скидок
    CSaleDiscount::DoProcessOrder($arOrder, $arOptions, $arErrors);
    $arBasketItems = $arOrder['BASKET_ITEMS'];

    foreach($arBasketItems as $arItem) {
        $arResult['DISCOUNT_SUM'] += (int)$arItem['QUANTITY'] * (int)$arItem['DISCOUNT_PRICE'];
        $arResult['TOTAL_SUM'] += (int)$arItem['QUANTITY'] * (int)$arItem['PRICE'];
        $arResult['ITEMS'][] = $arItem;
    }

    if ($arResult['DISCOUNT_SUM'] > 0) {
        $arResult['DISCOUNT_SUM'] = $arResult['TOTAL_SUM'] + $arResult['DISCOUNT_SUM'];
    }

}

if ($arParams['IS_AJAX'] !== 'Y') {

    $this->IncludeComponentTemplate();

} else if (0 !== count($_REQUEST)) {

    $arJsonResult = [
        'STATUS' => 0,
        'RELOAD' => 0,
        'MSG'    => false,
        'ERRORS' => [],
    ];

    $arEventFields = [];
    $arJsonResult['REQUEST'] = $_REQUEST;
    $validateEmail = false;

    foreach ($_REQUEST as $code => $value) {

        if ($code === 'FIO') {

            if (empty($value)) {
                $arJsonResult['ERRORS'][$code] = Loc::getMessage('ERROR_FIO');
            } else {
                $arEventFields[$code] = $value;
            }

        } else if ($code === 'PHONE') {

            if (empty($value)) {
                $arJsonResult['ERRORS'][$code] = Loc::getMessage('ERROR_PHONE');
            } else {
                $arEventFields[$code] = $value;
            }

        } else if ($code === 'EMAIL') {

            $validateEmail = filter_var($value, FILTER_VALIDATE_EMAIL);
            if (empty($value)) {
				$arJsonResult['ERRORS'][$code] = Loc::getMessage('ERROR_EMAIL');
            } else {
                if (!$validateEmail) {
                    $arJsonResult['ERRORS'][$code] = Loc::getMessage('ERROR_EMAIL');
                } else {
                    $arEventFields[$code] = $value;
                }
			}

        }

    }

    if (check_bitrix_sessid()) {
        if (empty($arJsonResult['ERRORS']) && $arParams['EVENT_TYPE']) {

            if (!$USER->IsAuthorized()) {
                $clearPhone = preg_replace('~\D+~', '', $arEventFields['PHONE']);
                $userId = 0;
                $userBy = 'ID';
                $userOrder = 'asc';

                $rsUsers = CUser::GetList(
                    $userBy,
                    $userOrder,
                    [
                        'LOGIN' => $clearPhone,
                    ],
                    [
                        'FIELDS' => [
                            'ID',
                        ]
                    ]
                );
                if ($arUser = $rsUsers->Fetch()) {
                    $userId = $arUser['ID'];
                } else {
                    $password = Random::getStringByAlphabet(6, Random::ALPHABET_NUM);

                    $user = new CUser;
                    $arFields = [
                        'NAME'              => $arEventFields['FIO'],
                        'LAST_NAME'         => '',
                        'EMAIL'             => $arEventFields['EMAIL'],
                        'LOGIN'             => $clearPhone,
                        'LID'               => 's1',
                        'ACTIVE'            => 'Y',
                        'GROUP_ID'          => [2, 3, 4],
                        'PASSWORD'          => $password,
                        'CONFIRM_PASSWORD'  => $password,
                        'PERSONAL_PHONE'    => $arEventFields['PHONE']
                    ];

                    $userId = $user->Add($arFields);

                    //if ((int)$userId > 0) {
                    //    $smsText = 'Ваш пароль для входа на сайт - ' . $password;

                        /*if (CModule::IncludeModule('imaginweb.sms')) {
                            CIWebSMS::Send($clearPhone, $smsText);
                        }*/

                        /*\Bitrix\Main\Loader::includeModule('iqsms.sender');
                        $oManager = \Iqsms\Sender\Manager::getInstance();
                        $result = $oManager->send($clearPhone, $smsText);*/

                        /* Раскомментировать для включения отправки смс */
                        /*$oManager->sendTemplate('SMS_CONFIRM', array(
                            'PHONE' => $clearPhone,
                            'SMS_CODE' => $_SESSION['SALE_ACTIONS']['SMS_CODE']
                        ));*/
                    //}
                }
            } else {
                $userId = $USER->GetID();
            }

            if ((int)$userId > 0) {

                // print_r($arResult['ITEMS']['0']['PRODUCT_ID']);

                // Узнаем айди торогового предложения.
                $currentPredID = $arResult['ITEMS']['0']['PRODUCT_ID'];
                // Получаем айди товара, в который входит торг. предложение по айдишнику торогового предложения.
                $CurrentPosMassID = CCatalogSKU::GetProductInfo($currentPredID);
                // Айди товара
                $CurrentPosID = $CurrentPosMassID['ID'];
                //Получаем свойства этого товара.
                $propCurrentPos =CIBlockElement::GetByID($CurrentPosID)->GetNextElement()->GetProperties();
                // Получаем значение "предзаказа"
                $ComingSoonCheck = $propCurrentPos['COMING_SON']['VALUE'];
                // Если флаг "предзаказа" установлен "да" - вызываем модифицированную функцию быстрого оформления. Если нет - обычную.
                if ($ComingSoonCheck == "Да" or $ComingSoonCheck == "ДА") {

                    $arOrder = CYouWanna::checkoutCurrentUserOrderForFastOrderingEvgCustom(
                        $userId,
                        $arEventFields['FIO'],
                        $arEventFields['PHONE'],
                        $arEventFields['EMAIL']
                    );

                } else {
                    $arOrder = CYouWanna::checkoutCurrentUserOrderForFastOrdering(
                        $userId,
                        $arEventFields['FIO'],
                        $arEventFields['PHONE'],
                        $arEventFields['EMAIL']
                    );
                }


                $arJsonResult['ORDER_MESSAGE'] = $arOrder['MESSAGE'];

                if ((int)$arOrder['ORDER_ID'] > 1) {
                    $arJsonResult['STATUS'] = 1;
                    $arJsonResult['MSG_ERROR'] = Loc::getMessage('SUCCESS_MSG');

                    if (!empty($arEventFields['EMAIL'])) {
                        //CEvent::Send($arParams['EVENT_TYPE'], SITE_ID, $arEventFields, 'N');
                    }
                } else {
                    $arJsonResult['MSG_ERROR'] = Loc::getMessage('ORDER_ERROR');
                }

            } else {
                $arJsonResult['MSG_ERROR'] = Loc::getMessage('USER_ERROR');
            }
        }
    } else {
        $arJsonResult['MSG_ERROR'] = Loc::getMessage('MSG_ERROR');
    }

    exit(json_encode($arJsonResult));
}
