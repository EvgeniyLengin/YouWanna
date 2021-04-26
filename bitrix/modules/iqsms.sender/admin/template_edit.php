<?
	require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");

	$MODULE_ID = "iqsms.sender";
	$MODULE_CODE = $MODULE_ID;

	\Bitrix\Main\Localization\Loc::loadMessages(__FILE__);
	\Bitrix\Main\Loader::includeModule($MODULE_ID);


	CUtil::InitJSCore('jquery');
	$dir = str_replace($_SERVER['DOCUMENT_ROOT'], '', _normalizePath(dirname(__FILE__)));


	$oTemplateSite = new \Iqsms\Sender\Template\SiteTable();
	$oTemplate     = new \Iqsms\Sender\TemplateTable();

	$app = \Bitrix\Main\Application::getInstance();
	$req = $app->getContext()->getRequest();

	$bUtf = defined("BX_UTF");


	if ($req->isPost() && check_bitrix_sessid('sessid') && $req->getPost('method') && $req->getPost('method') == 'getTemplateTypeFields') {
		$APPLICATION->RestartBuffer();
		header('Content-Type: application/json');

		$arJson = array(
			'error'    => array(),
			'response' => array()
		);

		do {


			if (!$req->getPost('type') || strlen(trim($req->getPost('type'))) <= 0) {
				$arJson['error'] = array(
					'error_code' => 'invalid_text',
					'error_msg'  => GetMessage($MODULE_CODE . '.AJAX.INVALID_TYPE'),
					'error_more' => array()
				);
				break;
			}

			$bdr = $oTemplateType->getList(array(
				'filter' => array(
					'ID' => $req->getPost('type')
				)
			));
			if ($ar = $bdr->fetch()) {
				$fields = GetMessage($MODULE_CODE . '.AJAX.TEMPLATE_TYPE_FIELD_SITE_NAME');
				$fields .= GetMessage($MODULE_CODE . '.AJAX.TEMPLATE_TYPE_FIELD_SERVER_NAME');
				$fields .= nl2br(trim($ar['DESCR'], "\r\n "));

				$ar['DESCR'] = $fields;

				$arJson['response'] = array(
					'item' => $ar
				);
			}
			else {
				$arJson['error'] = array(
					'error_code' => '0',
					'error_msg'  => GetMessage($MODULE_CODE . '.AJAX.UNKNOW_TYPE'),
					'error_more' => ''
				);
				break;

			}
		} while (false);


		if (!empty($arJson['error'])) {
			echo json_encode(array(
				'error'  => ($bUtf ? $arJson['error'] : $APPLICATION->ConvertCharsetArray($arJson['error'], LANG_CHARSET, 'UTF-8')),
				'status' => 0
			));
		}
		else {
			echo json_encode(array(
				'response' => ($bUtf ? $arJson['response'] : $APPLICATION->ConvertCharsetArray($arJson['response'], LANG_CHARSET, 'UTF-8')),
				'status'   => 1
			));

		}
		die();
	}


	$sTableID    = $MODULE_ID;
	$sCurPage    = $APPLICATION->GetCurPage();
	$page_prefix = $MODULE_ID . '_template_';
	$errors      = null;

	// ПРОВЕРКА ПРАВ ДОСТУПА
	$PREMISION_DEFINE = $APPLICATION->GetGroupRight($MODULE_ID);

	if ($PREMISION_DEFINE != 'W') {
		$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
		die();
	}


	// Навигация над формой
	$oMenu = new CAdminContextMenu(array(
		array(
			"TEXT"  => GetMessage($MODULE_CODE . '.NAV_BTN.RETURN'),
			"LINK"  => $page_prefix . 'list.php?lang=' . LANG,
			"TITLE" => GetMessage($MODULE_CODE . '.NAV_BTN.RETURN'),
		),
	));


	// визуализаторы
	$fname = 'iqsms_sender_template_edit_form';

	// РЕДАКТИРОВАНИЕ
	$arSID = array();
	if ($req->get('ID')) {
		$dbr = $oTemplate->getList(array(
			'filter' => array(
				'ID' => intval($req->get('ID'))
			)
		));
		if ($ar = $dbr->fetch()) {
			$arResult = $ar;
		}

		// привязки
		$dbr = $oTemplateSite->getList(array(
			'filter' => array(
				'TID' => intval($req->get('ID'))
			)));
		while ($ar = $dbr->fetch()) {
			$arSID[$ar['ID']] = $ar['SID'];
		}
	}


	// сайты
	$arSite = array();
	$dbr    = \CSite::GetList($by = 'sort', $order = 'asc');
	while ($ar = $dbr->Fetch()) {
		$arSite[$ar['ID']] = $ar;
	}

	//типы почтовых событий
	$arEventTypes    = array();
	$arEventTypeList = array(
		'REFERENCE_ID' => array(),
		'REFERENCE'    => array(),
	);
	$dbrEventType    = \CEventType::GetList(array('LID' => LANGUAGE_ID), array('SORT' => 'ASC'));
	while ($arEventType = $dbrEventType->Fetch()) {
		$arEventTypes[$arEventType['EVENT_NAME']] = $arEventType['DESCRIPTION'];

		$arEventTypeList['REFERENCE_ID'][] = $arEventType['EVENT_NAME'];
		$arEventTypeList['REFERENCE'][]    = '[' . $arEventType['EVENT_NAME'] . '] ' . $arEventType['NAME'];
	}


	// СОХРАНЕНИЕ. ПРИМЕНЕНИЕ
	if (($apply || $save) && check_bitrix_sessid() && $req->isPost()) {

		do {

			$errors   = array();
			$arFields = array();

			$arFields['EVENT']      = ($req->getPost('EVENT') && (trim($req->getPost('EVENT'))) == 'Y' ? true : false);
			$arFields['TYPE']       = ($req->getPost('TYPE') && strlen(trim($req->getPost('TYPE'))) > 0 ? trim($req->getPost('TYPE')) : '');
			$arFields['NAME']       = ($req->getPost('NAME') ? trim($req->getPost('NAME')) : '');
			$arFields['ACTIVE']     = ($req->getPost('ACTIVE') ? true : false);
			$arFields['PHONE']      = ($req->getPost('PHONE') ? trim($req->getPost('PHONE')) : '');
			$arFields['PHONE_COPY'] = ($req->getPost('PHONE_COPY') ? trim($req->getPost('PHONE_COPY')) : '');
			$arFields['TEXT']       = ($req->getPost('TEXT') ? preg_replace("#[ ]+#", ' ', trim($req->getPost('TEXT'))) : '');

			if ($arFields['EVENT']) {
				$arFields['TYPE'] = ($req->getPost('EVENT_TYPE') && isset($arEventTypes[(trim($req->getPost('EVENT_TYPE')))]) ? trim($req->getPost('EVENT_TYPE')) : '');
			}


			$arResult = $arFields;

			if (strlen(trim($arFields['NAME'])) <= 0) {
				$errors[] = new \Bitrix\Main\Entity\EntityError(GetMessage($MODULE_CODE . '.FIELD_ERROR.EMPTY_NAME'));
				break;
			}
			if (strlen(trim($arFields['TYPE'])) <= 0) {
				if ($arFields['EVENT']) {
					$errors[] = new \Bitrix\Main\Entity\EntityError(GetMessage($MODULE_CODE . '.FIELD_ERROR.EMPTY_EVENT_TYPE'));
				}
				else {
					$errors[] = new \Bitrix\Main\Entity\EntityError(GetMessage($MODULE_CODE . '.FIELD_ERROR.EMPTY_TYPE'));
				}
				break;
			}
			if (strlen(trim($arFields['PHONE'])) <= 0) {
				$errors[] = new \Bitrix\Main\Entity\EntityError(GetMessage($MODULE_CODE . '.FIELD_ERROR.EMPTY_PHONE'));
				break;
			}

			$arTemplateSite = array();
			if ($req->getPost('SITE')) {
				foreach ((array)$req->getPost('SITE') as $sid => $val) {
					if (isset($arSite[$sid])) {
						$arTemplateSite[] = $sid;
					}
				}
			}

			if (count($arTemplateSite) <= 0) {
				$errors[] = new \Bitrix\Main\Entity\EntityError(GetMessage($MODULE_CODE . '.FIELD_ERROR.EMPTY_SITE'));
				break;
			}


			if (empty($errors)) {
				if ($req->get('ID')) {
					// обнолвнеие
					$result = $oTemplate->update(intval($req->get('ID')), $arFields);

					if ($result->isSuccess()) {


						$arSidDelete = array_diff(array_values($arSID), $arTemplateSite);
						$arSidAdd    = array_diff($arTemplateSite, array_values($arSID));
						// развернем массив для удобства удаления
						$arSIDflip = array_flip($arSID);

						//удаляем
						foreach ($arSidDelete as $sid) {
							$oTemplateSite->delete($arSIDflip[$sid]);
						}
						//добавляем
						foreach ($arSidAdd as $sid) {
							$r = $oTemplateSite->add(array(
								'TID' => intval($req->get('ID')),
								'SID' => $sid
							));
						}

						if ($apply) {
							LocalRedirect($APPLICATION->GetCurPageParam());
						}
                        elseif ($save) {
							LocalRedirect('/bitrix/admin/' . $page_prefix . 'list.php?lang=' . LANG);
						}
					}
					else {
						$errors = $result->getErrors();
					}
				}
				else {
					// обавление
					$result = $oTemplate->add($arFields);
					if ($result->isSuccess()) {

						//добавляем
						foreach ($arTemplateSite as $sid) {
							$oTemplateSite->add(array(
								'TID' => intval($result->getId()),
								'SID' => $sid
							));
						}

						if ($apply) {
							LocalRedirect($APPLICATION->GetCurPageParam('ID=' . $result->getId(), array('ID')));
						}
                        elseif ($save) {
							LocalRedirect('/bitrix/admin/' . $page_prefix . 'list.php?lang=' . LANG);
						}
					}
					else {
						$errors = $result->getErrors();
					}
				}
			}

		} while (false);

	}


	$tab = new CAdminTabControl('edit', array(
		array(
			'DIV'   => 'edit',
			'TAB'   => GetMessage($MODULE_CODE . '.TAB.EDIT'),
			'ICON'  => '',
			'TITLE' => GetMessage($MODULE_CODE . '.TAB.EDIT')),
	));

	$APPLICATION->SetTitle(GetMessage($MODULE_CODE . '.PAGE_TITLE'));

	require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");


	\Iqsms\Sender\Manager::getInstance()->getAdminHeader();
	\Iqsms\Sender\Manager::getInstance()->addAdminPageCssJs();

	$oManager = \Iqsms\Sender\Manager::getInstance();

	if ($errors && is_array($errors)) {
		$arStr = array();
		foreach ($errors as $error) {
			$arStr[] = $error->getMessage();
		}
		\CAdminMessage::ShowMessage(implode('<br />', $arStr));
	}


	$oMenu->Show();


