<?require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog.php");

//
//
// $today = date("m.d.y");
// $file = "/home/bitrix/www/bitrix/forbasket.txt";
// $textfile = file_get_contents($file);
// $today = trim($today);
// $textfile = trim($textfile);
// // echo $today;
//
// if($today == $textfile) {
//
//         // echo "Y|$today|$textfile|";
// } else {
//         // echo "N|$today|$textfile|";
//     file_put_contents($file, $today);
//
//
//
//
//
//
//
//
//
//
//     $filter = array("UF_CHECKCRONN" => "1");
//     $arParams["SELECT"] = array( "UF_*");
//     $rsUsers = CUser::GetList(($by="ID"), ($order="asc"), $filter, $arParams);
//
//     while ($arUser = $rsUsers->Fetch()):
//
//        $idUser = $arUser['ID'];
//        $nameUser = $arUser['NAME'];
//        if ($nameUser == "") {
//          $nameUser = $arUser['LAST_NAME'];
//        }
//
//        $emailUser = $arUser['EMAIL'];
//        $BASKETKODE = $arUser['UF_BASKETCODE'];
//
//        $unBase = base64_decode($BASKETKODE);
//        $basketMassive = unserialize($unBase);
//
//
//
//     // Собираем данные в письмо письмо.
//     $productForMessage = "";
//     $tmpPrice = 0;
//     foreach ($basketMassive as $key => $value) {
//
//     $tmpPrice = $tmpPrice + $value['PRICE'];
//     $priceItem = round($value['PRICE']);
//     // $value['URLPHOTO']
//     // $value['nazvanie'].
//     // $value['SIZE']
//     // $value['QUANT']
//     // $priceItem.
//     $productForMessage .= '
//     <div style="max-width: 612px;width:100%; display:block;height:auto; min-height:175px;clear:both;margin: 0 auto;">
//         <div style="width:100%; margin:0 auto;height:auto;">
//                 <div style="float:left; min-width:103px; width:100%; max-width:120px;">
//                     <img src='.$value['URLPHOTO'].' style="width:100%;">
//                 </div>
//                 <div style="float:left;  height:auto;padding-left:25px; width:100%; min-width:160px; max-width:180px;">
//                     <div style="width:100%; text-transform:uppercase; font-size:12px;">'.$value['nazvanie'].'</div>
//                     <div style="clear:both; height: 20px; line-height: 32px;width:100%;"><div style="float:left; width:110px;">Размер</div><div style="float:left">'.$value['SIZE'].'</div></div>
//                     <div style="clear:both; height: 20px; line-height: 32px;width:100%;"><div style="float:left; width:110px;">Количество</div><div style="float:left">'.$value['QUANT'].'</div></div>
//                     <div style="clear:both; height: 20px; line-height: 32px;width:100%;"><div style="float:left; width:110px;">Цвет</div><div style="float:left">'.$value['COLOR'].'</div></div>
//                     <div style="clear:both; height: 20px; line-height: 32px;width:100%;font-weight:600;">'.$priceItem.' Р</div>
//
//                 </div>
//
//         </div>
//     </div>
//
//     ';
//     }
//
//
//     //Собрали товары - отправляем письмо
//     $to      = "$emailUser";
//     // $to      = "ze@german-web.ru";
//     $subject = "$nameUser! Ваша корзина уже готова";
//     $message = '
//     <html>
//     <head>
//        <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />
//       <title>Ожидается оплата</title>
//
//     </head>
//         <body style="">
//             <table style="width: 100%;border:0px;max-width: 612px; color:black; padding:10px; padding-top:0px;">
//                 <tr style="max-width:100%; width: 100%; color:#1F1B20;">
//                     <td style="width:100%; text-align:center; font-size:25px;font-weight:bold; letter-spacing:5px;">YOU WANNA</td> <br>
//
//                 </tr>
//                 <tr style="max-width:100%; width: 100%; color:#1F1B20;">
//                     <td style="width:100%; text-align:center; font-size:16px; letter-spacing:0.1em;">MOSCOW</td>
//                 </tr>
//                 <tr>
//                     <td style="width:100%; text-align:center; font-size:18px;font-weight:normal; padding-top:25px; padding-bottom: 24px; border-bottom: 1px solid lightgray;">Здравствуйте, '.$nameUser.'! </td> <br>
//                 </tr>
//
//                 <tr style="margin-bottom:40px;">
//                     <td style="text-align:center; padding-top:10px;">Мы сохранили Вашу корзину с понравившимися Вам моделями.</td> <br>
//                     <td style="text-align:center; padding-top:10px; margin-bottom: 40px;">Оформите заказ, пока они есть в наличии:</td> <br>
//
//                 </tr>
//
//                 </table>
//
//                 <table style="width: 100%;border:0px;max-width: 612px; color:black; padding:10px; padding-top:0px;">
//                      '.$productForMessage.'
//                         <tr>
//                              <td colspan="2" style="text-align:left; padding-top:15px; padding-left: 36px;"><b>Итого: '.$tmpPrice.' р.</b></td>
//                              <td></td>
//                          </tr>
//                  </table>
//
//
//
//
//
//                  <table style="width: 100%;border:0px;max-width: 612px; color:black; padding:10px; padding-top:0px;">
//                  <tr style="text-align:center; width:100%; margin-top:25px;margin-bottom:25px;display:block;">
//                  <td style="text-align:center; width:100%; margin-top:25px;margin-bottom:25px;display:block;">Посмотреть Вашу корзину Вы можете в Личном Кабинете:</td>
//                  </tr>
//                 <tr>
//                     <td style="padding-bottom: 45px; border-bottom: 1px solid lightgray;">
//                         <a href="https://you-wanna.ru/personal/" style="display:block; width:205px; height:44px; background-color: #000000; color: #FFFFFF; text-decoration:none; text-align:center; vertical-align: middle; margin:0 auto; line-height: 41px;padding-left:10px; padding-right:10px;">Перейти в личный кабинет</a>
//                     </td>
//                 </tr>
//
//                 <tr>
//                     <td style="text-align:center; color: lightgray; font-size:14px; padding-top: 25px;">С уважением, команда YOU WANNA.</td>
//                 </tr>
//                     <tr>
//                          <td style="text-align:center; padding-top:10px;">
//                          <a href="https://www.facebook.com/youwannamoscow/" target="_blank"> <img style="max-width:25px" src="https://you-wanna.ru/facebook.png" /></a>
//                          <a href="https://www.instagram.com/youwanna_official/" target="_blank"> <img style="max-width:25px" src="https://you-wanna.ru/in1.png" /></a>
//                          </td>
//                      </tr>
//                 <tr>
//                      <td style="text-align:center; color: lightgray; font-size:14px; padding-top: 10px;" >© 2015—2020 YOU WANNA</td>
//                 </tr>
//
//
//             </table>
//         </body>
//     </html>
//     ';
//     $headers = 'From: sales@you-wanna.ru' . "\r\n" .
//      'Content-type: text/html; charset=utf-8 \r\n'.
//         'Reply-To: sales@you-wanna.ru' . "\r\n" .
//         'X-Mailer: PHP/' . phpversion();
//
//     $ss = mail($to, $subject, $message, $headers);
//     if (!$ss) {
//         echo "net";
//     } else {
//         $text = "";
//         echo "Отправлено успешно для $nameUser  на $emailUser";
//         $user = new CUser;
//         $user->Update($idUser, Array("UF_BASKETCODE" => $text));
//         $user->Update($idUser, Array("UF_CHECKCRONN" => false));
//         echo "<br>";
//         echo "Код корзины обнулен";
//         echo "<br>";
//         echo "Тригер установлен на false";
//     }
//
//     // ПИСЬМО ОТПРАВЛЕНО
//
//
//     endwhile;
//
//
// }
// print_r($homepage);




?>
