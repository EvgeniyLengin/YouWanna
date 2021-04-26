<?php
#Google ReCaptcha
@require_once 'classes/autoload.php';
define("RE_SITE_KEY","6LfjlUoUAAAAAIcKTHSg2A7Mlw2Pv9DrFvmW4_x8");
define("RE_SEC_KEY","6LfjlUoUAAAAAFKmJ5KXWEY5uVrPDGl08laZpYqJ");
define("LOG_FILENAME", $_SERVER["DOCUMENT_ROOT"]."/log.txt");

use Bitrix\Main;
use Bitrix\Main\Application;




// подключаем пространство имен класса HighloadBlockTable и даём ему псевдоним HLBT для удобной работы
use Bitrix\Highloadblock\HighloadBlockTable as HLBT;
// id highload-инфоблока
const MY_HL_BLOCK_ID = 1;
//подключаем модуль highloadblock
CModule::IncludeModule('highloadblock');

//Функция получения экземпляра класса:
function GetEntityDataClass($HlBlockId) {
	if (empty($HlBlockId) || $HlBlockId < 1)
	{
		return false;
	}
	$hlblock = HLBT::getById($HlBlockId)->fetch();
	$entity = HLBT::compileEntity($hlblock);
	$entity_data_class = $entity->getDataClass();
	return $entity_data_class;
}














// Уникальный код для reCaptcha на странице
function getReCaptchaId(){
    static $iIndexOnPage = 0;
    $iIndexOnPage++;
    return 'g-recaptcha-'.$iIndexOnPage;
}

function dump($val = false) {
    global $USER;
    if ($USER->IsAuthorized() && isset($val)) {
        echo '<pre>';
        print_r($val);
        echo '</pre>';
    }
}

# автозагрузка классов
\Bitrix\Main\Loader::registerAutoLoadClasses(
    null,
    array(
        # Основной класс сайта
        'CYouWanna'                    => '/local/php_interface/classes/CYouWanna.php',
        'CYouWannaCity'                => '/local/php_interface/classes/CYouWannaCity.php',
        'CYouWannaCookie'              => '/local/php_interface/classes/CYouWannaCookie.php',
        'GeoLocation'                  => '/local/php_interface/classes/GeoLocation.php',
        'Yandex\Translate\Translator'  => '/local/php_interface/classes/yandex-translate/Translator.php',
        'Yandex\Translate\Translation' => '/local/php_interface/classes/yandex-translate/Translation.php',
        'Yandex\Translate\Exception'   => '/local/php_interface/classes/yandex-translate/Exception.php',
        'Redaper\ImportFrom1C' 	       => '/local/php_interface/classes/redaper/importfrom1c.php',
    )
);

# менеджер событий
$eventManager = \Bitrix\Main\EventManager::getInstance();

/**
 * Подключение своих обработчиков определения Местоположения по Ip адресу
 *
 * @return \Bitrix\Main\EventResult
 */
function addCustomGeoIpHandler() {
    return new \Bitrix\Main\EventResult(
        \Bitrix\Main\EventResult::SUCCESS,
        array(
            '\Bitrix\Main\Service\GeoIp\GeoLocationByHandlersBuildList' =>
                '/local/php_interface/classes/GeoLocationByHandlersBuildList.php',
        ),
        'main'
    );
}
# Событие для подключения своих обработчиков определения Местоположения по Ip адресу
$eventManager->addEventHandler(
    'main',
    'onMainGeoIpHandlersBuildList',
    'addCustomGeoIpHandler'
);

# Правим адрес в ссылках, согласно выбранного языка
// $eventManager->addEventHandler('main', 'OnEndBufferContent', array(
//     'CYouWanna',
//     'editHrefByLanguageForSite'
// ));

#  Устанавливаем Язык для пользователя и делаем редирект, если текущий язык не совпадает
// $eventManager->addEventHandler('main','OnPageStart', array(
//     'CYouWanna',
//     'setUserLanguageFromUrl'
// ));

# Добавление артикула в символьный код товара, если его там ещё нет
# Так же переводит и добавляет Английское название для поиска
$eventManager->addEventHandler('iblock', 'OnAfterIBlockElementAdd', array(
    'CYouWanna',
    'setArticleFromElementCode'
));

# формирование минимальной цены для товаров при работе с торговыми предложениями
// TODO - перерасчет не будет происходить при удалении цены, так как отсутствует обработчик
// TODO - на событие OnBeforePriceDelete
$eventManager->addEventHandler('iblock', 'OnAfterIBlockElementUpdate', array(
    'CYouWanna',
    'doIBlockAfterSave'
));
$eventManager->addEventHandler('iblock', 'OnAfterIBlockElementAdd', array(
    'CYouWanna',
    'doIBlockAfterSave'
));

$eventManager->addEventHandler('catalog', 'OnPriceAdd', array(
    'CYouWanna',
    'doIBlockAfterSave'
));
$eventManager->addEventHandler('catalog', 'OnPriceUpdate', array(
    'CYouWanna',
    'doIBlockAfterSave'
));
# присвоение номера телефона в качестве логина зрегистрированному пользователю
$eventManager->addEventHandler('main', 'OnBeforeUserRegister', array(
    'CYouWanna',
    'phoneNumberToEmail'
));
# присвоение номера телефона в качестве логина зрегистрированному при оформлении заказа пользователю
$eventManager->addEventHandler('main', 'OnBeforeUserAdd', array(
    'CYouWanna',
    'phoneNumberToLoginSimple'
));
# добавление стоимости доставки к сумме заказа, если доставка не по России
$eventManager->addEventHandler('sale', 'OnSaleOrderBeforeSaved', array(
    'CYouWanna',
    'deliveryPriceOrder'
));
/*$eventManager->addEventHandler('main', 'OnBeforeUserUpdate', array(
    'CYouWanna',
    'phoneNumberToLogin'
));*/


