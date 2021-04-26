<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if (isset($_REQUEST['SECTION_CODE']) && $_REQUEST['SECTION_CODE'] !== '') {
    /* Получить код родительского раздела инфоблока по коду текущего раздела */
    $menuSectionCode = CYouWanna::getParentSectionCodeByCode($_REQUEST['SECTION_CODE']);

    if(CModule::IncludeModule("iblock"))
    {
        $rsSections = CIBlockSection::GetList(
            array(),
            array(
                'IBLOCK_CODE' => CYouWanna::IBLOCK_TYPE_CATALOG,
                'CODE'        => $menuSectionCode
            ),
            false
        );

        if ($arSection = $rsSections->GetNext()) {
            foreach ( $arResult as $key => $item ){
                if ( $item['TEXT'] === $arSection['NAME'] ){
                    $arResult[$key]['SELECTED'] = true;
                }
            }
        }
    }

}

if(CModule::IncludeModule("iblock")) {

    /* Получаем все CHILD_SECTION из настроек меню */
    $childs = array();
    foreach ($arResult as $key => $value) {
        if(isset($value['PARAMS']['CHILD_SECTION']) && $value['PARAMS']['CHILD_SECTION'] != '') {
            /* Сперва проверим, что это пункт из каталога, и он активен */
            if(is_numeric($value['PARAMS']['CHILD_SECTION'])) {
                $arFilter = Array('IBLOCK_ID'=>3, 'GLOBAL_ACTIVE'=>'N', 'ID'=>$value['PARAMS']['CHILD_SECTION']); /* Ищем по ID неактивные разделы */
                $db_list = CIBlockSection::GetList(Array(), $arFilter, true);
                $db_list->NavStart(999);

                while($ar_result = $db_list->GetNext()) /* Если находим, убираем его из результирующего массива и завершаем итерацию */
                {
                    unset($arResult[$key]);
                    continue;
                }
            }
            $childs[] = $value['PARAMS']['CHILD_SECTION'];
        }
        if(isset($value['PARAMS']['PARENT_SECTION']) && $value['PARAMS']['PARENT_SECTION'] != '') {
            $pid = $value['PARAMS']['PARENT_SECTION'];
            $arResult['CHILDS_SECTIONS'][$pid]['ITEMS'][] = array(
                'NAME'=> $value['TEXT'],
                'LINK'=> $value['LINK'],
                'SELECTED'=> ($value['LINK'] !== $APPLICATION->GetCurPage(false) ? false : true)
            );
            unset($arResult[$key]);
        }
    }

    /* Разделы каталога */
    $arFilter = Array('IBLOCK_ID'=>3, 'GLOBAL_ACTIVE'=>'Y', 'SECTION_ID'=>$childs);
    $db_list = CIBlockSection::GetList(Array(), $arFilter, true);
    $db_list->NavStart(999);

    while($ar_result = $db_list->GetNext())
    {        
        $arResult['CHILDS_SECTIONS'][$ar_result['IBLOCK_SECTION_ID']]['ITEMS'][] = array(
            'NAME'=> $ar_result['NAME'],
            'LINK'=> $ar_result['SECTION_PAGE_URL'],
            'SELECTED'=> ($ar_result['SECTION_PAGE_URL'] !== $APPLICATION->GetCurPage(false) ? false : true),
            'PIC'=> ($ar_result["PICTURE"] != '' ? CFile::GetPath($ar_result["PICTURE"]) : false)
        );

    }

    /* Бестселлеры, скидки, новинки */
    $arFilter = Array('IBLOCK_ID'=>3, 'GLOBAL_ACTIVE'=>'Y', 'DEPTH_LEVEL'=>1);
    $db_list = CIBlockSection::GetList(Array(), $arFilter, true);
    $db_list->NavStart(999);

    while($ar_result = $db_list->GetNext())
    {        
        $arResult['CHILDS_SECTIONS']['root']['ITEMS'][] = array(
            'NAME'=> $ar_result['NAME'],
            'LINK'=> $ar_result['SECTION_PAGE_URL'],
            'SELECTED'=> ($ar_result['SECTION_PAGE_URL'] !== $APPLICATION->GetCurPage(false) ? false : true),
            'PIC'=> ($ar_result["PICTURE"] != '' ? CFile::GetPath($ar_result["PICTURE"]) : false)
        );
    }

    /* Картинки для подменю */
    $arFilter = Array('IBLOCK_ID'=>3, 'GLOBAL_ACTIVE'=>'Y', 'ID'=>$childs);
    $db_list = CIBlockSection::GetList(Array(), $arFilter, true);
    $db_list->NavStart(999);

    while($ar_result = $db_list->GetNext())
    {      
        if($ar_result["PICTURE"] != '') $arResult['CHILDS_SECTIONS'][$ar_result['ID']]['PIC'] = CFile::GetPath($ar_result["PICTURE"]);
    }
}
// echo '<pre style="display: none">';
// print_r($arResult);
// echo '</pre>';
?>