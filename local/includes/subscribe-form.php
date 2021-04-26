<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>

<div class="yw-section yw-section--subscribe">
    <div class="container">
        <div class="row">
            <div class="col-md-8 push-center subscribe-wrapper">
                <div class="subscribe-heading">
                    <? if (LANGUAGE_ID === 'ru') { ?>
                        узнавайте первыми <br/>о новинках, акциях и скидках
                    <? } else if (LANGUAGE_ID === 'en') { ?>
                        learn first about <br/>about news, promotions and discounts
                    <? } ?>
                </div>
                <div class="subscribe-meta">
                    <? if (LANGUAGE_ID === 'ru') { ?>
                        обещаем, спама не будет
                    <? } else if (LANGUAGE_ID === 'en') { ?>
                        Promise, there will not be spam
                    <? } ?>
                </div>
                <? $APPLICATION->IncludeComponent(
                    "bitrix:sender.subscribe",
                    "subscribe.main.page",
                    array(
                        "COMPONENT_TEMPLATE"      => ".default",
                        "USE_PERSONALIZATION"     => "N",
                        "CONFIRMATION"            => "N",
                        "HIDE_MAILINGS"           => "N",
                        "SHOW_HIDDEN"             => "N",
                        "USER_CONSENT"            => "N",
                        "USER_CONSENT_ID"         => "0",
                        "USER_CONSENT_IS_CHECKED" => "Y",
                        "USER_CONSENT_IS_LOADED"  => "N",
                        "AJAX_MODE"               => "Y",
                        "AJAX_OPTION_JUMP"        => "N",
                        "AJAX_OPTION_STYLE"       => "Y",
                        "AJAX_OPTION_HISTORY"     => "N",
                        "AJAX_OPTION_ADDITIONAL"  => "",
                        "CACHE_TYPE"              => "N",
                        "CACHE_TIME"              => "3600",
                        "SET_TITLE"               => "N"
                    ),
                    false
                ); ?>
                <div class="subscribe-agree">
                    <? if (LANGUAGE_ID === 'ru') { ?>
                        нажимая на кнопку «подписаться», вы даете согласие  на обработку<br/>ваших  персональных данных и соглашаетесь с <a href="/personal/privacy-policy/">условиям конфиденциальности</a>
                    <? } else if (LANGUAGE_ID === 'en') { ?>
                        By clicking on the "Subscribe" button, you consent to the processing of <br/> your personal data and agree to the <a href="#"> privacy conditions </a>
                    <? } ?>
                </div>
            </div>
        </div>
    </div>
</div>