# установить количество доступного товара по количеству основного склада, при создании товара
/*$eventManager->addEventHandler(
    'catalog',
    'OnProductAdd',
    [
        'CYouWanna',
        'setProductQtyOnProductUpdate'
    ]
);*/
# добавляем свои REST методы в инфраструктуру Битрикс (для загрузки файла из 1С)
$eventManager->addEventHandler('rest', 'OnRestServiceBuildDescription', array(
    'Redaper\ImportFrom1C',
    'onRestServiceBuildDescriptionHandler'
));


/* /Для подтягивания промокодов в RetailCrm */
include_once 'classes/RetailCrmInit.php';

$eventManager->addEventHandler('sale', 'OnSaleOrderSaved', array(
	'RetailCrmInit',
	'my_OnOrderSave'
));
/* Для подтягивания промокодов в RetailCrm/ */

/* /Очистка таблицы Instashop с удалением файлов фотографий старще 30 дней */
function clearInsta(){
    $connection = Application::getConnection();
    $tableName = 'webfly_instagram';
    $toRemove = '';
    $i = 0;

    $sqlText = <<<SQLHEAD
SELECT * FROM {$tableName} WHERE DATE < (NOW() - INTERVAL 30 DAY)
SQLHEAD;

    $res = $connection->query($sqlText);
    while ($row = $res->fetch()) {
        $i++;
        $toRemove = $row['ID']; // id записи на удаление
        CFile::Delete($row['PREVIEW_PICTURE']); // удаляем картинку
        $sqlTextRemove = <<<SQLHEAD
DELETE  FROM {$tableName} WHERE ID={$toRemove}
SQLHEAD;
        $connection->query($sqlTextRemove); // удаляем запись из таблицы
    };
    AddMessage2Log("Удалено постов инсты: " . $i, "you_wanna");
    return 'clearInsta();';
}
/* Очистка таблицы Instashop с удалением файлов фотографий старще 30 дней/ */


/* /Очистка таблицы Instashop от дублей записей */
function clearDoublePostsInsta(){
    $connection = Application::getConnection();
    $tableName = 'webfly_instagram';

    $sqlText = <<<SQLHEAD
CREATE TEMPORARY TABLE `temp`
as (
   SELECT min(id) as id
   FROM webfly_instagram
   GROUP BY LINK, POST_ID
);
SQLHEAD;

    $sqlTextR = <<<SQLHEAD
DELETE from webfly_instagram
WHERE webfly_instagram.id not in (
   SELECT id FROM temp
);
SQLHEAD;
    $connection->query($sqlText); // Создаем временную таблицу
    $connection->query($sqlTextR); // удаляем дубли из таблицы
    AddMessage2Log("Удалены посты-дубли инсты", "you_wanna");
    return 'clearDoublePostsInsta();';
}


// AddEventHandler("main",'OnFileSave','OnFileSave');
// function OnFileSave(&$arFile, $fileName, $module)
// {
// 	print_r($arFile);
// 	die('stop');
// }
/* Очистка таблицы Instashop от дублей записей/ */

