<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
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
?>
<div class="category-covers">
    <div class="category-covers__row">
        <? foreach($arResult["ITEMS"] as $arItem): ?>
        <?
            $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
            $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));

            if($arItem["PROPERTIES"]["CATS"]["VALUE"]) {
                $res = CIBlockSection::GetByID($arItem["PROPERTIES"]["CATS"]["VALUE"]);
                if($ar_res = $res->GetNext()) {
                    $href = $ar_res['SECTION_PAGE_URL'];
                }
            }
        ?>
            <a href="<?= $href; ?>" class="category-covers__item" style="background-image: url('<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>')">
                <div class="category-covers__title"><?= $arItem["NAME"]?></div>
                <div class="category-covers__mask"></div>
            </a>
        <? endforeach; ?>
    </div>
</div>
