<?
	$MESS["iqsms.sender_PARTNER_URI"]  = "http://iqsms.ru/";
	$MESS["iqsms.sender_PARTNER_NAME"] = "СМС Дисконт";
	$MESS["iqsms.sender_MODULE_NAME"]  = "СМС Дисконт";
	$MESS["iqsms.sender_MODULE_DESCRIPTION"]
									   = "Модуль для смс оповещений пользователей о заказах, делать массовые и одиночные рассылки через сервис СМС Дисконт (iqsms.ru) и контролировать статусы доставки сообщений.";


	$MESS["iqsms.sender.SEND_PASSWORD.TEMPLATE_TYPE.NAME"]  = "Отправка пароля пользователю после регистрации при оформлении заказа";
	$MESS["iqsms.sender.SEND_PASSWORD.TEMPLATE_TYPE.DESCR"] = "#PHONE# - номер телефона";
	$MESS["iqsms.sender.SEND_PASSWORD.TEMPLATE.TEXT.main"]  = "Вы успешно зарегистрированны, для входа используйте логин - #LOGIN# и пароль - #PASSWORD#";
	$MESS["iqsms.sender.SEND_PASSWORD.TEMPLATE.PHONE.main"] = "#PHONE#";
	$MESS["iqsms.sender.SEND_PASSWORD.TEMPLATE.NAME.main"]  = "Отправка пароля пользователю после регистрации при оформлении заказа";


	$MESS["iqsms.sender.TRACKING_NUMBER.TEMPLATE_TYPE.NAME"]  = "Добавлен идентификатор отправления";
	$MESS["iqsms.sender.TRACKING_NUMBER.TEMPLATE_TYPE.DESCR"]
															  = "#PHONE# - номер телефона
#ORDER_ID# - номер заказа
#ORDER_STATUS_NAME# - название статуса заказа
#PRICE# - общая стоимость заказа
#SUM_PAID# - оплаченная сумма заказа
#PRICE_REST# - осталось оплатить за заказа
#DELIVERY_NAME# - наименование способа доставки
#DELIVERY_ID# - идентификатор службы доставки
#PRICE_DELIVERY# - стоимость доставки заказа
#PAY_SYSTEM_ID# - платежная система, которой (будет) оплачен заказ
#PAY_SYSTEM_NAME# - наименование способа оплаты
#PROPERTY_VALUE_{CODE}# -  шаблон подстановки значения свойства заказа, например #PROPERTY_VALUE_ADRES#
#TRACKING_NUMBER# - идентификатор отправления, если их несколько, они будут через запятую";
	$MESS["iqsms.sender.TRACKING_NUMBER.TEMPLATE.TEXT.main"]  = "Добрый день!  Идентификатор отправления вашего заказа  №#ORDER_ID# -  #TRACKING_NUMBER#";
	$MESS["iqsms.sender.TRACKING_NUMBER.TEMPLATE.PHONE.main"] = "#PHONE#";


	$MESS["iqsms.sender.TRACKING_NUMBER.TEMPLATE.NAME.main"] = "Добавлен идентификатор отправления";
	$MESS["iqsms.sender.ORDER_STATUS.TEMPLATE_TYPE.NAME"]    = "Заказ - смена статуса";
	$MESS["iqsms.sender.ORDER_STATUS.TEMPLATE_TYPE.DESCR"]
															 = "#PHONE# - номер телефона
#ORDER_ID# - номер заказа
#ORDER_STATUS_NAME# - название статуса заказа
#PRICE# - общая стоимость заказа
#SUM_PAID# - оплаченная сумма заказа
#PRICE_REST# - осталось оплатить за заказа
#DELIVERY_NAME# - наименование способа доставки
#DELIVERY_ID# - идентификатор службы доставки
#PRICE_DELIVERY# - стоимость доставки заказа
#PAY_SYSTEM_ID# - платежная система, которой (будет) оплачен заказ
#PAY_SYSTEM_NAME# - наименование способа оплаты
#PROPERTY_VALUE_{CODE}# -  шаблон подстановки значения свойства заказа, например #PROPERTY_VALUE_ADRES#
#TRACKING_NUMBER# - идентификатор отправления, если их несколько, они будут через запятую";
	$MESS["iqsms.sender.ORDER_STATUS.TEMPLATE.TEXT.main"]    = "Статус вашего заказа №#ORDER_ID# -  #ORDER_STATUS_NAME#";

	$MESS["iqsms.sender.ORDER_STATUS.TEMPLATE.PHONE.main"] = "#PHONE#";
	$MESS["iqsms.sender.ORDER_STATUS.TEMPLATE.NAME.main"]  = "Смена статуса заказа";
	$MESS["iqsms.sender.ORDER_PAYED.TEMPLATE_TYPE.NAME"]   = "Заказ оплачен";
	$MESS["iqsms.sender.ORDER_PAYED.TEMPLATE_TYPE.DESCR"]
														   = "#PHONE# - номер телефона
