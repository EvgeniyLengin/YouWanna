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
$page = $APPLICATION->GetCurPage();
preg_match('/([\d].*)\//', $page, $pActive);
$pActive = $pActive[1];
?>
<script>
    $(document).ready(function() {
        var textToggle = $('.shop-item a.active').text();
        $('.shop-item .toggle').text(textToggle);
        $('.shop-item .toggle').click(function() {
            $('.shops-menu .hm').toggle();
        });
    });
</script>
<ul class="shops-menu">
    <li class="shop-item show-mobile"><a class="toggle">Выберите магазин</a></li>
<?foreach($arResult["ITEMS"] as $arItem):?>
    <?
    if($arParams['LANGUAGE_ID'] === 'ru') {
        $arItem['NAME_RU'] = $arItem['NAME'];
    } else {
        $arItem['NAME_'.strtoupper($arParams['LANGUAGE_ID'])] = $arItem['PROPERTIES']['NAME_'.strtoupper($arParams['LANGUAGE_ID'])]['VALUE'];
    }
    ?>
    <? if(strlen($arItem['PROPERTIES']['MAP']['VALUE']) > 15) { ?>
	<li class="shop-item hm">
        <a <? if($arItem['ID'] === $pActive) { ?> class="active" <? } ?> href="<?echo $arItem["DETAIL_PAGE_URL"]?>">
            <?echo $arItem["NAME_".strtoupper(LANGUAGE_ID)]?>
        </a>
	</li>
    <? } ?>
<?endforeach;?>
</ul>