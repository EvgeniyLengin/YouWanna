<?php
/**
 * Date: 03.03.2018
 * Time: 16:02
 */
?>
<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<?
$INPUT_ID = trim($arParams["~INPUT_ID"]);
if (strlen($INPUT_ID) <= 0)
    $INPUT_ID = "title-search-input";
$INPUT_ID = CUtil::JSEscape($INPUT_ID);

$CONTAINER_ID = trim($arParams["~CONTAINER_ID"]);
if (strlen($CONTAINER_ID) <= 0)
    $CONTAINER_ID = "title-search";
$CONTAINER_ID = CUtil::JSEscape($CONTAINER_ID);

if ($arParams["SHOW_INPUT"] !== "N"):?>
    <form action="/catalog/search/" class="searchform" name="search">
        <div class="search-submit">
            <input type="submit" value="" name="s" title="Поиск">
        </div>
        <div class="search-input">
            <input type="text" name="q" placeholder="Поиск">
        </div>
    </form>
<? endif ?>
<script type="text/javascript">
    var jsControl = new JCTitleSearch({
        //'WAIT_IMAGE': '/bitrix/themes/.default/images/wait.gif',
        'AJAX_PAGE': '<?echo CUtil::JSEscape(POST_FORM_ACTION_URI)?>',
        'CONTAINER_ID': '<?echo $CONTAINER_ID?>',
        'INPUT_ID': '<?echo $INPUT_ID?>',
        'MIN_QUERY_LEN': 2
    });
</script>
