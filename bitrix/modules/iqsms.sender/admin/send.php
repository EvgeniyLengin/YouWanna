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

	$bAjax = false;
	if ($req->isAjaxRequest() && $req->getPost('method') && $req->getPost('method') == 'get_content') {
		$bAjax = true;
	}

	if ($req->isPost() && check_bitrix_sessid('sessid') && $req->getPost('method')) {


		$arJson = array(
			'error'    => array(),
			'response' => array()
		);


		switch ($req->getPost('method')) {
			case 'send_sms': {

				if (!$req->getPost('phone')) {
					$arJson['error'] = array(
						'code' => 'invalid_phone',
						'msg'  => GetMessage($MODULE_CODE . '.AJAX.INVALID_PHONE'),
						'more' => array()
					);
					break;
				}

				$arPhone = $oManager->getMultyPhone($req->getPost('phone'));


				if (!$arPhone) {
					$arJson['error'] = array(
						'code' => 'invalid_phone',
						'msg'  => GetMessage($MODULE_CODE . '.AJAX.INVALID_PHONE'),
						'more' => array()
					);
					break;
				}


				$text = $req->getPost('text');
				$text = (!$bUtf ? $APPLICATION->ConvertCharset($text, 'UTF-8', "Windows-1251") : $text);

				if (!$text || strlen(trim($text)) <= 0) {
					$arJson['error'] = array(
						'code' => 'invalid_text',
						'msg'  => GetMessage($MODULE_CODE . '.AJAX.INVALID_TEXT'),
						'more' => array()
					);
					break;
				}

				$schedule = null;
				if (!!$req->getPost('schedule_on')) {
					$schedule = new \Bitrix\Main\Type\DateTime();

					$datetime = $req->getPost('schedule');
					if (preg_match('/\d{2}\.\d{2}\.\d{4}\s\d{2}:\d{2}:\d{2}/', $datetime, $match)) {
						$schedule = \Bitrix\Main\Type\DateTime::createFromUserTime($match[0]);
					}
                    elseif (preg_match('/\d{2}\.\d{2}\.\d{4}/', $datetime, $match)) {
						$schedule = \Bitrix\Main\Type\DateTime::createFromUserTime($match[0]);
					}
				}

				$sender = trim($req->getPost('sender'));

				$arErrorsAll = array();
				foreach ($arPhone as $phone) {


					$result = $oManager->send($phone, $text, null, $schedule, $sender);
					if (!$result->isSuccess()) {

						$arErrors = $result->getErrors();
						foreach ($arErrors as $error) {
							$arErrorsAll[$phone] = $error->getMessage();
							break;
						}
					}
				}


				$arJson['response'] = array();
				if (count($arErrors)) {
					$arJson['response']['msg']    = GetMessage($MODULE_CODE . '.AJAX.SEND_ERROR');
					$arJson['response']['errors'] = $arErrorsAll;
				}
				else {
					$arJson['response']['msg'] = GetMessage($MODULE_CODE . '.AJAX.SEND_OK');
				}


				break;
			}
			case 'filter_phone': {

				if (!\Bitrix\Main\Loader::includeModule('sale')) {
					$arJson['error'] = array(
						'code' => 'module_not_found',
						'msg'  => GetMessage($MODULE_CODE . '.AJAX.GET_PHONE.MODULE_SALE_NOT_FOUND'),
						'more' => array()
					);
					break;
				}

				$arFilter = array();

				if (strlen(trim($req->getPost('order_payed'))) && in_array($req->getPost('order_payed'), array('1', '0'))) {
					$arFilter['PAYED'] = $req->getPost('order_payed');
				}

				if (strlen(trim($req->getPost('order_canceled'))) && in_array($req->getPost('order_canceled'), array('1', '0'))) {
					$arFilter['CANCELED'] = $req->getPost('order_canceled');
				}


				if (strlen(trim($req->getPost('date_from'))) > 0) {
					$arFilter['>DATE_INSERT'] = $req->getPost('date_from');
				}

				if (strlen(trim($req->getPost('date_to'))) > 0) {
					$arFilter['<DATE_INSERT'] = $req->getPost('date_to');
				}




				$arOrderID  = array();
				$oPropValue = new \CSaleOrderPropsValue();
				$oOrder     = new \Bitrix\Sale\Internals\OrderTable();
				$dbrOrder   = $oOrder->getList(array(
					'filter' => $arFilter,
					'select' => array(
						'ID', 'DATE_INSERT', 'PAYED', 'CANCELED', 'PERSON_TYPE_ID'
					)
				));
				while ($arOrder = $dbrOrder->fetch()) {
					//разбиваем по типам плательщиков
					$arOrderID[$arOrder['PERSON_TYPE_ID']][] = $arOrder['ID'];
				}


				$arPhones  = array();
				foreach ($arOrderID as $ptid => $arId) {
					if (count($arId) && strlen($oManager->getParam('PERSON_TYPE_' . $ptid, ''))) {
						echo '**';
						$dbrProp = $oPropValue->GetList(array("SORT" => "ASC"),
							array(
								"ORDER_ID"       => $arId,
								"CODE" => trim($oManager->getParam('PERSON_TYPE_' . $ptid))
							)
						);

						while($arProp = $dbrProp->fetch())
                        {
                            if(strlen($arProp['VALUE']))
                            {
							    $arPhones[] = $arProp['VALUE'];
                            }
                        }
					}
				}
				$arPhones = array_unique($arPhones);

				if(!empty($arPhones))
                {
					$arJson['response']['phones'] = implode(PHP_EOL, $arPhones);
                }
                else
                {
					$arJson['response']['phones'] = GetMessage($MODULE_CODE . '.AJAX.GET_PHONE.NOT_FOUND');
                }

				break;
			}
		}


		$APPLICATION->RestartBuffer();
		header('Content-Type: application/json');

		if (!empty($arJson['error'])) {
			echo json_encode(array(
				'error' => ($bUtf ? $arJson['error'] : $APPLICATION->ConvertCharsetArray($arJson['error'], LANG_CHARSET, 'UTF-8')),
			));
		}
		else {
			echo json_encode(array(
				'response' => ($bUtf ? $arJson['response'] : $APPLICATION->ConvertCharsetArray($arJson['response'], LANG_CHARSET, 'UTF-8')),
			));

		}
		die();
	}


	$sCurPage = $APPLICATION->GetCurPage();

	$oSort  = new CAdminSorting($sTableID, "SORT", "ASC");
	$sAdmin = new CAdminList($sTableID, $oSort);

	// сайты
	$arSite = array();
	$dbr    = \CSite::GetList($by = 'sort', $order = 'asc');
	while ($ar = $dbr->Fetch()) {
		$arSite[$ar['ID']] = '[' . $ar['ID'] . '] ' . $ar['NAME'];
	}


	//шаблоны
	$arSiteData     = $oManager->getSiteData();
	$arTemplate     = array(
		'REFERENCE'    => array(GetMessage($MODULE_CODE . '_FORM.TEMPLATE_PLACEHOLDER')),
		'REFERENCE_ID' => array(0)
	);
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
		$arTemplate['REFERENCE'][]    = '[' . $ar['TYPE'] . '] ' . $ar['NAME'];
		$arTemplate['REFERENCE_ID'][] = $ar['ID'];

		$arTemplateData[$ar['ID']] = $ar;
	}

	//отправитель
	$arSender  = array(
		'REFERENCE'    => array(),
		'REFERENCE_ID' => array()
	);
	$arSenders = $oManager->getSenders();
	foreach ($arSenders as $sender) {
		$arSender['REFERENCE'][]    = $sender;
		$arSender['REFERENCE_ID'][] = $sender;
	}

	$arSelectYesNoEmpty = array(
		'REFERENCE'    => array(
			GetMessage($MODULE_CODE . '_FORM.SELECT_EMPTY'),
			GetMessage($MODULE_CODE . '_FORM.SELECT_NO'),
			GetMessage($MODULE_CODE . '_FORM.SELECT_YES')
		),
		'REFERENCE_ID' => array(
			'', '0', '1'
		)
	);

	$APPLICATION->SetTitle(GetMessage($MODULE_CODE . '_PAGE_LIST_TITLE'));

	require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");


	\Iqsms\Sender\Manager::getInstance()->addAdminPageCssJs();
	\Iqsms\Sender\Manager::getInstance()->getAdminHeader();


