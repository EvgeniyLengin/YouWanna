<?php

use Bitrix;
use Bitrix\Main;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\Data\Cache;
use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;
use Bitrix\Main\SystemException;
use Bitrix\Main\Context;
use Bitrix\Currency\CurrencyManager;
use Bitrix\Sale;
use Bitrix\Sale\Order;
use Bitrix\Sale\Delivery;
use Bitrix\Sale\PaySystem;
use Bitrix\Main\Security\Random;

//use Bitrix\Sale\Basket;
//use Bitrix\Sale\DiscountCouponsManager;
use Bitrix\Sale\Internals\DiscountCouponTable;

//use Bitrix\Sale\Order;
//use Yandex\Translate\Translator;

/**
 * Class CYouWanna
 */
class CYouWanna
{

    /**
     * Ключ от dadata
     */
    const DADATA_TOKEN = 'dd46a436533991060be0fcd19801e40f4584c652';

    /**
     * Ключ от Яндекс.Перевода
     */
    const YANDEX_TRANSLATE_API_KEY = 'trnsl.1.1.20180328T125014Z.17b4c05cdea9b122.2e3a0406ee399ce1bcdfa8d658780473e2abf31e';

    /**
     *
     */
    const IBLOCK_TYPE_YOUWANNA = 'youwanna';

    /**
     *
     */
    const IBLOCK_TYPE_CATALOG = 'catalog';

    /**
     *
     */
    const PROPERTY_USER_ID = 'USER_ID';

    /**
     *
     */
    const TYPE_IBLOCK_PERSONAL = 'personal';

    /**
     * Языковые фразы
     */
    const IBLOCK_CODE_LANGUAGE_TEXT = 'language-text';

    /**
     * Вакансии
     */
    const IBLOCK_CODE_VACANCIES = 'vacancies';

    /**
     * Вопросы и ответы
     */
    const IBLOCK_CODE_FAQ = 'faq';


    /**
     * Каталог
     */
    const IBLOCK_CODE_CATALOG = 'catalog';

    /**
     * Каталог
     */
    const IBLOCK_ID_CATALOG = 3;


    /**
     * Адреса магазинов
     */
    const IBLOCK_STORE_CODE = 'retail-shops';

    /**
     * Баннеры в шапке
     */
    const IBLOCK_BANNERS_ON_PAGE = 'banners-on-page';

    /**
     * Получить идентификатор инфоблока по его символьному коду
     *
     * @param string $code Символьный код инфоблока
     *
     * @return int Идентификатор инфоблока
     *
     * @throws LoaderException
     * @throws ArgumentException
     */
    final public static function getIblockIdByCode($code)
    {
        if (null === $code || '' === trim($code)) {
            throw new ArgumentException('Empty iblock code');
        }

        $result = 0;
        $cache = Cache::createInstance();

        if ($cache->initCache(PHP_INT_MAX, __FILE__ . __LINE__ . $code, 'tmp')) {
            $result = $cache->getVars();
        } elseif ($cache->startDataCache()) {
            Loader::includeModule('iblock');

            $dbRes = CIBlock::GetList(
                ['SORT' => 'ASC'],
                ['CODE' => $code, 'CHECK_PERMISSIONS' => 'N']
            );

            $result = $dbRes->Fetch();

            $result = (int)$result['ID'];


            if ($result === 0) {
                $cache->abortDataCache();
            }

            $cache->endDataCache($result);
        }

        return $result;
    }


    /**
     * Получить код родительского раздела инфоблока по коду текущего раздела
     *
     * @param string $code Символьный код раздела
     *
     * @return int Символьный код родительского раздела
     *
     * @throws LoaderException
     * @throws ArgumentException
     */
    final public static function getParentSectionCodeByCode($code)
    {
        if (null === $code || '' === trim($code)) {
            return '';
        }

        $result = '';
        $parentSectId = 0;
        $cache = Cache::createInstance();

        if ($cache->initCache(PHP_INT_MAX, __FILE__ . __LINE__ . $code, 'tmp')) {
            $result = $cache->getVars();
        } elseif ($cache->startDataCache()) {
            Loader::includeModule('iblock');

            $rsSections = CIBlockSection::GetList(
                array(),
                array(
                    'IBLOCK_CODE' => CYouWanna::IBLOCK_TYPE_CATALOG,
                    'CODE'        => $code
                ),
                false
            );
            if ($arSection = $rsSections->GetNext()) {
                if ($arSection['DEPTH_LEVEL'] > 1) {
                    $parentSectId = $arSection['IBLOCK_SECTION_ID'];
                }
            }
            if ((int)$parentSectId > 0) {
                $res = CIBlockSection::GetByID($parentSectId);
                if ($ar_res = $res->GetNext()) {
                    $result = $ar_res['CODE'];
                }
            } else {
                $result = $code;
            }

            if ($result === '') {
                $cache->abortDataCache();
            }

            $cache->endDataCache($result);
        }

        return $result;
    }


