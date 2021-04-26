<?
	use Bitrix\Main\Localization\Loc;
	Loc::loadMessages(__FILE__);
?>

<div class="exitblock">
    <div class="fon"></div>    
    <div class="modaltext subscribe-wrapper">   
	    <div class="subscribe-heading" style="font-size: 40px; text-align: center; line-height: 40px; text-transform: uppercase;">
            <?= Loc::getMessage('FT_ACTIONS_TEXT') ?>
        </div>
        <div class="subscribe-meta">
            <?= Loc::getMessage('FT_PROMISES') ?>
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
                "CACHE_TYPE"              => "A",
                "CACHE_TIME"              => "3600",
                "SET_TITLE"               => "N"
            ),
            false
        ); ?>
    </div>
    <div class="closeblock">+</div>
</div>

<script>
// функция возвращает cookie с именем name, если есть, если нет, то undefined    
function getCookie(name) {
    var matches = document.cookie.match(new RegExp(
    "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
    ));
    return matches ? decodeURIComponent(matches[1]) : undefined;
}
// проверяем, есть ли у нас cookie, с которой мы не показываем окно и если нет, запускаем показ
var alertwin = getCookie("alertwin");
if (alertwin != "no") { 
    $(document).mouseleave(function(e){
        if (e.clientY < 0) {
            $(".exitblock").fadeIn("fast");    
            // записываем cookie на 1 день, с которой мы не показываем окно
            var date = new Date;
            date.setDate(date.getDate() + 1);    
            document.cookie = "alertwin=no; path=/; expires=" + date.toUTCString();       
        }    
    });
    $(document).click(function(e) {
        if (($(".exitblock").is(':visible')) && (!$(e.target).closest(".exitblock .modaltext").length)) {
            $(".exitblock").remove();
        }
    });  
}
</script>

<style>
.exitblock {    
    display:none;    
    position:fixed;
    left:0;
    top:0;
    width:100%;
    height:100%;
    z-index:100000;
}    
.exitblock .fon {
    background-color: #111;
    background-color: rgba(0,0,0,0.75);
    position: fixed;
    width: 100%;
    height: 100%;
}
.exitblock .modaltext {
    box-sizing: border-box;  
    padding: 60px 40px;
    background: #fff;
    position:fixed;
    top:40%;
    left:50%;
    margin-left:-25%;
    width:50%;
}        
.closeblock {
    cursor: pointer;
    position: fixed;
    line-height: 60px;
    font-size: 82px;
    transform: rotate(45deg);
    text-align: center;
    top: 20px;
    right: 30px;
    color: #fff;  
    color: rgba(255,255,255,0.5);  
}
.closeblock:hover {
    color: #fff;    
}
</style>