?>

    <script type="text/javascript">
        var IQsmsSenderEventType = <?=json_encode($oManager->prepareEncoding($arEventTypes));?>;
    </script>

    <form action="<? $APPLICATION->GetCurPage() ?>" method="POST" name="<?= $fname ?>">
		<? echo bitrix_sessid_post(); ?>



		<? $tab->Begin(); ?>
		<? $tab->BeginNextTab(); ?>


		<? if ($req->get('ID')): ?>
            <tr>
                <td><?= GetMessage($MODULE_CODE . '.FIELD_LABEL.ID'); ?> </td>
                <td><?= $req->get('ID') ?></td>
            </tr>
		<? endif; ?>

        <tr>
            <td><?= GetMessage($MODULE_CODE . '.FIELD_LABEL.ACTIVE'); ?> <span class="reg">*</span></td>
            <td><?= InputType('checkbox', 'ACTIVE', 'Y', ($arResult['ACTIVE'] ? 'Y' : 'N'), ''); ?></td>
        </tr>
        <tr>
            <td><?= GetMessage($MODULE_CODE . '.FIELD_LABEL.NAME'); ?> <span class="reg">*</span></td>
            <td><?= InputType('text', 'NAME', $arResult['NAME'], ''); ?><?= ShowJSHint(GetMessage($MODULE_CODE . '.FIELD_LABEL.NAME_HINT')); ?> </td>
        </tr>
        <tr>
            <td><?= GetMessage($MODULE_CODE . '.FIELD_LABEL.SITE'); ?> <span class="reg">*</span></td>
            <td>
				<?
					foreach ($arSite as $sid => $arItem) {
						echo '<p>' . InputType('checkbox', 'SITE[' . $sid . ']', 'Y', (in_array($sid, $arSID) ? 'Y' : ''), false, '[' . $arItem['ID'] . '] ' . $arItem['NAME']) . '</p>';
					}
				?>
            </td>
        </tr>
        <tr>
            <td><?= GetMessage($MODULE_CODE . '.FIELD_LABEL.EVENT'); ?></td>
            <td><?= InputType('checkbox', 'EVENT', 'Y', ($arResult['EVENT'] ? 'Y' : ''), ''); ?></td>
        </tr>
        <tr class="event_type_box <?= ($arResult['EVENT'] ? 'active' : ''); ?>">
            <td><?= GetMessage($MODULE_CODE . '.FIELD_LABEL.EVENT_TYPE'); ?><span class="reg">*</span></td>
            <td><?= SelectBoxFromArray('EVENT_TYPE', $arEventTypeList, $arResult['TYPE'], ''); ?></td>
        </tr>
        <tr class="type_box <?= ($arResult['EVENT'] ? 'hide' : ''); ?>">
            <td><?= GetMessage($MODULE_CODE . '.FIELD_LABEL.TYPE'); ?> <span class="reg">*</span></td>
            <td><?= InputType('text', 'TYPE', $arResult['TYPE'], ''); ?><?= ShowJSHint(GetMessage($MODULE_CODE . '.FIELD_LABEL.TYPE_HINT')); ?> </td>
        </tr>

        <tr>
            <td><?= GetMessage($MODULE_CODE . '.FIELD_LABEL.PHONE'); ?> <span class="reg">*</span></td>
            <td><?= InputType('text', 'PHONE', $arResult['PHONE'], ''); ?> <?= ShowJSHint(GetMessage($MODULE_CODE . '.FIELD_LABEL.PHONE_HINT')); ?></td>
        </tr>
        <tr>
            <td><?= GetMessage($MODULE_CODE . '.FIELD_LABEL.PHONE_COPY'); ?> </td>
            <td><?= InputType('text', 'PHONE_COPY', $arResult['PHONE_COPY'], ''); ?> <?= ShowJSHint(GetMessage($MODULE_CODE . '.FIELD_LABEL.PHONE_COPY_HINT')); ?></td>
        </tr>

        <tr class="content_type-html-row">
            <td style="text-align:center;" colspan="2"><?= GetMessage($MODULE_CODE . '.FIELD_LABEL.TEXT'); ?> <?= ShowJSHint(GetMessage($MODULE_CODE . '.FIELD_LABEL.TEXT_HINT')); ?></td>
        </tr>
        <tr class="content_type-html-row">
            <td colspan="2">
                <textarea name="TEXT" rows="10" placeholder="<?= GetMessage($MODULE_CODE . '.FIELD_LABEL.TEXT_PLACEHOLDER'); ?>"><?= $arResult['TEXT']; ?></textarea>
                <br/>
                <div class="template_fields_box">

                </div>
            </td>
        </tr>


		<? $tab->EndTab(); ?>
		<? $tab->Buttons(array("disabled" => ($PREMISION_DEFINE != "W"),)); ?>
		<? $tab->End(); ?>
    </form>


<? require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php"); ?>
