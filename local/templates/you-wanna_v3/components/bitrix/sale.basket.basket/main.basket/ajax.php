<?
define('STOP_STATISTICS', true);
define('NO_AGENT_CHECK', true);

use Bitrix\Main\Loader;

if (isset($_REQUEST['site_id']) && is_string($_REQUEST['site_id']))
{
	$siteID = trim($_REQUEST['site_id']);
	if ($siteID !== '' && preg_match('/^[a-z0-9_]{2}$/i', $siteID) === 1)
	{
		define('SITE_ID', $siteID);
	}
}

require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');

$request = Bitrix\Main\Application::getInstance()->getContext()->getRequest();
$request->addFilter(new \Bitrix\Main\Web\PostDecodeFilter);

if (!check_bitrix_sessid() || !$request->isPost())
	return;

if (!Loader::includeModule('sale') || !Loader::includeModule('catalog'))
	return;

$params = array();

if ($request->get('via_ajax') === 'Y')
{
	$signer = new \Bitrix\Main\Security\Sign\Signer;
	try
	{
		$params = $signer->unsign($request->get('signedParamsString'), 'sale.basket.basket');
		$params = unserialize(base64_decode($params));
	}
	catch (\Bitrix\Main\Security\Sign\BadSignatureException $e)
	{
		die();
	}
}

global $APPLICATION;

$APPLICATION->IncludeComponent(
	'bitrix:sale.basket.basket',
	'main.basket',
	$params
);