<?

	namespace Iqsms\Sender;

	use Bitrix\Main\Config\Option;
	use Bitrix\Main\Loader;
	use Bitrix\Main\Localization\Loc;
	use Iqsms\Sender\Error;
	use Iqsms\Sender\Manager;
	use Iqsms\Sender\ManagerTable;
	use Iqsms\Sender\Result;

	Loc::loadMessages(__FILE__);


	Class Service
	{

		const REQUEST_SUCCESS = 'success';
		const REQUEST_ERROR = 'error';

		private $oHttp = null;

		private $arParams
			= array(
				'user'      => '',
				'pass'      => '',
				'from'      => '',
				'site_id'   => '',
				'site_name' => ''
			);

		private $arSenders = null;

		/**
		 * Конструктор
		 *
		 * @param array $arParams
		 * - string USER
		 * - string PWD
		 * - string SADR
		 * - integer TEST_MODE
		 */
		public function __construct($user, $pass, $from, $sid, $site_name = '')
		{
			if (is_null($this->oHttp)) {
				$this->oHttp = new \Bitrix\Main\Web\HttpClient();
			}

			$this->arParams['user']      = $user;
			$this->arParams['pass']      = $pass;
			$this->arParams['from']      = $from;
			$this->arParams['site_id']   = $sid;
			$this->arParams['site_name'] = $site_name;
		}

		/**
		 * Получение значения параметры
		 *
		 * @param      $name
		 * @param null $default_value
		 *
		 * @return null
		 */
		private function _getParam($name, $default_value = null)
		{
			return (isset($this->arParams[$name]) ? $this->arParams[$name] : $default_value);
		}


		/**
		 * Сообщения
		 *
		 * @param $code
		 *
		 * @return mixed|string
		 */
		private function _getMsg($code)
		{
			return GetMessage('iqsms.sender.service.' . $code);
		}

		public function getLogin()
		{
			return $this->_getParam('user');
		}

		public function getDefaultSender()
		{
			return $this->_getParam('from');
		}


		public function getSenders()
		{
			if (is_null($this->arSenders)) {
				$this->arSenders = array();

				$p = array(
					'login'    => $this->_getParam('user'),
					'password' => $this->_getParam('pass'),
				);

				$url      = 'http://json.gate.iqsms.ru/senders/';
				$response = $this->oHttp->post($url, json_encode($p));
				$response = json_decode($this->_getFromUtf($response), true);

				if ($response['status'] == 'ok') {
					foreach ($response['senders'] as $sender) {
						$this->arSenders[] = $sender;
					}
				}

			}
			return $this->arSenders;
		}



		/**
		 * Проверка баланса
		 * @return Result
		 */
		public function getBalance()
		{
			$result = new Result();

			$p = array(
				'login'    => $this->_getParam('user'),
				'password' => $this->_getParam('pass'),
			);

			$url      = 'http://api.iqsms.ru/messages/v2/balance.json';
			$response = $this->oHttp->post($url, json_encode($p));
			$response = json_decode($this->_getFromUtf($response), true);


			if ($response['status'] == 'ok') {
				$result->setResult(floatval($response["balance"][0]['balance']) . $this->_getMsg('CURRENCY_' . $response["balance"][0]['type']));
			}
			elseif ($response['status'] == 'error') {
				$result->setResult($response['description']);
			}
			else {
				$result->setError(new Error($this->_getMsg('ERROR_DESCRIPTION_BALANCE_RESPONSE'), \Iqsms\Sender\ERROR_SERVICE_CUSTOM, array('response' => $response)));
			}
			return $result;
		}


		public function sendPack($arPack)
		{
			$result = new Result();

			$arPack = array_merge($arPack, array(
				'login'    => $this->_getParam('user'),
				'password' => $this->_getParam('pass'),
			));
			$arPack = $this->_getPrepared($arPack);

			$url      = 'http://json.gate.iqsms.ru/send/';
			$response = $this->oHttp->post($url, json_encode($arPack));


			file_put_contents($_SERVER['DOCUMENT_ROOT'].'/logs/iqsms.log', json_encode($arPack));



			$response = json_decode($this->_getFromUtf($response), true);
			if ($response['status'] == 'ok') {
				$arMsg = array();
				foreach ($response['messages'] as $msg) {
					$r = new Result();
					switch ($msg['status']) {
						case 'accepted': {
							$r->setResult(\Iqsms\Sender\SMS_STATUS_SENT);
							$r->setMore('params', array(
								'messageId' => $msg['smscId']
							));
							break;
						}
						case 'queued': {
							$r->setResult(\Iqsms\Sender\SMS_STATUS_SENT);
							$r->setMore('params', array(
								'messageId' => $msg['smscId']
							));
							break;
						}
						case 'delivered': {
							$r->setResult(\Iqsms\Sender\SMS_STATUS_DELIVERED);
							$r->setMore('params', array(
								'messageId' => $msg['smscId']
							));
							break;
						}
						case 'delivery error': {
							$r->setResult(\Iqsms\Sender\SMS_STATUS_ERROR);
							$r->setMore('params', array(
								'messageId'         => $msg['smscId'],
								'error_description' => $msg['status']
							));
							break;
						}
						case 'smsc submit': {
							$r->setResult(\Iqsms\Sender\SMS_STATUS_SENT);
							$r->setMore('params', array(
								'messageId' => $msg['smscId']
							));
							break;
						}
						case 'smsc reject': {
							$r->setResult(\Iqsms\Sender\SMS_STATUS_ERROR);
							$r->setMore('params', array(
								'messageId'         => $msg['smscId'],
								'error_description' => $msg['status']
							));
							break;
						}
						case 'incorrect id': {
							$r->setResult(\Iqsms\Sender\SMS_STATUS_ERROR);
							$r->setMore('params', array(
								'messageId'         => $msg['smscId'],
								'error_description' => $msg['status']
							));
							break;
						}
						default: {
							$r->setResult(\Iqsms\Sender\SMS_STATUS_ERROR);
							$r->setMore('params', array(
								'messageId'         => $msg['smscId'],
								'error_description' => $msg['status']
							));
							break;
						}
					}

					$arMsg[$msg['clientId']] = $r;
				}
				$result->setResult(array('messages' => $arMsg));
			}
			elseif ($response['status'] == 'error') {
				$result->setError(new Error($response['description'], \Iqsms\Sender\ERROR_SERVICE_CUSTOM, array('response' => $response)));
			}
			else {
				$result->setError(new Error($this->_getMsg('ERROR_DESCRIPTION_BALANCE_RESPONSE'), \Iqsms\Sender\ERROR_SERVICE_CUSTOM, array('response' => $response)));
			}
			return $result;
		}

		public function statusPack($arPack)
		{
			$result = new Result();

			$arPack = array_merge($arPack, array(
				'login'    => $this->_getParam('user'),
				'password' => $this->_getParam('pass'),
			));
			$arPack = $this->_getPrepared($arPack);

			$url      = 'http://json.gate.iqsms.ru/messages/v2/status.json';
			$response = $this->oHttp->post($url, json_encode($arPack));


			$response = json_decode($this->_getFromUtf($response), true);
			if ($response['status'] == 'ok') {
				$arMsg = array();
				foreach ($response['messages'] as $msg) {
					$r = new Result();
					switch ($msg['status']) {
						case 'accepted': {
							$r->setResult(\Iqsms\Sender\SMS_STATUS_SENT);
							$r->setMore('params', array(
								'messageId' => $msg['smscId']
							));
							break;
						}
						case 'queued': {
							$r->setResult(\Iqsms\Sender\SMS_STATUS_SENT);
							$r->setMore('params', array(
								'messageId' => $msg['smscId']
							));
							break;
						}
						case 'delivered': {
							$r->setResult(\Iqsms\Sender\SMS_STATUS_DELIVERED);
							$r->setMore('params', array(
								'messageId' => $msg['smscId']
							));
							break;
						}
						case 'delivery error': {
							$r->setResult(\Iqsms\Sender\SMS_STATUS_ERROR);
							$r->setMore('params', array(
								'messageId'         => $msg['smscId'],
								'error_description' => $msg['status']
							));
							break;
						}
						case 'smsc submit': {
							$r->setResult(\Iqsms\Sender\SMS_STATUS_SENT);
							$r->setMore('params', array(
								'messageId' => $msg['smscId']
							));
							break;
						}
						case 'smsc reject': {
							$r->setResult(\Iqsms\Sender\SMS_STATUS_ERROR);
							$r->setMore('params', array(
								'messageId'         => $msg['smscId'],
								'error_description' => $msg['status']
							));
							break;
						}
						case 'incorrect id': {
							$r->setResult(\Iqsms\Sender\SMS_STATUS_ERROR);
							$r->setMore('params', array(
								'messageId'         => $msg['smscId'],
								'error_description' => $msg['status']
							));
							break;
						}
						default: {
							$r->setResult(\Iqsms\Sender\SMS_STATUS_ERROR);
							$r->setMore('params', array(
								'messageId'         => $msg['smscId'],
								'error_description' => $msg['status']
							));
							break;
						}
					}

					$arMsg[$msg['clientId']] = $r;
				}
				$result->setResult(array('messages' => $arMsg));
			}
			elseif ($response['status'] == 'error') {
				$result->setError(new Error($response['description'], \Iqsms\Sender\ERROR_SERVICE_CUSTOM, array('response' => $response)));
			}
			else {
				$result->setError(new Error($this->_getMsg('ERROR_DESCRIPTION_BALANCE_RESPONSE'), \Iqsms\Sender\ERROR_SERVICE_CUSTOM, array('response' => $response)));
			}
			return $result;
		}


		// кодировка должна быть UTF-8
		private function _getPrepared($str)
		{
			return (!Manager::isUTF() ? \Bitrix\Main\Text\Encoding::convertEncoding($str, LANG_CHARSET, "UTF-8") : $str);
		}

		// ответ в UTF-8 приходит, поэтому подготовим для внутреннего пользования
		private function _getFromUtf($str)
		{
			return (!Manager::isUTF() ? \Bitrix\Main\Text\Encoding::convertEncoding($str, "UTF-8", LANG_CHARSET) : $str);
		}



		private function _strlen($str)
		{
			return (Manager::isUTF() ? mb_strlen($str) : strlen($str));
		}


	}
