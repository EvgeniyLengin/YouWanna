<?php
/**
 * Date: 02.03.2018
 * Time: 23:21
 */


?>
<? if($arResult['ITEMS']) { ?>
    <? foreach($arResult['ITEMS'] as $key => $arItem) { ?>
        <?
            $arItem['NAME'] = CYouWanna::multiTranslate($arItem['NAME'], $arParams['LANGUAGE_ID']);
            $arItem['PREVIEW_TEXT'] = CYouWanna::multiTranslate($arItem['PREVIEW_TEXT'], $arParams['LANGUAGE_ID']);
            $arItem['PROPERTIES']['LINK_NAME']['VALUE'] = CYouWanna::multiTranslate($arItem['PROPERTIES']['LINK_NAME']['VALUE'], $arParams['LANGUAGE_ID']);
            $photo = CFile::ResizeImageGet($arItem['PREVIEW_PICTURE']['ID'], array("width"=>1920, "height"=>900), BX_RESIZE_IMAGE_EXACT);
            $video = false;
            $video = $arItem['PROPERTIES']['VIDEO']['VALUE']['path'] != '';
            $f = $arItem['PROPERTIES']['VIDEO']['VALUE']['path'];
            $t = end(explode('.', $f));

            $videoMob = false;
            $videoMob = $arItem['PROPERTIES']['VIDEO_MOB']['VALUE']['path'] != '';
            $fMob = $arItem['PROPERTIES']['VIDEO_MOB']['VALUE']['path'];
            $tMob = end(explode('.', $fMob));
        ?>
        <?if($video):?>
            <div class="yw-section yw-section--video<?if($key == 0){?> active<?}?><?if($videoMob):?> d-none d-md-block<?endif?>" id="#page<?=$key?>">
                <video loop muted autoplay class="yw-video">
                    <source src="<?=$f?>" type="video/<?=strtolower($t)?>" />
                </video>
            </div>
            <?if($videoMob):?>
            <div class="yw-section yw-section--video<?if($key == 0){?> active<?}?> d-md-none" id="#page<?=$key?>">
                
                <video loop muted autoplay class="yw-video">
                    <source src="<?=$fMob?>" type="video/<?=strtolower($tMob)?>" />
                </video>
                
            </div>
            <?endif?>
        <?else:?>        
	        <div class="section fp-section fp-section--cover <?if($key == 0){?>active<?}?>" data-color="Y" id="#page<?=$key?>" style="background-image:url(<?=$photo['src']?>)">
	            <div class="slide-info col push-center push-middle">
	                <p class="title"><?=$arItem['NAME']?></p>
	                <p class="description"><?=$arItem['PREVIEW_TEXT']?></p>
	                <a href="<?=$arItem['PROPERTIES']['LINK']['VALUE']?>" class="more-link" title="<?=$arItem['NAME']?>"><?=$arItem['PROPERTIES']['LINK_NAME']['VALUE']?></a>
	            </div>
	            <div class="slide-info__mask"></div>
	        </div>
        <?endif?>
    <? } ?>
<? } ?>