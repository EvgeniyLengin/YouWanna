<?php require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php';?>

<? $APPLICATION->IncludeFile(
    (($_REQUEST['n'] == 'Y') ? SITE_DIR.'ajax/get_basket.php' : SITE_DIR.'ajax/get_basket_new.php'),
    [
        'WITHOUT_BX_HEADER' => 'Y',
    ]
); ?>

<?php require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php';