// При оформлении заказа
AddEventHandler("sale","OnOrderAdd","customMoveUserGroup");
function customMoveUserGroup(){
    global $USER;

    $tmp_user_id = $USER->GetID();

    $arFilter = Array("USER_ID" => $tmp_user_id);
    $sql = CSaleOrder::GetList(array("DATE_INSERT" => "DESC"), $arFilter);

    $tmp_items = [];
    while ($result = $sql->Fetch()){
        $tmp_items[] = $result;
    }

    $currentOrderId = $tmp_items['0']['ID'];
    $currentOrderDate = $tmp_items['0']['DATE_INSERT'];
    //Текущая цена
    $currentOrderPrice = $tmp_items['0']['PRICE'];
    $tmpPrice = $arOrder['PRICE'];
    //
    $arOrderZ = CSaleOrder::GetByID($currentOrderId);
    // параметры
    $userNameord = $arOrderZ['USER_NAME'];
    $userEmailord = $arOrderZ['USER_EMAIL'];
        //подргужаем товары из корзины
        $products_in_cart = CSaleBasket::GetList(
        array(), // сортировка
        array(
            'FUSER_ID' => CSaleBasket::GetBasketUserID(),
            'LID' => SITE_ID,
            'ORDER_ID' => NULL
        ),
        false, // группировать
        false, // постраничная навигация
        array()
    );


    $fm =    $products_in_cart->arResult;
    //Информация о товарах

    $productInfo = [];

        foreach ($fm as $key => $value) {
        // айди торгового предложения
                $tmp_id_p = $value['PRODUCT_ID'];

                $mxResult = CCatalogSku::GetProductInfo($tmp_id_p);
        // айди родителя этого торг предложения.
                $parrentOrderId = $mxResult['ID'];
                $productCurrentName = $value['NAME'];
                $tmp_auantity = $value["QUANTITY"];
                $tmp_auantity = round($tmp_auantity, 0);
                $arSKU = CCatalogSKU::getOffersList($parrentOrderId, 0, array('ACTIVE' => 'Y'), array('NAME'), array("CODE"=>array('COLOR', 'SIZE')));
                $tmp_size = $arSKU[$parrentOrderId][$tmp_id_p]["PROPERTIES"]['SIZE']['VALUE'];
                $tmp_color = $arSKU[$parrentOrderId][$tmp_id_p]["PROPERTIES"]['COLOR']['VALUE'];
                $propItems =CIBlockElement::GetByID($parrentOrderId)->GetNextElement()->GetProperties();
                $imgValue = $propItems["HOVER_PHOTO"]['VALUE'];
                $URL = $_SERVER['HTTP_ORIGIN'].CFile::GetPath($imgValue);

                $productInfo[] = ["kolvo" => "$tmp_auantity", "NameProduct" => "$productCurrentName", "SIZE" => "$tmp_size", "COLOR" => "$tmp_color", "IMG_PATH" => "$URL"];
        }


        foreach ($productInfo as $key => $value) {
            $productFinal   .= "<li>".$value['NameProduct']."."." Размер ".$value['SIZE'].". Цвет - ".$value['COLOR'].". </li>";
        }


        $to      = $userEmailord;
        $subject = 'Ваш заказ №'.$currentOrderId;
        $message = '
        <html>
        <head>
           <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />
          <title>Вы оформили заказ</title>

        </head>
            <body style="">
                <table style="width: 100%;border:0px;max-width: 612px; color:black; padding:25px; padding-top:0px;">
                    <tr style="max-width:100%; width: 100%; color:#1F1B20;">
                        <td style="width:100%; text-align:center; font-size:25px;font-weight:bold; letter-spacing:5px;">YOU WANNA</td> <br>

                    </tr>
                    <tr style="max-width:100%; width: 100%; color:#1F1B20;">
                        <td style="width:100%; text-align:center; font-size:16px; letter-spacing:0.1em;">MOSCOW</td>
                    </tr>
                    <tr>
                        <td style="width:100%; text-align:center; font-size:18px;font-weight:normal; padding-top:25px; padding-bottom: 24px; border-bottom: 1px solid lightgray;">Здравствуйте, ' .$userNameord.'! </td> <br>
                    </tr>
                    <tr style="text-align:left;">
                        <td style="padding-top: 35px; padding-bottom: 20px;">Вы оформили заказ <b>№'.$currentOrderId.'</b> от <b>'.$currentOrderDate.'</b> на сайте www.YOU-WANNA.ru</td>
                    </tr>
                    <tr>
                        <td><b>Состав заказа:</b></td> <br>
                        <tr>
                        <td>
                            <ul>'.$productFinal.'
                            </ul>
                        </td>
                        </tr>
                        <tr>
                            <td><b>Общая сумма: ' .$currentOrderPrice.' р.</b></td>
                        </tr>
                    </tr>
                    <tr>
                        <td style="padding-top: 25px; padding-bottom: 20px; text-align: center;">
                            Посмотреть Ваш заказ Вы можете в Личном Кабинете:
                        </td>
                        <tr>
                            <td style="padding-bottom: 45px; border-bottom: 1px solid lightgray;">
                                <a href="https://you-wanna.ru/personal/order/" style="display:block; width:250px; height:44px; background-color: #000000; color: #FFFFFF; text-decoration:none; text-align:center; vertical-align: middle; margin:0 auto; line-height: 41px;">Перейти в личный кабинет</a>
                            </td>
                        </tr>
                    </tr>
                    <tr>
                        <td style="text-align:center; color: lightgray; font-size:14px; padding-top: 25px;">С уважением, команда YOU WANNA.</td>
                    </tr>
                        <tr>
                             <td style="text-align:center; padding-top:10px;">
                             <a href="https://www.facebook.com/youwannamoscow/" target="_blank"> <img style="max-width:25px" src="https://you-wanna.ru/facebook.png" /></a>
                             <a href="https://www.instagram.com/youwanna_official/" target="_blank"> <img style="max-width:25px" src="https://you-wanna.ru/in1.png" /></a>
                             </td>
                         </tr>
                    <tr>
                         <td style="text-align:center; color: lightgray; font-size:14px; padding-top: 10px;" >© 2015—2020 YOU WANNA</td>
                    </tr>


                </table>
            </body>
        </html>
        ';
        $headers = 'From: sales@you-wanna.ru' . "\r\n" .
         'Content-type: text/html; charset=utf-8 \r\n'.
            'Reply-To: sales@you-wanna.ru' . "\r\n" .
            'X-Mailer: PHP/' . phpversion();

        $ss = mail($to, $subject, $message, $headers);

        if (!$ss) {
            echo "net";
        } else {
            echo "Отправлено успешно";
        }


}

