<?
	require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");

	$MODULE_ID = "iqsms.sender";
	$MODULE_CODE = $MODULE_ID;

	\Bitrix\Main\Localization\Loc::loadMessages(__FILE__);
	\Bitrix\Main\Loader::includeModule($MODULE_ID);

	CUtil::InitJSCore('jquery');

	$oManagerTable = new \Iqsms\Sender\ManagerTable();
	$oManager      = \Iqsms\Sender\Manager::getInstance();
	$oTemplate     = new \Iqsms\Sender\TemplateTable();
	$app           = \Bitrix\Main\Application::getInstance();
	$req           = $app->getContext()->getRequest();
	$asset         = \Bitrix\Main\Page\Asset::getInstance();


	$dir  = str_replace($_SERVER['DOCUMENT_ROOT'], '', _normalizePath(dirname(__FILE__)));
	$bUtf = $oManager::isUTF();

	$PREMISION_DEFINE = $APPLICATION->GetGroupRight($MODULE_ID);

	if ($PREMISION_DEFINE == "D") $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
	if ($PREMISION_DEFINE == 'W') {
		$bReadOnly = false;
	}
	else  $bReadOnly = true;


	$sTableID = 'iqsms_sender_list_table';
	$sCurPage = $APPLICATION->GetCurPage();
	$sSAPEdit = $MODULE_ID . '_edit.php';
	$sReport  = $MODULE_ID . '_report.php';
	$sList  = $MODULE_ID . '_list.php';


	$oSort  = new CAdminSorting($sTableID, "SORT", "ASC");
	$sAdmin = new CAdminList($sTableID, $oSort);

	// меню

	// Массовые операции удаления ---------------------------------
	if ($arID = $sAdmin->GroupAction()) {
		switch ($req->getPost('action_button')) {
			case "delete":
				foreach ($arID as $id) {
					$res = $oManagerTable->delete($id);
				}
				break;
		}
	}


	// сайты
	$arSite = array();
	$dbr    = \CSite::GetList($by = 'sort', $order = 'asc');
	while ($ar = $dbr->Fetch()) {
		$arSite[$ar['ID']] = '[' . $ar['ID'] . '] ' . $ar['NAME'];
	}


	//шаблоны
	$arSiteData     = $oManager->getSiteData();
	$arTemplateData = array();
	$dbrTemplate    = $oTemplate->getList(array(
		'select' => array(
			'*'
		),
		'filter' => array(
			'SITE.SID' => $oManager->getCurrentSiteId()
		)
	));
	while ($ar = $dbrTemplate->fetch()) {
		$arTemplateData[$ar['TYPE']] = $ar['NAME'];
	}

	// Сортировка ------------------------------
	$by = 'ID';
	if (isset($_GET['by']) && in_array($_GET['by'], array('ID', 'PHONE', 'STATUS', 'CREATED'))) $by = $_GET['by'];
	$arOrder = array($by => ($_GET['order'] == 'ASC' ? 'ASC' : 'DESC'));


	// Постраничная навигация ------------------
	$navyParams        = CDBResult::GetNavParams(CAdminResult::GetNavSize(
		$sTableID,
		array('nPageSize' => 20, 'sNavID' => $APPLICATION->GetCurPage())
	));
	$usePageNavigation = true;
	if ($navyParams['SHOW_ALL']) {
		$usePageNavigation = false;
	}
	else {
		$navyParams['PAGEN'] = (int)$navyParams['PAGEN'];
		$navyParams['SIZEN'] = (int)$navyParams['SIZEN'];
	}


	// Запрос -----------------------------------
	$arQuery = array(
		'select' => array('*'),
		'order'  => $arOrder
	);
	if ($usePageNavigation) {

		$totalCount = 0;
		$totalPages = 0;
		$dbrCount   = $oManagerTable->getList(array(
			'select' => array('CNT')
		));
		if ($ar = $dbrCount->fetch()) {
			$totalCount = $ar['CNT'];
		}

		if ($totalCount > 0) {
			$totalPages = ceil($totalCount / $navyParams['SIZEN']);
			if ($navyParams['PAGEN'] > $totalPages) {
				$navyParams['PAGEN'] = $totalPages;
			}
			$arQuery['limit']  = $navyParams['SIZEN'];
			$arQuery['offset'] = $navyParams['SIZEN'] * ($navyParams['PAGEN'] - 1);
		}
		else {
			$navyParams['PAGEN'] = 1;
			$arQuery['limit']    = $navyParams['SIZEN'];
			$arQuery['offset']   = 0;
		}
	}

	$dbResultList = new CAdminResult($oManagerTable->getList($arQuery), $sTableID);
	if ($usePageNavigation) {
		$dbResultList->NavStart($arQuery['limit'], $navyParams['SHOW_ALL'], $navyParams['PAGEN']);
		$dbResultList->NavRecordCount = $totalCount;
		$dbResultList->NavPageCount   = $totalPages;
		$dbResultList->NavPageNomer   = $navyParams['PAGEN'];
	}
	else {
		$dbResultList->NavStart();
	}


	$sAdmin->NavText($dbResultList->GetNavPrint(GetMessage($MODULE_CODE . '_PAGE_LIST_TITLE_NAV_TEXT')));

	$sAdmin->AddHeaders(array(
		array(
			"id"      => 'ID',
			"content" => GetMessage($MODULE_CODE . '_HEAD.ID'),
			"sort"    => 'ID',
			"default" => true
		),
		array(
			"id"      => 'PHONE',
			"content" => GetMessage($MODULE_CODE . '_HEAD.PHONE'),
			"sort"    => 'PHONE',
			"default" => true
		),
		array(
			"id"      => 'STATUS',
			"content" => GetMessage($MODULE_CODE . '_HEAD.STATUS'),
			"sort"    => 'STATUS',
			"default" => true
		),
		array(
			"id"      => 'TEXT',
			"content" => GetMessage($MODULE_CODE . '_HEAD.TEXT'),
			"sort"    => 'TEXT',
			"default" => true
		),
		array(
			"id"      => 'SITE_ID',
			"content" => GetMessage($MODULE_CODE . '_HEAD.SITE_ID'),
			"sort"    => 'SITE_ID',
			"default" => true
		),
		array(
			"id"      => 'CREATED',
			"content" => GetMessage($MODULE_CODE . '_HEAD.CREATED'),
			"sort"    => 'CREATED',
			"default" => true
		),
		array(
			"id"      => 'COMMENT',
			"content" => GetMessage($MODULE_CODE . '_HEAD.COMMENT'),
			"sort"    => 'COMMENT',
			"default" => true
		),


	));


	while ($sArActions = $dbResultList->NavNext(true, 's_')) {


		$row = &$sAdmin->AddRow($s_ID, $sArActions);
		$row->AddField('PHONE', $s_PHONE);

		$error_status = '';
		switch ($s_STATUS) {
			case \Iqsms\Sender\SMS_STATUS_ERROR: {

				if (isset($s_PARAMS['error_description'])) {
					$error_status .= '<br>' . $s_PARAMS['error_description'];
				}
				break;
			}
		}
		$row->AddField('STATUS', GetMessage($MODULE_CODE . '_HEAD.STATUS_' . $s_STATUS) . $error_status);

		$row->AddField('TEXT', (isset($arTemplateData[$s_TYPE]) ? '<small><b>' . $arTemplateData[$s_TYPE] . '</b></small><br>' : '') . $s_TEXT);
		$row->AddField('CREATED', $s_CREATED);
		$row->AddField('COMMENT', '<div class="sms_comment_box" > <span>' . TruncateText($s_COMMENT, 30) . '</span><div class="more">' . $s_COMMENT . '</div></div>');
		$row->AddField('SITE_ID', (isset($arSite[$s_SITE_ID]) ? $arSite[$s_SITE_ID] : ''));
		$row->AddField('ID', $s_ID);

	}


	$sAdmin->AddFooter(
		array(
			array(
				"title" => GetMessage($MODULE_CODE . '_LIST_SELECTED'),
				"value" => $dbResultList->SelectedRowsCount()
			),
			array(
				"counter" => true,
				"title"   => GetMessage($MODULE_CODE . '_LIST_CHECKED'),
				"value"   => "0"
			),
		)
	);

	if (!$bReadOnly) {
		$sAdmin->AddGroupActionTable(
			array(
				"delete" => GetMessage($MODULE_CODE . '_LIST_DELETE'),
			)
		);
	}

	// меню
	$sContent = array(
		array(
			"TEXT"  => GetMessage($MODULE_CODE . '_MENU_BTN_REPORT'),
			"LINK"  => 'javascript:window.open("' . $sList . "?lang=" . LANG . '&method=file&sessid='.bitrix_sessid().'");',
			"TITLE" => GetMessage($MODULE_CODE . '_MENU_BTN_REPORT'),
			"ICON"  => "btn_new",
		),
	);
	$sMenu    = new CAdminContextMenu($sContent);


	$sAdmin->CheckListMode();
	$APPLICATION->SetTitle(GetMessage($MODULE_CODE . '_PAGE_LIST_TITLE'));

	require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");


	\Iqsms\Sender\Manager::getInstance()->addAdminPageCssJs();


?>
<?
	$oManager->getAdminHeader();
?>

<?php
	$sMenu->Show();
?>

	<div class="iqsms-sender_error_description_box">
		<div class="close"><?= GetMessage($MODULE_CODE . '_FORM.CLOSE_ERROR'); ?></div>
		<div class="descr">

		</div>
	</div>

	<div class="iqsms_sender_list_table_box">
		<?


			$sAdmin->DisplayList();


		?>
	</div>


<?
	require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php");
?>
