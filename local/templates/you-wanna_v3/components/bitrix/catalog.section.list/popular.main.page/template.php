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


$strSectionEdit = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "SECTION_EDIT");
$strSectionDelete = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "SECTION_DELETE");
$arSectionDeleteParams = array("CONFIRM" => GetMessage('CT_BCSL_ELEMENT_DELETE_CONFIRM'));

?>
<div class="popular-wrapper mw1500 push-center"><?

    if (0 < $arResult["SECTIONS_COUNT"])
    {
    ?>
    <div class="popular-items">
        <?
        $intCurrentDepth = 1;
        $boolFirst = true;
        $g = 0;
        foreach ($arResult['SECTIONS'] as $key => &$arSection)
        {
            if((array)$arSection['PICTURE'] && $arSection['SORT'] <= 50) {
                $this->AddEditAction($arSection['ID'], $arSection['EDIT_LINK'], $strSectionEdit);
                $this->AddDeleteAction($arSection['ID'], $arSection['DELETE_LINK'], $strSectionDelete, $arSectionDeleteParams);

                ?>
                <div id="<?= $this->GetEditAreaId($arSection['ID']); ?>" class="popular-item item-<?=$g+1?><?if($g >= 3){?> non1920<?}?>">
                    <div class="image">
                        <img src="<?= $arSection['PICTURE']['SAFE_SRC']?>" alt="<?= $arSection["NAME"] ?>">
                    </div>
                    <div class="title">
                        <a href="<? echo $arSection["SECTION_PAGE_URL"]; ?>"><? echo $arSection["NAME"]; ?>
                            <? if ($arParams["COUNT_ELEMENTS"]) {
                                    ?>(<? echo $arSection["ELEMENT_CNT"]; ?>)<?
                                }?>
                        </a>
                    </div>
                </div>
        <?  $g++;
            }
        }
    }?>
    </div>
</div>