// При статусе "Проверено, ждем оплату"
AddEventHandler("sale", "OnSaleStatusOrder", "customRemStatus");
function customRemStatus($ID){

        $arOrder = CSaleOrder::GetByID($ID);
        $userEmail = $arOrder['USER_EMAIL'];
        $userName = $arOrder['USER_NAME'];

            $orderStatus = $arOrder['STATUS_ID'];
            print_r($orderStatus);
            if ($orderStatus == "PW") {

                $to      = $userEmail;
                $subject = 'Ваш заказ №'.$ID." доступен к оплате";
                $message = '
                <html>
                <head>
                   <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />
                  <title>Ожидается оплата</title>

                </head>
                    <body style="">
                        <table style="width: 100%;border:0px;max-width: 612px; color:black; padding:25px; padding-top:0px;">
                            <tr style="max-width:100%; width: 100%; color:#1F1B20;">
                                <td style="width:100%; text-align:center; font-size:25px;font-weight:bold; letter-spacing:5px;">YOU WANNA</td> <br>

                            </tr>
                            <tr style="max-width:100%; width: 100%; color:#1F1B20;">
                                <td style="width:100%; text-align:center; font-size:16px; letter-spacing:0.1em;">MOSCOW</td>
                            </tr>
                            <tr>
                                <td style="width:100%; text-align:center; font-size:18px;font-weight:normal; padding-top:25px; padding-bottom: 24px; border-bottom: 1px solid lightgray;">Здравствуйте, ' .$userName.'! </td> <br>
                            </tr>
                            <tr style="text-align:left;">
                                <td style="padding-top: 35px; padding-bottom: 20px; text-align:center;">Наличие вещей подтверждено менеджером, теперь вы можете оплачивать заказ.</td>
                            </tr>
                            <tr>
                                <td style="text-align:center;">Статус вашего заказа изменился на “ожидание оплаты”</td> <br>

                                <tr>
                                    <td style="text-align:center;padding-top:15px; padding-bottom:15px;">Чтобы произвести оплату, перейдите в личный кабинет и нажмите кнопку “оплатить”</td>
                                </tr>
                            </tr>
                            <tr>
                                <td style="padding-bottom: 45px; border-bottom: 1px solid lightgray;">
                                    <a href="https://you-wanna.ru/personal/order/" style="display:block; width:250px; height:44px; background-color: #000000; color: #FFFFFF; text-decoration:none; text-align:center; vertical-align: middle; margin:0 auto; line-height: 41px;">Перейти в личный кабинет</a>
                                </td>
                            </tr>

                            <tr>
                                <td style="text-align:center; color: lightgray; font-size:14px; padding-top: 25px;">С уважением, команда YOU WANNA.</td>
                            </tr>
                                <tr>
                                     <td style="text-align:center; padding-top:10px;">
                                     <a href="https://www.facebook.com/youwannamoscow/" target="_blank"> <img style="max-width:25px" src="https://you-wanna.ru/facebook.png" /></a>
                                     <a href="https://www.instagram.com/youwanna_official/" target="_blank"> <img style="max-width:25px" src="https://you-wanna.ru/in1.png" /></a>
                                     </td>
                                 </tr>
                            <tr>
                                 <td style="text-align:center; color: lightgray; font-size:14px; padding-top: 10px;" >© 2015—2020 YOU WANNA</td>
                            </tr>


                        </table>
                    </body>
                </html>
                ';
                $headers = 'From: sales@you-wanna.ru' . "\r\n" .
                 'Content-type: text/html; charset=utf-8 \r\n'.
                    'Reply-To: sales@you-wanna.ru' . "\r\n" .
                    'X-Mailer: PHP/' . phpversion();

                $ss = mail($to, $subject, $message, $headers);
                if (!$ss) {
                    echo "net";
                } else {
                    echo "Отправлено успешно";
                }

            } else {


            }
}
// Корзина для аякса
AddEventHandler("main", "OnBeforeProlog", "MyOnBeforePrologHandler", 50);
function MyOnBeforePrologHandler()
{
$userIDProlog = $GLOBALS['USER']->GetID();
$_SESSION['logged_in_user_id'] = $userIDProlog;

}



function BasketItemsForAjax() {


CModule::IncludeModule("sale");
CModule::IncludeModule("main");

    $arBasketItems = array();



$dbBasketItems = CSaleBasket::GetList(
        array(
                "NAME" => "ASC",
                "ID" => "ASC"
            ),
        array(
                "FUSER_ID" => CSaleBasket::GetBasketUserID(),
                "LID" => "s1",
                "ORDER_ID" => "NULL"
            ),
        false,
        false,
        array("ID", "CALLBACK_FUNC", "MODULE",
              "PRODUCT_ID", "QUANTITY", "DELAY",
              "CAN_BUY", "PRICE", "WEIGHT")
    );





$orderMassive = [];
    while ($arProps = $dbBasketItems->Fetch()) {

        $prodId = $arProps['PRODUCT_ID'];
        $mxResult = CCatalogSku::GetProductInfo($prodId);
        $quantity = $arProps['QUANTITY'];
        $price = $arProps['PRICE'];
        $parrentCurrentId = $mxResult['ID'];
        $propCHILDREN=CIBlockElement::GetByID($prodId)->GetNextElement()->GetProperties();
        $size = $propCHILDREN['SIZE']['VALUE'];
        $prop=CIBlockElement::GetByID($parrentCurrentId)->GetNextElement()->GetProperties();
        $standartFields =CIBlockElement::GetByID($parrentCurrentId)->GetNextElement()->GetFields();
        $hover_photo = $prop['HOVER_PHOTO']['VALUE'];
        $URLORDER = $standartFields['DETAIL_PAGE_URL'];
        $URLPHOTO = $_SERVER['HTTP_ORIGIN'].CFile::GetPath($hover_photo);
        $NAME = $standartFields['NAME'];
        $COLOR = $propCHILDREN['COLOR']['VALUE'];
        $ARTICLE = $standartFields['CODE'];

        $entity_data_class = GetEntityDataClass(1);
        $rsData = $entity_data_class::getList(array(
           'order' => array('ID'=>'ASC'),
           'select' => array('*'),
           'filter' => array('UF_XML_ID' => $COLOR)
        ));
        $checkImage = 0;
        while($el = $rsData->fetch()){

            $imageId = $el['UF_COLOR_FILE'];
            $imageId = trim($imageId);


            if ($imageId != 0) {
                $colorItem  = CFile::GetPath($imageId);
                $checkImage = Y;
            } else {
                $colorItem = $el['UF_NAME'];
                $checkImage = N;
            }

        }





        $orderMassive[] = ['nazvanie' => "$NAME", "URLPHOTO" => "$URLPHOTO", "URLORDER" => "$URLORDER", "PRICE" => "$price",
         "QUANT" => "$quantity", "SIZE"=>"$size", "COLOR" => "$COLOR", "COLORITEM" => "$colorItem", "CHECKIMAGE" => "$checkImage", "ARTICLE" => "$ARTICLE"];


    }





            $serializeBasket = serialize($orderMassive);
            $BaseBasket = base64_encode($serializeBasket);


           $userId =  trim($_POST['iduser']);
           $user = new CUser;
           $rsUser = CUser::GetByID($userId);
           $arUser = $rsUser->Fetch();
           $emailuser = $arUser['EMAIL'];

           $nameuser = $arUser['NAME'];
           if($nameuser == "") {
                $nameuser = $arUser['LOGIN'];
           }

           //для записи
           $massPush = ["USERNAME" => "$nameuser", 'EMAILUSER' => "$emailuser", "KORZINA" => "$BaseBasket"];
           $serialMassPush = serialize($massPush);
           $pathForDir = $_SERVER['DOCUMENT_ROOT']."/basketfiles/$userId.txt";
           file_put_contents($pathForDir, $serialMassPush);
           echo "<pre>";
           // print_r($_SERVER);
           echo "</pre>";

}

