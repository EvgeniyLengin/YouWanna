<?php

phpinfo()


// подключение служебной части пролога
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
use Bitrix\Main\Web\HttpClient;

//айди заказа
$currentOrderId = 4444;
//общая цена
$currentOrderPrice = 848;
//  Юмя юзера
$userNameord = "Валентин";
// Емейл юзера
$userEmailord "ze@german-web.ru";
//Название товара
$productCurrentName "тапочки";
//Количество
$tmp_auantity ="1";

$productFinal = "";

foreach ($productInfo as $key => $value) {
    $productFinal   .= "<li>".$value['NameProduct']."."." Размер ".$value['SIZE']." Цвет - ".$value['COLOR'].". </li>";
}


$to      = $userEmailord;
$subject = 'Ващ заказ №'.$currentOrderId;
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
                <td style="width:100%; text-align:center; font-size:16px; letter-spacing:0.1em;">MOSCOW</td>
            </tr>
            <tr>
                <td style="width:100%; text-align:center; font-size:18px;font-weight:normal; padding-top:25px; padding-bottom: 24px; border-bottom: 1px solid lightgray;">Здравствуйте,'.$userNameord.'! </td> <br>
            </tr>
            <tr style="text-align:left;">
                <td style="padding-top: 35px; padding-bottom: 20px;">Вы оформили заказ № 12345 от dd.mm.yyyy на сайте www.YOU-WANNA.ru</td>
            </tr>
            <tr>
                <td><b>Состав заказа:</b></td> <br>
                <td>
                    <ul>'.$productFinal.'
                    </ul>
                </td>
                <br>
                <td><b>Общая сумма:'.$currentOrderId.' р</b></td>
            </tr>
            <tr>
                <td style="padding-top: 25px; padding-bottom: 20px; text-align: center;">
                    Посмотреть Ваш заказ Вы можете в Личном Кабинете:
                </td>
                <br>
                <td style="padding-bottom: 45px; border-bottom: 1px solid lightgray;">
                    <a href="https://you-wanna.ru/personal/order/" style="display:block; width:250px; height:44px; background-color: #000000; color: #FFFFFF; text-decoration:none; text-align:center; vertical-align: middle; margin:0 auto; line-height: 41px;">Перейти в личный кабинет</a>
                    </td>
            </tr>
            <tr>
                <td style="text-align:center; color: lightgray; font-size:14px; padding-top: 25px;">С уважением, команда YOU WANNA.</td> <br>
                 <td style="text-align:center; padding-top:10px;">
                 <a href="https://www.facebook.com/youwannamoscow/" target="_blank"> <img style="max-width:25px" src="https://you-wanna.ru/facebook.png" /></a>
                 <a href="https://www.instagram.com/youwanna_official/" target="_blank"> <img style="max-width:25px" src="https://you-wanna.ru/in1.png" /></a>
                 </td> <br>

                 <td style="text-align:center; color: lightgray; font-size:14px; padding-top: 10px;" >© 2015—2020 YOU WANNA</td>
            </tr>


        </table>
    </body>
</html>
';
$headers = 'From: sale@you-wanna.ru' . "\r\n" .
 'Content-type: text/html; charset=utf-8 \r\n'.
    'Reply-To: sale@you-wanna.ru' . "\r\n" .
    'X-Mailer: PHP/' . phpversion();

$ss = mail($to, $subject, $message, $headers);
if (!$ss) {
    echo "net";
} else {
    echo "Отправлено успешно";
}


// подключение служебной части эпилога
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");
