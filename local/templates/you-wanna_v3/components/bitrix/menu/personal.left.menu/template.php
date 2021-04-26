<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

# Вывод языковых фраз
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

global $USER; ?>

<? if (!empty($arResult)): ?>

    <ul class="s-tabs">

        <?
            foreach ($arResult as $arItem) :

                if ($arParams["MAX_LEVEL"] == 1 && $arItem["DEPTH_LEVEL"] > 1) {
                    continue;
                }
        ?>

                <? if ($arItem["SELECTED"]) : ?>
                    <li class="s-tabs__item s-tabs__item--active">
                        <span><?= $arItem["TEXT"] ?></span>
                    </li>
                <? else:?>
                    <li class="s-tabs__item">
                        <a href="<?= $arItem["LINK"] ?>"><?= $arItem["TEXT"] ?></a>
                    </li>
                <? endif ?>

        <? endforeach ?>

    </ul>

<? endif ?>