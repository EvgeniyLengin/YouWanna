<?php
/**
 * Date: 02.03.2018
 * Time: 17:06
 */
$this->SetFrameMode(true);
?>
<?if(!empty($arResult)) {?>
    <?if ($title = $arParams["TITLE"]) {?>
        <h4><?=$title?></h4>
        <ul class="list">
            <?foreach ($arResult as $arItem) {?>
                <li>
                    <a href="<?=$arItem["LINK"]?>" <?if ($arItem["LINK"] === '/upload/Privacy_policy.pdf'){echo 'target="_blank"';}?>>
                        <?= CYouWanna::multiTranslate($arItem['TEXT'], $arParams['LANGUAGE_ID']) ?>
                    </a>
                </li>
            <?}?>
        </ul>
    <?} else {?>
        <ul class="list bottom-no-title">
            <?foreach ($arResult as $arItem) {?>
                <li>
                    <a href="<?=$arItem["LINK"]?>" <?if ($arItem["LINK"] === '/upload/Privacy_policy.pdf'){echo 'target="_blank"';}?>>
                        <?= CYouWanna::multiTranslate($arItem['TEXT'], $arParams['LANGUAGE_ID']) ?>
                    </a>
                </li>
            <?}?>
        </ul>
    <?}?>
<?}?>
