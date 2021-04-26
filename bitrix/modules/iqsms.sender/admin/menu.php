<?

	\Bitrix\Main\Localization\Loc::loadMessages(__FILE__);

	$MODULE_ID   = 'iqsms.sender';
	$MODULE_CODE = 'iqsms_sender';

	$moduleSort = 10000;
	$i          = 0;
	$MOD_RIGHT  = $APPLICATION->GetGroupRight($MODULE_ID);

	if ($MOD_RIGHT > "D") {
		$aMenu = array(
			"parent_menu" => "global_menu_services", // �������� � ������ "�������"
			"sort"        => $moduleSort,
			"section"     => $MODULE_ID,             // ��� ������ ����
			"url"         => '/bitrix/admin/' . $MODULE_ID . '_list.php?lang=' . LANGUAGE_ID,
			"text"        => GetMessage($MODULE_CODE . '_MAIN_MENU_LINK_NAME'),       // ����� ������ ����
			"title"       => GetMessage($MODULE_CODE . '_MAIN_MENU_LINK_DESCRIPTION'), // ����� ����������� ���������
			"icon"        => $MODULE_CODE . '_icon', // ����� ������
			"page_icon"   => $MODULE_CODE . '_page_icon', // ������� ������
			"items_id"    => $MODULE_CODE . '_main_menu_items',  // ������������� �����
			"items"       => array(),
			'more_url'    => array(
				'/bitrix/admin/' . $MODULE_ID . '_report.php?lang=' . LANGUAGE_ID
			),
		);

		$arPage = array(
			'send.php', 'list.php', 'template_list.php'
		);

		foreach ($arPage as $item) {


			$aMenu['items'][] = array(
				'url'       => '/bitrix/admin/' . $MODULE_ID . '_' . $item . '?lang=' . LANGUAGE_ID,
				'more_url'  => array(
					'/bitrix/admin/' . $MODULE_ID . '_' . str_replace('list.php', 'edit.php', $item) . '?lang=' . LANGUAGE_ID
				),
				'module_id' => $MODULE_ID,
				'text'      => GetMessage($MODULE_CODE . '_' . substr($item, 0, strpos($item, '.')) . '_MENU_LINK_NAME'),
				"title"     => GetMessage($MODULE_CODE . '_' . substr($item, 0, strpos($item, '.')) . '_MENU_LINK_DESCRIPTION'),
				//"icon"        => $MODULE_CODE.'_'.$item.'_icon', // ����� ������
				// "page_icon"   => $MODULE_CODE.'_'.$item.'_page_icon', // ������� ������
				'sort'      => $moduleSort + $i,
			);

		}


		$aMenu['items'][] = array(
			'url'       => '/bitrix/admin/settings.php?lang=' . LANGUAGE_ID . '&mid=' . $MODULE_ID . '&mid_menu=1',
			'more_url'  => array(),
			'module_id' => $MODULE_ID,
			'text'      => GetMessage($MODULE_CODE . '_OPTIONS_MENU_LINK_NAME'),
			"title"     => GetMessage($MODULE_CODE . '_OPTIONS_MENU_LINK_NAME'),
			'sort'      => $moduleSort + $i,
		);

		$aModuleMenu[] = $aMenu;
		return $aModuleMenu;
	}
	return false;
