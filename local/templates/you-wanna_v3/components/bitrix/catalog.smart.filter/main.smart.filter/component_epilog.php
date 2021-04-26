<?
/** @var array $templateData */
/** @var @global CMain $APPLICATION */

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) {
    die();
}

CJSCore::Init(array('fx', 'popup'));

if (is_array($templateData['CHECKED_SIZES'])) {
    CGulliverCookie::setCookie('sizes_from_detail_page', urlencode(json_encode($templateData['CHECKED_SIZES'])), 600);
}