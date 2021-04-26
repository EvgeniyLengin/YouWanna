<?php
/**
 * Date: 02.03.2018
 * Time: 17:06
 */
$this->SetFrameMode(true);
?>
<?if(!empty($arResult)) {?>
    <ul class="list show-on-small static-pages">
        <?foreach ($arResult as $key => $arItem) {?>
                <li class="list__item<?if ($arItem["SELECTED"]){?> active<?}?>">
                    <a class="<?= !$key == 0 ?: 'open-catalog' ?>" href="<?=$arItem["LINK"]?>"><?= CYouWanna::multiTranslate($arItem['TEXT'], $arParams['LANGUAGE_ID']) ?></a>
                </li>
        <?}?>
    </ul>
<?}?>