<?

	namespace Iqsms\Sender;

	const SMS_STATUS_SENT              = 1; //отправлено
	const SMS_STATUS_DELIVERED         = 2; //доставлено
	const SMS_STATUS_ERROR             = 3; //ошибка
	const SMS_STATUS_WAIT              = 4; //ожидание отправки

	const ERROR_SERVICE_INITIALIZATION = 1001; // не удалось проинициализировать, подключить, не найдне
	const ERROR_SERVICE_RESPONSE       = 1002; // неизвестный ответ сревиса
	const ERROR_SERVICE_CUSTOM         = 1003; // произвольная ошибка иная

	const ERROR_TEMPLATE_NOT_FOUND     = 2002; //не наден шаблон
	const ERROR_INVALID_PHONE          = 2003; // не верно указан номер телефона

	const ERROR_EVENT                  = 3001; // ошики связанные с событиями

	use Bitrix\Main\Application;
	use \Bitrix\Main\Entity;
	use Bitrix\Main\Localization\Loc;
	use Bitrix\Main\Type\DateTime;
	use Bitrix\Main\Loader;

	Loc::loadMessages(__FILE__);

	//include_once(dirname(__FILE__).'/../include.php');


	Class ManagerTable extends Entity\DataManager
	{

		public static
		function getFilePath()
		{
			return __FILE__;
		}

		public static
		function getTableName()
		{
			return 'iqsms_sender_list';
		}

		public static
		function getMap()
		{
			return array(
				new Entity\IntegerField('ID', array(
					'primary'      => true,
					'autocomplete' => true
				)),
				new Entity\StringField('PHONE', array(
					'required' => true
				)),
				new Entity\TextField('TEXT', array(
					'required' => true
				)),
				new Entity\TextField('COMMENT'), //  напрмиер режим отладки, содержимое ошибки
				new Entity\IntegerField('STATUS', array(
					'required'  => true,
					'validator' => function () {
						return array(
							new Entity\Validator\Range(0, 99)
						);
					}
				)),
				new Entity\DatetimeField('CREATED', array(
					'required' => true
				)),
				new Entity\DatetimeField('SCHEDULE'),
				new Entity\StringField('SENDER', array(
					'required' => true
				)),
				new Entity\StringField('TYPE'),
				new Entity\StringField('SITE_ID', array(
					'required'  => true,
					'validator' => function () {
						return array(
							new Entity\Validator\Range(2, 2)
						);
					}
				)),
				new Entity\TextField('PARAMS', array( // параметры для конкретных сервисов, чтобы хранить ккие то данные например для mainsms.ru - messageId
					'required'                => false,
					'save_data_modification'  => function () {
						return array(
							function ($value) {
								return serialize($value);
							}
						);
					},
					'fetch_data_modification' => function () {
						return array(
							function ($value) {
								return unserialize($value);
							}
						);
					}
				)),
				new Entity\ExpressionField('CNT', 'COUNT(ID)')
			);
		}

	}

	final Class Manager extends \Iqsms_Sender_Manager_Demo
	{

		static private $instance = null;

		protected $module_id = 'iqsms.sender';
		protected $bDebug = false; // режим отладки
		protected $arSmsTemplate = array(); // массивы шаблонов, чтобы не запрашивать повторно
		protected $arSmsTemplateEmail = array();
		protected $arService = array(); // массив проинициалиированных объектов сервисов
		protected $siteID = null;
		protected $arSiteData = array(); // данные по текущему сайту
		protected $arSMSId = array(); // Список ID добавленных смс для отправки


		protected $oOption = null; //параметры
		protected $oManagerTable = null; // объект для работы с таблицей, в окторой хранится история отправленных смс
		protected $oTemplate = null; // объект для работы с шаблонами


		private function __construct()
		{
			$this->oManagerTable = new ManagerTable();
			$this->oOption       = new \Bitrix\Main\Config\Option();

			// отладка
			$this->bDebug = ($this->getParam('DEBUG', 'N') == 'Y');

			// нициализация подклчюения к сервису, для каждого сайта
			$this->arService = array();
			$dbr             = \CSite::GetList($by = 'sort', $order = 'asc');
			while ($ar = $dbr->Fetch()) {
				$this->arService[$ar['ID']] = new \Iqsms\Sender\Service(
					$this->getParam('LOGIN', '', $ar['ID']),
					$this->getParam('PASS', '', $ar['ID']),
					$this->getParam('FROM', '', $ar['ID']),
					$ar['ID'],
					$ar['NAME']
				);
				$arSite[$ar['ID']]          = '[' . $ar['ID'] . '] ' . $ar['NAME'];
			}
		}

		public function reConnect()
		{
			// нициализация подклчюения к сервису, для каждого сайта
			$this->arService = array();
			$dbr             = \CSite::GetList($by = 'sort', $order = 'asc');
			while ($ar = $dbr->Fetch()) {
				$this->arService[$ar['ID']] = new \Iqsms\Sender\Service(
					$this->getParam('LOGIN', '', $ar['ID']),
					$this->getParam('PASS', '', $ar['ID']),
					$this->getParam('FROM', '', $ar['ID']),
					$ar['ID'],
					$ar['NAME']
				);
				$arSite[$ar['ID']]          = '[' . $ar['ID'] . '] ' . $ar['NAME'];
			}
		}

		public function __destruct()
		{
			$this->sendQueue();
		}

		public function getTable()
		{
			return $this->oManagerTable;
		}

		final function prepareEncoding($data)
		{
			if (self::isUTF()) return $data;
			return \Bitrix\Main\Text\Encoding::convertEncoding($data, 'WINDOWS-1251', 'UTF-8');
		}


		/**
		 * Отправка добавленных сообщений в базу, автоматически отправляется в деструкторе класса  __destruct
		 * @return bool
		 * @throws \Bitrix\Main\ArgumentException
		 * @throws \Exception
		 */
		private function sendQueue()
		{
			if (!count($this->arSMSId)) return false;

			$arSms = array();

			$dbr = $this->oManagerTable->getList(array(
				'filter' => array(
					'STATUS' => $this->_getConst('SMS_STATUS_WAIT'),
					'ID'     => $this->arSMSId
				),
				'order'  => array(
					'SITE_ID' => 'ASC',
					'CREATED' => 'ASC'
				)
			));
			while ($ar = $dbr->fetch()) {
				//"2008-07-12T14:30:01Z
				if (is_null($ar['SCHEDULE'])) {
					$arSms[$ar['SITE_ID']][0][$ar['ID']] = $ar;
				}
				else {
					$time = gmdate("Y-m-d\TH:i:s\Z", $ar['SCHEDULE']->getTimestamp());

					$arSms[$ar['SITE_ID']][$time][$ar['ID']] = $ar;
				}
			}

			foreach ($arSms as $siteId => $arTimeItems) {
				//готовим сообщения
				foreach ($arTimeItems as $t => $arItems) {

					$arFields = array(
						'messages' => array()
					);

					if ($t != 0) {
						$arFields['scheduleTime'] = $t;
					}

					$arItemParts = array_chunk($arItems, 200, true);
					foreach ($arItemParts as $arItemPart) {
						$arFields['messages'] = array();

						foreach ($arItemPart as $id => $arItem) {
							$arFields['messages'][] = array(
								"phone"    => $arItem['PHONE'],
								"clientId" => $id,
								"text"     => $arItem['TEXT'],
								"sender"   => $arItem['SENDER']
							);
						}

						$result = $this->getService($siteId)->sendPack($arFields);
						if ($result->isSuccess()) {
							$arSmsIdResult = $result->getResult();
							foreach ($arSmsIdResult['messages'] as $id => $smsResult) {
								$this->oManagerTable->update($id, array(
									'STATUS'  => $smsResult->getResult(),
									'COMMENT' => '',
									'PARAMS'  => ($smsResult->getMore('params') ? $smsResult->getMore('params') : '')
								));
							}
						}
						else {
							//						echo '<pre>';
							//						print_r($result->getErrors());
							//						echo '</pre>';
						}

						sleep(1);
					}
				}
			}


			$this->arSMSId = array();
		}

		/**
		 * @param null $site_id
		 *
		 * @return \Iqsms\Sender\Service
		 */
		public function getService($site_id = null)
		{
			if (is_null($site_id)) {
				$site_id = $this->getCurrentSiteId();
			}
			return $this->arService[$site_id];
		}

		private function __clone()
		{

		}

		/**
		 * @return Manager
		 */
		static public function getInstance()
		{
			if (is_null(self::$instance)) {
				$c              = __CLASS__;
				self::$instance = new $c();
			}
			return self::$instance;
		}


		/*  Добавление скриптов и стилей на страницы модуля */
		public function addAdminPageCssJs()
		{
			\CJSCore::Init(array('jquery'));
			$path = getLocalPath('modules/' . $this->module_id);
			if (file_exists($_SERVER["DOCUMENT_ROOT"] . $path . '/admin/css/style.css')) {
				echo '<style type="text/css" >' . file_get_contents($_SERVER["DOCUMENT_ROOT"] . $path . '/admin/css/style.css') . '</style>';
			}
			if (file_exists($_SERVER["DOCUMENT_ROOT"] . $path . '/admin/js/script.js')) {
				echo '<script type="text/javascript" >' . file_get_contents($_SERVER["DOCUMENT_ROOT"] . $path . '/admin/js/script.js') . '</script>';
			}
		}


		/**
		 * Если включен режим отладки
		 * @return bool
		 */
		public function isDebug()
		{
			return $this->bDebug;
		}


		public function send($phone, $text, $site_id = null, $schedule = null, $sender = null)
		{
			// подклчюение сервиса активного

			if (is_null($site_id)) {
				$site_id = $this->getCurrentSiteId();
			}

			$arFields = array(
				'phone'   => $phone,
				'text'    => $text,
				'site_id' => $site_id
			);

			$event = new \Bitrix\Main\Event($this->module_id, "OnBeforeSend", array($arFields));
			$event->send();

			foreach ($event->getResults() as $eventResult) {
				$arParameters = $eventResult->getParameters();

				switch ($eventResult->getType()) {
					case \Bitrix\Main\EventResult::ERROR: {

						$msg = (isset($arParameters['error_msg']) ? $arParameters['error_msg'] : $this->getMsg('MANAGER.EVENT_ONBEFORE_SEND_ERROR_EVENTRESULT'));
						return $this->_getObjResult($this->_getObjError($msg, $this->_getConst('ERROR_EVENT'), array()));

						break;
					}
					case \Bitrix\Main\EventResult::SUCCESS: {
						// успешно
						break;
					}
					case \Bitrix\Main\EventResult::UNDEFINED: {
						/* обработчик вернул неизвестно что вместо объекта класса \Bitrix\Main\EventResult
					   его результат по прежнему доступен через getParameters
					   */
						break;
					}
				}
			}

			if (!isset($arParameters[0]['phone']) || !isset($arParameters[0]['text'])) {
				$this->_getObjResult($this->_getObjError($this->getMsg('MANAGER.EVENT_ONBEFORE_SEND_EMPTY_PARAMS'), $this->_getConst('ERROR_EVENT')));
			}
			else {
				$phone = $arParameters[0]['phone'];
				$text  = $arParameters[0]['text'];
			}

			return $this->sendSms($phone, $text, $site_id, null, $schedule, $sender);
		}


		public function sendTemplate($template, $arFields = array(), $site_id = null, $schedule = null, $sender = null)
		{

			if (is_null($site_id)) {
				$site_id = $this->getCurrentSiteId();
			}

			// получаем шаблоны если не сделали эо ранее
			if (!isset($this->arSmsTemplate[$site_id][$template])) {
				if (is_null($this->oTemplate)) {
					$this->oTemplate = new TemplateTable();
				}

				$dbrTemplate = $this->oTemplate->getList(array(
					'filter' => array(
						'ACTIVE'   => true,
						'TYPE'     => $template,
						'SITE.SID' => $site_id,
                        'EVENT' => false
					)
				));
				while ($arTemplate = $dbrTemplate->fetch()) {
					$this->arSmsTemplate[$site_id][$template][] = $arTemplate;
				}
			}


			// проверка наличия шаблонов
			if (!isset($this->arSmsTemplate[$site_id][$template]) || empty($this->arSmsTemplate[$site_id][$template])) {
				return $this->_getObjResult($this->_getObjError($this->getMsg('MANAGER.NOT_FOUNT_TEMPLATES'), $this->_getConst('ERROR_TEMPLATE_NOT_FOUND')));
			}


			$event = new \Bitrix\Main\Event($this->module_id, "OnBeforeSendTemplate", array($template, $arFields));
			$event->send();

			foreach ($event->getResults() as $eventResult) {
				$arParameters = $eventResult->getParameters();

				if (!isset($arParameters[1])) {
					$this->_getObjResult($this->_getObjError($this->getMsg('MANAGER.EVENT_ONBEFORE_SEND_TEMPLATE_EMPTY_PARAMS'), $this->_getConst('ERROR_EVENT')));
				}

				$arFields = $arParameters[1];

				switch ($eventResult->getType()) {
					case \Bitrix\Main\EventResult::ERROR: {

						$msg = (isset($arParameters['error_msg']) ? $arParameters['error_msg'] : $this->getMsg('MANAGER.EVENT_ONBEFORE_SEND_TEMPLATE_ERROR_EVENTRESULT'));
						return $this->_getObjResult($this->_getObjError($msg, $this->_getConst('ERROR_EVENT'), array()));

						break;
					}
					case \Bitrix\Main\EventResult::SUCCESS: {
						// успешно
						break;
					}
					case \Bitrix\Main\EventResult::UNDEFINED: {
						/* обработчик вернул неизвестно что вместо объекта класса \Bitrix\Main\EventResult
					   его результат по прежнему доступен через getParameters
					   */
						break;
					}
				}
			}


			// обходим шаблоны и отправляем
			$arSentResult = array(
				'count'   => 0,
				'errors'  => null,
				'results' => array()
			);

			foreach ($this->arSmsTemplate[$site_id][$template] as $arTemplate) {
				$arSentResult['count']++;


				$this->prepareTemplate($arTemplate, $arFields, $site_id);

				$res = $this->sendSms($arTemplate['PHONE'], $arTemplate['TEXT'], $site_id, $arTemplate['TYPE'], $schedule);

				//копия
				if (!is_null($arTemplate['PHONE_COPY']) && $arPhoneCopy = explode(',', $arTemplate['PHONE_COPY'])) {
					foreach ($arPhoneCopy as $phoneCopy) {
						if ($this->isValidPhone($phoneCopy)) {
							$this->sendSms($this->getPreparePhone($phoneCopy), $arTemplate['TEXT'], $site_id, $arTemplate['TYPE'], $schedule, $sender);
						}
					}
				}

				if ($res->isSuccess()) {
					$arSentResult['results'][] = $res;
				}
				else {
					$arSentResult['errors'] = (array)$arSentResult['errors'];
					$errors                 = $res->getErrors();
					if (isset($errors[0])) {
						$arSentResult['errors'][] = $errors[0];
					}
				}
			}

			$result = $this->_getObjResult();
			$result->setMore('count', $arSentResult['count']);
			$result->setMore('errors', $arSentResult['errors']);
			$result->setMore('results', $arSentResult['results']);

			return $result;
		}


		public function sendTemplateEmail($template, $arFields = array(), $site_id = null, $schedule = null, $sender = null)
		{

			if (is_null($site_id)) {
				$site_id = $this->getCurrentSiteId();
			}

			// получаем шаблоны если не сделали эо ранее
			if (!isset($this->arSmsTemplate[$site_id][$template])) {
				if (is_null($this->oTemplate)) {
					$this->oTemplate = new TemplateTable();
				}

				$dbrTemplate = $this->oTemplate->getList(array(
					'filter' => array(
						'ACTIVE'   => true,
						'TYPE'     => $template,
						'SITE.SID' => $site_id,
                        'EVENT' => true
					)
				));
				while ($arTemplate = $dbrTemplate->fetch()) {
					$this->arSmsTemplate[$site_id][$template][] = $arTemplate;
				}
			}

			// проверка наличия шаблонов
			if (!isset($this->arSmsTemplate[$site_id][$template]) || empty($this->arSmsTemplate[$site_id][$template]) ) {
				return $this->_getObjResult($this->_getObjError($this->getMsg('MANAGER.NOT_FOUNT_TEMPLATES'), $this->_getConst('ERROR_TEMPLATE_NOT_FOUND')));
			}

			$event = new \Bitrix\Main\Event($this->module_id, "OnBeforeSendTemplateEmail", array($template, $arFields));
			$event->send();

			foreach ($event->getResults() as $eventResult) {
				$arParameters = $eventResult->getParameters();

				if (!isset($arParameters[1])) {
					$this->_getObjResult($this->_getObjError($this->getMsg('MANAGER.EVENT_ONBEFORE_SEND_TEMPLATE_EMAIL_EMPTY_PARAMS'), $this->_getConst('ERROR_EVENT')));
				}

				$arFields = $arParameters[1];

				switch ($eventResult->getType()) {
					case \Bitrix\Main\EventResult::ERROR: {

						$msg = (isset($arParameters['error_msg']) ? $arParameters['error_msg'] : $this->getMsg('MANAGER.EVENT_ONBEFORE_SEND_TEMPLATE_EMAIL_ERROR_EVENTRESULT'));
						return $this->_getObjResult($this->_getObjError($msg, $this->_getConst('ERROR_EVENT'), array()));

						break;
					}
					case \Bitrix\Main\EventResult::SUCCESS: {
						// успешно
						break;
					}
					case \Bitrix\Main\EventResult::UNDEFINED: {
						/* обработчик вернул неизвестно что вместо объекта класса \Bitrix\Main\EventResult
					   его результат по прежнему доступен через getParameters
					   */
						break;
					}
				}
			}


			// обходим шаблоны и отправляем
			$arSentResult = array(
				'count'   => 0,
				'errors'  => null,
				'results' => array()
			);

			foreach ($this->arSmsTemplate[$site_id][$template] as $arTemplate) {
				$arSentResult['count']++;


				$this->prepareTemplate($arTemplate, $arFields, $site_id);

				$res = $this->sendSms($arTemplate['PHONE'], $arTemplate['TEXT'], $site_id, $arTemplate['TYPE']);

				//копия
				if (!is_null($arTemplate['PHONE_COPY']) && $arPhoneCopy = explode(',', $arTemplate['PHONE_COPY'])) {
					foreach ($arPhoneCopy as $phoneCopy) {
						if ($this->isValidPhone($phoneCopy)) {
							$this->sendSms($this->getPreparePhone($phoneCopy), $arTemplate['TEXT'], $site_id, $arTemplate['TYPE'],$schedule, $sender );
						}
					}
				}

				if ($res->isSuccess()) {
					$arSentResult['results'][] = $res;
				}
				else {
					$arSentResult['errors'] = (array)$arSentResult['errors'];
					$errors                 = $res->getErrors();
					if (isset($errors[0])) {
						$arSentResult['errors'][] = $errors[0];
					}
				}
			}

			$result = $this->_getObjResult();
			$result->setMore('count', $arSentResult['count']);
			$result->setMore('errors', $arSentResult['errors']);
			$result->setMore('results', $arSentResult['results']);

			return $result;
		}

		/**
		 * Запрос баланса
		 *
		 * @param null $site_id
		 *
		 * @return Result
		 */
		public function getBalance($site_id = null)
		{
			return $this->getService($site_id)->getBalance();
		}


		/**
		 * Проверка, кодировка сайта UTF-8 или нет
		 * @return bool
		 */
		final static public function isUTF()
		{
			return (defined('BX_UTF') && BX_UTF === true);
		}

		static public function isWin()
		{
			return !self::isUTF();
		}

		final static public function isAdminSection()
		{
			return (defined('ADMIN_SECTION') && defined('ADMIN_SECTION') === true);
		}

		/**
		 * Возвращает идентификатор текущего сайта
		 * @return null|string
		 */
		public function getCurrentSiteId()
		{
			// если админка то определяем сайт поумолчанию или  по текущему домену
			if ($this->isAdminSection() || SITE_ID == LANGUAGE_ID) {

				if (!$this->siteID) {
					$host = Application::getInstance()->getContext()->getRequest()->getHttpHost();
					$host = preg_replace('/(:[\d]+)/', '', $host);

					//ищем по домену
					$oSite = new \CSite();
					$dbr   = $oSite->GetList($by = 'sort', $order = 'asc', array(
						'ACTIVE' => 'Y',
						'DOMAIN' => $host
					));
					if ($ar = $dbr->Fetch()) {
						$this->siteID = $ar['LID'];
					}
					else {
						// сайт поумолчанию
						$dbr = $oSite->GetList($by = 'sort', $order = 'asc', array(
							'DEFAULT' => 'Y'
						));
						if ($ar = $dbr->Fetch()) {
							$this->siteID = $ar['LID'];
						}
						else {
							$dbr = $oSite->GetList($by = 'sort', $order = 'asc', array());
							if ($ar = $dbr->Fetch()) {
								$this->siteID = $ar['LID'];
							}
                        }
					}
				}
				return $this->siteID;
			}

			return SITE_ID;
		}

		/**
		 * Возвращает парамтеры сайта, для подставновки этих значений в шаблоны сообщений - SERVER_NAME напримре
		 *
		 * @param null $site_id
		 *
		 * @return mixed
		 */
		public function getSiteData($site_id = null)
		{
			if (is_null($site_id)) {
				$site_id = $this->getCurrentSiteId();
			}
			if (!isset($this->arSiteData[$site_id])) {
				$oSite = new \CSite();
				$dbr   = $oSite->GetByID($site_id);
				if ($ar = $dbr->Fetch()) {
					$this->arSiteData[$site_id] = $ar;
				}
				else {
					$this->arSiteData[$site_id] = false;
				}
			}
			return $this->arSiteData[$site_id];
		}


		/**
		 * Получение значения одного из параметров модуля
		 *
		 * @param      $name
		 * @param null $default_value
		 *
		 * @return string
		 * @throws \Bitrix\Main\ArgumentNullException
		 */
		public function getParam($name, $default_value = null, $site_id = null)
		{
			if (is_null($site_id)) {
				$site_id = $this->getCurrentSiteId();
			}

			return $this->oOption->get($this->module_id, $name, $default_value, $site_id);
		}

		/**
		 * Возврат языкозависимое сообщение об ошибке или успешности
		 *
		 * @param      $name
		 * @param null $arReplace
		 *
		 * @return mixed|string
		 */
		protected function getMsg($name, $arReplace = null)
		{
			return GetMessage($this->module_id . '.' . $name, $arReplace);
		}

		protected function _getConst($name)
		{
			return (defined('Iqsms\Sender\\' . $name) ? constant('Iqsms\Sender\\' . $name) : null);
		}

		protected function _getNewObj($name)
		{
			return new $name();
		}

		protected function _getObj($name)
		{
			return (isset($this->$name) ? $this->$name : null);
		}

		protected function _getObjError($message, $code = 0)
		{
			return new Error($message, $code);
		}

		protected function _getObjResult($result = null)
		{
			return new Result($result);
		}

		/**
		 * Шапка в административной части сайтов
		 *
		 * @param bool $return
		 *
		 * @return bool|string
		 */
		public function getAdminHeader($return = false)
		{
			// сайты
			$arSite = array();
			$dbr    = \CSite::GetList($by = 'sort', $order = 'asc');
			while ($ar = $dbr->Fetch()) {
				$arSite[$ar['ID']] = '[' . $ar['ID'] . '] ' . $ar['NAME'];
			}

			/**
			 * @var $service \Iqsms\Sender\Service
			 */
			$service = $this->getService();

			ob_start();
			?>
            <div class="c_iqsms_sender_header_area">
                <div class="logo_box">
					<?= $this->getMsg('MANAGER.LABEL_SAFE'); ?>
                </div>
                <div class="info_box">
					<?= $this->getMsg('MANAGER.LABEL_SITE'); ?><?= $arSite[$this->getCurrentSiteId()]; ?>
                    <br>
					<?= $this->getMsg('MANAGER.LABEL_LOGIN'); ?><?= $service->getLogin(); ?>
                    <br>
					<?= $this->getMsg('MANAGER.LABEL_BALANCE'); ?><?= $service->getBalance()->getResult(); ?>
                    &nbsp;&nbsp;&nbsp;&nbsp; <a href="https://iqsms.ru/payments/" target="_blank" class="btn_balance_add"><?= $this->getMsg('MANAGER.LABEL_BALANCE_ADD'); ?></a>
                    <br>
                </div>
            </div>
			<?
			$html = ob_get_clean();

			if ($return) {
				return $html;
			}
			else {
				echo $html;
				return true;
			}

		}


		/**
		 * Разбирает список телефонов
		 *
		 * @param $phone
		 *
		 * @return array|bool
		 */
		public function getMultyPhone($phone)
		{
			$phone   = preg_replace("/\n/", ',', $phone);
			$phone   = preg_replace("/ /", ',', $phone);
			$arPhone = explode(',', $phone);

			$arPhone = array_diff($arPhone, array('', ' '));

			if (count($arPhone)) {
				return $arPhone;
			}
			else {
				return false;
			}
		}

		/**
		 * Возвращает валидные номера телефонов из списка
		 *
		 * @param $phone
		 *
		 * @return array|bool
		 */
		public function getPreparedMultyPhone($phone)
		{

			$arPhoneTmp = $this->getMultyPhone($phone);
			$arPhone    = array();

			foreach ($arPhoneTmp as $phone) {
				if ($this->isValidPhone($phone)) {
					$arPhone[] = $this->getPreparePhone($phone);
				}
			}

			if (count($arPhone)) {
				return $arPhone;
			}
			else {
				return false;
			}
		}

		/**
		 * При наличии проблем с соединением с смс сервисом, помещется в очеедь
		 * которя потом обрабатывается
		 * @return bool
		 */
		public function agentSendSmsQueue($offset = 0)
		{

			$arSms = array();

			$dbr = $this->oManagerTable->getList(array(
				'filter' => array(
					'STATUS' => $this->_getConst('SMS_STATUS_WAIT')
				),
				'order'  => array(
					'SITE_ID' => 'ASC',
					'CREATED' => 'ASC'
				),
				'limit'  => '200',
				'offset' => $offset
			));
			while ($ar = $dbr->fetch()) {
				//"2008-07-12T14:30:01Z
				if (is_null($ar['SCHEDULE'])) {
					$arSms[$ar['SITE_ID']][0][$ar['ID']] = $ar;
				}
				else {
					$time = gmdate("Y-m-d\TH:i:s\Z", $ar['SCHEDULE']->getTimestamp());

					$arSms[$ar['SITE_ID']][$time][$ar['ID']] = $ar;
				}


			}

			foreach ($arSms as $siteId => $arTimeItems) {
				//готовим сообщения
				foreach ($arTimeItems as $t => $arItems) {

					$arFields = array(
						'messages' => array()
					);

					if ($t != 0) {
						$arFields['scheduleTime'] = $t;
					}

					foreach ($arItems as $id => $arItem) {
						$arFields['messages'][] = array(
							"phone"    => $arItem['PHONE'],
							"clientId" => $id,
							"text"     => $arItem['TEXT'],
							"sender"   => $arItem['SENDER']
						);
					}

					$result = $this->getService($siteId)->sendPack($arFields);
					if ($result->isSuccess()) {
						$arSmsIdResult = $result->getResult();
						foreach ($arSmsIdResult['messages'] as $id => $smsResult) {
							$this->oManagerTable->update($id, array(
								'STATUS'  => $smsResult->getResult(),
								'COMMENT' => '',
								'PARAMS'  => ($smsResult->getMore('params') ? $smsResult->getMore('params') : '')
							));
						}
					}
					else {
						//						echo '<pre>';
						//						print_r($result->getErrors());
						//						echo '</pre>';
					}
					sleep(1);
				}
			}

			return $offset;
		}


		/**
		 * Используется агентом, для обновления статусов сообщений, для сервисов где есть задержки
		 * @return bool
		 * @throws \Bitrix\Main\ArgumentException
		 */
		public function agentCheckSmsStatus()
		{

			$arSms = array();

			$dbr = $this->oManagerTable->getList(array(
				'filter' => array(
					'STATUS' => $this->_getConst('SMS_STATUS_SENT')
				),
				'order'  => array(
					'SITE_ID' => 'ASC',
					'CREATED' => 'ASC'
				),
				'limit'  => '200'
			));
			while ($ar = $dbr->fetch()) {
				if (strlen(trim($ar['PARAMS']['messageId'])) <= 0) {
					$this->oManagerTable->update($ar['ID'], array('STATUS' => \Bxmaker\SmsCampaign\SMS_STATUS_ERROR));
					continue;
				}
				$arSms[$ar['SITE_ID']][$ar['ID']] = $ar;
			}

			foreach ($arSms as $siteId => $arItems) {

				$arFields = array(
					'messages' => array()
				);

				foreach ($arItems as $id => $arItem) {
					$arFields['messages'][] = array(
						"smscId"   => $arItem['PARAMS']['messageId'],
						"clientId" => $id
					);
				}

				$result = $this->getService($siteId)->statusPack($arFields);
				if ($result->isSuccess()) {
					$arSmsIdResult = $result->getResult();
					foreach ($arSmsIdResult['messages'] as $id => $smsResult) {
						$this->oManagerTable->update($id, array(
							'STATUS'  => $smsResult->getResult(),
							'COMMENT' => '',
							'PARAMS'  => ($smsResult->getMore('params') ? $smsResult->getMore('params') : '')
						));
					}
				}
				else {
					//						echo '<pre>';
					//						print_r($result->getErrors());
					//						echo '</pre>';
				}
				sleep(1);

			}

			return true;

		}


		public function getSenders($site_id = null)
		{
			return $this->getService($site_id)->getSenders();

		}


	}