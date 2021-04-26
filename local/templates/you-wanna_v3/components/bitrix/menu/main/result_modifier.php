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

?>