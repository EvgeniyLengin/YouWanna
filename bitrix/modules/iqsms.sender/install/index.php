<?
    use Bitrix\Main\Localization\Loc as Loc;

    Loc::loadMessages(__FILE__);


    class iqsms_sender extends CModule
    {

        var $MODULE_ID = "iqsms.sender";
        var $PARTNER_NAME = "iqsms";
        var $PARTNER_URI = "http://iqsms.ru/";

        var $MODULE_VERSION;
        var $MODULE_VERSION_DATE;
        var $MODULE_NAME;
        var $MODULE_DESCRIPTION;
        var $PARTNER_ID = "iqsms";

        /**
         * Массив зависимостей, для обработки событий других модулей
         * @var array
         */
        private $arModuleDependences = array(

//            array('main', 'OnBuildGlobalMenu', '\Iqsms\Sender\Handler', 'main_onBuildGlobalMenu'),

            // Пользователи
            //регистрация
            array('main', 'OnBeforeUserAdd', '\Iqsms\Sender\Handler', 'main_OnBeforeUserAdd', 100000),
            //array('main', 'OnAfterUserAdd', '\Iqsms\Sender\Handler', 'main_OnAfterUserAdd', 100000),

            //изменение пароля
//            array('main', 'OnBeforeUserUpdate', '\Iqsms\Sender\Handler', 'main_OnBeforeUserUpdate', 100000),
//            array('main', 'OnAfterUserUpdate', '\Iqsms\Sender\Handler', 'main_OnAfterUserUpdate', 100000),

            // заказы
            array('main', 'OnBeforeEventAdd', '\Iqsms\Sender\Handler', 'main_OnBeforeEventAdd', 100000),

            //D7
            array('sale', 'OnShipmentTrackingNumberChange', '\Iqsms\Sender\Handler', 'sale_OnShipmentTrackingNumberChange', 100000),
            array('sale', 'OnSaleStatusOrderChange', '\Iqsms\Sender\Handler', 'sale_OnSaleStatusOrderChange', 100000),
            array('sale', 'OnSaleOrderCanceled', '\Iqsms\Sender\Handler', 'sale_OnSaleOrderCanceled', 100000),
            array('sale', 'OnSaleOrderPaid', '\Iqsms\Sender\Handler', 'sale_OnSaleOrderPaid', 100000),
            array('sale', 'OnSaleOrderSaved', '\Iqsms\Sender\Handler', 'sale_OnSaleOrderSaved', 100000),

            //old
            array('sale', 'OnBeforeOrderAdd', '\Iqsms\Sender\Handler', 'sale_OnBeforeOrderAdd', 100000),
            array('sale', 'OnOrderAdd', '\Iqsms\Sender\Handler', 'sale_OnOrderAdd', 100000),
            array('sale', 'OnBeforeOrderUpdate', '\Iqsms\Sender\Handler', 'sale_OnBeforeOrderUpdate', 100000),
            array('sale', 'OnOrderUpdate', '\Iqsms\Sender\Handler', 'sale_OnOrderUpdate', 100000),
            array('sale', 'OnSalePayOrder', '\Iqsms\Sender\Handler', 'sale_OnSalePayOrder', 100000),
            array('sale', 'OnSaleCancelOrder', '\Iqsms\Sender\Handler', 'sale_OnSaleCancelOrder', 100000),
            array('sale', 'OnSaleStatusOrder', '\Iqsms\Sender\Handler', 'sale_OnSaleStatusOrder', 100000),

        );


        public function __construct()
        {
            include(__DIR__ . '/version.php');

            $this->MODULE_DIR = \Bitrix\Main\Loader::getLocal('modules/iqsms.sender');

            $this->isLocal = !!strpos($this->MODULE_DIR, '/local/modules/');

            $this->MODULE_NAME = Loc::getMessage($this->MODULE_ID . '_MODULE_NAME');
            $this->MODULE_DESCRIPTION = Loc::getMessage($this->MODULE_ID . '_MODULE_DESCRIPTION');
            $this->PARTNER_NAME = GetMessage('iqsms.sender_PARTNER_NAME');
            $this->PARTNER_URI = GetMessage('iqsms.sender_PARTNER_URI');
            $this->MODULE_VERSION = empty($arModuleVersion['VERSION']) ? '' : $arModuleVersion['VERSION'];
            $this->MODULE_VERSION_DATE = empty($arModuleVersion['VERSION_DATE']) ? '' : $arModuleVersion['VERSION_DATE'];



        }

        function DoInstall()
        {
            RegisterModule($this->MODULE_ID);
            $this->InstallDB();
            $this->InstallFiles();
            $this->InstallAgents();
            $this->InstallDependences();
            $this->InstallTemplates();

            return true;
        }

        function DoUninstall()
        {
            $this->UnInstallDB();
            $this->UnInstallFiles();
            $this->UnInstallAgents();
            $this->UnInstallDependences();
            $this->UnInstallTemplates();

            COption::RemoveOption($this->MODULE_ID);
            UnRegisterModule($this->MODULE_ID);

            return true;
        }

        /**
         * Добавление в базу необходимых таблиц для работы модуля
         * @return bool
         */
        function InstallDB()
        {
            global $DB, $DBType, $APPLICATION;

            //         Database tables creation
            $DB->RunSQLBatch(dirname(__FILE__) . "/db/mysql/install.sql");

            return true;
        }


        /**
         * Удаление таблиц модуля
         * @return bool|void
         */
        function UnInstallDB()
        {
            global $DB, $DBType, $APPLICATION;

            $DB->RunSQLBatch(dirname(__FILE__) . "/db/mysql/uninstall.sql");

            return true;
        }


        /**
         * Копирование файлов
         * @return bool|void
         */
        function InstallFiles($arParams = array())
        {
            // копируем рядом
            if ($this->isLocal) {
                CopyDirFiles($this->MODULE_DIR . "/install/components/", $_SERVER["DOCUMENT_ROOT"] . "/local/components/", true, true);
            } else {
                CopyDirFiles($this->MODULE_DIR . "/install/components/", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/components/", true, true);
            }

            @copy($this->MODULE_DIR . "/install/css/iqsms.sender.css", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/themes/.default/iqsms.sender.css");

            if (file_exists($path = $this->MODULE_DIR . '/admin')) {
                if ($dir = opendir($path)) {
                    while (false !== $item = readdir($dir)) {
                        if (in_array($item, array('.', '..', 'menu.php')))
                            continue;

                        if (!file_exists($file = $_SERVER['DOCUMENT_ROOT'] . '/bitrix/admin/' . $this->MODULE_ID . '_' . $item))
                            file_put_contents($file, '<' . '? require($_SERVER["DOCUMENT_ROOT"]."/' . ($this->isLocal ? 'local' : 'bitrix') . '/modules/' . $this->MODULE_ID . '/admin/' . $item . '");?' . '>');
                    }
                }
            }


            if (file_exists($path = $this->MODULE_DIR . '/tools')) {
                if ($dir = opendir($path)) {

                    CheckDirPath($_SERVER["DOCUMENT_ROOT"] . "/bitrix/tools/" . $this->MODULE_ID . '/');

                    while (false !== $item = readdir($dir)) {
                        if (in_array($item, array('.', '..')))  continue;

                        $file = $_SERVER['DOCUMENT_ROOT'] . '/bitrix/tools/' . $this->MODULE_ID . '/' . $item;

                        if (!file_exists($file))
                            file_put_contents($file, '<' . '? require($_SERVER["DOCUMENT_ROOT"]."/'
                                . ($this->isLocal ? 'local' : 'bitrix') . '/modules/' . $this->MODULE_ID . '/tools/' . $item . '");?' . '>');
                    }
                }
            }
            return true;
        }

        /**
         * Удаление файлов
         * @return bool|void
         */
        function UnInstallFiles()
        {

            if (is_dir($this->MODULE_DIR . "/install/components/" . $this->PARTNER_ID . "/")) {
                $d = dir($this->MODULE_DIR . "/install/components/" . $this->PARTNER_ID . "/");
                while ($entry = $d->read()) {
                    if ($entry == '.' || $entry == '..') continue;

                    DeleteDirFilesEx('/local/components/' . $this->PARTNER_ID . '/' . $entry . '/');
                    DeleteDirFilesEx('/local/components/' . $this->PARTNER_ID . '/' . $entry . '/');
                }
                $d->close();
            }

			@unlink($_SERVER["DOCUMENT_ROOT"] . "/bitrix/themes/.default/iqsms.sender.css");


			if (file_exists($path = $this->MODULE_DIR . '/admin')) {
                if ($dir = opendir($path)) {
                    while (false !== $item = readdir($dir)) {
                        if (in_array($item, array('.', '..', 'menu.php')))
                            continue;

                        if (file_exists($file = $_SERVER['DOCUMENT_ROOT'] . '/bitrix/admin/' . $this->MODULE_ID . '_' . $item))
                            unlink($file);
                    }
                }
            }

            if (file_exists($path = $this->MODULE_DIR . '/tools')) {
                if ($dir = opendir($path)) {
                    while (false !== $item = readdir($dir)) {
                        if (in_array($item, array('.', '..'))) continue;

                        if (file_exists($file = $_SERVER['DOCUMENT_ROOT'] . '/bitrix/tools/' . $this->MODULE_ID . '/' . $item))
                        {
                            unlink($file);
                        }
                    }
                }
            }



            return true;
        }

        /**
         * Установка агентов
         */
        public function InstallAgents()
        {
            $oAgent = new CAgent();
            $oAgent->AddAgent('\Iqsms\Sender\Agent::cleanSmsHistory();', $this->MODULE_ID, 'N', 3600);
            $oAgent->AddAgent('\Iqsms\Sender\Agent::checkSmsStatus();', $this->MODULE_ID, 'N', 50);
//            $oAgent->AddAgent('\Iqsms\Sender\Agent::agentSendSmsQueue();', $this->MODULE_ID, 'N', 50);
        }

        /**
         * Удаление агентов
         */
        public function UnInstallAgents()
        {
            $oAgent = new CAgent();
            $oAgent->RemoveModuleAgents($this->MODULE_ID);
        }


        /**
         * Установка обработчиков событий
         */
        public function InstallDependences()
        {
            foreach ($this->arModuleDependences as $item) {
                if (count($item) < 4) continue;

                RegisterModuleDependences($item[0], $item[1], $this->MODULE_ID, $item[2], $item[3], (isset($item[4]) ? intval($item[4]) : 100));
            }
        }

        /**
         * Удаление обработчиков событий
         */
        public function UnInstallDependences()
        {
            foreach ($this->arModuleDependences as $item) {
                if (count($item) < 4) continue;

                UnRegisterModuleDependences($item[0], $item[1], $this->MODULE_ID, $item[2], $item[3]);
            }
        }


        /**
         * Добавление типов шаблонов смс и самих шаблонов
         */
        public function InstallTemplates()
        {
            \Bitrix\Main\Loader::includeModule($this->MODULE_ID);

            $oTemplateSite = new \Iqsms\Sender\Template\SiteTable();
            $oTemplate = new \Iqsms\Sender\TemplateTable();

            $arSite = array();
            $dbr = \CSite::GetList($by = 'sort', $order = 'asc');
            while ($ar = $dbr->Fetch()) {
                $arSite[] = $ar['ID'];
            }


            $arTypes = array(

                // Пользователи
//                'MAIN_USER_ADD' => array('main'), // регистрация пользователя
//                'MAIN_USER_CHANGEPASSWORD' => array('main'), //изменение пароля

                // Интернет-магазин
                'ORDER_NEW' => array('main'), //новый зазказ
                'TRACKING_NUMBER' => array('main'), //идентификтаор отправления
                'ORDER_CANCELED' => array('main'), //
                'ORDER_PAYED' => array('main'), //
                'ORDER_STATUS' => array('main'), //
                'SEND_PASSWORD' => array('main'), //

            );

            foreach ($arTypes as $code => $arTemplates) {
                if (!is_array($arTemplates)) continue;

                // Шаблоны
                foreach ($arTemplates as $template) {

                    $resTemplate = $oTemplate->add(array(
                        'TYPE' => $code,
                        'NAME' => GetMessage($this->MODULE_ID . '.' . $code . '.TEMPLATE.NAME.' . $template),
                        'ACTIVE' => true,
                        'PHONE' => GetMessage($this->MODULE_ID . '.' . $code . '.TEMPLATE.PHONE.' . $template),
                        'TEXT' => GetMessage($this->MODULE_ID . '.' . $code . '.TEMPLATE.TEXT.' . $template),
						'EVENT' => false
                    ));

                    //добавляем
                    foreach ($arSite as $sid) {
                        $oTemplateSite->add(array(
                            'TID' => intval($resTemplate->getId()),
                            'SID' => $sid
                        ));
                    }
                }
            }
        }

        /**
         * Добавление типов шаблонов смс и самих шаблонов
         */
        public function UnInstallTemplates()
        {
            //        \Bitrix\Main\Loader::includeModule($this->MODULE_ID);
            //        $oTemplateType = new \Iqsms\Sender\Template\TypeTable();
            //        $oTemplate = new \Iqsms\Sender\TemplateTable();

            // это не нужно, так как эти данные хранятся в базе, а во время удаления таблицы из базы стираются

        }

    }

