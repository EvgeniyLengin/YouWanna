<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use \Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

// Подгрузка информации о постраничной навигации
$navCountPage = $arResult["NAV_PARAMS"]['NAV_OBJECT']->getPageCount();
$navPage = $arResult["NAV_PARAMS"]['NAV_OBJECT']->getCurrentPage();
$navCountItem = $arResult["NAV_PARAMS"]['NAV_OBJECT']->getRecordCount();
$navSize = $arResult["NAV_PARAMS"]['NAV_OBJECT']->getLimit();
$navItemsLeft = $navCountItem - ($navPage * $navSize);
$navNum = $arResult["NAV_PARAMS"]['NAV_OBJECT']->getCurrentPage();
if ($navItemsLeft < $navSize) {
    $navSize = $navItemsLeft;
}

if(!empty($arResult["USER_NAME"]) && !empty($arResult["ITEMS"])):

    $cardCount = count($arResult["ITEMS"]);
    $cardWidth = is_numeric($arResult["WIDTH"])?intval($arResult["WIDTH"]):60;
    $cardHeight = is_numeric($arResult["HEIGHT"])?intval($arResult["HEIGHT"]):60;
    $cardsInRow = is_numeric($arParams["CARDS_IN_ROW"])?intval($arParams["CARDS_IN_ROW"]):5;
    if($cardsInRow > $cardCount){
        $cardsInRow = $cardCount;
    }
    $bShowBio = ($arParams["SHOW_BIO"] == "Y" || empty($arParams["SHOW_BIO"]))?true:false;
    $bShowHead = ($arParams["SHOW_USER_NAME"] == "Y" || empty($arParams["SHOW_USER_NAME"]))?true:false;
    $url = sprintf("https://www.instagram.com/%s/",$arResult["USER_NAME"]);
?>
    <div class="row instashop-page-wrapper push-center">        
        <? foreach($arResult["ITEMS"] as $arItem) { ?>
            <?
            $params = json_decode($arItem['PARAMS']);
            $likes = $params->likes;
            $comments = $params->comments;
            $pSec = $arItem['DATE']->getTimestamp(); //время публикации в секундах
            $nSec = time(); //время в секундах сейчас
            $diffSec = $nSec-$pSec; //сколько секунд прошло
            $hour = floor($diffSec/3600);  //сколько часов прошло (в переводе с секунд)
            $hourOlo = round($diffSec/86400);  //сколько часов прошло (в переводе с секунд)

            if($hour > 48) {
                $pastTime = floor($hour/24).Loc::getMessage('DAYS_AGO');
            } elseif($hour > 23 && $hour < 48) {
                $pastTime = Loc::getMessage('DAY_AGO');
            } elseif($hour < 24 && $hour > 4) {
                $pastTime = $hour.Loc::getMessage('HOURS_AGO');
            } elseif ($hour < 5 && $hour > 1) {
                $pastTime = $hour.Loc::getMessage('LESS_FIVE_HOURS');
            } elseif ($hour < 2 && $hour != 0) {
                $pastTime = Loc::getMessage('HOUR_AGO');
            } else {
                $pastTime = floor($diffSec/60);
                $pastTime = $pastTime.Loc::getMessage('MINS_AGO');
            }
            ?>
            <div class="col-lg-4 col-md-6 col-sm-12 item-wrapper">
                <a href="/instashop/item/<?=$arItem['ID']?>/" class="item-photo <?=$hour?>">
                    <img src="<?=$arItem['MINI']['src']?>" alt="">
                </a>
                <div class="item-desc">
                    <span class="likes">
                        <?= $likes ?>
                    </span>
                    <span class="comments">
                        <?= $comments ?>
                    </span>
                    <span class="time">
                        <?= $pastTime ?>
                    </span>
                </div>
            </div>
        <? } ?>
    </div>    
<?else:?>
    <?=Loc::getMessage("WF_INSTAPOST_TEMPLATE_NO_DATA")?>
<? endif; ?>