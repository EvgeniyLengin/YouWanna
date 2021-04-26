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
use \Bitrix\Main\Localization\Loc;
$this->setFrameMode(true);
Loc::loadMessages(__FILE__);

?>

<?if($arParams["DISPLAY_TOP_PAGER"]):?>
	<?=$arResult["NAV_STRING"]?><br />
<?endif;?>
<?/*<pre>*/?>
    <?/*print_r($arResult['ITEMS']);*/?>
<?/*</pre>*/?>
<br/>
<br/>
<div class="row reviews-wrapper push-center">


    <?foreach($arResult["ITEMS"] as $arItem):?>
    <div class="review-item row">
        <?
            $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
            $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
        ?>
        <div class="col col-3">
            <div class="review-author">
                <p><?=$arItem['NAME']?></p>
                <p><?=$arItem['PROPERTIES']['CITY']['VALUE']?></p>
            </div>
        </div>
        <div class="col col-9" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
            <div class="review-inner">
                <p class="review-comment"><?=$arItem['PREVIEW_TEXT']?></p>
                <p class="review-date"><?=$arItem['ACTIVE_FROM']?></p>
            </div>
            <? if($arItem['DETAIL_TEXT'] !== '') { ?>
                <div class="review-manager">
                    <a class="toggle-link" onclick="$('.manager-answer-<?=$arItem['ID']?>').toggle()"><?= Loc::getMessage('SHOP_ANSWER_LINK') ?></a>
                    <p class="text manager-answer-<?=$arItem['ID']?>" style="display: none"><?=$arItem['DETAIL_TEXT']?></p>
                </div>
            <? } ?>
        </div>
    </div>
    <?endforeach;?>
</div>
<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
	<br />
<div class="navibar-otz">
	<?=$arResult["NAV_STRING"]?>
</div>
<?endif;?>