    /**
     * Добавление артикула в символьный код товара, если его там ещё нет
     * Так же переводит и добавляет Английское название для поиска
     *
     * @param array  $arFields
     * @param string $checkCode
     * @param string $setActive
     *
     * @return string
     * @throws \Bitrix\Main\LoaderException
     */
    public static function setArticleFromElementCode($arFields = array(), $checkCode = 'N', $setActive = 'Y')
    {
        if (!Loader::includeModule('iblock')) {
            throw new LoaderException('Module iblock not found');
        }

        if ($arFields['IBLOCK_ID'] === (int)self::getIBlockIdByCode(self::IBLOCK_CODE_CATALOG) ||
            !$arFields['IBLOCK_ID']) {

            //$elementCount = 0;

            $arFilter = [
                'IBLOCK_ID'              => self::getIBlockIdByCode(self::IBLOCK_CODE_CATALOG),
                '!PROPERTY_CML2_ARTICLE' => false
            ];

            if ($arFields['ID']) {
                $arFilter['ID'] = $arFields['ID'];
            }
            if ($checkCode === 'Y') {
                $arFilter['CODE'] = false;
            }
            if ($setActive === 'Y') {
                $arFilter['ACTIVE'] = 'Y';
            } else {
                $arFilter['ACTIVE'] = 'N';
            }

            /** @noinspection PhpDynamicAsStaticMethodCallInspection */
            $list = \CIBlockElement::GetList(
                [
                    'ID' => 'DESC'
                ],
                $arFilter,
                false,
                false,
                [
                    'ID',
                    'IBLOCK_ID',
                    'NAME',
                    'CODE',
                    'PROPERTY_NAME_EN',
                    'PROPERTY_CML2_ARTICLE',
                    'XML_ID'
                ]
            );

            $arTranslitParams = [
                'max_len'               => 255,
                'change_case'           => 'N',
                'replace_space'         => '-',
                'replace_other'         => '-',
                'delete_repeat_replace' => true
            ];

            while ($temp = $list->GetNext()) {

                $newNewCodeVen = str_replace([' ', '(', ')', '-'], '', $temp['PROPERTY_CML2_ARTICLE_VALUE']);

                $el = new CIBlockElement;

                /*if (mb_stripos($temp['CODE'], trim($newNewCodeVen)) === false) {*/

                $newName = $temp['NAME'] . '-' . $newNewCodeVen;

                $newCode = CUtil::translit(strtolower($newName), 'ru', $arTranslitParams);
                $newCodeVen = CUtil::translit($newNewCodeVen, 'ru', $arTranslitParams);

                $PROP = [];
                if (empty($temp['PROPERTY_NAME_EN_VALUE'])) {
                    $PROP['NAME_EN'] = self::multiTranslate($temp['NAME'], 'en');
                }
                $PROP['CML2_ARTICLE'] = $newCodeVen;

                $arProductArray = [
                    'CODE'            => $newCode,
                    'XML_ID'          => $newCodeVen,
                    'PROPERTY_VALUES' => $PROP,
                ];
                $res = $el->Update($temp['ID'], $arProductArray);

                //echo 'ID обновленного товара ' . $temp['ID'] . '<br>';

                //$elementCount++;

                /*}*/

            }

            //echo 'Обновлено элементов ' . $elementCount;

        }

        return 'setArticleFromElementCode();';

    }


    /**
     * Получить данные для переменной SMART_FILTER_PATH из адреса к списку товаров каталога
     *
     * @param $catalogPath
     *
     * @return bool|string
     */
    public static function smartFilterPath($catalogPath)
    {
        $array = explode('/', $catalogPath);
        $filterKey = array_search('filter', $array, true);
        $applyKey = array_search('apply', $array, true);

        if ($filterKey) {
            $str = '';
            for ($i = $filterKey + 1; $i < $applyKey; $i++) {
                $str .= $array[$i] . '/';
            }

            $str = rtrim($str, '/');

            return $str;
        }

        return false;
    }

