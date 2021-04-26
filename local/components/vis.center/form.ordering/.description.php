<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

$arComponentDescription = array(
    'NAME'        => GetMessage('vis.center.promo.NAME'),
    'DESCRIPTION' => GetMessage('vis.center.promo.DESCRIPTION'),
    'ICON'        => '/images/icon.gif',
    'SORT'        => 10,
    'CACHE_PATH'  => 'Y',
    'PATH'        => array(
        'ID'   => 'promo-feedback',
        'NAME' => GetMessage('vis.center.promo.COMPONENTS_GROUP'),
    ),
    'COMPLEX'     => 'N',
);