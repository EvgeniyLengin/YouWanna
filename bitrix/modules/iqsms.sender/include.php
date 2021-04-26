<?

Class Iqsms_Sender_Manager_Demo {

    static private $instance = null;

    private $bDemo = null;
    private $bDemoExpired = null;
    public $phoneRegExpStr = null;


    private function __construct()
    {
    }

    private function __clone()
    {
    }


    /**
     * Проверка демо режима
     * @return bool
     */
    final public function isDemo()
    {
        return false;
    }

    /**
     * Проверка не истекло ли время демо режима
     * @return bool
     */
    final public function isExpired()
    {
        return false;
    }

    protected function _getPhoneRegExp()
    {

        if(is_null($this->phoneRegExpStr))
        {
            $arPhoneRegExp = array(
                '7940[0-9]{7}' => '1.8;Абхазия',
                '[78]93[0-9]{8}' => '1.62;Мегафон',
                '[78]92[0-9]{8}' => '1.62;Мегафон',
                '[78]9[0-9]{9}' => '0.25;Россия',
                '770[012][0-9]{7}' => '1.8;Казахстан Kcell',
                '7778[0-9]{7}' => '1.8;Казахстан Kcell',
                '7775[0-9]{7}' => '1.8;Казахстан Kcell',
                '380[0-9]{9}' => '3.9;Украина',
                '375[0-9]{9}' => '1.8;Белоруссия',
                '77[0-9]{9}' => '1.8;Казахстан (база)',
                '66[89][0-9]{8}' => '2.25;Таиланд',
                '996[0-9]{9}' => '1.8;Киргизия',
                '374[0-9]{8}' => '1.8;Армения',
                '1[0-9]{10}' => '5.6;США',
                '861[0-9]{9,11}' => '3;Китай',
                '994[0-9]{9}' => '1.8;Азербайджан',
                '9989[0-9]{8}' => '1.8;Узбекистан',
                '201[0-9]{9}' => '1.8;Египет',
                '45[0-9]{8}' => '3;Дания',
                '3712[0-9]{7}' => '3;Латвия',
                '382[0-9]{8}' => '2.25;Черногория',
                '992[0-9]{9}' => '1.8;Таджикистан',
                '44[0-9]{10}' => '3;Великобритания',
                '373[0-9]{8}' => '3;Молдова',
                '49[0-9]{10,11}' => '3.75;Германия',
                '46[0-9]{5,11}' => '4;Швеция',
                '420[0-9]{9}' => '3;Чехия'
            );

            $regExp = array();
            foreach($arPhoneRegExp as $reg => $name)
            {
                $regExp[] = '^'.$reg.'$';
            }

            $this->phoneRegExpStr = '/'.implode('|', $regExp).'/';
        }


        return $this->phoneRegExpStr;
    }

    /**
     * Проверка правильноси номера
     * @param $phone
     *
     * @return bool
     */
    public function isValidPhone($phone)
    {
        $phone = $this->getPreparePhone($phone);

        if(preg_match($this->_getPhoneRegExp(), $phone, $match))
        {
            return true;
        }
        return false;
    }

    /**
     * Подготовка номера телефона
     *
     * @param $phone
     *
     * @return mixed
     */
    public function getPreparePhone($phone)
    {
        $phone = preg_replace('/[^\d]+/', '', $phone);

        if(preg_match($this->_getPhoneRegExp(), $phone, $match))
        {
            if(preg_match('/^893[0-9]{8}$|^892[0-9]{8}$|^89[0-9]{9}$/', $match[0], $match2))
            {
                return preg_replace('/^8/', '7', $match2[0]);
            }
            return $match[0];
        }
        return $phone;
    }


    /**
     * Единый формат ошибок для записи в базу, для быстрого анализа неполадок
     *
     * @param array $errors
     *
     * @return string
     */
    protected function getCommentFromErrors($errors = array())
    {
        $comment = '';
        foreach ($errors as $error) {
            $comment .= $error->getMessage() . "\r\n>>>>>>>>\r\n" . var_export($error->getMore(), true) . "\r\n<<<<<<<<<\r\n---------\r\n\r\n";
        }

        return $comment;
    }

    /**
     * Замена в шаблоне плэйсхолдеров на действительные значения
     *
     * @param       $arTemplate
     * @param array $arFields
     */
    protected function prepareTemplate(&$arTemplate, $arFields = array())
    {
        if ($arSiteData = $this->getSiteData()) {
            $arFields['SITE_NAME'] = $arSiteData['SITE_NAME'];
            $arFields['SERVER_NAME'] = $arSiteData['SERVER_NAME'];
        }
        foreach ($arFields as $find => $replacement) {
            $arTemplate['PHONE'] = trim(preg_replace('/#' . trim($find) . '#/', (string) $replacement, $arTemplate['PHONE']));
            $arTemplate['TEXT'] = trim(preg_replace('/#' . trim($find) . '#/', (string) $replacement, $arTemplate['TEXT']));
        }
    }



    /**
     * Отправка сообщения и добавление при успешном результате даных в базу
     *
     * @param      $phone
     * @param      $text
     * @param null $template
     *
     * @return \Iqsms\Sender\Result
     */
    protected function sendSms($phone, $text, $site_id, $template = null, $schedule = null, $sender = null)
    {
        global $USER;

        if(is_null($sender))
        {
            $sender = $this->getService($site_id)->getDefaultSender();
        }

        if (!$this->isValidPhone($phone)) {

            return $this->_getObjResult($this->_getObjError($this->getMsg('MANAGER.ERROR_INVALID_PHONE'), $this->_getConst('ERROR_INVALID_PHONE'), array(
                'PHONE'          => $phone,
                'PHONE_PREPARED' => $this->getPreparePhone($phone),
                'TEXT'           => $text,
                'SITE_ID'        => $site_id,
                'TEMPLATE'       => $template
            )));
        }

        $phone = $this->getPreparePhone($phone);

        if ($this->_getObj('bDebug')) {
            $result = $this->_getObjResult($this->_getConst('SMS_STATUS_DELIVERED'));
            $result->setMore('phone', $phone);
            $result->setMore('text', $text);
            $result->setMore('template', $template);


            $resSaveSms = $this->_getObj('oManagerTable')->add(array(
                'PHONE'      => $this->getPreparePhone($phone),
                'TEXT'       => $text,
                'CREATED'    => $this->_getNewObj('\Bitrix\Main\Type\DateTime'),
                'SCHEDULE'    => $schedule,
                'SENDER'    => $sender,
                'STATUS'     => $result->getResult(),
                'TYPE'    => $template,
                'COMMENT'    => $this->getMsg('MANAGER.SEND_SMS_DEBUG_MODE'),
                'SITE_ID'    => $site_id
            ));
            if (!$resSaveSms->isSuccess()) {
                $result->setMore('save_sms_error', $resSaveSms->getErrorMessages());
            }

            return $result;
        }

        // отправка
        $arSms = array(
            'PHONE'      => $phone,
            'TEXT'       => $text,
            'CREATED'    => $this->_getNewObj('\Bitrix\Main\Type\DateTime'),
            'SCHEDULE'    =>  $schedule,
            'SENDER'    => $sender,
            'STATUS'     => $this->_getConst('SMS_STATUS_WAIT'), //значение поумолчанию
            'TYPE'    => $template,
            'SITE_ID'    => $site_id
        );



        $resSaveSms = $this->_getObj('oManagerTable')->add($arSms);
        if (!$resSaveSms->isSuccess()) {
            return $this->_getObjResult($this->_getObjError($resSaveSms->getErrorMessages(), 'save_sms_error', $arSms));
        }

        $result = $this->_getObjResult($this->_getConst('SMS_STATUS_WAIT'));
        $result->setMore('phone', $phone);
        $result->setMore('text', $text);
        $result->setMore('template', $template);
        $result->setMore('id', $resSaveSms->getId());

        $this->arSMSId[] = $resSaveSms->getId();

        return $result;
    }

}

?>