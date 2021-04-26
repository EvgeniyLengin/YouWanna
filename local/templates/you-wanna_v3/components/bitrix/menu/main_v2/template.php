<?php
// echo "<pre>";
// print_r($arResult);
// echo "</pre>";





$this->SetFrameMode(true);

?>


<?

if (\Bitrix\Main\Loader::includeModule('iblock')) {

    $elementIterator = \Bitrix\Iblock\ElementTable::getList([
        'select' => [
            '*',
        ],
        'filter' => [
            'IBLOCK_ID' => 16,
        ]
    ]);

    $elemmMassId = [];

    foreach ($elementIterator->fetchAll() as $element) {
        $idelem = $element['ID'];

        $photoelem = $element['DETAIL_PICTURE'];
        $URL = mb_strtolower(CFile::GetPath($photoelem));
        $code = trim($element['CODE']);
        $urlelem = $element['DETAIL_TEXT'];
        $elemmMass[$urlelem]['photo'] = $URL;
        $elemmMass[$urlelem]['idelem'] = $idelem;
        $elemmMass[$urlelem]['code'] = $code;

    }


}

/*Без подключения этого класса работать не будет*/
// if(CModule::IncludeModule("iblock")){
//
//    /*Вместо IBLOCK_ID, впишите id инфоблока, вместо ELEMENT_ID впишите id элемента*/
//    $db_props = CIBlockElement::GetProperty(16, 9608, "sort", "asc", array());
//    /*Перечисляем все его свойства*/
//   while($ar_props = $db_props->Fetch()){
//        /*Выводим все параметры данного свойства*/
//        echo "<pre>";
//        print_r($ar_props);
//        echo "</pre>";
//    }
//
// }
?>



<?php

if (!empty($arResult)) : ?>
    <ul class="yw-topmenu">
        <?php foreach ($arResult as $key => $arItem) : ?>
            <?if(trim($arItem['TEXT']) == '') continue;?>
            <li class="yw-topmenuitem<?php if ($arItem["SELECTED"] = true) { ?> active<? }

             ?><?if(count($arResult['CHILDS_SECTIONS'][$arItem['PARAMS']['CHILD_SECTION']]['ITEMS']) >= 0) {?> parent<? } ?><?


            if($arItem['PARAMS']['ONLY_MOB'] == 'Y') {?> yw-desc-hide<?}?>">
                <a href="<?php echo $arItem["LINK"] ?>">
                    <?php echo CYouWanna::multiTranslate($arItem['TEXT'], $arParams['LANGUAGE_ID']) ?>
                </a>

            <?if ((count($arResult['CHILDS_SECTIONS'][$arItem['PARAMS']['CHILD_SECTION']]['ITEMS']) > 0)  && (trim($arItem['PARAMS']['CHILD_SECTION']) != "root")  ) { ?>
                <div class="yw-topmenusub-wrap">
             <?php  print_r(trim($arItem['PARAMS']['CHILD_SECTION']));     ?>
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


            <?php }  else { ?>
    <div class="yw-topmenusub-wrap">

                <?php $currentLinkUrl = $arItem['LINK'];
                $currentLinkUrl = mb_strtolower($currentLinkUrl);
                ?>
                 <ul class="yw-topmenusub" style="background-image: url(<?= $elemmMass[$currentLinkUrl]['photo'] ?>)">
                <?

                    if ($elemmMass[$currentLinkUrl]) {
                        $idelementCur = $elemmMass[$currentLinkUrl]['idelem'];
                        $photoURLCur = $elemmMass[$currentLinkUrl]['photo'];
                        $getParamInCode = $elemmMass[$currentLinkUrl]['code'];
                        /*Вместо IBLOCK_ID, впишите id инфоблока, вместо ELEMENT_ID впишите id элемента*/
                        $db_props = CIBlockElement::GetProperty(16, $idelementCur, "sort", "asc", array());
                        /*Перечисляем все его свойства*/
                        while($ar_props = $db_props->Fetch()){
                            /*Выводим все параметры данного свойства*/

                            if($ar_props['CODE'] == "QAPR") { ?>
                                <?php if($ar_props['VALUE_XML_ID'] != "") { ?>

                                <li class="yw-topmenusubitem">
                                   <a href="<?php  if (trim($getParamInCode) != "") {
                                       echo $ar_props['VALUE_XML_ID']."?gt=".$getParamInCode;
                                       // echo $ar_props['VALUE_XML_ID'];
                                   } else {
                                       echo $ar_props['VALUE_XML_ID'];
                                   }
                                    ?>"> <?= $ar_props['VALUE_ENUM']; ?>   </a>
                               </li>
                           <?php } ?>

                            <?php
                            }
                        }
                    }



                ?>
        </ul>
    </div>

<?    } ?>







            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>
