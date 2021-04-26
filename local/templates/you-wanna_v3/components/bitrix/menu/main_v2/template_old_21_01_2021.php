<?php
$this->SetFrameMode(true);
?>

<?php if (!empty($arResult)) : ?>
    <ul class="yw-topmenu">
        <?php foreach ($arResult as $key => $arItem) : ?>
            <?if(trim($arItem['TEXT']) == '') continue;?>
            <li class="yw-topmenuitem<?php if ($arItem["SELECTED"]) { ?> active<? } ?><?if(count($arResult['CHILDS_SECTIONS'][$arItem['PARAMS']['CHILD_SECTION']]['ITEMS']) > 0) {?> parent<? } ?><?if($arItem['PARAMS']['ONLY_MOB'] == 'Y') {?> yw-desc-hide<?}?>">
                <a href="<?php echo $arItem["LINK"] ?>">
                    <?php echo CYouWanna::multiTranslate($arItem['TEXT'], $arParams['LANGUAGE_ID']) ?>
                </a>

                <?if ( isset( $arResult['CHILDS_SECTIONS'][$arItem['PARAMS']['CHILD_SECTION']] ) ):?>
                <div class="yw-topmenusub-wrap">
                    <ul class="yw-topmenusub"<?if( $arResult['CHILDS_SECTIONS'][$arItem['PARAMS']['CHILD_SECTION']]['PIC']){?> style="background-image: url(<?=$arResult['CHILDS_SECTIONS'][$arItem['PARAMS']['CHILD_SECTION']]['PIC']?>)"<?}?>>
                        <?foreach ( $arResult['CHILDS_SECTIONS'][$arItem['PARAMS']['CHILD_SECTION']]['ITEMS'] as $key => $value ):?>
                           <li class="yw-topmenusubitem<?php if ($value["SELECTED"]) { ?> active<? } ?>"<?if($value['PIC']){?> data-bg="<?=$value['PIC']?>"<?}?>>
                               <a href="<?=$value['LINK']?>">
                                    <?php echo CYouWanna::multiTranslate($value['NAME'], $arParams['LANGUAGE_ID']) ?>
                               </a>
                           </li>
                        <?endforeach;?>
                    </ul>
                </div>
                <?endif?>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>