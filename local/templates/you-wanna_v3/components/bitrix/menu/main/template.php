<?php
$this->SetFrameMode(true);
?>
<?php if (!empty($arResult)) : ?>
    <ul class="list mobile">
        <?php foreach ($arResult as $key => $arItem) : ?>
            <li class="list__item<?php if ($arItem["SELECTED"]) { ?> active<? } ?>">
                <a href="<?php echo $arItem["LINK"] ?>">
                    <?php echo CYouWanna::multiTranslate($arItem['TEXT'], $arParams['LANGUAGE_ID']) ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>