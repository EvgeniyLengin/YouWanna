<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);

$arCurView = $arViewStyles[$arParams['VIEW_MODE']];

$strSectionEdit = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "SECTION_EDIT");
$strSectionDelete = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "SECTION_DELETE");
$arSectionDeleteParams = array("CONFIRM" => GetMessage('CT_BCSL_ELEMENT_DELETE_CONFIRM'));

?>

<div class="left-catalog-menu"><?

    if (0 < $arResult["SECTIONS_COUNT"]) { ?>
    <ul class="list">
        <?
        $intCurrentDepth = 1;
        $boolFirst = true;
        foreach ($arResult['SECTIONS'] as $arSection) {
        //$this->AddEditAction($arSection['ID'], $arSection['EDIT_LINK'], $strSectionEdit);
        //$this->AddDeleteAction($arSection['ID'], $arSection['DELETE_LINK'], $strSectionDelete, $arSectionDeleteParams);
            $txt = CYouWanna::multiTranslate($arSection['NAME'], $arParams['LANGUAGE_ID'], false, true);
        ?>
        <li id="<?= $this->GetEditAreaId($arSection['ID']); ?>"
            class="list__item<?if($arSection['CODE'] === $arParams['ACTIVE_SECTION']){?> active<?}?>">
            <a href="<?= $arSection["SECTION_PAGE_URL"]; ?>">
                <?=$txt?>
                <? if ($arParams["COUNT_ELEMENTS"]) {
                    ?>(<?= $arSection["ELEMENT_CNT"]; ?>)<?
                }?>
            </a>
        </li>
        <? }?>            
            
    </ul>
    <?} ?>
</div>