    /**
     * Реализует запись свойства минимальной стоимости товара при любом изменении торгового предложения
     * или товара.
     * Метод нужен для реализации сортировки товаров по стоимости
     * Метод вызывается из обработчиков
     *
     * @param array $arg1
     * @param bool  $arg2
     */
    public static function doIBlockAfterSave($arg1, $arg2 = false)
    {
        $ELEMENT_ID = false;
        $IBLOCK_ID = false;
        $OFFERS_IBLOCK_ID = false;
        $OFFERS_PROPERTY_ID = false;
        $strDefaultCurrency = null;

        if (CModule::IncludeModule('currency')) {
            $strDefaultCurrency = CCurrency::GetBaseCurrency();
        }

        //Check for catalog event
        /** @noinspection CallableParameterUseCaseInTypeContextInspection */
        /** @noinspection OffsetOperationsInspection */
        if (is_array($arg2) && $arg2['PRODUCT_ID'] > 0) {
            //Get iblock element
            /** @noinspection PhpDynamicAsStaticMethodCallInspection */
            /** @noinspection OffsetOperationsInspection */
            $rsPriceElement = CIBlockElement::GetList(
                [],
                [
                    'ID' => $arg2['PRODUCT_ID']
                ],
                false,
                false,
                ['ID', 'IBLOCK_ID']
            );
            /** @noinspection PhpAssignmentInConditionInspection */
            if ($arPriceElement = $rsPriceElement->Fetch()) {
                $arCatalog = CCatalog::GetByID($arPriceElement['IBLOCK_ID']);
                if (is_array($arCatalog)) {
                    //Check if it is offers iblock
                    if ($arCatalog['OFFERS'] === 'Y') {
                        //Find product element
                        $rsElement = CIBlockElement::GetProperty(
                            $arPriceElement['IBLOCK_ID'],
                            $arPriceElement['ID'],
                            'sort',
                            'asc',
                            ['ID' => $arCatalog['SKU_PROPERTY_ID']]
                        );
                        $arElement = $rsElement->Fetch();
                        if ($arElement && $arElement['VALUE'] > 0) {
                            $ELEMENT_ID = $arElement['VALUE'];
                            $IBLOCK_ID = $arCatalog['PRODUCT_IBLOCK_ID'];
                            $OFFERS_IBLOCK_ID = $arCatalog['IBLOCK_ID'];
                            $OFFERS_PROPERTY_ID = $arCatalog['SKU_PROPERTY_ID'];
                        }
                    } //or iblock which has offers
                    elseif ($arCatalog['OFFERS_IBLOCK_ID'] > 0) {
                        $ELEMENT_ID = $arPriceElement['ID'];
                        $IBLOCK_ID = $arPriceElement['IBLOCK_ID'];
                        $OFFERS_IBLOCK_ID = $arCatalog['OFFERS_IBLOCK_ID'];
                        $OFFERS_PROPERTY_ID = $arCatalog['OFFERS_PROPERTY_ID'];
                    } //or it's regular catalog
                    else {
                        $ELEMENT_ID = $arPriceElement['ID'];
                        $IBLOCK_ID = $arPriceElement['IBLOCK_ID'];
                        $OFFERS_IBLOCK_ID = false;
                        $OFFERS_PROPERTY_ID = false;
                    }
                }
            }
        } //Check for iblock event
        elseif (is_array($arg1) && $arg1['ID'] > 0 && $arg1['IBLOCK_ID'] > 0) {
            //Check if iblock has offers
            /** @var array $arOffers */
            $arOffers = CIBlockPriceTools::GetOffersIBlock($arg1['IBLOCK_ID']);
            if (is_array($arOffers)) {
                $ELEMENT_ID = $arg1['ID'];
                $IBLOCK_ID = $arg1['IBLOCK_ID'];
                $OFFERS_IBLOCK_ID = $arOffers['OFFERS_IBLOCK_ID'];
                $OFFERS_PROPERTY_ID = $arOffers['OFFERS_PROPERTY_ID'];
            }
        }

        if ($ELEMENT_ID) {
            static $arPropCache = [];
            if (!array_key_exists($IBLOCK_ID, $arPropCache)) {
                //Check for MINIMAL_PRICE property
                $rsProperty = CIBlockProperty::GetByID('MINIMUM_PRICE', $IBLOCK_ID);
                $arProperty = $rsProperty->Fetch();
                if ($arProperty) {
                    /** @noinspection OffsetOperationsInspection */
                    $arPropCache[$IBLOCK_ID] = $arProperty['ID'];
                } else {
                    /** @noinspection OffsetOperationsInspection */
                    $arPropCache[$IBLOCK_ID] = false;
                }
            }

            /** @noinspection OffsetOperationsInspection */
            if ($arPropCache[$IBLOCK_ID]) {
                //Compose elements filter
                if ($OFFERS_IBLOCK_ID) {
                    /** @noinspection PhpDynamicAsStaticMethodCallInspection */
                    $rsOffers = CIBlockElement::GetList(
                        [],
                        [
                            'IBLOCK_ID'                       => $OFFERS_IBLOCK_ID,
                            'PROPERTY_' . $OFFERS_PROPERTY_ID => $ELEMENT_ID
                        ],
                        false,
                        false,
                        ['ID']
                    );
                    $arProductID = null;
                    /** @noinspection PhpAssignmentInConditionInspection */
                    while ($arOffer = $rsOffers->Fetch()) {
                        $arProductID[] = $arOffer['ID'];
                    }

                    if (!is_array($arProductID)) {
                        $arProductID = [$ELEMENT_ID];
                    }
                } else {
                    $arProductID = [$ELEMENT_ID];
                }

                $minPrice = false;
                //Get prices
                $rsPrices = CPrice::GetList(
                    [],
                    [
                        '!CATALOG_GROUP_ID' => 8, // Исключаем ID типа цены для Сотрудников
                        'PRODUCT_ID'        => $arProductID
                    ]
                );
                /** @noinspection PhpAssignmentInConditionInspection */
                while ($arPrice = $rsPrices->Fetch()) {
                    if ($strDefaultCurrency !== $arPrice['CURRENCY'] && CModule::IncludeModule('currency')) {
                        $arPrice['PRICE'] = CCurrencyRates::ConvertCurrency(
                            $arPrice['PRICE'],
                            $arPrice['CURRENCY'],
                            $strDefaultCurrency
                        );
                    }

                    $PRICE = $arPrice['PRICE'];

                    if ($minPrice === false || $minPrice > $PRICE) {
                        $minPrice = $PRICE;
                    }
                }

                //Save found minimal price into property
                if ($minPrice !== false) {
                    CIBlockElement::SetPropertyValuesEx(
                        $ELEMENT_ID,
                        $IBLOCK_ID,
                        [
                            'MINIMUM_PRICE' => $minPrice
                        ]
                    );
                }
            }
        }
    }