#ORDER_ID# - номер заказа
#ORDER_STATUS_NAME# - название статуса заказа
#PRICE# - общая стоимость заказа
#SUM_PAID# - оплаченная сумма заказа
#PRICE_REST# - осталось оплатить за заказа
#DELIVERY_NAME# - наименование способа доставки
#DELIVERY_ID# - идентификатор службы доставки
#PRICE_DELIVERY# - стоимость доставки заказа
#PAY_SYSTEM_ID# - платежная система, которой (будет) оплачен заказ
#PAY_SYSTEM_NAME# - наименование способа оплаты
#PROPERTY_VALUE_{CODE}# -  шаблон подстановки значения свойства заказа, например #PROPERTY_VALUE_ADRES#
#TRACKING_NUMBER# - идентификатор отправления, если их несколько, они будут через запятую";

	$MESS["iqsms.sender.ORDER_PAYED.TEMPLATE.TEXT.main"]  = "Ваш заказ №#ORDER_ID# оплачен";
	$MESS["iqsms.sender.ORDER_PAYED.TEMPLATE.PHONE.main"] = "#PHONE#";
	$MESS["iqsms.sender.ORDER_PAYED.TEMPLATE.NAME.main"]  = "Заказ оплачен";
	$MESS["iqsms.sender.ORDER_NEW.TEMPLATE_TYPE.NAME"]    = "Новый заказ";

	$MESS["iqsms.sender.ORDER_NEW.TEMPLATE_TYPE.DESCR"]
														= "#PHONE# - номер телефона
#ORDER_ID# - номер заказа
#ORDER_STATUS_NAME# - название статуса заказа
#PRICE# - общая стоимость заказа
#SUM_PAID# - оплаченная сумма заказа
#PRICE_REST# - осталось оплатить за заказа
#DELIVERY_NAME# - наименование способа доставки
#DELIVERY_ID# - идентификатор службы доставки
#PRICE_DELIVERY# - стоимость доставки заказа
#PAY_SYSTEM_ID# - платежная система, которой (будет) оплачен заказ
#PAY_SYSTEM_NAME# - наименование способа оплаты
#PROPERTY_VALUE_{CODE}# -  шаблон подстановки значения свойства заказа, например #PROPERTY_VALUE_ADRES#
#TRACKING_NUMBER# - идентификатор отправления, если их несколько, они будут через запятую";
	$MESS["iqsms.sender.ORDER_NEW.TEMPLATE.TEXT.main"]  = "Ваш заказ №#ORDER_ID# на сумму - #PRICE# руб. принят. Отследить статус заказа можно в личном кабинете.";
	$MESS["iqsms.sender.ORDER_NEW.TEMPLATE.PHONE.main"] = "#PHONE#";
	$MESS["iqsms.sender.ORDER_NEW.TEMPLATE.NAME.main"]  = "Новый заказ";

	$MESS["iqsms.sender.ORDER_CANCELED.TEMPLATE_TYPE.NAME"]  = "Заказ отменен";
	$MESS["iqsms.sender.ORDER_CANCELED.TEMPLATE_TYPE.DESCR"]
															 = "#PHONE# - номер телефона
#ORDER_ID# - номер заказа
#ORDER_STATUS_NAME# - название статуса заказа
#PRICE# - общая стоимость заказа
#SUM_PAID# - оплаченная сумма заказа
#PRICE_REST# - осталось оплатить за заказа
#DELIVERY_NAME# - наименование способа доставки
#DELIVERY_ID# - идентификатор службы доставки
#PRICE_DELIVERY# - стоимость доставки заказа
#PAY_SYSTEM_ID# - платежная система, которой (будет) оплачен заказ
#PAY_SYSTEM_NAME# - наименование способа оплаты
#PROPERTY_VALUE_{CODE}# -  шаблон подстановки значения свойства заказа, например #PROPERTY_VALUE_ADRES#
#TRACKING_NUMBER# - идентификатор отправления, если их несколько, они будут через запятую";
	$MESS["iqsms.sender.ORDER_CANCELED.TEMPLATE.TEXT.main"]  = "Ваш заказ №#ORDER_ID# отменен";
	$MESS["iqsms.sender.ORDER_CANCELED.TEMPLATE.PHONE.main"] = "#PHONE#";

	$MESS["iqsms.sender.ORDER_CANCELED.TEMPLATE.NAME.main"]           = "Заказ отменен";
	$MESS["iqsms.sender.MAIN_USER_CHANGEPASSWORD.TEMPLATE_TYPE.NAME"] = "Изменение пароля";
	$MESS["iqsms.sender.MAIN_USER_CHANGEPASSWORD.TEMPLATE_TYPE.DESCR"]
																	  = "#PHONE# - телефон
#LOGIN# - логин
#PASSWORD# - пароль";
	$MESS["iqsms.sender.MAIN_USER_CHANGEPASSWORD.TEMPLATE.TEXT.main"] = "Ваш пароль на сайте #SERVER_NAME# успешно изменен. Используйте для входа логин - #LOGIN# и пароль #PASSWORD#";

	$MESS["iqsms.sender.MAIN_USER_CHANGEPASSWORD.TEMPLATE.PHONE.main"] = "#PHONE#";
	$MESS["iqsms.sender.MAIN_USER_CHANGEPASSWORD.TEMPLATE.NAME.main"]  = "Изменение пароля пользователя";
	$MESS["iqsms.sender.MAIN_USER_ADD.TEMPLATE_TYPE.NAME"]             = "Регистрация пользователя";
	$MESS["iqsms.sender.MAIN_USER_ADD.TEMPLATE_TYPE.DESCR"]
																	   = "#PHONE# - телефон получателя
#LOGIN# - логин пользователя
#PASSWORD# - пароль пользователя";

	$MESS["iqsms.sender.MAIN_USER_ADD.TEMPLATE.TEXT.main"]                            = "Вы успешно зарегистрированы на сайте #SERVER_NAME#. Для входа используйте логин - #LOGIN# и пароль #PASSWORD#";
	$MESS["iqsms.sender.MAIN_USER_ADD.TEMPLATE.PHONE.main"]                           = "#PHONE#";
	$MESS["iqsms.sender.MAIN_USER_ADD.TEMPLATE.NAME.main"]                            = "Регистрация пользователя";



?>