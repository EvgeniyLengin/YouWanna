<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use \Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

if(!empty($arResult["USER_NAME"]) && !empty($arResult["ITEMS"]) /*&& !empty($arResult["USER_INFO"])*/) {

    $cardCount = count($arResult["ITEMS"]);
    $cardWidth = is_numeric($arResult["WIDTH"]) ? intval($arResult["WIDTH"]) : 60;
    $cardHeight = is_numeric($arResult["HEIGHT"]) ? intval($arResult["HEIGHT"]) : 60;
    $cardsInRow = is_numeric($arParams["CARDS_IN_ROW"]) ? intval($arParams["CARDS_IN_ROW"]) : 5;
    if ($cardsInRow > $cardCount) {
        $cardsInRow = $cardCount;
    }
    $widgetBodyWidth = $cardsInRow * ($cardWidth + 5);
    $widgetBodyHeight = ceil($cardCount / $cardsInRow) * ($cardHeight + 5);
    $widgetWidth = 30 + $widgetBodyWidth;
    $url = sprintf("https://www.instagram.com/%s/", $arResult["USER_NAME"]);
?>

    <div class="insta-wrapper">
        <div class="instashop__grid">
            <? foreach($arResult["ITEMS"] as $arItem):
                $params = json_decode($arItem['PARAMS']);
                $likes = $params->likes;
                $comments = $params->comments;
                $pSec = strtotime($arItem['DATE']); //время публикации в секундах
                $nSec = time(); //время в секундах сейчас
                $diffSec = $nSec-$pSec; //сколько секунд прошло
                $hour = floor($diffSec/3600);  //сколько часов прошло (в переводе с секунд)
                $hourOlo = round($diffSec/86400);  //сколько часов прошло (в переводе с секунд)

                if ($hour > 72) {
                    $pastTime = floor($hour/24).Loc::getMessage('DAYS_AGO');
                } elseif ($hour > 23 && $hour < 48) {
                    $pastTime = Loc::getMessage('DAY_AGO');
                } elseif ($hour < 24 && $hour > 4) {
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

                <div class="instashop-item">
                    <a href="instashop/item/<?=$arItem['ID']?>/" class="instashop-item__photo <?=$hour?>">
                        <div class="instashop-item__photo-wrapper">
                            <img src="<?=$arItem['MINI']['src']?> " alt="">
                        </div>
                    </a>
                    <div class="instashop-item__data">
                        <div>
                            <span class="instashop-item__likes">
                                <?=$likes?>
                            </span>
                                <span class="instashop-item__comments">
                                <?=$comments?>
                            </span>
                        </div>
                        <span class="instashop-item__time">
                            <?=$pastTime?>
                        </span>
                    </div>
                </div>

            <? endforeach; ?>
            <div class="instashop-item instashop-item--all">
                <a href="/instashop/" class="instashop-item__photo">
                    <div class="instashop-item__photo-wrapper">
                        <span class="m-auto">смотреть все</span>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <? /* ?>
    <div class="row insta-wrapper push-center">
        <?foreach($arResult["ITEMS"] as $arItem):?>
            <?
            $params = json_decode($arItem['PARAMS']);
            $likes = $params->likes;
            $comments = $params->comments;
            $pSec = strtotime($arItem['DATE']); //время публикации в секундах
            $nSec = time(); //время в секундах сейчас
            $diffSec = $nSec-$pSec; //сколько секунд прошло
            $hour = floor($diffSec/3600);  //сколько часов прошло (в переводе с секунд)
            $hourOlo = round($diffSec/86400);  //сколько часов прошло (в переводе с секунд)

            if($hour > 72) {
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
            <?//die()?>
            <div class="col col-4 item-wrapper">
                <a href="instashop/item/<?=$arItem['ID']?>/" class="item-photo <?=$hour?>">
                    <img src="/upload/resize_cache/instagram/5fe/300_300_2/5fedb41cf0aa5bd270f9f5f319af7e0c.jpg" alt="">
                </a>
                <div class="item-desc">
                    <span class="likes">
                        <?=$likes?>
                    </span>
                    <span class="comments">
                        <?=$comments?>
                    </span>
                    <span class="time">
                        <?=$pastTime?>
                    </span>
                </div>
            </div>
        <?endforeach?>

        <div class="col col-4 item-wrapper all-link">
            <a href="instashop/" class="push-center push-middle"><?= Loc::getMessage('SHOW_ALL') ?></a>
        </div>
    </div>
 <? */ ?>
<? } else { ?>
    <?=Loc::getMessage("WF_INSTAPOST_TEMPLATE_NO_DATA")?>
<? } ?>