    /**
     * Получаем изображения для слайдера, разбитые по цветам SKU
     *
     * @param array  $arOffers      - Массив торговых предложений
     * @param array  $arImages      - Массив с дополнительными фото
     * @param array  $detailPicture - Массив с главным фото
     * @param string $propertyCode  - Код множественного свойства фото
     *
     * @return array
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\SystemException
     */
    public static function getSliderForOffer(
        $arOffers = [],
        $arImages = [],
        $detailPicture = [],
        $propertyCode = 'COLOR'
    ) {
        $reqParams = [];
        $arColors = [];

        if (0 !== count($arOffers)) {

            $itemProp = reset($arOffers)['PROPERTIES'][$propertyCode];

            $hlBlock = \Bitrix\Highloadblock\HighloadBlockTable::getList(
                ['filter' => ['=TABLE_NAME' => $itemProp['USER_TYPE_SETTINGS']['TABLE_NAME']]]
            )->fetch();

            if ($hlBlock['ID']) {
                $reqParams['HLB'] = $hlBlock;

                foreach ($arOffers as $arOffer) {
                    if ($arOffer['PROPERTIES'][$propertyCode]['VALUE']) {
                        $reqParams['VALUES'][$arOffer['PROPERTIES'][$propertyCode]['VALUE']] =
                            $arOffer['PROPERTIES'][$propertyCode]['VALUE'];
                    }
                }

                $entity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($reqParams['HLB']);
                $entityDataClass = $entity->getDataClass();
                $fieldsList = $entityDataClass::getMap();

                if (isset($fieldsList['ID'])) {
                    $fieldsList = $entityDataClass::getEntity()->getFields();
                }

                $directoryOrder = [];
                if (isset($fieldsList['UF_COLOR_SORT'])) {
                    $directoryOrder['UF_COLOR_SORT'] = 'ASC';
                }
                $directoryOrder['ID'] = 'ASC';

                $arFilter = [
                    'order' => $directoryOrder,
                    'limit' => 100
                ];
                if (!empty($reqParams['VALUES'])) {
                    $arFilter['filter'] = array(
                        '=UF_XML_ID' => $reqParams['VALUES'],
                    );
                }

                $rsPropEnums = $entityDataClass::getList($arFilter);
                while ($arEnum = $rsPropEnums->fetch()) {
                    if (!array_key_exists($arEnum['UF_XML_ID'], $arColors)) {
                        $arColors[$arEnum['UF_XML_ID']]['COLOR_NAME'] = $arEnum['UF_NAME'];
                        $arColors[$arEnum['UF_XML_ID']]['SKU_IMAGE'] = CFile::GetFileArray($arEnum['UF_COLOR_FILE']);
                        $arColors[$arEnum['UF_XML_ID']]['SKU_HEX'] = $arEnum['UF_HEX'];
                    }
                }

                if (0 !== count($arColors) && 0 !== count($arImages)) {
                    foreach ($arImages as $onePhoto) {
                        $onePhotoArray = CFile::GetFileArray($onePhoto);
                        preg_match('/\{([^{}]*)\}/', $onePhotoArray['ORIGINAL_NAME'], $name);
                        if (in_array($name[1], array_keys($arColors))) {
                            $arColors[$name[1]]['PICTURES'][] = [
                                'ORIGINAL_NAME' => $onePhotoArray['ORIGINAL_NAME'],
                                'ID'            => (int)$onePhotoArray['ID'],
                                'SRC'           => $onePhotoArray['SRC'],
                                'WIDTH'         => (int)$onePhotoArray['WIDTH'],
                                'HEIGHT'        => (int)$onePhotoArray['HEIGHT']
                            ];
                        }
                    }

                    foreach ($arColors as &$arColorSort) {
                        // Сортируем фото по названию
                        uasort($arColorSort['PICTURES'], function ($a, $b) {
                            if (strnatcasecmp($a['ORIGINAL_NAME'], $b['ORIGINAL_NAME']) == 0) {
                                return 0;
                            }
                            return strnatcasecmp($a['ORIGINAL_NAME'], $b['ORIGINAL_NAME']) > 0 ? 1 : -1;
                        });
                    }
                    unset($arColorSort);

                    if (0 !== count($detailPicture)) {
                        foreach ($arColors as &$arColor) {
                            if (0 === count($arColor['PICTURES'])) {
                                $arColor['PICTURES'][] = [
                                    'ID'     => (int)$detailPicture['ID'],
                                    'SRC'    => $detailPicture['SRC'],
                                    'WIDTH'  => (int)$detailPicture['WIDTH'],
                                    'HEIGHT' => (int)$detailPicture['HEIGHT']
                                ];
                            }
                        }
                        unset($arColor);
                    }
                }

            }

        }

        return $arColors;
    }

    /**
     * Приведение номера телефона к единому формату вида 79999999999
     *
     * @param string $phone - телефон в произвольном формате
     *
     * @return string
     */
    public static function phoneStandartize($phone)
    {
        return preg_replace('!\D+!', '', $phone);
    }


    /**
     * Функция перевода, доработана с переводом на несколько языков
     *
     * @param        $word
     * @param string $lang
     * @param bool   $force
     * @param bool   $noCache
     *
     * @return mixed|string
     */
    public static function multiTranslate($word, $lang = 'ru', $force = false, $noCache = false)
    {
        if ($lang === 'ru' && !$force) {
            return $word;
        }
        /** @noinspection BadExceptionsProcessingInspection */
        try {
            $translatesWord = '';

            $cache = Bitrix\Main\Data\Cache::createInstance();

            if ($cache->initCache(31536000, $word . $lang, 'youwanna/lang') && !$noCache) {
                $translatesWord = $cache->getVars();
            }
			else{
				$translatesWord = $word;
			}

			/*elseif($noCache) {
                $translator = new Yandex\Translate\Translator(self::YANDEX_TRANSLATE_API_KEY);
                $temp = $translator->translate($word,
                    ($force && LANGUAGE_ID === 'ru') ? $lang . '-ru' : 'ru-' . $lang)->getResult();
                $translatesWord = reset($temp);
            } else {
                if ($cache->startDataCache()) {
                    $translator = new Yandex\Translate\Translator(self::YANDEX_TRANSLATE_API_KEY);
                    $temp = $translator->translate($word,
                        ($force && LANGUAGE_ID === 'ru') ? $lang . '-ru' : 'ru-' . $lang)->getResult();
                    $translatesWord = reset($temp);
                    $cache->endDataCache($translatesWord);
                }
            }*/

            return $translatesWord;
        } catch (Yandex\Translate\Exception $e) {
            return '';
        }
    }

    /**
     * Функция проверки наличия переведенного слова вручную
     *
     * @param        $word
     * @param        $word_eng
     * @param string $lang
     * @param bool   $force
     * @param bool   $noCache
     *
     * @return mixed|string
     */
    public static function multiTranslateAlt($word, $word_eng = false, $lang = 'ru', $force = false, $noCache = false)
    {

        if (!!$word_eng && trim($word_eng) != "" && $lang == 'en') {
            return $word_eng;
        } elseif ($lang == 'en') {
            return self::multiTranslate($word, $lang, $force, $noCache);
        } else {
            return $word;
        }
    }


