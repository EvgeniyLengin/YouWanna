<?php
$to      = 'ze@german-web.ru';
$subject = 'the subject';
$message = "


<html>
<head>
   <meta http-equiv=\'Content-Type\' content=\'text/html; charset=utf-8\' />
  <title>Ожидается оплата</title>

</head>
<body>
<table cellspacing=0 style='width:100%;max-width:600px;font-family:Verdana;color:#5e5e5e;font-size:14px;'>
<tr style='height:80px;'>
   <td style='padding:0 0 0 30px;'><img src='mail-img/logo.png' alt='logo'></td>
   <td style='padding:0 30px 0 0;text-align:right;'>geo-map.net</td>
</tr>


<tr style='height:80px;background: #f7f7f7;'>
   <td style='padding:0 0 0 30px;''><img src='mail-img/logo.png' alt='logo'></td>
   <td style='padding:0 30px 0 0;text-align:right;'>geo-map.net</td>
</tr>
</table>

   </body>
</html>


";








$headers = "From: sales@you-wanna.ru" . "\r\n" .
 "Content-type: text/html; charset=utf-8 \r\n".
    "Reply-To: sales@you-wanna.ru" . "\r\n" .
    "X-Mailer: PHP/" . phpversion();



$sendMail = mail($to, $subject, $message, $headers);

if ($sendMail) {
    echo 'yes';
} else {
    echo 'no';
}
?>
