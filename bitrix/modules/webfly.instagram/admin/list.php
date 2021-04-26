<?
$sMainModuleInclude = $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/";
require_once($sMainModuleInclude . "prolog_admin_before.php");

use Bitrix\Main\Loader,
    Bitrix\Main\Localization\Loc,
    Bitrix\Main\HttpApplication,
    Webfly\Instagram\Request,
    Webfly\Instagram\InstagramTable;

global $APPLICATION;
Loader::includeModule('webfly.instagram');
CJSCore::Init(array('ajax'));

Loc::loadMessages(__FILE__);
Loc::loadMessages(Loader::getLocal("modules/webfly.instagram/lib/instagram.php"));

$request = HttpApplication::getInstance()->getContext()->getRequest();
$server = HttpApplication::getInstance()->getContext()->getServer();

$bCheckAuth = (bool)($request["mode"] === "checkAuth" && $request->isPost());
if($bCheckAuth){
    die(Request::getInstance()->login());
}
require_once($server['DOCUMENT_ROOT'] . '/bitrix/modules/iblock/classes/general/subelement.php');

$APPLICATION->SetTitle(Loc::getMessage("WEBFLY_INSTAGRAM_TITLE2"));

$oInstaHelper = CWebflyInstagramHelper::getInstance();
$arPropInsta = $oInstaHelper->getHashTagProperty();

$sTableID = 'tbl_webfly_instagram';
$arHideFields = array();
$by = isset($request['by'])?$request["by"]:"ID";
$byOrder = isset($request['order'])?$request["order"]:"ASC";
$oSort = new CAdminSorting($sTableID, "ID", "asc");
$lAdmin = new CAdminSubList($sTableID, $oSort, $sPropertiesAdminUrl, $arHideFields);
$arFilterFields = array("find","find_id","find_name","find_tag","find_new_post");
$arFilterFieldsMap = array("ID","ID","NAME","PROPERTY_".$arPropInsta["ID"],"NEW_POSTS");
$lAdmin->InitFilter($arFilterFields);
$arFilter = array();

foreach ($arFilterFields as $k => $f){
    if(!empty($request[$f])){
        if($f == "find_tag"){
            $arFilter[$arFilterFieldsMap[$k]] = urldecode($request[$f]);
        }else{
            $arFilter[$arFilterFieldsMap[$k]] = $request[$f];
        }
    }
}
$lAdmin->AddHeaders(array(
    array("id" => "ID", "content" => "ID", "sort" => "ID", "default" => true),
    array("id" => "NAME", "sort" => "NAME", "default" => true, "content" => Loc::getMessage('WEBFLY_INSTAGRAM_WARE_NAME')),
    array("id" => "PICTURE", "sort" => "PICTURE", "default" => true, "content" => Loc::getMessage("WEBFLY_INSTAGRAM_PICTURE")),
    array("id" => "HASH_TAG", "sort" => "HASH_TAG", "default" => true, "content" => Loc::getMessage("WEBFLY_INSTAGRAM_HASH_TAG")),
    array("id" => "NEW_POSTS", "sort" => "NEW_POSTS", "default" => true, "content" => Loc::getMessage("WEBFLY_INSTAGRAM_NEW_POSTS")),
));
require_once($sMainModuleInclude . "prolog_admin_after.php");
$arHeaderFieldsVisible = array(
    "ID" => true,
    "NAME" => false,
    "PICTURE" => false,
    "HASH_TAG" => false,
    "NEW_POSTS" => false
);
$arHeaderFields = $lAdmin->GetVisibleHeaderColumns();
if (!in_array("ID", $arHeaderFields)){
    $arHeaderFields[] = "ID";
}
$arNewPostQuantity = array();
$arWareCount = InstagramTable::getItemCount();
if (!empty($arWareCount)) {
    foreach ($arWareCount as $arr) {
        $arNewPostQuantity[$arr["WARE"]] = $arr;
    }
}

$arHeaderFields = array_values($arHeaderFields);
$arHeaderFieldsVisible = array_merge($arHeaderFieldsVisible, array_fill_keys($arHeaderFields, true));

