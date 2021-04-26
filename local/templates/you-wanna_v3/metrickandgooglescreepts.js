// <script src="//code-ya.jivosite.com/widget/6PhYzLIIJi" async></script>
/**
 * Created on 13.01.2017.
 */


/* работа с целями */
//ЭТО goals.js
var Hit = function (getParams)
{
	var url;
	url = top.location.pathname;
	getParams = getParams || false;
	if (getParams != false)
	{
		if (0 !== getParams.indexOf("/"))
		{
			if (0 == getParams.indexOf("#"))
			{
				url += getParams;
			}
			else
			{
				url += "?" + getParams;
			}
		}
	}
	if (top.yaCounter30624962 !== undefined)
	{
		top.yaCounter30624962.hit(url, null, null);
	}
	if (top.ga !== undefined)
	{
		ga('send', 'pageview', url);
	}
	if (IS_DEV_MODE === true)
	{
		console.log("[hit] " + url);
	}
};

var Goal = function (targetName, params)
{
	if (top.yaCounter30624962 !== undefined)
	{
		if (params == undefined)
		{
			params = {}
		}
		params["url"] = top.location.pathname;
		top.yaCounter30624962.reachGoal(targetName, params);
	}
	if (IS_DEV_MODE === true)
	{
		console.log("[goal] " + targetName);
	}
};

/*
Фиксация целей
 */
$(function () {
	//добавление в корзину
	$('body').on('click', '.js-catalog-add-to-basket', function () {
		Goal('korzina');
	});
    pixel();
    // calltouch();
    YandexMetric();
    gtmone();
});
// конец goals.js

// Пиксель подключается в хедере
function pixel() {
    let px = "<script>!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,document,'script','https://connect.facebook.net/en_US/fbevents.js');fbq('init', '843622079372397');fbq('track', 'PageView');</script>";
        // !function(f,b,e,v,n,t,s)
        // {if(f.fbq)return;n=f.fbq=function(){n.callMethod?n.callMethod.apply(n,arguments):n.queue.push(arguments)};
        // if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
        // n.queue=[];t=b.createElement(e);t.async=!0;
        // t.src=v;s=b.getElementsByTagName(e)[0];
        // s.parentNode.insertBefore(t,s)}(window,document,'script',
        // 'https://connect.facebook.net/en_US/fbevents.js');
        // fbq('init', '843622079372397');
        // fbq('track', 'PageView');
        // console.log('s');
    // </script>";
    $('body').append(px);
}
//calltouch  footer
// function calltouch() {
//     let addcall = "<script  type='text/javascript'> (function(w,d,n,c){w.CalltouchDataObject=n;w[n]=function(){w[n]['callbacks'].push(arguments)};if(!w[n]['callbacks']){w[n]['callbacks']=[]}w[n]['loaded']=false;if(typeof c!=='object'){c=[c]}w[n]['counters']=c;for(var i=0;i<c.length;i+=1){p(c[i])}function p(cId){var a=d.getElementsByTagName('script')[0],s=d.createElement('script'),i=function(){a.parentNode.insertBefore(s,a)};s.type='text/javascript';s.async=true;s.src='https://mod.calltouch.ru/init.js?id='+cId;if(w.opera=='[object Opera]'){d.addEventListener('DOMContentLoaded',i,false)}else{i()}}})(window,document,'ct','cczl1vws'); </script>";
//     $('body').append(addcall);
// }
//солянка из футера
jQuery(document).on('mousedown touchstart', '#bx_subscribe_btn_sljzMT', function() { window.ct('goal','sub') });
jQuery(document).on('mousedown touchstart', 'a[href="/instashop/"]', function() { window.ct('goal','inst') });
jQuery(document).on('mousedown touchstart', 'a[href="/about/contacts/"]', function() { window.ct('goal','contacts') });
jQuery(document).on('mousedown touchstart', '.header button.callback-modal-button', function() { window.ct('goal','call') });
jQuery(document).on('mousedown touchstart', 'a[href="/personal/basket/"]', function() { window.ct('goal','basket') });
jQuery(document).on('mousedown touchstart', 'a[href="/personal/order/"]', function() { window.ct('goal','lk') });

//<!-- Yandex.Metrika counter -->
//
function YandexMetric() {
    let metrikaY = "<script async type='text/javascript' > (function(m,e,t,r,i,k,a){m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)}; m[i].l=1*new Date();k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)}) (window, document, 'script', 'https://mc.yandex.ru/metrika/tag.js', 'ym'); ym(45473325, 'init', { clickmap:true, trackLinks:true, accurateTrackBounce:true, webvisor:true, trackHash:true, ecommerce:'dataLayer' });</script>";
     $('body').append(metrikaY);

}

function gtmone() {
    let gtagmanagerone = "<script src='https://www.googletagmanager.com/gtag/js?id=UA-107276732-1'></script>";
    $('body').append(gtagmanagerone);
    let gtagmanagertwo = "<script async>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','GTM-WB9HGWS');</script>";
    $('body').append(gtagmanagertwo);

    let gtmthree = "<script>window.dataLayer = window.dataLayer || [];function gtag() {dataLayer.push(arguments)};gtag('js', new Date());gtag('config', 'UA-107276732-1');</script>";
    $('body').append(gtmthree);
}
