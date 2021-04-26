<?
use \Bitrix\Main\Localization\Loc;

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");   
$APPLICATION->SetTitle("�������� ������ ������");
?>

<?
if ($_GET['ORDER_ID'] and $_GET['hash'])
{
        \Bitrix\Main\Loader::includeModule("sale");
        \Bitrix\Main\Loader::includeModule("catalog");
        IncludeTemplateLangFile(__FILE__);
        $order=\Bitrix\Sale\Order::load($_GET['ORDER_ID']);
        
        $propertyCollection = $order->getPropertyCollection();
        $emailPropValue = $propertyCollection->getUserEmail();
        $email=$emailPropValue->getValue();
        $phonePropValue=$propertyCollection->getPhone();
        $phone=$phonePropValue->getValue();
        $arPaymentsCollection = $order->loadPaymentCollection();
        $currentPaymentOrder = $arPaymentsCollection->current();
        $invoiceId=$currentPaymentOrder->getField("ID");
        $PAY_ID=$order->getPaymentSystemId();
        
        $two_stage_payment=false;
        $widget_f='charge';
        
        $db_ptype = CSalePaySystemAction::GetList($arOrder = Array(), Array("ACTIVE"=>"Y", "PAY_SYSTEM_ID"=>$PAY_ID[0]));
        while ($ptype = $db_ptype->Fetch())
        {
               $CLOUD_PARAMS=unserialize($ptype['PARAMS']);
               if ($CLOUD_PARAMS['TYPE_SYSTEM']['VALUE']) $two_stage_payment=$CLOUD_PARAMS['TYPE_SYSTEM']['VALUE'];
        } 
 
 

        if ($order->getField("STATUS_ID")==$CLOUD_PARAMS['STATUS_AU']['VALUE'] and $_GET['test1']!="Y")
        {
             echo Loc::getMessage("WRONG_AU_STATUS");
             die();
        }
        
        if ($order->isPaid() and $_GET['test1']!="Y")
        {
             echo Loc::getMessage("WRONG_ORDER_PAY");
             die();
        }
        
        $hash=md5($_SERVER["HTTP_HOST"].$_GET['ORDER_ID'].$order->getPrice().$email);
        
        if ($_GET['hash']!=$hash) 
        {
              echo Loc::getMessage("WRONG_HASH");
              die();
        }
       /* 
        $db_ptype = CSalePaySystemAction::GetList($arOrder = Array(), Array("ACTIVE"=>"Y", "PAY_SYSTEM_ID"=>$PAY_ID[0]));
        while ($ptype = $db_ptype->Fetch())
        {
               $CLOUD_PARAMS=unserialize($ptype['PARAMS']);
        }  */
            
        $basket = \Bitrix\Sale\Basket::loadItemsForOrder($order);
        $basketItems = $basket->getBasketItems();
        $data=array();
        $items=array();
        foreach ($basketItems as $basketItem) {
            $prD=\Bitrix\Catalog\ProductTable::getList(
                array(
                    'filter'=>array('ID'=>$basketItem->getField('PRODUCT_ID')),
                    'select'=>array('VAT_ID'),
                )
            )->fetch();
            if($prD){
                if($prD['VAT_ID']==0){
                    $nds=null;
                }
                else{
                    $nds=floatval($basketItem->getField('VAT_RATE'))==0 ? 0 : $basketItem->getField('VAT_RATE')*100;
                }
            }else{
                $nds=null;
            }
    
            $items[]=array(
                    'label'=>$basketItem->getField('NAME'),
                    'price'=>number_format($basketItem->getField('PRICE'),2,".",''),
                    'quantity'=>$basketItem->getQuantity(),
                    'amount'=>number_format(floatval($basketItem->getField('PRICE')*$basketItem->getQuantity()),2,".",''),
                    'vat'=>$nds,
                    'ean13'=>null
            );
        }
        
        //��������� ��������
        if ($order->getDeliveryPrice() > 0) 
        {
            $items[] = array(
                'label' => '��������',
                'price' => number_format($order->getDeliveryPrice(), 2, ".", ''),
                'quantity' => 1,
                'amount' => number_format($order->getDeliveryPrice(), 2, ".", ''),
                'vat' => null,
                'ean13' => null
            );
        }
    
        $data['cloudPayments']['customerReceipt']['Items']=$items;
        $data['cloudPayments']['customerReceipt']['taxationSystem']='';
        $data['cloudPayments']['customerReceipt']['email']=$email;
        $data['cloudPayments']['customerReceipt']['phone']=$phone;
}
else 
{
    die(Loc::getMessage("ORDER_NOT_FOUND"));
}


if ($two_stage_payment)
{
    $widget_f='auth';
}
if ($CLOUD_PARAMS['WIDGET_LANG']['VALUE']) $lang_widget=$CLOUD_PARAMS['WIDGET_LANG']['VALUE'];
else $lang_widget='ru-RU';
?>


<script type="text/javascript" src="/bitrix/js/main/jquery/jquery-1.8.3.min.js?151126639193636"></script>
<script src="https://widget.cloudpayments.ru/bundles/cloudpayments"></script>
<div>
				��� ����� <b>�<?=$order->getId()?></b> �� <?=$order->getDateInsert()?> ������� ������.
        ����� ����� ������: <b>�<?=$order->getId()?></b><br><br>
				�� ������ ������� �� ����������� ������ ������ � <a href="/personal/order/">������������ ������� �����</a>. 
        �������� ��������, ��� ��� ����� � ���� ������ ��� ���������� ����� ������ ����� � ������ ������������ �����.			
</div>
<br><br>
<button class="cloudpay_button" id="payButton">��������</button>
<div id="result" style="display:none"></div>

<script type="text/javascript">
    var payHandler = function () {
        var widget = new cp.CloudPayments({language:'<?=$lang_widget?>'});
        widget.<?=$widget_f?>({ // options
                publicId: '<?=$CLOUD_PARAMS['APIPASS']['VALUE']?>',
                description: '����� � <?=$order->getId()?> �� "<?=$_SERVER['HTTP_HOST']?>" �� <?=$order->getDateInsert()?>', 
                amount: <?=number_format($order->getField('PRICE'), 2, '.', '')?>,
                currency: '<?=$order->getCurrency()?>',
                email: '<?=$email?>',
                invoiceId: '<?=$order->getId()?>',
                accountId: '<?=htmlspecialcharsbx($order->getUserId());?>',
                data: <?=CUtil::PhpToJSObject($data,false,true)?>,
            },
                function (options) { // success
                    <?if ($CLOUD_PARAMS['SUCCESS_URL']['VALUE'])
                    {
                       ?>
                         window.location.href="<?=$CLOUD_PARAMS['SUCCESS_URL']['VALUE']?>";
                       <?
                    }
                    else
                    {
                    ?>
                        BX("result").innerHTML="����� �������";
                        BX.style(BX("result"),"color","green");
                        BX.style(BX("result"),"display","block");
                    <?
                    }
                    ?>
                },
                function (reason, options) { // fail
                    <?if ($CLOUD_PARAMS['FAIL_URL']['VALUE'])
                    {
                       ?>
                         window.location.href="<?=$CLOUD_PARAMS['FAIL_URL']['VALUE']?>";
                       <?
                    }
                    else
                    {
                    ?>
                          BX("result").innerHTML=reason;
                          BX.style(BX("result"),"color","red");
                          BX.style(BX("result"),"display","block");
                    <?
                    }
                    ?>
                });
    };        
    $("#payButton").on("click", payHandler); //������ "��������"
</script>





<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
?>