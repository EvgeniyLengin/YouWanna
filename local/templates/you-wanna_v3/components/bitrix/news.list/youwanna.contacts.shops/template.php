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
?>
<div class="news-list contacts-list push-center row">
    <? if ($arParams["DISPLAY_TOP_PAGER"]): ?>
        <?= $arResult["NAV_STRING"] ?><br/>
    <? endif; ?>
    <? foreach ($arResult["ITEMS"] as $key => $arItem): ?>
    <?
    $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'],
        CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
    $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'],
        CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"),
        array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));

    if ($arParams['LANGUAGE_ID'] === 'ru') {
        $arItem['NAME_RU'] = $arItem['NAME'];
        $arItem['PREVIEW_TEXT_RU'] = $arItem['PREVIEW_TEXT'];
    } else {
        $arItem['NAME_' . strtoupper($arParams['LANGUAGE_ID'])] = $arItem['PROPERTIES']['NAME_' . strtoupper($arParams['LANGUAGE_ID'])]['VALUE'];
        $arItem['PREVIEW_TEXT_' . strtoupper($arParams['LANGUAGE_ID'])] = $arItem['PROPERTIES']['PREVIEW_TEXT_' . strtoupper($arParams['LANGUAGE_ID'])]['VALUE'];
    }
    ?>
    <? if ($key === 0) { ?>
    <div class="col-md-4 news-item">
        <div class="news-item-inner first">
            <? if ($arParams["DISPLAY_NAME"] != "N" && $arItem["NAME"]): ?>
                <? if (!$arParams["HIDE_LINK_WHEN_NO_DETAIL"] || ($arItem["DETAIL_TEXT"] && $arResult["USER_HAVE_ACCESS"])): ?>
                    <span class="contacts-name"
                          href="<?= $arItem["DETAIL_PAGE_URL"] ?>"><b><?= $arItem["NAME_".strtoupper(LANGUAGE_ID)] ?></b></span><br/>
                <? else: ?>
                    <b><?= $arItem["NAME_".strtoupper(LANGUAGE_ID)] ?></b><br/>
                <? endif; ?>
            <? endif; ?>

            <span class="contacts-address"><?= $arItem['PROPERTIES']['ADDRESS_'.strtoupper(LANGUAGE_ID)]['VALUE'] ?></span>

            <? foreach ($arItem['PROPERTIES']['PHONE_'.strtoupper(LANGUAGE_ID)]['VALUE'] as $phone) { ?>
                <a href="tel:<?= $phone ?>" class="contacts-phone">
                    <?= $phone ?>
                </a>
            <? } ?>

            <span class="contacts-preview"><?= $arItem['PREVIEW_TEXT_'.strtoupper(LANGUAGE_ID)] ?></span>

            <a class="contacts-email"
               href="mailto:<?= $arItem['PROPERTIES']['EMAIL']['VALUE'] ?>"><?= $arItem['PROPERTIES']['EMAIL']['VALUE'] ?></a>
        </div>
    </div>
    <div class="col-md-8 row">
        <? } else { ?>
            <div class="col-sm-6 news-item" id="<?= $this->GetEditAreaId($arItem['ID']); ?>">
                <div class="news-item-inner">
                    <? if ($arParams["DISPLAY_NAME"] != "N" && $arItem["NAME"]): ?>
                        <? if (!$arParams["HIDE_LINK_WHEN_NO_DETAIL"] || ($arItem["DETAIL_TEXT"] && $arResult["USER_HAVE_ACCESS"])): ?>
                            <a class="contacts-name"
                               href="<?= $arItem["DETAIL_PAGE_URL"] ?>"><b><?= $arItem["NAME_".strtoupper(LANGUAGE_ID)] ?></b></a><br/>
                        <? else: ?>
                            <b><?= $arItem["NAME_".strtoupper(LANGUAGE_ID)] ?></b><br/>
                        <? endif; ?>
                    <? endif; ?>

                    <span class="contacts-address"><?= $arItem['PROPERTIES']['ADDRESS_'.strtoupper(LANGUAGE_ID)]['VALUE'] ?></span>

                    <? foreach ($arItem['PROPERTIES']['PHONE_'.strtoupper(LANGUAGE_ID)]['VALUE'] as $phone) { ?>
                        <a href="tel:<?= $phone ?>" class="contacts-phone">
                            <?= $phone ?>
                        </a>
                    <? } ?>

                    <span class="contacts-preview"><?= $arItem['PREVIEW_TEXT_'.strtoupper(LANGUAGE_ID)] ?></span>

                    <a class="contacts-email"
                       href="mailto:<?= $arItem['PROPERTIES']['EMAIL']['VALUE'] ?>"><?= $arItem['PROPERTIES']['EMAIL']['VALUE'] ?></a>
                </div>
            </div>
        <? } ?>
        <? endforeach; ?>
        <? if ($arParams["DISPLAY_BOTTOM_PAGER"]): ?>
            <br/><?= $arResult["NAV_STRING"] ?>
        <? endif; ?>
    </div>
    <script>
        $(document).ready(function () {
            $('.news-item-inner.first').height($('.col.col-6.news-item:first-child .news-item-inner').height());
        })
    </script>