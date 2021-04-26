/**
 * Created on 13.01.2017.
 */


/* работа с целями */

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
});