    /**
     * Правим адрес в ссылках, согласно выбранного языка.
     * Если поставить между <a и href= что-либо, например, class - то замена не будет происходить.
     * Этим можно пользоваться.
     *
     * @param $content
     */
    public static function editHrefByLanguageForSite(&$content)
    {
        if (!defined('ADMIN_SECTION')) {
            if (LANGUAGE_ID !== 'ru') {
                $content = preg_replace(
                    '#(<a href=")(?!\/en\/)(?!\/upload\/)(?!\/bitrix\/)(?!\/local\/)(\/)#',
                    '$1/' . LANGUAGE_ID . '/', $content
                );
            }
        }
    }


    /**
     * Подписка пользователя на рассылку после Оформления заказа
     *
     * @param $orderId
     *
     * @throws \Bitrix\Main\ArgumentNullException
     */
    public static function subscribeUserFromOrder($orderId)
    {
        $rubId = 1; // Id рубрики рассылки

        /** int $orderId ID заказа */
        $order = Sale\Order::load($orderId);

        $propertyCollection = $order->getPropertyCollection();
        $arProps = $propertyCollection->getArray();

        foreach ($arProps['properties'] as $arProp) {

            if ($arProp['CODE'] === 'SUBSCRIBE') {
                $value = reset($arProp['VALUE']);
                if ($value === 'Y' && CModule::IncludeModule('subscribe')) {
                    $userEmail = $propertyCollection->getUserEmail()->getValue();
                    $arFields = Array(
                        'USER_ID'      => $order->getUserId(),
                        'FORMAT'       => 'html',
                        'EMAIL'        => $userEmail,
                        'ACTIVE'       => 'Y',
                        'RUB_ID'       => $rubId,
                        'CONFIRMED'    => 'Y',
                        'SEND_CONFIRM' => 'N',
                    );
                    $subscr = new CSubscription;

                    $ID = $subscr->Add($arFields);

                    if ($ID) {
                        // Запускаем генерацию купона на скидку и его отправку по почте
                        self::generateCoupon($orderId, $userEmail);
                    } else {
                        //echo '<pre style="text-align:left;">'; print_r($subscr->LAST_ERROR); echo '</pre>'; // TODO удалить pre
                    }

                }
                break;
            }

        }
    }


    /**
     * Генерируем купон за заказ и отправляем его на почту пользователя
     *
     * @param $orderId
     * @param $userEmail
     *
     * @throws \Exception
     */
    public static function generateCoupon($orderId, $userEmail)
    {
        # делаем выборку активных правил, у которых в названии есть команда по созданию купонов
        $dbActiveDiscounts = CSaleDiscount::GetList(
            [],
            [
                'ACTIVE' => 'Y',
                'NAME'   => 'SUBSCRIBE_CREATE_COUPON',
            ]
        );
        if ($arDiscount = $dbActiveDiscounts->Fetch()) {

            $coupon = DiscountCouponTable::generateCoupon(true);
            $arCoupon = [
                'DISCOUNT_ID' => $arDiscount['ID'],
                'TYPE'        => DiscountCouponTable::TYPE_ONE_ORDER,
                'COUPON'      => $coupon,
                'DESCRIPTION' => '[ORDER_ID=' . $orderId . ']'
            ];
            if ($arDiscount['ACTIVE_FROM']) {
                $arCoupon['ACTIVE_FROM'] = new Bitrix\Main\Type\DateTime($arDiscount['ACTIVE_FROM']);
            }
            if ($arDiscount['ACTIVE_TO']) {
                $arCoupon['ACTIVE_TO'] = new Bitrix\Main\Type\DateTime($arDiscount['ACTIVE_TO']);
            }

            $couponsResult = DiscountCouponTable::add($arCoupon);
            if ($couponsResult->isSuccess()) {
                $couponInfo = 'Ваш купон на следующую покупку: ' . $coupon;
                $managerText = 'Отправлен купон на правило ID ' . $arCoupon['DISCOUNT_ID'] . '. Код купона: ' . $coupon;
                $arEventFields = [
                    'MESSAGE'  => $couponInfo,
                    'EMAIL_TO' => $userEmail,
                ];
                CEvent::Send('SUBSCRIBE_CREATE_COUPON', 's1', $arEventFields);
            } else {
                $managerText = 'Не удалось создать купон на правило ID ' . $arCoupon['DISCOUNT_ID'];
            }

            if ($managerText !== '') {
                // Записываем в заказ информацию о выданном купоне
                \Bitrix\Sale\Internals\OrderTable::update(
                    $orderId,
                    [
                        'COMMENTS' => $managerText,
                    ]
                );
            }

        }

    }