if(isset($_POST['iduser'])) {
    BasketItemsForAjax();
} else {

}


function updateEmail($id,$newmail) {
    $user = new CUser;

    $rsUser = CUser::GetByID($id);
    $arUser = $rsUser->Fetch();
    $emailuserOLD = $arUser['EMAIL'];
    if(trim($emailuserOLD) != trim($newmail)) {
            $user->Update($id, Array("EMAIL" => "$newmail"));
    }

}

if (isset($_POST['usermailrefresh'])) {
    $newmail = $_POST['usermailrefresh'];
    $id = $_POST['useridcheck'];
    updateEmail($id,$newmail);

} else {

}


//Сдек
AddEventHandler("sale", "OnSaleStatusOrder", "customRemStatusSDEK");
function customRemStatusSDEK($ID){
    $arOrderSDEK = CSaleOrder::GetByID($ID);
    $userEmailSDEK = $arOrderSDEK['USER_EMAIL'];
    $userNameSDEK = $arOrderSDEK['USER_NAME'];

    $CurId = $arOrderSDEK['ID'];
    $statusCur = $arOrderSDEK['STATUS_ID'];
    if ($CurId == "25215" && $statusCur == "GC") {

        $arOrderSDEK = CSaleOrder::GetByID($ID);

        // echo "<pre>".print_r($arOrderSDEK)."</pre>";

        $db_props = CSaleOrderPropsValue::GetOrderProps($ID);


        $arProps = [];
        $trackNumber = "";
        while ($arProps = $db_props->Fetch()) {
            if ($arProps['CODE'] == "TRACK") {
                $trackNumber = $arProps['VALUE'];
                break;
            }
        }



        $to      = $userEmailSDEK;
        $subject = 'Ваш заказ №'.$ID." отправлен";
        $message = '
        <html>
        <head>
           <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />
          <title>Ожидается оплата</title>

        </head>
            <body style="">
                <table style="width: 100%;border:0px;max-width: 612px; color:black; padding:25px; padding-top:0px;">
                    <tr style="max-width:100%; width: 100%; color:#1F1B20;">
                        <td style="width:100%; text-align:center; font-size:25px;font-weight:bold; letter-spacing:5px;">YOU WANNA</td> <br>

                    </tr>
                    <tr style="max-width:100%; width: 100%; color:#1F1B20;">
                        <td style="width:100%; text-align:center; font-size:16px; letter-spacing:0.1em;">MOSCOW</td>
                    </tr>
                    <tr>
                        <td style="width:100%; text-align:center; font-size:18px;font-weight:normal; padding-top:25px; padding-bottom: 24px; border-bottom: 1px solid lightgray;">Здравствуйте, '.$userNameSDEK.'! </td> <br>
                    </tr>

                    <tr>
                        <td style="text-align:center;">Ваш заказ отплавлен,</td> <br>

                        <tr>
                            <td style="text-align:center;padding-top:15px; padding-bottom:15px;">Проверить местонахождение посылки вы можете на сайте <a href="CDEK.RU">CDEK.RU</a> по трек-номеру отправления №'.$trackNumber.'</td>
                        </tr>
                    </tr>
                    <tr>
                        <td style="padding-bottom: 45px; border-bottom: 1px solid lightgray;">
                            <a href="https://you-wanna.ru/personal/order/" style="display:block; width:250px; height:44px; background-color: #000000; color: #FFFFFF; text-decoration:none; text-align:center; vertical-align: middle; margin:0 auto; line-height: 41px;">Перейти в личный кабинет</a>
                        </td>
                    </tr>

                    <tr>
                        <td style="text-align:center; color: lightgray; font-size:14px; padding-top: 25px;">С уважением, команда YOU WANNA.</td>
                    </tr>
                        <tr>
                             <td style="text-align:center; padding-top:10px;">
                             <a href="https://www.facebook.com/youwannamoscow/" target="_blank"> <img style="max-width:25px" src="https://you-wanna.ru/facebook.png" /></a>
                             <a href="https://www.instagram.com/youwanna_official/" target="_blank"> <img style="max-width:25px" src="https://you-wanna.ru/in1.png" /></a>
                             </td>
                         </tr>
                    <tr>
                         <td style="text-align:center; color: lightgray; font-size:14px; padding-top: 10px;" >© 2015—2020 YOU WANNA</td>
                    </tr>


                </table>
            </body>
        </html>
        ';
        $headers = 'From: sales@you-wanna.ru' . "\r\n" .
         'Content-type: text/html; charset=utf-8 \r\n'.
            'Reply-To: sales@you-wanna.ru' . "\r\n" .
            'X-Mailer: PHP/' . phpversion();

        $ss = mail($to, $subject, $message, $headers);
        if (!$ss) {
            echo "net";
        } else {
            echo "Отправлено успешно";
        }


//Письмо

}
}

AddEventHandler("sale", "OnSaleStatusOrder", "CustomTrigCompleteOrder");

