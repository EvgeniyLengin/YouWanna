 <?php

$pathForDir = $_SERVER['DOCUMENT_ROOT']."/basketfiles/";

$userFiles = scandir($pathForDir);
unset($userFiles['0'],$userFiles['1']);

$time = time();

foreach ($userFiles as $key => $value) {

    $filename = $value;
    $filecontent = unserialize(file_get_contents($pathForDir.$filename));


    $nameUser = $filecontent['USERNAME'];
    $emailUser = $filecontent['EMAILUSER'];
    //Скипаем если файл редактировался раньше 4х часов.
    if (file_exists($pathForDir.$filename)) {

        $time_cur = filectime($pathForDir.$filename);

        $time_diff = $time- $time_cur;
        $time_diff = (int)$time_diff;

        $stopTIME = 3600;


        if ((int)$time_diff < (int)$stopTIME) {
            echo "C момента брошенной корзины ".$nameUser."  &&  ".$emailUser." не прошло 1-го часа<br>";
            echo "Текущий счетчик: ".$time_diff." ЦЕЛЬ - 3600.  При достижении указанного кол-ва письмо отправится в начале следующего часа по MSK<br>";
            continue;
        }
// 14400
    }

    $basketBase64 = $filecontent['KORZINA'];
    $basketSerialize =  base64_decode($basketBase64);
    $basketClear = unserialize($basketSerialize);
    $checkCount = count($basketClear);

    // Собираем данные в письмо письмо.
    $productForMessage = "";
    $tmpPrice = 0;




    foreach ($basketClear as $key => $value) {

    $tmpPrice = $tmpPrice + $value['PRICE'];
    $priceItem = round($value['PRICE']);
    $CheckIMG = $value['CHECKIMAGE'];
    $articleItem = $value['ARTICLE'];


    if ($CheckIMG == "Y") {
        $url = $value['COLORITEM'];
        $colorText = "<td>  <img style='width:20px; margin-top:5px; height:20px; border-radius:50%;' src='https://you-wanna.ru/".$url."'>  </td>";



    } else {
        $colorText = "<td style='width: 40px;'>".$value['COLORITEM']."</td>";
    }

    $productForMessage .= "


    <table class='iksweb' style='margin-top: 30px;'>
        <tbody>
            <tr style='font-size:14px;'>
                <td rowspan='6' style='width: 140px;  '><img src=".$value['URLPHOTO']." style='max-width: 120px;width: 120px;height: 180px;object-fit: cover;'></td>
                <td colspan='2' style='font-size: 14px; line-height: 16px; width:140px;  ' class='headingz fixwidth'>".$value['nazvanie']."</td>
            </tr>
            <tr>
                <td colspan='2' style='font-size:12px; color:rgba(0, 0, 0, 0.5); padding-bottom:10px; width:140px;  ' class='articlee fixwidth'>Артикул: ".$articleItem."</td>
            </tr>
            <tr class='fixtd' style='font-size:14px;'>
                <td style='width:100px;  ' class='fixtdwidth'>Цвет</td>
                ".$colorText."
            </tr>
            <tr style='font-size:14px;'>
                <td class='fixtdwidth' style='width:100px;  '>Размер</td>
                <td style='width: 40px;' class='twofixwidth'>".$value['SIZE']."</td>
            </tr>
            <tr>
                <td style='width:100px;  ' class='fixtdwidth'>Количество</td>
                <td style='width: 40px;' class='twofixwidth'>".$value['QUANT']."</td>
            </tr>
            <tr>
                <td colspan='2' style='width:140px;   padding-top: 10px; font-weight:600;' class='priceitem fixwidth'>".$priceItem." Р</td>
            </tr>
        </tbody>
    </table>


    ";
    }




    //Собрали товары - отправляем письмо
    $to      = "$emailUser";
    // $to      = "imnigina@gmail.com";
    // $to      = "ze@german-web.ru";

    $subject = "$nameUser! Ваша корзина уже готова";
    $message = "
    <html>
    <head>
       <meta http-equiv=\'Content-Type\' content=\'text/html; charset=utf-8\' />
      <title>Ожидается оплата</title>

    </head>
        <body style=''>
            <table style='max-width:600px;   box-sizing: border-box; width:100%;'>
                <tr style='max-width:100%; width: 100%; color:#1F1B20;'>
                    <td style='width:100%; text-align:center; font-size:25px;font-weight:bold; letter-spacing:5px;'>YOU WANNA</td> <br>

                </tr>
                <tr style='max-width:100%; width: 100%; color:#1F1B20;'>
                    <td style='width:100%; text-align:center; font-size:16px; letter-spacing:0.1em;'>MOSCOW</td>
                </tr>
                <tr>
                    <td style='width:100%; text-align:center; font-size:14px;font-weight:normal; padding-top:25px; padding-bottom: 24px; border-bottom: 1px solid lightgray;'>Здравствуйте, ".$nameUser." </td> <br>
                </tr>

                <tr style=''>
                    <td style='text-align:left; padding-top:10px;'>Мы сохранили Вашу корзину с понравившимися Вам моделями.</td>


                </tr>
                <tr style='margin-bottom:40px;'>
                    <td style='text-align:left; padding-top:0px; margin-bottom: 40px;'>Оформите заказ, пока они есть в наличии:</td>
                </tr>
                </table>



                ".$productForMessage."



                <table style='width: 100%;border:0px;max-width: 612px; color:black;  padding-top:0px;'>
                <tr style='text-align:left; width:100%; margin-top:25px;margin-bottom:25px;display:block;'>
                <td style='text-align:left; width:100%; margin-top:25px;margin-bottom:25px;display:block;'>Посмотреть Вашу корзину Вы можете в Личном Кабинете:</td>
                </tr>
               <tr>
                   <td style='padding-bottom: 45px; border-bottom: 1px solid lightgray;'>
                       <a href='https://you-wanna.ru/personal/' style='display:block; width:260px; height:44px;float:left; background-color: #000000; color: #FFFFFF; text-decoration:none; text-align:center; vertical-align: middle; margin:0 auto; line-height: 41px;padding-left:10px; padding-right:10px;'>Перейти в личный кабинет</a>
                   </td>
               </tr>

               <tr>
                   <td style='text-align:center; color: lightgray; font-size:14px; padding-top: 25px;'>С уважением, команда YOU WANNA.</td>
               </tr>
                   <tr>
                        <td style='text-align:center; padding-top:10px;'>
                        <a href='https://www.facebook.com/youwannamoscow/' target='_blank'> <img style='max-width:25px' src='https://you-wanna.ru/facebook.png' /></a>
                        <a href='https://www.instagram.com/youwanna_official/' target='_blank'> <img style='max-width:25px' src='https://you-wanna.ru/in1.png' /></a>
                        </td>
                    </tr>
               <tr>
                    <td style='text-align:center; color: lightgray; font-size:14px; padding-top: 10px;' >© 2015—2020 YOU WANNA</td>
               </tr>


           </table>
       </body>
    </html>
    ";
    $headers = 'From: sales@you-wanna.ru' . "\r\n" .
     'Content-type: text/html; charset=utf-8 \r\n'.
        'Reply-To: sales@you-wanna.ru' . "\r\n" .
        'X-Mailer: PHP/' . phpversion();



    $ss = mail($to, $subject, $message, $headers);
    if (!$ss) {
        echo "ne otpravleno";
    } else {

        echo "Отправлено успешно для $nameUser  на $emailUser";

        echo "<br>";
        // die('STOP');
        unlink($pathForDir.$filename);

        echo "Запись о пользователе была удалена ".$pathForDir.$filename;
        echo "<br>";
    }






}






 ?>