    /**
     * Получаем предпочтительный язык пользователя
     *
     * @return string
     */
    public static function getUserLanguage()
    {
        $arLanguages = [];

        if ($list = strtolower($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            if (preg_match_all('/([a-z]{1,8}(?:-[a-z]{1,8})?)(?:;q=([0-9.]+))?/', $list, $list)) {
                $arLanguages = array_combine($list[1], $list[2]);
                foreach ($arLanguages as $n => $v) {
                    $arLanguages[$n] = $v ? $v : 1;
                }
                arsort($arLanguages, SORT_NUMERIC);
            }
        }

        return self::getBestLanguage($arLanguages);
    }


    /**
     * Определение приоритетного языка пользователя
     *
     * @param array $arLanguages
     *
     * @return string
     */
    public static function getBestLanguage($arLanguages = [])
    {
        $defaultLang = 'en';

        if (0 !== count($arLanguages)) {
            $arAliasesLang = [
                'ru' => ['ru', 'be', 'uk', 'ky', 'ab', 'mo', 'et', 'lv'],
                'en' => 'en'
            ];

            $arLang = [];
            foreach ($arAliasesLang as $lang => $alias) {
                if (is_array($alias)) {
                    foreach ($alias as $alias_lang) {
                        $arLang[strtolower($alias_lang)] = strtolower($lang);
                    }
                } else {
                    $arLang[strtolower($alias)] = strtolower($lang);
                }
            }

            foreach ($arLanguages as $lang => $priority) {
                $string = strtok($lang, '-'); // убираем то что идет после тире в языках вида "en-us, ru-ru"
                if (isset($arLang[$string])) {
                    $defaultLang = $arLang[$string];
                    break;
                }
            }
        }

        return $defaultLang;
    }


    /**
     * Устанавливаем Язык для пользователя и делаем редирект, если текущий язык не совпадает
     */
    public static function setUserLanguageFromUrl()
    {
        global $APPLICATION;
        $curUrl = str_replace('/' . LANGUAGE_ID . '/', '/', $APPLICATION->GetCurDir());

        if (!defined('ADMIN_SECTION')
            && false === strpos($curUrl, '/ajax/')
            && false === strpos($curUrl, '/bitrix/')
            && false === strpos($curUrl, '/local/')) {
            if (isset($_REQUEST['set_lang'])) {
                $lang = $_REQUEST['set_lang'];
            } else {
                $lang = CYouWannaCookie::getCookie('user_lang') ?: self::getUserLanguage();
            }

            if (!CYouWannaCookie::getCookie('user_lang') || isset($_REQUEST['set_lang'])) {
                CYouWannaCookie::setCookie('user_lang', $lang);
            }

            if (LANGUAGE_ID !== $lang) {
                if ($lang !== 'ru') {
                    LocalRedirect('/' . $lang . $curUrl);
                } else {
                    LocalRedirect($curUrl);
                }
            }
        }
    }


    /**
     * Запись телефона в логин пользователя при регистрации
     */
    public static function phoneNumberToEmail(&$arFields)
    {
        $arFields['LOGIN'] = preg_replace('~\D+~', '', $arFields['PERSONAL_PHONE']);
        if (trim($arFields['EMAIL']) === '') {
            $arFields['EMAIL'] = preg_replace('~\D+~', '', $arFields['PERSONAL_PHONE']) . '@you-wanna.ru';
        }
        return true;
    }

    /**
     * Запись телефона в логин пользователя при быстрой регистрации
     */
    public static function phoneNumberToLoginSimple(&$arFields)
    {
        $codeLength = 6;

        $newPass = Random::getStringByAlphabet($codeLength, Random::ALPHABET_NUM);
        $arFields['LOGIN'] = preg_replace('~\D+~', '', $arFields['PERSONAL_PHONE']);
        if ($arFields['UF_USER_REGISTER'] !== 'Y') {
            $arFields['PASSWORD'] = $newPass;
            $arFields['CONFIRM_PASSWORD'] = $newPass;
            $arFields['UF_PHONE_CONFIRM'] = 'Y';

            /*$phone = $arFields['PERSONAL_PHONE'];
            $text = 'Ваш логин для входа на сайт: '. $arFields['LOGIN'] . '. Пароль: ' . $newPass;

            mail($arFields['EMAIL'], 'Регистрационная информация с сайта YOU-WANNA', $text);

            \Bitrix\Main\Loader::includeModule('iqsms.sender');
            $oManager = \Iqsms\Sender\Manager::getInstance();*/

            /* Раскомментировать для включения отправки смс */
            /*$oManager->sendTemplate('SMS_NEW_PASS', array(
                'LOGIN'    => $arFields['LOGIN'],
                'PHONE'    => $phone,
                'NEW_PASS' => $newPass
            ));*/
        }


        /*$arEventFields = array(
            "PASSWORD" => $newPass,.
            'LOGIN'    => $arFields['LOGIN'],
            "EMAIL_TO" => $arFields['EMAIL']
        );

        if (CModule::IncludeModule("main")) {
            CEvent::Send("APP_SEND_PASSWORD", "s1", $arEventFields);
        };

        if (CModule::IncludeModule("imaginweb.sms")) {
            CIWebSMS::Send($phone, $text);
        }*/

        return true;
    }


    /**
     * Создание быстрого заказа из корзины текущего пользователя, с учетом скидок
     *
     * @param int    $userId
     * @param string $fio
     * @param string $phone
     * @param string $email
     *
     * @return array
     *
     * @throws \Bitrix\Main\ArgumentTypeException
     * @throws \Bitrix\Main\LoaderException
     * @throws \Bitrix\Main\ObjectNotFoundException
     * @throws \Bitrix\Main\SystemException
     */
    public static function checkoutCurrentUserOrderForFastOrdering($userId, $fio, $phone, $email = '')
    {
        $orderId = 0;
        $message = '';

        Loader::includeModule('sale');

        $fUser = Sale\Fuser::getId();
        $siteId = Context::getCurrent()->getSite();

        $arBasketItems = [];
        $dbBasketItems = CSaleBasket::GetList(
            [],
            [
                'FUSER_ID' => $fUser,
                'LID'      => $siteId,
                'CAN_BUY'  => 'Y',
                'ORDER_ID' => 'NULL'
            ],
            false,
            false,
            []
        );
        while ($arItem = $dbBasketItems->Fetch()) {
            $arBasketItems[] = $arItem;
        }

        if (0 !== count($arBasketItems)) {
            $arOrder = [
                'SITE_ID'      => $siteId,
                'USER_ID'      => $userId,
                'BASKET_ITEMS' => $arBasketItems
            ];
            $arOptions = [];
            $arErrors = [];
            // Обновляем цены в корзине, с учетом скидок
            // FIXED CRM AND BITRIX PRICE CSaleDiscount::DoProcessOrder($arOrder, $arOptions, $arErrors);
            $arBasketItems = $arOrder['BASKET_ITEMS'];
            $arPrices = [];
            foreach ($arBasketItems as $arItem) {
                $arPrices[$arItem['ID']] = $arItem['PRICE'];
            }

            $currencyCode = CurrencyManager::getBaseCurrency();

            // Создаём новый заказ
            $order = Order::create($siteId, $userId);
            $order->setPersonTypeId(1);
            $order->setField('CURRENCY', $currencyCode);

            // Получаем корзину

			/* FIXED CRM AND BITRIX PRICE
            $basket = Sale\Basket::loadItemsForFUser($fUser, $siteId);
            foreach ($basket as $basketItem) {
                $id = $basketItem->getId();
                if (isset($arPrices[$id])) {
                    $basketItem->setField('CUSTOM_PRICE', 'Y');
                    $basketItem->setField('PRICE', $arPrices[$id]);
                    $basketItem->save();
                }
            }*/

			\Bitrix\Sale\Compatible\DiscountCompatibility::stopUsageCompatible();

           // $order->setField('CURRENCY', $currencyCode);


			/**FIXED PRICE CRM AND BITRIX */
			$basket = Sale\Basket::loadItemsForFUser($fUser, $siteId);

			$basket->refreshData(array('PRICE', 'COUPONS'));

			$discounts = \Bitrix\Sale\Discount::buildFromBasket($basket, new \Bitrix\Sale\Discount\Context\Fuser($basket->getFUserId(true)));

			$discounts->calculate();

			$result = $discounts->getApplyResult(true);


            //$basket->refreshData(['PRICE']);
            $basket->save();

            // Сохраняем корзину в заказе
            $order->setBasket($basket);

            // Создаём отгрузки и устанавливаем способ доставки - "Без доставки" (он служебный)
            $shipmentCollection = $order->getShipmentCollection();
            $shipment = $shipmentCollection->createItem();
            $service = Delivery\Services\Manager::getById(Delivery\Services\EmptyDeliveryService::getEmptyDeliveryServiceId());
            $shipment->setFields(array(
                'DELIVERY_ID'   => $service['ID'],
                'DELIVERY_NAME' => $service['NAME'],
            ));
            $shipmentItemCollection = $shipment->getShipmentItemCollection();
            foreach ($basket as $basketItem) {
                $shipmentItem = $shipmentItemCollection->createItem($basketItem);
                $shipmentItem->setQuantity($basketItem->getQuantity());
            }

            // Создаём оплату со способом #2
            $paymentCollection = $order->getPaymentCollection();
            $payment = $paymentCollection->createItem();
            $paySystemService = PaySystem\Manager::getObjectById(2);
            $payment->setFields(array(
                'PAY_SYSTEM_ID'   => $paySystemService->getField('PAY_SYSTEM_ID'),
                'PAY_SYSTEM_NAME' => $paySystemService->getField('NAME'),
            ));

            // Устанавливаем свойства заказа
            $propertyCollection = $order->getPropertyCollection();
            $nameProp = $propertyCollection->getPayerName();
            $nameProp->setValue($fio);
            $phoneProp = $propertyCollection->getPhone();
            $phoneProp->setValue($phone);
            $emailProp = $propertyCollection->getUserEmail();
            $emailProp->setValue($email);
            $addressProp = $propertyCollection->getAddress();
            $addressProp->setValue('Быстрый заказ, без указания адреса');

            // Сохраняем заказ
            $order->doFinalAction(true);
            $result = $order->save();

            if ($result->isSuccess()) {
                $orderId = $order->getId();
                $message = 'Заказ успешно оформлен';
            }
        } else {
            $message = 'Корзина пуста';
        }

        return [
            'ORDER_ID' => $orderId,
            'MESSAGE'  => $message
        ];

    }



    public static function checkoutCurrentUserOrderForFastOrderingEvgCustom($userId, $fio, $phone, $email = '')
    {
        $orderId = 0;
        $message = '';

        Loader::includeModule('sale');

        $fUser = Sale\Fuser::getId();
        $siteId = Context::getCurrent()->getSite();

        $arBasketItems = [];
        $dbBasketItems = CSaleBasket::GetList(
            [],
            [
                'FUSER_ID' => $fUser,
                'LID'      => $siteId,
                'CAN_BUY'  => 'Y',
                'ORDER_ID' => 'NULL'
            ],
            false,
            false,
            []
        );
        while ($arItem = $dbBasketItems->Fetch()) {
            $arBasketItems[] = $arItem;
        }

        if (0 !== count($arBasketItems)) {
            $arOrder = [
                'SITE_ID'      => $siteId,
                'USER_ID'      => $userId,
                'BASKET_ITEMS' => $arBasketItems
            ];
            $arOptions = [];
            $arErrors = [];
            // Обновляем цены в корзине, с учетом скидок
            // FIXED CRM AND BITRIX PRICE CSaleDiscount::DoProcessOrder($arOrder, $arOptions, $arErrors);
            $arBasketItems = $arOrder['BASKET_ITEMS'];
            $arPrices = [];
            foreach ($arBasketItems as $arItem) {
                $arPrices[$arItem['ID']] = $arItem['PRICE'];
            }

            $currencyCode = CurrencyManager::getBaseCurrency();

            // Создаём новый заказ
            $order = Order::create($siteId, $userId);
            $order->setPersonTypeId(1);
            $order->setField('CURRENCY', $currencyCode);

            // Получаем корзину

            /* FIXED CRM AND BITRIX PRICE
            $basket = Sale\Basket::loadItemsForFUser($fUser, $siteId);
            foreach ($basket as $basketItem) {
                $id = $basketItem->getId();
                if (isset($arPrices[$id])) {
                    $basketItem->setField('CUSTOM_PRICE', 'Y');
                    $basketItem->setField('PRICE', $arPrices[$id]);
                    $basketItem->save();
                }
            }*/

            \Bitrix\Sale\Compatible\DiscountCompatibility::stopUsageCompatible();

           // $order->setField('CURRENCY', $currencyCode);


            /**FIXED PRICE CRM AND BITRIX */
            $basket = Sale\Basket::loadItemsForFUser($fUser, $siteId);

            $basket->refreshData(array('PRICE', 'COUPONS'));

            $discounts = \Bitrix\Sale\Discount::buildFromBasket($basket, new \Bitrix\Sale\Discount\Context\Fuser($basket->getFUserId(true)));

            $discounts->calculate();

            $result = $discounts->getApplyResult(true);


            //$basket->refreshData(['PRICE']);
            $basket->save();

            // Сохраняем корзину в заказе
            $order->setBasket($basket);

            // Создаём отгрузки и устанавливаем способ доставки - "Без доставки" (он служебный)
            $shipmentCollection = $order->getShipmentCollection();
            $shipment = $shipmentCollection->createItem();
            $service = Delivery\Services\Manager::getById(Delivery\Services\EmptyDeliveryService::getEmptyDeliveryServiceId());
            $shipment->setFields(array(
                'DELIVERY_ID'   => $service['ID'],
                'DELIVERY_NAME' => $service['NAME'],
            ));
            $shipmentItemCollection = $shipment->getShipmentItemCollection();
            foreach ($basket as $basketItem) {
                $shipmentItem = $shipmentItemCollection->createItem($basketItem);
                $shipmentItem->setQuantity($basketItem->getQuantity());
            }

            // Создаём оплату со способом #2
            $paymentCollection = $order->getPaymentCollection();
            $payment = $paymentCollection->createItem();
            $paySystemService = PaySystem\Manager::getObjectById(2);
            $payment->setFields(array(
                'PAY_SYSTEM_ID'   => $paySystemService->getField('PAY_SYSTEM_ID'),
                'PAY_SYSTEM_NAME' => $paySystemService->getField('NAME'),
            ));

            // Устанавливаем свойства заказа
            $propertyCollection = $order->getPropertyCollection();
            $nameProp = $propertyCollection->getPayerName();
            $nameProp->setValue($fio);
            $phoneProp = $propertyCollection->getPhone();
            $phoneProp->setValue($phone);
            $emailProp = $propertyCollection->getUserEmail();
            $emailProp->setValue($email);
            $addressProp = $propertyCollection->getAddress();
            $addressProp->setValue('Предзаказ товара. Адрес не указан.');

            // Сохраняем заказ
            $order->doFinalAction(true);
            $result = $order->save();

            if ($result->isSuccess()) {
                $orderId = $order->getId();
                $message = 'Заказ успешно оформлен';
            }
        } else {
            $message = 'Корзина пуста';
        }

        return [
            'ORDER_ID' => $orderId,
            'MESSAGE'  => $message
        ];

    }

    public static function getCoordNameByAddress($address)
    {
        // удаление лишних пробелов между словами
        $address = preg_replace("/ {2,}/", " ", $address);
        // замена пробелов на плюсы
        $address = str_replace(" ", "+", $address);
        // формируется урл для запроса
        $url_get_coord = "https://geocode-maps.yandex.ru/1.x/?geocode={$address}&format=json&results=1";
        $result = @file_get_contents($url_get_coord);
        // если произошла ошибка при отправке запроса или ответе сервера
        if (!$result) {
            return false;
        }
        $result = json_decode($result);
        // если ни чего не нашлось
        if (count($result->response->GeoObjectCollection->featureMember) == 0) {
            return false;
        }
        // получение координат точки
        $coord = $result->response->GeoObjectCollection->featureMember[0]->GeoObject->Point->pos;
        return explode(" ", $coord);
    }

    public static function getMetroNameByCoord($coord)
    {
        $coord_str = implode(",", $coord);
        $url_get_metro = "https://geocode-maps.yandex.ru/1.x/?geocode={$coord_str}&kind=metro&format=json&results=1";
        $result = @file_get_contents($url_get_metro);
        // если произошла ошибка при отправке запроса или ответе сервера
        if (!$result) {
            return false;
        }
        $result = json_decode($result);
        // если ни чего не нашлось
        if (count($result->response->GeoObjectCollection->featureMember) == 0) {
            return false;
        }
        return $result->response->GeoObjectCollection->featureMember[0]->GeoObject->name;
    }

    public static function deliveryPriceOrder(Main\Event $event)
    {

        $order = $event->getParameter("ENTITY");

        $propertyCollection = $order->getPropertyCollection();

        $propsData = [];

        /**
         * Собираем все свойства и их значения в массив
         *
         * @var \Bitrix\Sale\PropertyValue $propertyItem
         */
        foreach ($propertyCollection as $propertyItem) {
            if (!empty($propertyItem->getField("CODE"))) {
                $propsData[$propertyItem->getField("CODE")] = trim($propertyItem->getValue());
            }
        }

        /**
         * Перебираем свойства и изменяем нужные значения
         *
         * @var \Bitrix\Sale\PropertyValue $propertyItem
         */
        foreach ($propertyCollection as $propertyItem) {

            switch ($propertyItem->getField("CODE")) {

                // Установка полного адреса в формате: Адрес, Город, Индекс
                case 'DELIVERY_COST':

                    $deliveryPrice = $propsData['DELIVERY_COST'];

                    break;

                // Прописываем ФИО в одно поле
                case 'LOCATION':
                    $location = CSaleLocation::GetByID($propsData["LOCATION"], LANGUAGE_ID);
                    break;

            }
        }

        if ((int) $location['COUNTRY_ID'] !== 104) {
            $shipmentCollection = $order->getShipmentCollection();
            /** \Bitrix\Sale\Shipment $shipment */
            foreach ($shipmentCollection as $shipment)
            {
                if (!$shipment->isSystem()) {
                    $shipment->setFields(array(
                            'CURRENCY' => $order->getCurrency(),
                            'PRICE_DELIVERY' => $deliveryPrice,
                            'CUSTOM_PRICE_DELIVERY' => 'Y',
                        )
                    );
                }
            }
        }
        return true;
    }


}
