<?
	namespace Iqsms\Sender;


	use Bitrix\Main\Application;
	use \Bitrix\Main\Entity;
	use Bitrix\Main\Localization\Loc;
	use Bitrix\Main\Type\DateTime;
	use Bitrix\Main\Loader;

	Loc::loadMessages(__FILE__);


	class Handler
	{

		static private $module_id = 'iqsms.sender';

		/**
		 * @var \Bitrix\Main\Config\Option
		 */
		static private $oOption = null; // ��������� ������
		static private $siteID = null; // ����������� ����� �������� ��� ������������� ���������� ����������� � ����������� ������� ���
		static private $arTmpData = array(); // ��������� ������ ��� �������� ����� �������������



		static private function init()
		{
			if (is_null(self::$oOption)) self::$oOption = new \Bitrix\Main\Config\Option();
			if (is_null(self::$siteID)) self::$siteID = Manager::getInstance()->getCurrentSiteId();
		}


		/**
		 * ��������� ��������� ������
		 *
		 * @param        $name
		 * @param string $default_value
		 *
		 * @return string
		 * @throws \Bitrix\Main\ArgumentNullException
		 */
		static private function getParam($name, $default_value = '')
		{

			self::init();
			return self::$oOption->get(self::$module_id, $name, $default_value, self::$siteID);
		}


		//===========================================================================================
		// main
		//===========================================================================================

		public function main_OnBeforeUserAdd($arParams)
		{
			if (isset($arParams['LOGIN']) && isset($arParams['PASSWORD'])) {
				self::$arTmpData['main_user_add'][$arParams['LOGIN']]         = $arParams['PASSWORD'];
				$_SESSION['iqsms_BEFORE_USER_ADD.' . $arParams['LOGIN']] = $arParams['PASSWORD'];
			}
		}



		//===========================================================================================
		// sale ��������-������� �����������
		//===========================================================================================

		//����� ����������� ������
		public function sale_OnBeforeOrderAdd($arFields)
		{
			// �� ����� ���������� ������ ����������� ���������� ��������� ������
			// ����� �� ������� ����� ���,  ������������� � ������� ����������� ������ � ����� ������
			self::$arTmpData['sale_order_add_start'] = true;
		}

		//��������� ������
		public function sale_OnOrderAdd($ID, $arFields)
		{
			unset(self::$arTmpData['sale_order_add_start']);
		}

		//��������� ������
		public function sale_OnSaleOrderSaved($order)
		{
			/** @var  \Bitrix\Sale\Order $order */

			//self::sale_onEventOrderAdd($order->getId());
		}

		public function sale_onEventOrderAdd($ORDER_ID)
		{
			if (strlen($ORDER_ID) > 0) {


				// �� ���������� ���� � ���� ��������
				if (isset(self::$arTmpData['sale_onEventOrderAdd_' . $ORDER_ID])) {
					return;
				}
				self::$arTmpData['sale_onEventOrderAdd_' . $ORDER_ID] = true;

				$arOrderData = self::getOrderData($ORDER_ID);

				$persone_type = (isset($arOrderData['PERSON_TYPE_ID']) ? $arOrderData['PERSON_TYPE_ID'] : '');

				// ��������� ������� �� ���� � ������� ������� ����� ��������
				//				$user_field_phone = trim(self::getParam('USER_PHONE_FIELD', null));
				$user_field_phone = 'PERSONAL_MOBILE';
				$field_phone      = trim(self::getParam('PERSON_TYPE_' . $persone_type, null));
				$template_type    = trim(self::getParam('ORDER_NEW_TEMPLATE_TYPE_' . $persone_type, null));

				$savePhone = trim(self::getParam('SAVE_PHONE_' . $persone_type, 'N'));
				$sendPass  = trim(self::getParam('SEND_PASSWORD_' . $persone_type, 'N'));


				$PHONE = false;
				if ($field_phone && $template_type) {

					if (isset($arOrderData['PROPERTY_VALUE_' . $field_phone])) {
						// ���������� ����
						$PHONE = $arOrderData['PROPERTY_VALUE_' . $field_phone];
					}
				}


				//
				if ($field_phone && isset($arOrderData['PROPERTY_VALUE_' . $field_phone])) {
					$oUser = new \CUser();
					$dbr   = $oUser->GetList($by = '', $order = '', array('ID' => intval($arOrderData['USER_ID'])), array('SELECT' => array('*', 'UF_')));
					if ($ar = $dbr->Fetch()) {


						if (Manager::getInstance()->isValidPhone($arOrderData['PROPERTY_VALUE_' . $field_phone])) {

							if ($savePhone == 'Y') {
								// ���������
								$oUser->Update($ar['ID'], array(
									$user_field_phone => Manager::getInstance()->getPreparePhone($arOrderData['PROPERTY_VALUE_' . $field_phone])
								));
							}

							if ($sendPass) {


								if (isset($_SESSION['iqsms_BEFORE_USER_ADD.' . $ar['LOGIN']])) {
									// ���������
									$ar['PASSWORD'] = $_SESSION['iqsms_BEFORE_USER_ADD.' . $ar['LOGIN']];
									$ar['PHONE']    = $PHONE;

									Manager::getInstance()->sendTemplate('SEND_PASSWORD', $ar, $arOrderData['LID']);
									unset($_SESSION['iqsms_BEFORE_USER_ADD.' . $ar['LOGIN']]);
								}
							}
						}
					}
				}

				// ���� � ������
				if ($PHONE) {
					// ���������� ����
					$arOrderData['PHONE'] = $PHONE;

					Manager::getInstance()->sendTemplate($template_type, $arOrderData, $arOrderData['LID']);
				}
			}
		}


		// ����� ����������� ������
		public function sale_OnBeforeOrderUpdate($ID, $arFields)
		{
			if (isset(self::$arTmpData['sale_order_add_start']) && self::$arTmpData['sale_order_add_start'] === true) {
				return;
			}
		}

		// ���������� �����
		public function sale_OnOrderUpdate($ID, $arFields)
		{
			if (isset(self::$arTmpData['sale_order_add_start']) && self::$arTmpData['sale_order_add_start'] === true) {
				return;
			}
		}

		// ����� �������
		public function sale_OnSalePayOrder($ORDER_ID, $val)
		{
			if ($val == 'Y') {
				// �� ���������� ���� � ���� ��������
				if (isset(self::$arTmpData['sale_OnSalePayOrder' . $ORDER_ID])) {
					return;
				}
				self::$arTmpData['sale_OnSalePayOrder' . $ORDER_ID] = true;

				$arOrderData = self::getOrderData($ORDER_ID, true);

				$persone_type = (isset($arOrderData['PERSON_TYPE_ID']) ? $arOrderData['PERSON_TYPE_ID'] : '');

				// ��������� ������� �� ���� � ������� ������� ����� ��������
				$field_phone   = trim(self::getParam('PERSON_TYPE_' . $persone_type, null));
				$template_type = trim(self::getParam('ORDER_PAY_TEMPLATE_TYPE_' . $persone_type, null));

				if ($field_phone && $template_type) {

					if (isset($arOrderData['PROPERTY_VALUE_' . $field_phone])) {
						// ���������� ����
						$arOrderData['PHONE'] = $arOrderData['PROPERTY_VALUE_' . $field_phone];

						Manager::getInstance()->sendTemplate($template_type, $arOrderData, $arOrderData['LID']);
					}
				}
			}
		}

		// ����� �������
		public function sale_OnSaleCancelOrder($ORDER_ID, $val)
		{

			if ($val == 'Y') {
				// �� ���������� ���� � ���� ��������
				if (isset(self::$arTmpData['sale_OnSaleCancelOrder' . $ORDER_ID])) {
					return;
				}
				self::$arTmpData['sale_OnSaleCancelOrder' . $ORDER_ID] = true;

				$arOrderData = self::getOrderData($ORDER_ID, true);

				$persone_type = (isset($arOrderData['PERSON_TYPE_ID']) ? $arOrderData['PERSON_TYPE_ID'] : 1);

				// ��������� ������� �� ���� � ������� ������� ����� ��������
				$field_phone   = trim(self::getParam('PERSON_TYPE_' . $persone_type, null));
				$template_type = trim(self::getParam('ORDER_CANCELED_TEMPLATE_TYPE_' . $persone_type, null));


				if ($field_phone && $template_type) {

					if (isset($arOrderData['PROPERTY_VALUE_' . $field_phone])) {
						// ���������� ����
						$arOrderData['PHONE'] = $arOrderData['PROPERTY_VALUE_' . $field_phone];

						Manager::getInstance()->sendTemplate($template_type, $arOrderData, $arOrderData['LID']);
					}
				}
			}
		}

		// ����� ����� �������
		public function sale_OnSaleStatusOrder($ORDER_ID, $STATUS_ID)
		{
			$STATUS_ID = (string)$STATUS_ID;


			// �� ���������� ���� � ���� ��������
			if (isset(self::$arTmpData['sale_OnSaleStatusOrder_' . $STATUS_ID . '_' . $ORDER_ID])) {
				return;
			}
			self::$arTmpData['sale_OnSaleStatusOrder_' . $STATUS_ID . '_' . $ORDER_ID] = true;


			$arOrderData = self::getOrderData($ORDER_ID, true);

			$persone_type = (isset($arOrderData['PERSON_TYPE_ID']) ? $arOrderData['PERSON_TYPE_ID'] : '');

			// ��������� ������� �� ���� � ������� ������� ����� ��������
			$field_phone   = trim(self::getParam('PERSON_TYPE_' . $persone_type, null));
			$template_type = trim(self::getParam('ORDER_STATUS_' . $STATUS_ID . '_TEMPLATE_TYPE_' . $persone_type, null));

			if ($field_phone && $template_type) {

				if (isset($arOrderData['PROPERTY_VALUE_' . $field_phone])) {
					// ���������� ����
					$arOrderData['PHONE'] = $arOrderData['PROPERTY_VALUE_' . $field_phone];

					Manager::getInstance()->sendTemplate($template_type, $arOrderData, $arOrderData['LID']);
				}
			}

		}


		/**
		 * ���������� ������ ������ ��� �������� � ������ ��������� � �������� ������������ ������ -  ��������-�������
		 *
		 * @param $ORDER_ID
		 *
		 * @return array|bool|null
		 * @throws \Bitrix\Main\LoaderException
		 */
		private static function getOrderDataD7($ORDER_ID, $bRealId = false)
		{
			if (!Loader::includeModule('sale')) {
				return null;
			}

			// ��� ������������ �� �������� ������
			if (!$bRealId && strlen(trim(\Bitrix\Main\Config\Option::get('sale', 'account_number_template', ''))) > 0) {
				if ($orderDat = \Bitrix\Sale\Order::getList(array(
					'filter' => array(
						'ACCOUNT_NUMBER' => $ORDER_ID
					)
				))->fetch()
				) {
					$ORDER_ID = $orderDat['ID'];
				}
			}


			/** @var  \Bitrix\Sale\Order $order */
			$order   = \Bitrix\Sale\Order::load($ORDER_ID);
			$arOrder = $order->getFieldValues();

			$arOrder['ORDER_ID'] = $arOrder['ACCOUNT_NUMBER'];

			// ��������� ������� �� ���� � ������� ������� ����� ��������
			$presonTypeId = (isset($arOrder['PERSON_TYPE_ID']) ? $arOrder['PERSON_TYPE_ID'] : '1');
			$phoneField   = trim(self::getParam('PERSON_TYPE_' . $presonTypeId, 'PHONE'));
			$phoneDefault = null;
			$phone        = null;


			//��������
			$collections   = $order->getPropertyCollection();
			$arCollections = $collections->getArray();
			foreach ($arCollections['properties'] as $prop) {
				if ($prop['IS_PHONE'] == 'Y') {
					$phoneDefault = (isset($prop['VALUE'][0]) ? $prop['VALUE'][0] : null);
				}
				if ($prop['CODE'] == $phoneField) {
					$phone = (isset($prop['VALUE'][0]) ? $prop['VALUE'][0] : null);
				}

				$arOrder['PROPERTY_VALUE_' . $prop['CODE']] = (isset($prop['VALUE'][0]) ? $prop['VALUE'][0] : null);
			}

			if (is_null($phone) || !Manager::getInstance()->isValidPhone($phone)) {
				if (!is_null($phoneDefault) && Manager::getInstance()->isValidPhone($phoneDefault)) {
					$phone = $phoneDefault;
				}
			}

			$arOrder['PHONE'] = Manager::getInstance()->getPreparePhone($phone);


			//��������
			$arTrackNumber            = array();
			$arOrder['DELIVERY_NAME'] = '';
			$shipmentCollection       = $order->getShipmentCollection();
			foreach ($shipmentCollection as $shipment) {
				/** @var \Bitrix\Sale\Shipment $shipment */
				$arOrder['DELIVERY_NAME'] = $shipment->getDeliveryName();

				if (strlen(trim($track = $shipment->getField('TRACKING_NUMBER'))) > 0) {
					$arTrackNumber[] = $track;
				}
			}
			array_unique($arTrackNumber);
			$arOrder['TRACKING_NUMBER'] = implode(', ', $arTrackNumber);


			//������
			$arOrder['PAY_SYSTEM_NAME'] = '';
			$paymentCollection          = $order->getPaymentCollection();
			/** @var \Bitrix\Sale\Payment $payment */
			foreach ($paymentCollection as $payment) {
				$arOrder['PAY_SYSTEM_NAME'] = $payment->getPaymentSystemName();
			}


			//������� �������
			$orderStatus = \Bitrix\Sale\OrderStatus::getInitialStatus();
			$arStatus = \Bitrix\Sale\OrderStatus::getAllowedUserStatuses($arOrder['USER_ID'], $orderStatus);
			$arOrder['ORDER_STATUS_NAME'] = (isset($arStatus[$arOrder['STATUS_ID']]) ? $arStatus[$arOrder['STATUS_ID']] : $arOrder['STATUS_ID']);

			//������ ������� �������� ������ ����� ���������� ���� Bitrix. � ��������� ��������� ������� ��������� ������� getAllowedGroupStatuses � ����� "/bitrix/modules/sale/lib/statusbase.php"
			//$orderStatus                  = \Bitrix\Sale\OrderStatus::getInitialStatus();
			//$arStatus                     = \Bitrix\Sale\OrderStatus::getAllowedGroupStatuses(1, $orderStatus);
			//$arOrder['ORDER_STATUS_NAME'] = (isset($arStatus[$arOrder['STATUS_ID']]) ? $arStatus[$arOrder['STATUS_ID']] : $arOrder['STATUS_ID']);


			// ��������� ������

			$n = (intval(Manager::getInstance()->getParam('ORDER_PRICE_ROUND', '4')) >= 0 ? intval(Manager::getInstance()->getParam('ORDER_PRICE_ROUND', '4')) : 2);

			$arOrder['PRICE_REST'] = number_format($arOrder['PRICE'] - $arOrder['SUM_PAID'], $n, '.', ' ');
			$arOrder['PRICE']      = number_format($arOrder['PRICE'], $n, '.', ' ');
			$arOrder['SUM_PAID']   = number_format($arOrder['SUM_PAID'], $n, '.', ' ');

			$arOrder['PRICE_DELIVERY'] = number_format($arOrder['PRICE_DELIVERY'], $n, '.', ' ');
			$arOrder['DISCOUNT_VALUE'] = number_format($arOrder['DISCOUNT_VALUE'], $n, '.', ' ');


			return $arOrder;
		}


		/**
		 * ���������� ������ ������ ��� �������� � ������ ��������� � �������� ������������ ������ -  ��������-�������
		 *
		 * @param $ORDER_ID
		 *
		 * @return array|bool|null
		 * @throws \Bitrix\Main\LoaderException
		 */
		private static function getOrderData($ORDER_ID, $bRealId = false)
		{
			if (!Loader::includeModule('sale')) {
				return null;
			}

			if (class_exists('\Bitrix\Sale\OrderStatus') && class_exists('\Bitrix\Sale\Order')) {
				return self::getOrderDataD7($ORDER_ID, $bRealId);
			}


			$oOrder           = new \CSaleOrder();
			$oDelivery        = new \CSaleDelivery();
			$oDeliveryHandler = new \CSaleDeliveryHandler();
			$oPaySystem       = new \CSalePaySystem();
			$oOrderPropValue  = new \CSaleOrderPropsValue();
			$oOrderStatus     = new \CSaleStatus();


			// ��� ������������ �� �������� ������
			if (!$bRealId && strlen(trim(\Bitrix\Main\Config\Option::get('sale', 'account_number_template', ''))) > 0) {
				if ($orderDat = $oOrder->GetList(array(), array(
					'ACCOUNT_NUMBER' => $ORDER_ID
				))->fetch()
				) {
					$ORDER_ID = $orderDat['ID'];
				}
			}


			if ($arOrder = $oOrder->GetByID($ORDER_ID)) {

				$arOrder['ORDER_ID'] = $arOrder['ID'];

				if (isset($arOrder['ACCOUNT_NUMBER'])) {
					$arOrder['ORDER_ID'] = $arOrder['ACCOUNT_NUMBER'];
				}

				//-------------------------------------------
				// ���������� ������ ��������
				$arOrder['DELIVERY_NAME'] = '';
				if (count($arDeliveryTmp = explode(':', $arOrder['DELIVERY_ID'])) == 2) {
					// ������������������
					$bDeliveryFindStop = false;
					$dbrDelivery       = $oDeliveryHandler->GetList(
						array(
							'SORT' => 'ASC',
							'NAME' => 'ASC'
						),
						array(
							'SID' => $arDeliveryTmp[0]
						)
					);
					while (($arDelivery = $dbrDelivery->Fetch()) && !$bDeliveryFindStop) {
						foreach ($arDelivery['PROFILES'] as $deliveryProfileKey => $arDeliveryProfile) {
							if ($deliveryProfileKey == $arDeliveryTmp[1]) {
								$bDeliveryFindStop        = true;
								$arOrder['DELIVERY_NAME'] = $arDelivery['NAME'];
							}
						}
					}
				}
				else {
					$arDelivery = $oDelivery->GetByID(intval($arOrder['DELIVERY_ID']));
					if (isset($arDelivery['NAME'])) {
						$arOrder['DELIVERY_NAME'] = $arDelivery["NAME"];
					}
				}

				// -----------------------------------------
				// ������� ������
				$arPaySystem                = $oPaySystem->GetByID(intval($arOrder['PAY_SYSTEM_ID']), $arOrder['PERSON_TYPE_ID']);
				$arOrder['PAY_SYSTEM_NAME'] = (isset($arPaySystem['PSA_NAME']) ? $arPaySystem['PSA_NAME'] : '');


				//-------------------------------------------
				// �������� ������
				$dbrOrderPropValue = $oOrderPropValue->GetList(array(), array('ORDER_ID' => $arOrder['ID']));
				while ($arOrderPropValue = $dbrOrderPropValue->Fetch()) {
					$arOrder['PROPERTY_VALUE_' . $arOrderPropValue['CODE']] = $arOrderPropValue['VALUE'];
				}

				// ������� �������
				$arOrderStatus                = $oOrderStatus->GetByID($arOrder['STATUS_ID']);
				$arOrder['ORDER_STATUS_NAME'] = (isset($arOrderStatus['NAME']) ? $arOrderStatus['NAME'] : $arOrder['STATUS_ID']);

				// ��������� ������
				Manager::getInstance()->getParam('ORDER_PRICE_ROUND', '4');


				$n                         = (intval(Manager::getInstance()->getParam('ORDER_PRICE_ROUND', '4')) >= 0 ? intval(Manager::getInstance()->getParam('ORDER_PRICE_ROUND', '4')) : 2);
				$arOrder['PRICE_REST']     = number_format($arOrder['PRICE'] - $arOrder['SUM_PAID'], $n, '.', ' ');
				$arOrder['PRICE']          = number_format($arOrder['PRICE'], $n, '.', ' ');
				$arOrder['SUM_PAID']       = number_format($arOrder['SUM_PAID'], $n, '.', ' ');
				$arOrder['PRICE_DELIVERY'] = number_format($arOrder['PRICE_DELIVERY'], $n, '.', ' ');
				$arOrder['DISCOUNT_VALUE'] = number_format($arOrder['DISCOUNT_VALUE'], $n, '.', ' ');


				return $arOrder;
			}

			return null;
		}


		public function main_OnBeforeEventAdd($event, $lid, $arFields)
		{
			$site_id = $lid;

			//�������� ��� ����������� � �������� ��������
			$oManager = \Iqsms\Sender\Manager::getInstance();
			$oManager->sendTemplateEmail($event, $arFields, $site_id);

			switch ($event) {
				// �������� �����
				case 'SALE_NEW_ORDER': {
					self::sale_onEventOrderAdd(isset($arFields['ORDER_ID']) ? $arFields['ORDER_ID'] : '');
					break;
				}
			}
		}


		/**
		 * �������� ������� �����������
		 *
		 */
		public function sale_OnShipmentTrackingNumberChange($shipment)
		{
			\Bitrix\Main\Loader::includeModule('sale');

			$changedFields = $shipment->getFields()->getChangedValues();
			$orderId       = $shipment->getFields()->get('ORDER_ID');
			$trackId       = $changedFields['TRACKING_NUMBER'];

			$arOrder                    = Handler::getOrderDataD7($orderId, true);
			$arOrder['TRACKING_NUMBER'] = $trackId;

			$persone_type  = (isset($arOrder['PERSON_TYPE_ID']) ? $arOrder['PERSON_TYPE_ID'] : '1');
			$template_type = trim(self::getParam('ORDER_TRACKING_NUMBER_TEMPLATE_TYPE_' . $persone_type, null));

			if (Manager::getInstance()->isValidPhone($arOrder['PHONE']) && $template_type) {

				// ���������� ����
				$arOrder['PHONE']    = $arOrder['PHONE'];
				$arOrder['ORDER_ID'] = $arOrder['ACCOUNT_NUMBER'];

				//��������
				Manager::getInstance()->sendTemplate($template_type, $arOrder, $arOrder['LID']);

				//AddMessage2Log(' iqsms.sender  '. $template_type );
			}
			else {
				AddMessage2Log(' iqsms.sender ERROR  ' . __METHOD__ . ' valid phone - ' . (Manager::getInstance()->isValidPhone($arOrder['PHONE']) ? 'Y' : 'N') . ', template_type - ' . $template_type);
			}
		}

		/**
		 * ����� �������
		 *
		 */
		public function sale_OnSaleStatusOrderChange($order, $value, $value_old)
		{
			\Bitrix\Main\Loader::includeModule('sale');

			/**
			 * @var $order \Bitrix\Sale\Order
			 */


			// �� ���������� ���� � ���� ��������
			if (isset(self::$arTmpData['sale_OnSaleStatusOrder_' . $value . '_' . $order->getId()])) {
				return;
			}
			self::$arTmpData['sale_OnSaleStatusOrder_' . $value . '_' . $order->getId()] = true;


			/** @var \Bitrix\Sale\Order $order */

			if ($value != $value_old) {
				$arOrder = Handler::getOrderDataD7($order->getId(), true);

				$persone_type  = (isset($arOrder['PERSON_TYPE_ID']) ? $arOrder['PERSON_TYPE_ID'] : '1');
				$template_type = trim(self::getParam('ORDER_STATUS_' . $value . '_TEMPLATE_TYPE_' . $persone_type, null));

				if (Manager::getInstance()->isValidPhone($arOrder['PHONE']) && $template_type) {

					//��������
					Manager::getInstance()->sendTemplate($template_type, $arOrder, $arOrder['LID']);
				}
				else {
					AddMessage2Log(' iqsms.sender ERROR  ' . __METHOD__ . ' valid phone - ' . (Manager::getInstance()->isValidPhone($arOrder['PHONE']) ? 'Y' : 'N') . ', template_type - ' . $template_type);
				}
			}
		}

		/**
		 * ������ ������
		 *
		 */
		public function sale_OnSaleOrderCanceled($order)
		{
			\Bitrix\Main\Loader::includeModule('sale');

			// �� ���������� ���� � ���� ��������
			if (isset(self::$arTmpData['sale_OnSaleCancelOrder' . $order->getId()])) {
				return;
			}
			self::$arTmpData['sale_OnSaleCancelOrder' . $order->getId()] = true;

			/** @var \Bitrix\Sale\Order $order */
			$arOrder = Handler::getOrderDataD7($order->getId(), true);

			if ($arOrder['CANCELED'] == 'N') return;

			$persone_type  = (isset($arOrder['PERSON_TYPE_ID']) ? $arOrder['PERSON_TYPE_ID'] : '1');
			$template_type = trim(self::getParam('ORDER_CANCELED_TEMPLATE_TYPE_' . $persone_type, null));

			if (Manager::getInstance()->isValidPhone($arOrder['PHONE']) && $template_type) {

				//��������
				Manager::getInstance()->sendTemplate($template_type, $arOrder, $arOrder['LID']);
			}
			else {
				AddMessage2Log(' iqsms.sender ERROR  ' . __METHOD__ . ' valid phone - ' . (Manager::getInstance()->isValidPhone($arOrder['PHONE']) ? 'Y' : 'N') . ', template_type - ' . $template_type);
			}
		}

		/**
		 * ������ ������
		 *
		 */
		public function sale_OnSaleOrderPaid($order)
		{
			\Bitrix\Main\Loader::includeModule('sale');
			/** @var \Bitrix\Sale\Order $order */
			if ($order->isPaid()) {

				// �� ���������� ���� � ���� ��������
				if (isset(self::$arTmpData['sale_OnSalePayOrder' . $order->getId()])) {
					return;
				}
				self::$arTmpData['sale_OnSalePayOrder' . $order->getId()] = true;


				/** @var \Bitrix\Sale\Order $order */
				$arOrder = Handler::getOrderDataD7($order->getId(), true);

				if ($arOrder['PAYED'] != 'Y') return;

				$persone_type  = (isset($arOrder['PERSON_TYPE_ID']) ? $arOrder['PERSON_TYPE_ID'] : '1');
				$template_type = trim(self::getParam('ORDER_PAY_TEMPLATE_TYPE_' . $persone_type, null));

				if (Manager::getInstance()->isValidPhone($arOrder['PHONE']) && $template_type) {

					//��������
					Manager::getInstance()->sendTemplate($template_type, $arOrder, $arOrder['LID']);
				}
				else {
					AddMessage2Log(' iqsms.sender ERROR  ' . __METHOD__ . ' valid phone - ' . (Manager::getInstance()->isValidPhone($arOrder['PHONE']) ? 'Y' : 'N') . ', template_type - ' . $template_type);
				}
			}


		}


	}
