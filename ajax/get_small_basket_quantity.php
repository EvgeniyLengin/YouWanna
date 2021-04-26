<?
/**
 * Получение количетсва товаров в Корзине
 */

define('NO_KEEP_STATISTIC', true);
define('STOP_STATISTICS', true);
define('NO_AGENT_STATISTIC', true);
define('NO_AGENT_CHECK', true);
define('NOT_CHECK_PERMISSIONS', true);
define('PERFMON_STOP', true);

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

if (CModule::IncludeModule('sale')) {
    $quantity = 0;
    $dbBasketItems = CSaleBasket::GetList(
        [
            'NAME' => 'ASC',
            'ID'   => 'ASC'
        ],
        [
            'FUSER_ID' => CSaleBasket::GetBasketUserID(),
            'LID'      => SITE_ID,
            'ORDER_ID' => 'NULL'
        ],
        false,
        false,
        [
            'ID',
            'PRODUCT_ID',
            'QUANTITY',
            'CAN_BUY',
            'PRICE'
        ]
    );
    while ($arItems = $dbBasketItems->Fetch()) {
        if ($arItems['CAN_BUY'] === 'Y') {
            $quantity += (int)$arItems['QUANTITY'];
        }
    }

    echo $quantity;
}
?>

<script type="text/javascript">
    
</script>