$arNavParams = ($request["mode"] == "excel"? false:array("nPageSize" => CAdminSubResult::GetNavSize($sTableID, Request::PAGE_SIZE, $lAdmin->GetListUrl(true))));
if(!empty($arFilter["NEW_POSTS"])){
    if($arFilter["NEW_POSTS"] == "Y"){
        $sKey = "ID";
    }else{
        $sKey = "!ID";
    }
    unset($arFilter["NEW_POSTS"]);
    $arFilter[$sKey] = array_keys($arNewPostQuantity);
}
$dbItemsList = $oInstaHelper->getDBWaresList($arFilter, array($by => $byOrder));

$dbResultList = new CAdminResult($dbItemsList, $sTableID);
$dbResultList->NavStart();
$sNavText = $dbResultList->GetNavPrint(Loc::getMessage("WEBFLY_INSTAGRAM_PAGES_NAV"));
$lAdmin->NavText($sNavText);

$arRows = array();
$arHashProp = $oInstaHelper->getHashTagProperty();
$sHashTagLinkTemplate = "/bitrix/admin/webfly_instagram_ware.php?lang=".LANGUAGE_ID."&ware=%s";
$sElemLinkTemplate = "/bitrix/admin/%s?IBLOCK_ID=%s&type=%s&ID=%s&lang=ru";
if($arHashProp["IS_CATALOG"]){
    $elementUrl = "cat_product_edit.php";
}else{
    $elementUrl = "iblock_element_edit.php";
}
while ($arItem = $dbResultList->Fetch()) {
    $sEditUrl = '';
    $sPropCode = "PROPERTY_{$arHashProp["ID"]}";
    $arItem["ID"] = intval($arItem["ID"]);
    $miniature = $oInstaHelper->getWarePhotoPreview($arItem["PREVIEW_PICTURE"]);

    $arRows[$arItem["ID"]] = $oRow = &$lAdmin->AddRow($arItem["ID"], $arItem, $sEditUrl, '', true);
    $oRow->AddField("ID", '<span style="line-height:70px;">'.$arItem["ID"].'</span>');
    if ($arHeaderFieldsVisible["NAME"]){
        $sWareLink = sprintf($sElemLinkTemplate,$elementUrl,$arHashProp["IBLOCK_ID"],$arHashProp["IBLOCK_CODE"],$arItem["ID"]);
        $oRow->AddViewField("NAME", '<a href="'.$sWareLink.'" target="_blank" style="line-height:70px;">'.$arItem["NAME"].'</a>');
    }
    if ($arHeaderFieldsVisible["PICTURE"]){
        $oRow->AddViewField("PICTURE", '<img src="'.$miniature["src"].'" style="border:1px solid #9fabb4;border-radius:4px;"/>');
    }
    if ($arHeaderFieldsVisible["HASH_TAG"]){
        $sHashPropValue = $arItem[$sPropCode."_VALUE"];
        $sHashTagLinks = "<a href='".sprintf($sHashTagLinkTemplate,$arItem["ID"])."' style='line-height:70px;'>#$sHashPropValue</a>";
        $oRow->AddViewField("HASH_TAG", $sHashTagLinks);
    }
    if($arHeaderFieldsVisible["NEW_POSTS"]){
        $sHashTagLinks = "<a href='".sprintf($sHashTagLinkTemplate,$arItem["ID"])."' style='margin-left:20px;color:#3f4b54;' class='adm-btn adm-btn-copy'>".Loc::getMessage("WEBFLY_INSTAGRAM_FILTER_NEW_POST_SHOW")."</a>";
        $oRow->AddViewField("NEW_POSTS", '<span style="line-height:70px;">'.intval($arNewPostQuantity[$arItem["ID"]]["CNT"]).'</span>'.$sHashTagLinks);
    }
}
unset($oRow);

