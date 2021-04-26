<?if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

$this->IncludeLangFile('template.php');

$cartId = $arParams['cartId'];


if ($arParams["SHOW_PRODUCTS"] == "Y" && ($arResult['NUM_PRODUCTS'] > 0 || !empty($arResult['CATEGORIES']['DELAY'])))
{
		try{
		$isColorProperty = true;
		
			$arColors = [];
		
			$hlBlock = \Bitrix\Highloadblock\HighloadBlockTable::getList(
				['filter' => ['=TABLE_NAME' => 'ColorReference']]
			)->fetch();
		
			if ($hlBlock['ID']) {
				$reqParams['HLB'] = $hlBlock;
		
				foreach ($skuBlockData['SKU_VALUES_LIST'] as $item) {
					$reqParams['VALUES'][$item['XML_ID']] = $item['XML_ID'];
				}
		
				$entity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($reqParams['HLB']);
				$entityDataClass = $entity->getDataClass();
				$fieldsList = $entityDataClass::getMap();
		
				if (isset($fieldsList['ID'])) {
					$fieldsList = $entityDataClass::getEntity()->getFields();
				}
		
				$directoryOrder = [];
				if (isset($fieldsList['UF_COLOR_SORT'])) {
					$directoryOrder['UF_COLOR_SORT'] = 'ASC';
				}
				$directoryOrder['ID'] = 'ASC';
		
				$arFilter = [
					'order' => $directoryOrder,
					'limit' => 1000
				];
				if (!empty($reqParams['VALUES'])) {
					$arFilter['filter'] = array(
						'=UF_XML_ID' => $reqParams['VALUES'],
					);
				}
		
				$rsPropEnums = $entityDataClass::getList($arFilter);
				while ($arEnum = $rsPropEnums->fetch()) {
					if (!array_key_exists($arEnum['UF_XML_ID'], $arColors)) {
						$arColors[$arEnum['UF_XML_ID']]['SKU_HEX'] = $arEnum['UF_HEX'];
					}
				}
			}
		}
		catch(Exception $e){
		
		}
	?>
	<div class="yw-basket-heading">Корзина</div>
	<div data-role="basket-item-list" class="bx-basket-item-list">

		<?if ($arParams["POSITION_FIXED"] == "Y"):?>
		<div id="<?=$cartId?>status" class="bx-basket-item-list-action" onclick="<?=$cartId?>.toggleOpenCloseCart()"><?=GetMessage("TSB1_COLLAPSE")?></div>
		<?endif?>

		

		<div id="<?=$cartId?>products" class="bx-basket-item-list-container">
			<?foreach ($arResult["CATEGORIES"] as $category => $items):
			if (empty($items))
				continue;
			?>
			<?foreach ($items as $v):?>
 			<?
 			$ID_PICTYRE = $v["PREVIEW_PICTURE"];
			 $URL = CFile::GetPath($ID_PICTYRE);
 			?>
			<div class="bx-basket-item-list-item">
				
				<div class="bx-basket-item-list-item-img">
					<?if ($arParams["SHOW_IMAGE"] == "Y" && $v["PICTURE_SRC"]):?>
					<?if($v["DETAIL_PAGE_URL"]):?>
					<a href="<?=$v["DETAIL_PAGE_URL"]?>"><img src="<?=$URL?>" alt="<?=$v["NAME"]?>"></a>
					<?else:?>
					<img src="<?=$URL?>" alt="<?=$v["NAME"]?>" />
					<?endif?>
					<?endif?>

				</div>
				<div class="bx-basket-item-list-item-description">
					<div class="bx-basket-item-list-item-name">
						<?if ($v["DETAIL_PAGE_URL"]):?>
						<a href="<?=$v["DETAIL_PAGE_URL"]?>"><?=$v["NAME"]?></a>
						<?else:?>
						<?=$v["NAME"]?>
						<?endif?>
					</div>
					<?
					   try{
						   // to do on switcher
						   $myStaticColor = $arColors[$v['PROPERTY_COLOR_VALUE']]['SKU_HEX'];
						   ?>
						   <div class="basket-item-property basket-item-property-scu-text basket-item-property--color" data-entity="basket-item-sku-block">
							   <div class="basket-item-property-name">Цвет</div>
							   <div class="basket-item-property-value">                                                            
								   <ul class="basket-item-scu-list">
										   <li class="basket-item-scu-item selected
											   " data-entity="basket-item-sku-field" data-initial="true" data-value-id="<?=$v['PROPERTY_COLOR_VALUE']?>" data-property="COLOR" data-hex="<?=$myStaticColor?>">
											   <span class="basket-item-scu-item-val" style="background-color: <?=$myStaticColor?>;"></span>
										   </li>
								   </ul>
							   </div>
						   </div>
						   <div class="basket-item-property basket-item-property-scu-text" data-entity="basket-item-sku-block">
							   <div class="basket-item-property-name">Размер</div>
							   <div class="basket-item-property-value">
								   <div class="basket-property-value-select"><span><?=$v['PROPERTY_SIZE_VALUE']?></span></div>
								   <ul disabled="" class="basket-item-scu-list" style="display: none;">
										   <li class="basket-item-scu-item selected
											   " data-entity="basket-item-sku-field" data-initial="true" data-value-id="<?=$v['PROPERTY_SIZE_VALUE']?>" data-sku-name="<?=$v['PROPERTY_SIZE_VALUE']?>" data-property="SIZE" data-hex="">
											   <span class="basket-item-scu-item-inner">
												   <span class="name"><?=$v['PROPERTY_SIZE_VALUE']?></span>
											   </span>
										   </li>
								   </ul>
							   </div>
						   </div>
						   <?
					   }
					   catch(Exception $e){
	   
					   }
					?>
					<div class="basket-items-list-item-amount">
						<div class="basket-item-block-amount" data-entity="basket-item-quantity-block">
							<span class="basket-item-amount-field-description">
							Количество                                    </span>
							<div class="basket-item-amount-box">
								<span class="basket-item-amount-btn-minus" onclick="set_Quantity('minus',<?=$v['ID']?>);" data-entity="basket-item-quantity-minus">-</span>
								<div class="basket-item-amount-filed-block">
									<input type="text" class="basket-item-amount-filed" onchange="<?=$cartId?>.UpdateQuantityItemFromCart(<?=$v['ID']?>,$(this).val(),<?=$v['PRODUCT_ID']?>)" value="<?=$v["QUANTITY"]?>" data-value="<?=$v["QUANTITY"]?>" data-entity="basket-item-quantity-field" id="basket-item-quantity-<?=$v['ID']?>">
								</div>
								<span class="basket-item-amount-btn-plus" onclick="set_Quantity('plus',<?=$v['ID']?>);" data-entity="basket-item-quantity-plus">+</span>
							</div>
						</div>
					</div>
					<div class="bx-basket-item-list-item-remove" onclick="<?=$cartId?>.removeItemFromCart(<?=$v['ID']?>)" title="<?=GetMessage("TSB1_DELETE")?>">Удалить</div>
					<?if (true):/*$category != "SUBSCRIBE") TODO */?>
					<div class="bx-basket-item-list-item-price-block">
						<?if ($arParams["SHOW_PRICE"] == "Y"):?>
						<div class="bx-basket-item-list-item-price"><?=$v["PRICE_FMT"]?></div>
						<?if ($v["FULL_PRICE"] != $v["PRICE_FMT"]):?>
						<div class="bx-basket-item-list-item-price-old"><?=$v["FULL_PRICE"]?></div>
						<?endif?>
						<?endif?>
					</div>
					<?endif?>
				</div>

			</div>
			<?endforeach?>
			<?endforeach?>
		</div>
	</div>
	<? require(realpath(dirname(__FILE__)).'/top_template.php'); ?>
	<?if ($arParams["PATH_TO_ORDER"] && $arResult["CATEGORIES"]["READY"]):?>
	<div class="bx-basket-item-list-button-container">
		<a href="<?=$arParams["PATH_TO_ORDER"]?>" class="btn btn-primary"><?=GetMessage("TSB1_2ORDER")?></a>
	</div>
	<?endif?>
	<script>
		BX.ready(function(){
			<?=$cartId?>.fixCart();
			var quantityInCart = <?=$arResult['NUM_PRODUCTS']?>;
			if(quantityInCart > 0){
				$(".js-link-cart-quantity").text(quantityInCart);
			}
			else{
				$(".js-link-cart-quantity").text("");
			}
		});
		function set_Quantity(action , id){
			var ind = $('#basket-item-quantity-'+id);
			if(action == 'minus'){
				ind.val(parseInt(ind.val())-1);
			}
			else{
				ind.val(parseInt(ind.val())+1);
			}
			ind.trigger('change');
		}
	</script>
	<?
}