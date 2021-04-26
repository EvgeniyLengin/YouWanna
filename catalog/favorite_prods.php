<?php
/**
 * Страница общего списка товаров
 *
 * @global CMain $APPLICATION
 */
require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php';

?>

<h1 class="page-title"><?= CYouWanna::multiTranslate('Избранные товары', LANGUAGE_ID) ?></h1>

<div class="row" id="favorites"></div>

<script>
    var favoriteElements = JSON.parse(localStorage.getItem('favoriteProds'));
    $('#favorites').load('/ajax/favorite_component.php', {
        'favorites[]': favoriteElements,
        'lang': LANGUAGE_ID
    });
</script>

<?php require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php';