<?
	global $APPLICATION;

	$BXMAKER_MODULE_ID = 'iqsms.sender';

	\Bitrix\Main\Localization\Loc::loadMessages(__FILE__);
	\Bitrix\Main\Loader::includeModule($BXMAKER_MODULE_ID);

	$PERMISSION = $APPLICATION->GetGroupRight($BXMAKER_MODULE_ID);

	$app = \Bitrix\Main\Application::getInstance();
	$req = $app->getContext()->getRequest();

	if ($PERMISSION != "W") {
		$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
		die();
	}

	$oManager = \Iqsms\Sender\Manager::getInstance();

	CUtil::InitJSCore('jquery');

	\Bitrix\Main\Loader::includeModule('sale');


	$bModuleInstalled = array(
		'sale' => \Bitrix\Main\ModuleManager::isModuleInstalled('sale')
	);

	$dir = str_replace($_SERVER['DOCUMENT_ROOT'], '', _normalizePath(dirname(__FILE__))) . '/admin';


	$arOptions = array(
		//	array(
		//		'KEY'     => 'GEN', // COption::GetString('iqsms.sender', 'GEN.DEBUG', '');
		//		'NAME'    => GetMessage('AP_EDIT_TAB.GEN'),
		//		'OPTIONS' => array(
		//
		//		)
		//	)
	);


	// доступные шаблоны сообщений
	$arTemplateList = array(
		'REFERENCE'    => array(
			GetMessage('AP_OPTION.HANDLER.NO_SELECT')
		),
		'REFERENCE_ID' => array(
			''
		)
	);
	$oTemplate      = new \Iqsms\Sender\TemplateTable();
	$dbrTemplate    = $oTemplate->getList();
	while ($arTemplate = $dbrTemplate->fetch()) {
		$arTemplateList['REFERENCE'][]    = '[' . $arTemplate['TYPE'] . '] ' . $arTemplate['NAME'];
		$arTemplateList['REFERENCE_ID'][] = $arTemplate['TYPE'];
	}

	// получаем массив сайтов
	$arSite = array();
	$dbr    = \CSite::GetList($by = 'sort', $order = 'asc');
	while ($ar = $dbr->Fetch()) {
		$arSite[$ar['ID']] = '[' . $ar['ID'] . '] ' . $ar['NAME'];
	}

	//группы пользвоателей
	$arGroup = array(
		'REFERENCE'    => array(),
		'REFERENCE_ID' => array()
	);
	$oGroup  = new Bitrix\Main\GroupTable();
	$dbr     = $oGroup->getList();
	while ($ar = $dbr->fetch()) {
		$arGroup['REFERENCE'][]    = '[' . $ar['ID'] . '] ' . $ar['NAME'];
		$arGroup['REFERENCE_ID'][] = $ar['ID'];

	}

	if (CModule::IncludeModule('sale')) {
		// Статусы заказов
		$arStatuses = array();
		if ($bModuleInstalled['sale']) {
			$oSaleStatus   = new \CSaleStatus();
			$dbrSaleStatus = $oSaleStatus->GetList(array("SORT" => 'ASC'), array('LID' => LANGUAGE_ID));

			$dbrSaleStatus = \CSaleStatus::GetList(
				array('SORT' => 'ASC'),
				array('LID' => LANGUAGE_ID),
				false,
				false,
				array('ID', 'SORT', /*'TYPE',*/
					'NOTIFY', 'LID', 'NAME', 'DESCRIPTION', $by)
			);
			while ($arSaleStatus = $dbrSaleStatus->Fetch()) {
				$arStatuses[$arSaleStatus['ID']] = $arSaleStatus;
			}
		}

		//округление стоимости заказа
		$arOrderPriceRoundValues = array(
			'REFERENCE'    => array(
				GetMessage('AP_OPTION.ORDER_PRICE_ROUND_PLACEHOLDER', array('#PRICE#' => '1023.5360')),
				GetMessage('AP_OPTION.ORDER_PRICE_ROUND_PLACEHOLDER', array('#PRICE#' => '1023.536')),
				GetMessage('AP_OPTION.ORDER_PRICE_ROUND_PLACEHOLDER', array('#PRICE#' => '1023.53')),
				GetMessage('AP_OPTION.ORDER_PRICE_ROUND_PLACEHOLDER', array('#PRICE#' => '1023.5')),
				GetMessage('AP_OPTION.ORDER_PRICE_ROUND_PLACEHOLDER', array('#PRICE#' => '1023')),
			),
			'REFERENCE_ID' => array('4', '3', '2', '1', '0')
		);
	}


	foreach ($arSite as $sid => $sname) {


		$key             = 'HANDLER';
		$arOptionCurrent = array(
			'KEY'     => $key, // COption::GetString('iqsms.sender', 'HANDLER.DEBUG', '');
			'NAME'    => GetMessage('AP_EDIT_TAB.SITE', array('#SITE#' => $sname)),
			'OPTIONS' => array()
		);


		$arOptionCurrent['OPTIONS'][] = array(
			'SID'           => $sid,
			'CODE'          => 'DEBUG',
			'CODE_NAME'     => GetMessage('AP_OPTION.GEN.DEBUG'),
			'TYPE'          => 'CHECKBOX',
			'DEFAULT_VALUE' => 'N'
		);
		$arOptionCurrent['OPTIONS'][] = array(
			'SID'           => $sid,
			'CODE'          => 'CLEAN_SMS_HISTORY',
			'CODE_NAME'     => GetMessage('AP_OPTION.GEN.CLEAN_SMS_HISTORY'),
			'TYPE'          => 'STRING',
			'DEFAULT_VALUE' => '30'
		);


		// ИНтернет-магазин
		if (CModule::IncludeModule('sale')) {


			//Округлять
			$arOptionCurrent['OPTIONS'][] = array(
				'SID'           => $sid,
				'GROUP'         => 'SALE',
				'CODE_NAME'     => GetMessage('AP_OPTION.' . $key . '.ORDER_PRICE_ROUND'),
				'CODE'          => 'ORDER_PRICE_ROUND',
				'TYPE'          => 'LIST',
				'VALUES'        => $arOrderPriceRoundValues,
				'DEFAULT_VALUE' => '',
			);


			//Типы плательщиков
			$arPersoneType = array();
			$dbrPType      = \CSalePersonType::GetList(Array("SORT" => "ASC"), Array("LID" => $sid));
			while ($arPType = $dbrPType->Fetch()) {


				// свойства текущего типа плательщика
				$arOrderProps = array(
					'REFERENCE'    => array(
						GetMessage('AP_OPTION.HANDLER.NO_SELECT')
					),
					'REFERENCE_ID' => array(
						''
					)
				);
				$dbrProp      = CSaleOrderProps::GetList(array('SORT' => 'ASC'), array('PERSON_TYPE_ID' => $arPType['ID']));
				while ($arProp = $dbrProp->Fetch()) {

					$arOrderProps['REFERENCE'][]    = $arProp['NAME'];
					$arOrderProps['REFERENCE_ID'][] = $arProp['CODE'];
				}

				// номер телефона
				$arOptionCurrent['OPTIONS'][] = array(
					'SID'           => $sid,
					'GROUP'         => 'SALE',
					'GROUP_NAME'    => GetMessage('AP_OPTION.' . $key . '.PERSON_TYPE_GROUP', array('#NAME#' => $arPType['NAME'])),
					'CODE'          => 'PERSON_TYPE_' . $arPType['ID'],
					'CODE_NAME'     => GetMessage('AP_OPTION.' . $key . '.PERSON_TYPE'),
					'TYPE'          => 'LIST',
					'VALUES'        => $arOrderProps,
					'DEFAULT_VALUE' => '',
				);

				// сохранять пользователю номер телефона
				$arOptionCurrent['OPTIONS'][] = array(
					'SID'           => $sid,
					'GROUP'         => 'SALE',
					'CODE'          => 'SAVE_PHONE_' . $arPType['ID'],
					'CODE_NAME'     => GetMessage('AP_OPTION.' . $key . '.SAVE_PHONE'),
					'TYPE'          => 'CHECKBOX',
					'VALUES'        => '',
					'DEFAULT_VALUE' => 'N',
				);
				// сохранять пользователю номер телефона
				$arOptionCurrent['OPTIONS'][] = array(
					'SID'           => $sid,
					'GROUP'         => 'SALE',
					'CODE'          => 'SEND_PASSWORD_' . $arPType['ID'],
					'CODE_NAME'     => GetMessage('AP_OPTION.' . $key . '.SEND_PASSWORD'),
					'TYPE'          => 'CHECKBOX',
					'VALUES'        => '',
					'DEFAULT_VALUE' => 'N',
				);

				// Магазин
				//Новый заказ
				$arOptionCurrent['OPTIONS'][] = array(
					'SID'           => $sid,
					'GROUP'         => 'SALE',
					'CODE_NAME'     => GetMessage('AP_OPTION.' . $key . '.ORDER_NEW_TEMPLATE_TYPE'),
					'CODE'          => 'ORDER_NEW_TEMPLATE_TYPE_' . $arPType['ID'],
					'TYPE'          => 'LIST',
					'VALUES'        => $arTemplateList,
					'DEFAULT_VALUE' => '',
				);

				//установлен трекер
				$arOptionCurrent['OPTIONS'][] = array(
					'SID'           => $sid,
					'GROUP'         => 'SALE',
					'CODE_NAME'     => GetMessage('AP_OPTION.' . $key . '.ORDER_TRACKING_NUMBER_TEMPLATE_TYPE'),
					'CODE'          => 'ORDER_TRACKING_NUMBER_TEMPLATE_TYPE_' . $arPType['ID'],
					'TYPE'          => 'LIST',
					'VALUES'        => $arTemplateList,
					'DEFAULT_VALUE' => '',
				);

				$arOptionCurrent['OPTIONS'][] = array(
					'SID'           => $sid,
					'CODE_NAME'     => GetMessage('AP_OPTION.' . $key . '.ORDER_CANCELED_TEMPLATE_TYPE'),
					'CODE'          => 'ORDER_CANCELED_TEMPLATE_TYPE_' . $arPType['ID'],
					'TYPE'          => 'LIST',
					'VALUES'        => $arTemplateList,
					'DEFAULT_VALUE' => '',
				);
				$arOptionCurrent['OPTIONS'][] = array(
					'SID'           => $sid,
					'CODE_NAME'     => GetMessage('AP_OPTION.' . $key . '.ORDER_PAY_TEMPLATE_TYPE'),
					'CODE'          => 'ORDER_PAY_TEMPLATE_TYPE_' . $arPType['ID'],
					'TYPE'          => 'LIST',
					'VALUES'        => $arTemplateList,
					'DEFAULT_VALUE' => '',
				);

				//Статусы заказов
				foreach ($arStatuses as $status_id => $arItem) {
					$arOptionCurrent['OPTIONS'][] = array(
						'SID'           => $sid,
						'CODE_NAME'     => GetMessage('AP_OPTION.' . $key . '.ORDER_STATUS_TEMPLATE_TYPE', array('#STATUS#' => $arItem['NAME'], '#STATUS_ID#' => $status_id)),
						'CODE'          => 'ORDER_STATUS_' . $arItem['ID'] . '_TEMPLATE_TYPE_' . $arPType['ID'],
						'TYPE'          => 'LIST',
						'DEFAULT_VALUE' => '',
						'VALUES'        => $arTemplateList
					);
				}
			}
		}


		/*
				// Пользователи
				$arUserFields = array(
					'REFERENCE' => array(
						GetMessage('AP_OPTION.HANDLER.NO_SELECT'), 'PERSONAL_MOBILE', 'PERSONAL_PHONE'
					),
					'REFERENCE_ID' => array(
						'', 'PERSONAL_MOBILE', 'PERSONAL_PHONE'
					)
				);
				$dbr = \CUserTypeEntity::GetList(array(), array(
					'ENTITY_ID' => 'USER'
				));
				while ($ar = $dbr->fetch()) {
					$arUserFields['REFERENCE'][] = $ar['FIELD_NAME'];
					$arUserFields['REFERENCE_ID'][] = $ar['FIELD_NAME'];
				}
				$arOptionCurrent['OPTIONS'][] = array(
					'SID' => $sid,
					'GROUP' => 'USER',
					'CODE_NAME' => GetMessage('AP_OPTION.' . $key . '.USER_PHONE_FIELD'),
					'CODE' => 'USER_PHONE_FIELD',
					'TYPE' => 'LIST',
					'VALUES' => $arUserFields,
					'DEFAULT_VALUE' => '',
				);
				$arOptionCurrent['OPTIONS'][] = array(
					'SID' => $sid,
					'GROUP' => 'USER',
					'CODE_NAME' => GetMessage('AP_OPTION.' . $key . '.USER_ADD_TEMPLATE_TYPE'),
					'CODE' => 'USER_ADD_TEMPLATE_TYPE',
					'TYPE' => 'LIST',
					'VALUES' => $arTemplateList,
					'DEFAULT_VALUE' => '',
				);
				$arOptionCurrent['OPTIONS'][] = array(
					'SID' => $sid,
					'GROUP' => 'USER',
					'CODE_NAME' => GetMessage('AP_OPTION.' . $key . '.USER_UPDATE_TEMPLATE_TYPE'),
					'CODE' => 'USER_UPDATE_TEMPLATE_TYPE',
					'TYPE' => 'LIST',
					'VALUES' => $arTemplateList,
					'DEFAULT_VALUE' => '',
				);

				*/


		$arOptionCurrent['OPTIONS'][] = array(
			'SID'           => $sid,
			'GROUP'         => 'SERVICE',
			'CODE_NAME'     => GetMessage('AP_OPTION.SERVICE.LOGIN'),
			'CODE'          => 'LOGIN',
			'TYPE'          => 'STRING',
			'DEFAULT_VALUE' => '',
		);
		$arOptionCurrent['OPTIONS'][] = array(
			'SID'           => $sid,
			'GROUP'         => 'SERVICE',
			'CODE_NAME'     => GetMessage('AP_OPTION.SERVICE.PASS'),
			'CODE'          => 'PASS',
			'TYPE'          => 'STRING',
			'TYPE_INPUT'    => 'password',
			'DEFAULT_VALUE' => '',
		);


		// вараинты
		$arSenders = $oManager->getSenders($sid);
		$arServiceList = array(
			'REFERENCE' => array(),
			'REFERENCE_ID' => array()
		);

		foreach($arSenders as $service)
		{
			$arServiceList['REFERENCE'][] = $service;
			$arServiceList['REFERENCE_ID'][] = $service;
		}


		$arOptionCurrent['OPTIONS']['FROM'] = array(
			'SID'           => $sid,
			'GROUP'         => 'SERVICE',
			'CODE_NAME'     => GetMessage('AP_OPTION.SERVICE.FROM'),
			'CODE'          => 'FROM',
			'TYPE'          => 'LIST',
			'VALUES' => $arServiceList,
		);

		$arOptions[] = $arOptionCurrent;
	}


	//////////////////////////////////////////////////////////////////////////////

	if ($PERMISSION == "W") {
		$oOption = new \Bitrix\Main\Config\Option();

		if (($apply || $save) && check_bitrix_sessid() && $req->isPost()) {
			foreach ($arOptions as $arOption) {
				
				foreach ($arOption['OPTIONS'] as $arItem) {

					switch ($arItem['TYPE']) {
						case 'STRING': {
							$oOption->set($BXMAKER_MODULE_ID,  $arItem['CODE'], ($req->getPost( $arItem['CODE'] . '_' . $arItem['SID']) ? trim($req->getPost( $arItem['CODE'] . '_' . $arItem['SID'])) : ''), $arItem['SID']);
							break;
						}
						case 'CHECKBOX': {
							$oOption->set($BXMAKER_MODULE_ID,  $arItem['CODE'], ($req->getPost( $arItem['CODE'] . '_' . $arItem['SID']) && $req->getPost( $arItem['CODE'] . '_' . $arItem['SID']) == 'Y' ? 'Y' : 'N'), $arItem['SID']);
							break;
						}
						case 'LIST': {
							$oOption->set($BXMAKER_MODULE_ID,  $arItem['CODE'], (!is_null($req->getPost( $arItem['CODE'] . '_' . $arItem['SID'])) && in_array($req->getPost( $arItem['CODE'] . '_' . $arItem['SID']), $arItem['VALUES']['REFERENCE_ID']) ? $req->getPost( $arItem['CODE'] . '_' . $arItem['SID']) : ''), $arItem['SID']);
							break;
						}
						case 'MULTY_LIST': {

							$strMultyVal = '';
							if ($req->getPost( $arItem['CODE'] . '_' . $arItem['SID'])
								&& is_array($req->getPost( $arItem['CODE'] . '_' . $arItem['SID']))
							) {

								$ar = array_diff(array_intersect($req->getPost( $arItem['CODE'] . '_' . $arItem['SID']), $arItem['VALUES']['REFERENCE_ID']), array('', ' '));
								if (count($ar)) {
									$strMultyVal = implode(',', $ar);
								}
							}

							$oOption->set(
								$BXMAKER_MODULE_ID,
								 $arItem['CODE'],
								$strMultyVal,
								$arItem['SID']);

							break;
						}
					}
				}
			}
		}
	}



	if($oManager->getService()->getLogin() == '') {

		$oManager->reConnect();

		foreach ($arSite as $sid => $sname) {

			$arSenders = $oManager->getSenders($sid);
			$arServiceList = array(
				'REFERENCE' => array(),
				'REFERENCE_ID' => array()
			);

			foreach($arSenders as $service)
			{
				$arServiceList['REFERENCE'][] = $service;
				$arServiceList['REFERENCE_ID'][] = $service;
			}


			$arOptionCurrent['OPTIONS']['FROM'] = array(
				'SID'           => $sid,
				'GROUP'         => 'SERVICE',
				'CODE_NAME'     => GetMessage('AP_OPTION.SERVICE.FROM'),
				'CODE'          => 'FROM',
				'TYPE'          => 'LIST',
				'VALUES' => $arServiceList,
			);
		}
	}