function CustomTrigCompleteOrder($ID){
        $massOrder = CSaleOrder::GetByID($ID);

        $statusOrderID = $massOrder['STATUS_ID'];
        $idOrderCurrent = $massOrder['ID'];
        if ($statusOrderID == "P") {

    $arOrder = CSaleOrder::GetByID($idOrderCurrent);

    $tmpPrice = $arOrder['PRICE'];
    $statusOrderID = ['STATUS_ID'];
    $userEmail = $arOrder['USER_EMAIL'];
    $userName = $arOrder['USER_NAME'];

    CModule::IncludeModule('sale');
   $res = CSaleBasket::GetList(array(), array("ORDER_ID" => $idOrderCurrent)); // ID заказа
   $json_product=array();

   $arItemz = [];

   while ($arItem = $res->Fetch()) {
       $nameOrd = $arItem['NAME'];
       $prodId = $arItem['PRODUCT_ID'];
       $tmp_auantity = $arItem["QUANTITY"];
       $tmp_auantity = round($tmp_auantity, 0);
      $propItems =CIBlockElement::GetByID($prodId)->GetNextElement()->GetProperties();
      $tmp_size = $propItems['SIZE']['VALUE'];
      $tmp_color = $propItems['COLOR']['VALUE'];
      $mxResult = CCatalogSku::GetProductInfo($prodId);
// айди родителя этого торг предложения.
      $parrentOrderId = $mxResult['ID'];
      $propItemsPar =CIBlockElement::GetByID($parrentOrderId)->GetNextElement()->GetProperties();
      $imgValue = $propItemsPar["HOVER_PHOTO"]['VALUE'];
      $URL = $_SERVER['HTTP_ORIGIN'].CFile::GetPath($imgValue);
      $arItemz[] = ['NAME' => $nameOrd, 'SIZE' => $tmp_size, 'COLOR' => $tmp_color, 'IMAGEPATH' => $URL, 'QUANTITY' => $tmp_auantity];

   }

   $productForMessage = "";
   foreach ($arItemz as $key => $value) {

       $productForMessage .= '<tr>
                  <td rowspan="5" style="text-align:center; padding-top:10px; ">
                  <img src="'.$value['IMAGEPATH'].'" style="max-width:120px;">
                  </td>
                  <td style="padding-top:10px; padding-left:10px; " colspan="2"><b>'.$value['NAME'].'</b></td>
              </tr>

              <tr>
                   <td style="padding-left:10px;"><b>Цвет</b></td>
                   <td>'.$value['COLOR'].'</td>
               </tr>

              <tr>
                   <td style="padding-left:10px;"><b>Размер</b></td>
                   <td>'.$value['SIZE'].'</td>
               </tr>

              <tr>

                   <td style="padding-left:10px;"><b>Количество</b></td>
                   <td>'.$value['QUANTITY'].'</td>
               </tr>

              <tr style="min-height:36px;">

                  <td style="min-height:36px;"></td>
                  <td style="min-height:36px;"></td>
              </tr>
              <tr style="min-height:36px;">

                  <td style="min-height:36px;"></td>
                  <td style="min-height:36px;"></td>
              </tr>

              <tr>';
      }

           $to      = $userEmail;
           $subject = 'Ваш заказ №'.$ID." оплачен";
           $message = '
           <html>
           <head>
              <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />
             <title>Ожидается оплата</title>

           </head>
               <body style="">
                   <table style="width: 100%;border:0px;max-width: 612px; color:black; padding:10px; padding-top:0px;">
                       <tr style="max-width:100%; width: 100%; color:#1F1B20;">
                           <td style="width:100%; text-align:center; font-size:25px;font-weight:bold; letter-spacing:5px;">YOU WANNA</td> <br>

                       </tr>
                       <tr style="max-width:100%; width: 100%; color:#1F1B20;">
                           <td style="width:100%; text-align:center; font-size:16px; letter-spacing:0.1em;">MOSCOW</td>
                       </tr>
                       <tr>
                           <td style="width:100%; text-align:center; font-size:18px;font-weight:normal; padding-top:25px; padding-bottom: 24px; border-bottom: 1px solid lightgray;">Здравствуйте, '.$userName.'! </td> <br>
                       </tr>

                       <tr>
                           <td style="text-align:center; padding-top:10px;">Ваш заказ №'.$ID.' оплачен.</td> <br>

                           <tr>
                               <td style="text-align:left;padding-top:15px; padding-bottom:15px;"><b>Состав заказа:</b></td>
                           </tr>
                       </tr>

                       </table>

                       <table style="width: 100%;border:0px;max-width: 612px; color:black; padding:10px; padding-top:0px;">
                            '.$productForMessage.'
                               <tr>
                                    <td colspan="2" style="text-align:left; padding-top:15px;"><b>Итого '.$tmpPrice.' р.</b></td>
                                    <td></td>
                                </tr>
                        </table>





                        <table style="width: 100%;border:0px;max-width: 612px; color:black; padding:10px; padding-top:0px;">

                       <tr >
                           <td style="padding-bottom: 45px; border-bottom: 1px solid lightgray;">
                               <a href="https://you-wanna.ru/personal/order/" style="display:block; width:250px; height:44px; background-color: #000000; color: #FFFFFF; text-decoration:none; text-align:center; vertical-align: middle; margin:0 auto; line-height: 41px;">Перейти в личный кабинет</a>
                           </td>
                       </tr>

                       <tr>
                           <td style="text-align:center; color: lightgray; font-size:14px; padding-top: 25px;">С уважением, команда YOU WANNA.</td>
                       </tr>
                           <tr>
                                <td style="text-align:center; padding-top:10px;">
                                <a href="https://www.facebook.com/youwannamoscow/" target="_blank"> <img style="max-width:25px" src="https://you-wanna.ru/facebook.png" /></a>
                                <a href="https://www.instagram.com/youwanna_official/" target="_blank"> <img style="max-width:25px" src="https://you-wanna.ru/in1.png" /></a>
                                </td>
                            </tr>
                       <tr>
                            <td style="text-align:center; color: lightgray; font-size:14px; padding-top: 10px;" >© 2015—2020 YOU WANNA</td>
                       </tr>


                   </table>
               </body>
           </html>
           ';
           $headers = 'From: sales@you-wanna.ru' . "\r\n" .
            'Content-type: text/html; charset=utf-8 \r\n'.
               'Reply-To: sales@you-wanna.ru' . "\r\n" .
               'X-Mailer: PHP/' . phpversion();

           $ss = mail($to, $subject, $message, $headers);
           if (!$ss) {
               echo "net";
           } else {
               echo "Отправлено успешно";
           }

//Закрытие проверки
   }

    }



