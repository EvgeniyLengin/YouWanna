<?
	namespace Iqsms\Sender;

	use Bitrix\Main\Type\Date;
	use Bitrix\Main\Type\DateTime;

	class Agent {

		private static $module_id = 'iqsms.sender';


		/**
		 * �������� ������ ���������
		 * @return string
		 * @throws \Bitrix\Main\ArgumentException
		 * @throws \Bitrix\Main\ArgumentNullException
		 */
		public static function cleanSmsHistory()
		{

			$option =  new \Bitrix\Main\Config\Option();

			$days = $option->get(self::$module_id, 'CLEAN_SMS_HISTORY', 30);

			$oManagerTable = new ManagerTable();
			$dbr = $oManagerTable->getList(array(
				'filter' => array(
					'<=CREATED' => Date::createFromTimestamp(time() - intval($days) * 60*60*24)
				),
				'order' => array(
					'CREATED' => 'ASC'
				),
				'limit' => '50'
			));
			while($ar = $dbr->fetch())
			{
				$oManagerTable->delete($ar['ID']);
			}

			return __METHOD__ . '();';
		}


		/**
		 * �������� ������� ������������ ���������, ���������� �� ���������� ��� ������ � �����������
		 * @return string
		 * @throws \Bitrix\Main\ArgumentException
		 * @throws \Bitrix\Main\ArgumentNullException
		 */
		public static function checkSmsStatus()
		{
			Manager::getInstance()->agentCheckSmsStatus();

			return __METHOD__ . '();';
		}

		/**
		 * �������� ��� �� �������
		 * @return string
		 */
		public static function agentSendSmsQueue($offset = 0)
		{
			$offset = Manager::getInstance()->agentSendSmsQueue($offset);

			return __METHOD__ . '('.$offset.');';
		}


	}