?>
    <div class="iqsms__sender__msg">
        <div class="title_box"><?= GetMessage($MODULE_CODE . '_FORM.MSG_LIST_LABEL'); ?></div>
        <div class="text_box">
            <div class="msg_box"></div>
        </div>
    </div>

    <div class="ap-iqsms-sender-send-box">
        <div class="title_box"><?= GetMessage($MODULE_CODE . '_FORM.SEND_BOX_LABEL'); ?></div>

        <div class="row_item">
            <small><?= GetMessage($MODULE_CODE . '_FORM.TEMPLATE'); ?></small>
			<?= SelectBoxFromArray('template', $arTemplate); ?>
        </div>

        <div class="row_item textarea_box">
            <small><?= GetMessage($MODULE_CODE . '_FORM.TEXT'); ?></small>
            <br/>
            <textarea name="text" rows="4" cols="10"></textarea>
        </div>
        <div class="info_box">
            <span class="text_size"><?= GetMessage($MODULE_CODE . '_FORM.INFO'); ?></span>
            <span class="btn_clean"><?= GetMessage($MODULE_CODE . '_FORM.BTN_CLEAN'); ?></span>
        </div>

        <div class=" sender_box">
            <label for="sender"><?= GetMessage($MODULE_CODE . '_FORM.SENDER'); ?></label>
			<?= SelectBoxFromArray('sender', $arSender); ?>
        </div>

        <div class=" translit_box">
            <input type="checkbox" name="translit" id="translit" value="Y"/>
            <label for="translit"><?= GetMessage($MODULE_CODE . '_FORM.BTN_TRANSLIT'); ?></label>
        </div>

        <div class=" schadule_checker_box">
            <input type="checkbox" name="schedule_on" id="schedule_on" value="Y"/>
            <label for="schedule_on"><?= GetMessage($MODULE_CODE . '_FORM.BTN_SCHEDULE_ON'); ?></label>
        </div>

        <div class=" schadule_box">
			<? echo CalendarDate("schedule_date", date('d.m.Y H:i:s'), "form1", "15", "class='schedule_date'  id='schedule_date' ") ?>
        </div>

        <div class="fileds_box">

        </div>

        <div class="row_item">
            <input class="adm-btn btn_send " type="button" value="<?= GetMessage($MODULE_CODE . '_FORM.SENT'); ?>"/>
        </div>

        <div class="phone_list_box">
            <div class="title_box"><?= GetMessage($MODULE_CODE . '_FORM.PHONE_LIST_LABEL'); ?></div>
            <div class="filter__box ">
                <div class="filter__box-row-view"><span><?= GetMessage($MODULE_CODE . '_FILTER.SHOW'); ?></span></div>
                <div class="filter__box-row">
                    <div class="filter__box-row-label"><?= GetMessage($MODULE_CODE . '_FILTER.ORDER_PAYED'); ?></div>
                    <div class="filter__box-row-value"><?= SelectBoxFromArray('ORDER_PAYED', $arSelectYesNoEmpty); ?></div>
                </div>
                <div class="filter__box-row">
                    <div class="filter__box-row-label"><?= GetMessage($MODULE_CODE . '_FILTER.ORDER_CANCELED'); ?></div>
                    <div class="filter__box-row-value"><?= SelectBoxFromArray('ORDER_CANCELED', $arSelectYesNoEmpty); ?></div>
                </div>
                <div class="filter__box-row filter__box-row--date">
                    <div class="filter__box-row-label"><?= GetMessage($MODULE_CODE . '_FILTER.DATE_FROM'); ?></div>
                    <div class="filter__box-row-value">
						<?= CalendarDate("DATE_FROM", date('d.m.Y H:i:s'), "", "15", "") ?>
                    </div>
                </div>
                <div class="filter__box-row filter__box-row--date">
                    <div class="filter__box-row-label"><?= GetMessage($MODULE_CODE . '_FILTER.DATE_TO'); ?></div>
                    <div class="filter__box-row-value">
						<?= CalendarDate("DATE_TO", date('d.m.Y H:i:s'), "", "15", "") ?>
                    </div>
                </div>
                <div class="filter__box-row">
                    <input type="button" class="adm-btn filter__box-btn" id="filter__box-btn" value="<?= GetMessage($MODULE_CODE . '_FILTER.BTN'); ?>"/>
                </div>

            </div>
            <div class="text_box">
                <small><?= GetMessage($MODULE_CODE . '_FORM.PHONE_LIST_TEXT'); ?> </small>
                <br><br>
                <textarea name="phone" id="" cols="30" rows="15"></textarea>
            </div>
        </div>

    </div>

    <br><br><br><br><br><br><br><br><br><br>
    <br><br><br><br><br><br><br><br><br><br>


    <script type="text/javascript">
        BX.message({
            'iqsms_sender_template_type': <?=json_encode($APPLICATION->ConvertCharsetArray($arTemplateData, LANG_CHARSET, 'UTF-8'));?>,
            'iqsms_sender_translit': <?=GetMessage($MODULE_CODE . '.translit');?>
        });
    </script>

<?
	require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php");
?>