if (!isset($request["mode"]) || !in_array($request["mode"], array("excel","subsettings"))) {
    $aContext = array();
    if (CWebflyInstagramRights::canWrite()) {
        $aContext[] = array(
            "ICON" => "properties",
            "TEXT" => Loc::getMessage("WEBFLY_INSTAGRAM_ITEM_BTN_SETTINGS"),
            "TITLE" => Loc::getMessage("WEBFLY_INSTAGRAM_ITEM_BTN_SETTINGS_TEXT"),
            "LINK" => "javascript:go2params()"
        );
        if(Request::getInstance()->isAuthNeeded()){
            $aContext[] = array(
                "ICON" => "btn_check_auth",
                "TEXT" => Loc::getMessage("WEBFLY_INSTAGRAM_CHECK_AUTH"),
                "TITLE" => Loc::getMessage("WEBFLY_INSTAGRAM_CHECK_AUTH_TEXT"),
                "LINK" => "javascript:checkAuth()"
            );
        }
    }
    $lAdmin->AddAdminContextMenu($aContext);?>
    <script type="text/javascript">
        function go2params(){
            window.open("/bitrix/admin/settings.php?lang=ru&mid=webfly.instagram&mid_menu=1");
        }
        function checkAuth(){
            BX.ajax.post(location.href,{mode:"checkAuth"},function(d){
                var tableList = BX("tbl_webfly_instagram_result_div"),
                    divAnswer = BX("check_auth_answer");
				if(!divAnswer){
					divAnswer = BX.create("div");
					divAnswer.id = "check_auth_answer";
                    divAnswer.style.fontWeight = "bold";
				}
                if(!!d){
                    var jsond = BX.parseJSON(d),
                        text = "";
                    if(!!jsond && typeof jsond == "object"){
                        if(!!jsond.errors.error){
                            text = jsond.errors.error.join(", ");
                        }
                    }else{
                        text = d.toString();
                    }
                    divAnswer.style.color = "red";
                    divAnswer.innerHTML = "<?=Loc::getMessage("WEBFLY_INSTAGRAM_AUTH_NO")?>: " + text;
                }else{
                    divAnswer.style.color = "green";
                    divAnswer.innerHTML = "<?=Loc::getMessage("WEBFLY_INSTAGRAM_AUTH_OK")?>";
                }
				if(!BX("check_auth_answer")){
                	tableList.parentNode.insertBefore(divAnswer,tableList);
				}
            });
        }
    </script><?
}
$oFilter = new CAdminFilter(
    $sTableID."_filter",
    array("ID",
        Loc::getMessage("WEBFLY_INSTAGRAM_FILTER_WARE"),
        Loc::getMessage("WEBFLY_INSTAGRAM_FILTER_NAME"),
        Loc::getMessage("WEBFLY_INSTAGRAM_FILTER_HASH_TAG"),
        Loc::getMessage("WEBFLY_INSTAGRAM_FILTER_NEW_POSTS")
    ),
    array("table_id" => $sTableID, "url" => $APPLICATION->GetCurPage())
);?>
<?php
?>
<form name="find_form" method="get" action="<?= $APPLICATION->GetCurPage()?>">
<?$oFilter->Begin();?>
    <tr><td>ID:</td>
        <td><input type="text" name="find" size="60" value="<?= htmlspecialchars($request["find"])?>"/></td></tr>
    <tr><td><?=Loc::getMessage("WEBFLY_INSTAGRAM_FILTER_WARE")?>:</td>
        <td><input type="text" name="find_id" size="60" value="<?= htmlspecialchars($request["find_id"])?>"/></td></tr>
    <tr><td><?=Loc::getMessage("WEBFLY_INSTAGRAM_FILTER_NAME")?>:</td>
        <td><input type="text" name="find_name" size="60" value="<?= htmlspecialchars($request["find_name"])?>"/></td></tr>
    <tr><td><?=Loc::getMessage("WEBFLY_INSTAGRAM_FILTER_HASH_TAG")?>:</td>
        <td><input type="text" name="find_tag" size="60" value="<?= htmlspecialchars($request["find_tag"])?>"/></td></tr>
    <tr><td><?=Loc::getMessage("WEBFLY_INSTAGRAM_FILTER_NEW_POSTS")?>:</td>
        <td>
            <?
            $arr = array(
                "reference" => array(Loc::getMessage("WEBFLY_INSTAGRAM_DA"),Loc::getMessage("WEBFLY_INSTAGRAM_NET")),
                "reference_id" => array("Y","N")
            );
            echo SelectBoxFromArray("find_new_post", $arr, $request["find_new_post"], GetMessage("WEBFLY_INSTAGRAM_ALL"), "");
            ?>
        </td>
    </tr>
    <?
    $oFilter->Buttons(array("form"=>"find_form"));
    $oFilter->End();
    ?>
</form>
<?if(in_array($request["mode"],array("list","frame"))){
    $APPLICATION->RestartBuffer();
}
$lAdmin->CheckListMode();
$lAdmin->DisplayList();
if(in_array($request["mode"],array("list","frame"))){
    die();
}
require($sMainModuleInclude . "epilog_admin.php");