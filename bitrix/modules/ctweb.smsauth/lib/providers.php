<?
//В данном файле определяем список провайдеров, с которыми работаем
//Подключаем исполняемые файлы, по каждому провайдеру отдельно

class CTWEB_SMSAUTH_PROVIDERS {

    function __construct(){
        //Подключаем файлы с обработчиками
        self::includeFiles();
    }

    function getProviders(){
        //В данном файле определяем список провадеров, с которыми будем работать
        $arProviders = array(
            "smsru" => "sms.ru",
            "smscru" => "smsc.ru",
            "smsaeroru" => "smsaero.ru",
            "redsmsru" => "redsms.ru",
            "bytehandcom" => "bytehand.com",
            "iqsmsru" => "iqsms.ru",
            "infosmskaru" => "infosmska.ru",
            "p1smsru" => "p1sms.ru",
            "itsmsru" => "it-sms.ru",
            "prostorsmsru" => "prostor-sms.ru",
            "smssendingru" => "sms-sending.ru",
            "smsuslugiru" => "sms-uslugi.ru",
        );

        ksort($arProviders);

        //Сортируем массив по алфавиту
        asort($arProviders);

        return $arProviders;
    }

    function getProvcurl(){
        //В данном файле определяем список провадеров, с которыми будем работать
        $arProviders = array(
            "smsru",
            "smscru",
            "smsaeroru",
            "bytehandcom",
            "iqsmsru",
            "p1smsru",
            "itsmsru",
            "prostorsmsru",
            "smssendingru",
            "infosmskaru",
            "smsuslugiru",
        );

        return $arProviders;
    }

    function includeFiles(){
        $arProviders = self::getProviders();

        foreach($arProviders as $file=>$val){
            require_once('providers/cwsa_'.$file.'.php');
        }
    }

}