?>
<?

	\Iqsms\Sender\Manager::getInstance()->addAdminPageCssJs();

	\Iqsms\Sender\Manager::getInstance()->getAdminHeader();


	// TABS
	$tabs = array();
	foreach ($arOptions as $k => $arOption) {
		$tabs[] = array(
			'DIV'   => $arOption['KEY'] . $k,
			'TAB'   => $arOption['NAME'],
			'ICON'  => '',
			'TITLE' => (isset($arOption['DESCRIPTION']) ? $arOption['DESCRIPTION'] : $arOption['NAME'])
		);
	}

	$tab = new CAdminTabControl('options_tabs', $tabs);

	$tab->Begin();
?>


<form class="iqsms_sender_option_edit_box" method="post"
	  action="<? echo $APPLICATION->GetCurPage() ?>?mid=<?= urlencode($mid) ?>&amp;lang=<?= LANGUAGE_ID ?>&amp;mid_menu=<?= $mid_menu ?>"><?= bitrix_sessid_post(); ?>

	<?
		$oOption = new \Bitrix\Main\Config\Option();

		$i = 0;


		// проходим по блокам параметров
		foreach ($arOptions as $k => $arOption) {
			// новая влкадка
			$tab->BeginNextTab();


			$key   = $arOption['KEY'];
			$group = '';
			$i++;

			if ($i >= 0) {
				?>
				<tr>
					<td colspan="2"
						style="padding:15px; border:1px solid #00b058; background: rgba(0, 221, 98, 0.25);"><?= GetMessage('AP_OPTION.' . $key . '.GROUP.TEMPLATE_TYPE_LIST_DESCRIPTION'); ?></td>
				</tr>
				<?
			}

			// параметры блока
			foreach ($arOption['OPTIONS'] as $arItem) {
				// Главный заголовок блока
				if (isset($arItem['GROUP']) && $arItem['GROUP'] != $group) {
					$group = $arItem['GROUP'];
					?>
					<tr class="heading">
						<td colspan="2"><?= GetMessage('AP_OPTION.' . $key . '.GROUP.' . $arItem['GROUP']); ?></td>
					</tr>

					<?
				}
				?>

				<?
				// Подзаголовок
				if (isset($arItem['GROUP_NAME'])) {
					?>
					<tr class="heading">
						<td colspan="2" style="font-size: 0.9em;  background: #fff;"><?= $arItem['GROUP_NAME']; ?></td>
					</tr>
					<?
				}
				?>

				<tr>
					<td class="first"
						style="width:30%;"><?= (isset($arItem['CODE_NAME']) ? $arItem['CODE_NAME'] : GetMessage('AP_OPTION.' .  $arItem['CODE'])); ?></td>
					<td><?
							switch ($arItem['TYPE']) {
								case 'STRING': {
									echo InputType((!!$arItem['TYPE_INPUT'] ? $arItem['TYPE_INPUT'] : 'text'),  $arItem['CODE'] . '_' . $arItem['SID'], $oOption->get($BXMAKER_MODULE_ID,  $arItem['CODE'], $arItem['DEFAULT_VALUE'], $arItem['SID']), '');
									break;
								}

								case 'CHECKBOX': {
									echo InputType('checkbox',  $arItem['CODE'] . '_' . $arItem['SID'], 'Y', array($oOption->get($BXMAKER_MODULE_ID,  $arItem['CODE'], $arItem['DEFAULT_VALUE'], $arItem['SID'])));
									break;
								}

								case 'LIST': {
									echo SelectBoxFromArray( $arItem['CODE'] . '_' . $arItem['SID'], $arItem['VALUES'], $oOption->get($BXMAKER_MODULE_ID,  $arItem['CODE'], $arItem['DEFAULT_VALUE'], $arItem['SID']));
									break;
								}

								case 'MULTY_LIST': {

									$vals = $oOption->get($BXMAKER_MODULE_ID,  $arItem['CODE'], $arItem['DEFAULT_VALUE'], $arItem['SID']);

									echo SelectBoxMFromArray(
										 $arItem['CODE'] . '_' . $arItem['SID'] . '[]',
										$arItem['VALUES'],
										explode(',', $vals),
										'', '', 8
									);
									break;
								}
							}
							ShowJSHint(GetMessage('AP_OPTION.' .  $arItem['CODE'] . '.HELP'));

						?></td>
					<td></td>
				</tr>
				<?
			}
		}
		$tab->Buttons(array("disabled" => false));

		$tab->End();
	?>

</form>
