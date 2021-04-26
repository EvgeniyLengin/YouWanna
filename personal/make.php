<?php
/**
 * Страница оформления заказа
 *
 * @var CMain $APPLICATION
 */
require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php';
$pageTitle = (LANGUAGE_ID === 'en' ? 'Ordering' : 'Оформление заказа');
$APPLICATION->SetTitle($pageTitle);?>
             
			<? $APPLICATION->IncludeComponent(
                'bitrix:sale.order.ajax', 
                'custom.order.ajax',
                Array(
                    'ACTION_VARIABLE'                => 'soa-action',
                    'ADDITIONAL_PICT_PROP_3'         => '-',
                    'ADDITIONAL_PICT_PROP_4'         => '-',
                    'ALLOW_APPEND_ORDER'             => 'Y',
                    'ALLOW_AUTO_REGISTER'            => 'Y',
                    'ALLOW_NEW_PROFILE'              => 'N',
                    'ALLOW_USER_PROFILES'            => 'N',
                    'BASKET_IMAGES_SCALING'          => 'adaptive',
                    'BASKET_POSITION'                => 'after',
                    'COMPATIBLE_MODE'                => 'Y',
                    'DELIVERIES_PER_PAGE'            => '9',
                    'DELIVERY_FADE_EXTRA_SERVICES'   => 'N',
                    'DELIVERY_NO_AJAX'               => 'Y',
                    'DELIVERY_NO_SESSION'            => 'Y',
                    'DELIVERY_TO_PAYSYSTEM'          => 'd2p',
                    'DISABLE_BASKET_REDIRECT'        => 'N',
                    'ONLY_FULL_PAY_FROM_ACCOUNT'     => 'N',
                    'PATH_TO_AUTH'                   => '/auth/',
                    'PATH_TO_BASKET'                 => '/personal/basket/',
                    'PATH_TO_PAYMENT'                => 'payment.php',
                    'PATH_TO_PERSONAL'               => 'index.php',
                    'PAY_FROM_ACCOUNT'               => 'N',
                    'PAY_SYSTEMS_PER_PAGE'           => '9',
                    'PICKUPS_PER_PAGE'               => '5',
                    'PICKUP_MAP_TYPE'                => 'yandex',
                    'PRODUCT_COLUMNS_HIDDEN'         => array(),
                    'PRODUCT_COLUMNS_VISIBLE'        => array('PREVIEW_PICTURE', 'PROPS'),
                    'PROPS_FADE_LIST_1'              => array(),
                    'SEND_NEW_USER_NOTIFY'           => 'N',
                    'SERVICES_IMAGES_SCALING'        => 'adaptive',
                    'SET_TITLE'                      => 'N',
                    'SHOW_BASKET_HEADERS'            => 'N',
                    'SHOW_COUPONS_BASKET'            => 'Y',
                    'SHOW_COUPONS_DELIVERY'          => 'Y',
                    'SHOW_COUPONS_PAY_SYSTEM'        => 'Y',
                    'SHOW_DELIVERY_INFO_NAME'        => 'Y',
                    'SHOW_DELIVERY_LIST_NAMES'       => 'Y',
                    'SHOW_DELIVERY_PARENT_NAMES'     => 'Y',
                    'SHOW_MAP_IN_PROPS'              => 'N',
                    'SHOW_NEAREST_PICKUP'            => 'N',
                    'SHOW_NOT_CALCULATED_DELIVERIES' => 'L',
                    'SHOW_ORDER_BUTTON'              => 'final_step',
                    'SHOW_PAY_SYSTEM_INFO_NAME'      => 'Y',
                    'SHOW_PAY_SYSTEM_LIST_NAMES'     => 'Y',
                    'SHOW_PICKUP_MAP'                => 'Y',
                    'SHOW_STORES_IMAGES'             => 'Y',
                    'SHOW_TOTAL_ORDER_BUTTON'        => 'N',
                    'SHOW_VAT_PRICE'                 => 'Y',
                    'SKIP_USELESS_BLOCK'             => 'Y',
                    'SPOT_LOCATION_BY_GEOIP'         => 'Y',
                    'TEMPLATE_LOCATION'              => 'popup',
                    'TEMPLATE_THEME'                 => 'blue',
                    'USER_CONSENT'                   => 'N',
                    'USER_CONSENT_ID'                => '0',
                    'USER_CONSENT_IS_CHECKED'        => 'Y',
                    'USER_CONSENT_IS_LOADED'         => 'N',
                    'USE_CUSTOM_ADDITIONAL_MESSAGES' => 'N',
                    'USE_CUSTOM_ERROR_MESSAGES'      => 'N',
                    'USE_CUSTOM_MAIN_MESSAGES'       => 'N',
                    'USE_ENHANCED_ECOMMERCE'         => 'N',
                    'USE_PRELOAD'                    => 'Y',
                    'USE_PREPAYMENT'                 => 'N',
                    'USE_YM_GOALS'                   => 'N'
                )
            ); ?>


<? require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php';