// Подключение в init.php вашего сайта
use Bitrix\Main\Loader;
use Bitrix\Main\SystemException;

class AllProductDiscount{
    /**
     * @return XML_ID|array
     * @throws SystemException
     * @throws \Bitrix\Main\LoaderException
     */
    public static function getFull($arrFilter = array(), $arSelect = array()){
        if(!Loader::includeModule('sale')) throw new SystemException('Не подключен модуль Sale');

        //Все товары со скидкой!!!
    // Группы пользователей
    global $USER;
    $arUserGroups = $USER->GetUserGroupArray();
    if (!is_array($arUserGroups)) $arUserGroups = array($arUserGroups);
    // Достаем старым методом только ID скидок привязанных к группам пользователей по ограничениям
    $actionsNotTemp = \CSaleDiscount::GetList(array("ID" => "ASC"),array("USER_GROUPS" => $arUserGroups),false,false,array("ID"));
    while($actionNot = $actionsNotTemp->fetch()){
      $actionIds[] = $actionNot['ID'];
    }
    $actionIds=array_unique($actionIds); sort($actionIds);
    // Подготавливаем необходимые переменные для разборчивости кода
    global $DB;
    $conditionLogic = array('Equal'=>'=','Not'=>'!','Great'=>'>','Less'=>'<','EqGr'=>'>=','EqLs'=>'<=');
    $arSelect = array_merge(array("ID","IBLOCK_ID","XML_ID"),$arSelect);
    $city='MSK';
    // Теперь достаем новым методом скидки с условиями. P.S. Старым методом этого делать не нужно из-за очень высокой нагрузки (уже тестировал)
    $actions = \Bitrix\Sale\Internals\DiscountTable::getList(array(
      'select' => array("ID","ACTIONS_LIST"),
      'filter' => array("ACTIVE"=>"Y","USE_COUPONS"=>"N","DISCOUNT_TYPE"=>"P","LID"=>SITE_ID,
      "ID"=>$actionIds,
      array(
        "LOGIC" => "OR",
        array(
          "<=ACTIVE_FROM"=>$DB->FormatDate(date("Y-m-d H:i:s"),"YYYY-MM-DD HH:MI:SS",\CSite::GetDateFormat("FULL")),
          ">=ACTIVE_TO"=>$DB->FormatDate(date("Y-m-d H:i:s"),"YYYY-MM-DD HH:MI:SS",\CSite::GetDateFormat("FULL"))
        ),
        array(
          "=ACTIVE_FROM"=>false,
          ">=ACTIVE_TO"=>$DB->FormatDate(date("Y-m-d H:i:s"),"YYYY-MM-DD HH:MI:SS",\CSite::GetDateFormat("FULL"))
        ),
        array(
          "<=ACTIVE_FROM"=>$DB->FormatDate(date("Y-m-d H:i:s"),"YYYY-MM-DD HH:MI:SS",\CSite::GetDateFormat("FULL")),
          "=ACTIVE_TO"=>false
        ),
        array(
          "=ACTIVE_FROM"=>false,
          "=ACTIVE_TO"=>false
        ),
      ))
    ));
    // Перебираем каждую скидку и подготавливаем условия фильтрации для CIBlockElement::GetList
    while($arrAction = $actions->fetch()){
      $arrActions[$arrAction['ID']] = $arrAction;
    }
    foreach($arrActions as $actionId => $action){
      $arPredFilter = array_merge(array("ACTIVE_DATE"=>"Y", "CAN_BUY"=>"Y"),$arrFilter); //Набор предустановленных параметров
      $arFilter = $arPredFilter; //Основной фильтр
      $dopArFilter = $arPredFilter; //Фильтр для доп. запроса
      $dopArFilter["=XML_ID"] = array(); //Пустое значения для первой отработки array_merge
      //Магия генерации фильтра
      foreach($action['ACTIONS_LIST']['CHILDREN'] as $condition){
        foreach($condition['CHILDREN'] as $keyConditionSub=>$conditionSub){
          $cs=$conditionSub['DATA']['value']; //Значение условия
          $cls=$conditionLogic[$conditionSub['DATA']['logic']]; //Оператор условия
          //$arFilter["LOGIC"]=$conditionSub['DATA']['All']?:'AND';
          $CLASS_ID = explode(':',$conditionSub['CLASS_ID']);

          if($CLASS_ID[0]=='ActSaleSubGrp') {
            foreach($conditionSub['CHILDREN'] as $keyConditionSubElem=>$conditionSubElem){
              $cse=$conditionSubElem['DATA']['value']; //Значение условия
              $clse=$conditionLogic[$conditionSubElem['DATA']['logic']]; //Оператор условия
              //$arFilter["LOGIC"]=$conditionSubElem['DATA']['All']?:'AND';
              $CLASS_ID_EL = explode(':',$conditionSubElem['CLASS_ID']);

              if($CLASS_ID_EL[0]=='CondIBProp') {
                $arFilter["IBLOCK_ID"]=$CLASS_ID_EL[1];
                $arFilter[$clse."PROPERTY_".$CLASS_ID_EL[2]]=array_merge((array)$arFilter[$clse."PROPERTY_".$CLASS_ID_EL[2]],(array)$cse);
                $arFilter[$clse."PROPERTY_".$CLASS_ID_EL[2]]=array_unique($arFilter[$clse."PROPERTY_".$CLASS_ID_EL[2]]);
              }elseif($CLASS_ID_EL[0]=='CondIBName') {
                $arFilter[$clse."NAME"]=array_merge((array)$arFilter[$clse."NAME"],(array)$cse);
                $arFilter[$clse."NAME"]=array_unique($arFilter[$clse."NAME"]);
              }elseif($CLASS_ID_EL[0]=='CondIBElement') {
                $arFilter[$clse."ID"]=array_merge((array)$arFilter[$clse."ID"],(array)$cse);
                $arFilter[$clse."ID"]=array_unique($arFilter[$clse."ID"]);
              }elseif($CLASS_ID_EL[0]=='CondIBTags') {
                $arFilter[$clse."TAGS"]=array_merge((array)$arFilter[$clse."TAGS"],(array)$cse);
                $arFilter[$clse."TAGS"]=array_unique($arFilter[$clse."TAGS"]);
              }elseif($CLASS_ID_EL[0]=='CondIBSection') {
                $arFilter[$clse."SECTION_ID"]=array_merge((array)$arFilter[$clse."SECTION_ID"],(array)$cse);
                $arFilter[$clse."SECTION_ID"]=array_unique($arFilter[$clse."SECTION_ID"]);
              }elseif($CLASS_ID_EL[0]=='CondIBXmlID') {
                $arFilter[$clse."XML_ID"]=array_merge((array)$arFilter[$clse."XML_ID"],(array)$cse);
                $arFilter[$clse."XML_ID"]=array_unique($arFilter[$clse."XML_ID"]);
              }elseif($CLASS_ID_EL[0]=='CondBsktAppliedDiscount') { //Условие: Были применены скидки (Y/N)
                foreach($arrActions as $tempAction){
                  if(($tempAction['SORT']<$action['SORT']&&$tempAction['PRIORITY']>$action['PRIORITY']&&$cse=='N')||($tempAction['SORT']>$action['SORT']&&$tempAction['PRIORITY']<$action['PRIORITY']&&$cse=='Y')){
                    $arFilter=false;
                    break 4;
                  }
                }
              }
            }
          }elseif($CLASS_ID[0]=='CondIBProp') {
            $arFilter["IBLOCK_ID"]=$CLASS_ID[1];
            $arFilter[$cls."PROPERTY_".$CLASS_ID[2]]=array_merge((array)$arFilter[$cls."PROPERTY_".$CLASS_ID[2]],(array)$cs);
            $arFilter[$cls."PROPERTY_".$CLASS_ID[2]]=array_unique($arFilter[$cls."PROPERTY_".$CLASS_ID[2]]);
          }elseif($CLASS_ID[0]=='CondIBName') {
            $arFilter[$cls."NAME"]=array_merge((array)$arFilter[$cls."NAME"],(array)$cs);
            $arFilter[$cls."NAME"]=array_unique($arFilter[$cls."NAME"]);
          }elseif($CLASS_ID[0]=='CondIBElement') {
            $arFilter[$cls."ID"]=array_merge((array)$arFilter[$cls."ID"],(array)$cs);
            $arFilter[$cls."ID"]=array_unique($arFilter[$cls."ID"]);
          }elseif($CLASS_ID[0]=='CondIBTags') {
            $arFilter[$cls."TAGS"]=array_merge((array)$arFilter[$cls."TAGS"],(array)$cs);
            $arFilter[$cls."TAGS"]=array_unique($arFilter[$cls."TAGS"]);
          }elseif($CLASS_ID[0]=='CondIBSection') {
            $arFilter[$cls."SECTION_ID"]=array_merge((array)$arFilter[$cls."SECTION_ID"],(array)$cs);
            $arFilter[$cls."SECTION_ID"]=array_unique($arFilter[$cls."SECTION_ID"]);
          }elseif($CLASS_ID[0]=='CondIBXmlID') {
            $arFilter[$cls."XML_ID"]=array_merge((array)$arFilter[$cls."XML_ID"],(array)$cs);
            $arFilter[$cls."XML_ID"]=array_unique($arFilter[$cls."XML_ID"]);
          }elseif($CLASS_ID[0]=='CondBsktAppliedDiscount') { //Условие: Были применены скидки (Y/N)
            foreach($arrActions as $tempAction){
              if(($tempAction['SORT']<$action['SORT']&&$tempAction['PRIORITY']>$action['PRIORITY']&&$cs=='N')||($tempAction['SORT']>$action['SORT']&&$tempAction['PRIORITY']<$action['PRIORITY']&&$cs=='Y')){
                $arFilter=false;
                break 3;
              }
            }
          }
        }
      }
      if($arFilter!==false&&$arFilter!=$arPredFilter){
        if(!isset($arFilter['=XML_ID'])){
          //Делаем запрос по каждому из фильтров, т.к. один фильтр не получится сделать из-за противоречий условий каждой скидки
          $res = \CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
          while($ob = $res->GetNextElement()){
            $arFields = $ob->GetFields();
            $poductsArray['IDS'][] = $arFields["ID"];
          }
        }elseif(!empty($arFilter['=XML_ID'])){
          //Подготавливаем массив для отдельного запроса
          $dopArFilter['=XML_ID'] = array_unique(array_merge($arFilter['=XML_ID'],$dopArFilter['=XML_ID']));
        }
      }
    }

    if(isset($dopArFilter)&&!empty($dopArFilter['=XML_ID'])){
      //Делаем отдельный запрос по конкретным XML_ID
      $res = \CIBlockElement::GetList(array(), $dopArFilter, false, array("nTopCount"=>count($dopArFilter['=XML_ID'])), $arSelect);
      while($ob = $res->GetNextElement()){
        $arFields = $ob->GetFields();
        $poductsArray['IDS'][] = $arFields["ID"];
      }
    }
    $poductsArray['ids']=array_unique($poductsArray['ids']);

        return $poductsArray;
    }
}
