<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

/**
 * @var array $arParams
 */
?>
<script id="basket-total-template" type="text/html">
	<div class="basket-checkout-container" data-entity="basket-checkout-aligner">
		<div class="yw-basket-heading"><?=Loc::getMessage('SBB_ORDER_TITLE')?></div>
        <?
        if ($arParams['HIDE_COUPON'] !== 'Y')
        {
            ?>
            <div class="basket-coupon-section">
                <div class="basket-coupon-block-field">
                    <div class="basket-coupon-block-field-description">
                        <?=Loc::getMessage('SBB_COUPON_ENTER')?>:
                    </div>
                    <div class="form">
                        <div class="form-group" style="position: relative;">
                            <input type="text" class="form-control" id="" placeholder="<?=Loc::getMessage('SBB_PROMO')?>" data-entity="basket-coupon-input">
                            <span class="basket-coupon-block-coupon-btn"><?=Loc::getMessage('SBB_ADD')?></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="basket-coupon-alert-section">
                <div class="basket-coupon-alert-inner">
                    {{#COUPON_LIST}}
                    <div class="basket-coupon-alert text-{{CLASS}}">
						<span class="basket-coupon-text">
							<strong>{{COUPON}}</strong> - <?=Loc::getMessage('SBB_COUPON')?> {{JS_CHECK_CODE}}
							{{#DISCOUNT_NAME}}({{{DISCOUNT_NAME}}}){{/DISCOUNT_NAME}}
						</span>
						<span class="close-link" data-entity="basket-coupon-delete" data-coupon="{{COUPON}}">
							<?=Loc::getMessage('SBB_DELETE')?>
						</span>
                    </div>
                    {{/COUPON_LIST}}
                </div>
            </div>
            <?
        }
        ?>
        <div class="yw-basket-checkout">
	        {{#ITEMS_COUNT}}
	        <div class="basket-checkout-section">
				<div class="basket-checkout-section-inner">
					<div class="basket-checkout-block basket-checkout-block-items-count">
						<div class="yw-basket-checkout-row basket-checkout-block-items-count-inner">
							<div class="yw-basket-checkout-title"><?=Loc::getMessage('SBB_BASKET_QUANTITY')?> <?=Loc::getMessage('SBB_GOODS')?>:</div>
							<div class="yw-basket-checkout-value basket-checkout-block-items-count-value" data-entity="basket-items-count">{{{ITEMS_COUNT}}}</div>	
						</div>				
					</div>
				</div>
			</div>
	        {{/ITEMS_COUNT}}
	        {{#DISCOUNT_PRICE_FORMATED}}
			<div class="basket-checkout-section">
				<div class="basket-checkout-section-inner">			
					<div class="basket-checkout-block basket-checkout-block-total-price">
						<div class="basket-checkout-block-total-price-inner">						
								<div class="yw-basket-checkout-row basket-coupon-block-total-price">
									<div class="yw-basket-checkout-title"><?=Loc::getMessage('SBB_TOTAL')?></div>
									<div class="yw-basket-checkout-value basket-coupon-block-total-price-old">
										{{{PRICE_WITHOUT_DISCOUNT_FORMATED}}}
									</div>
								</div>							
						</div>
					</div>
				</div>
			</div>
			{{/DISCOUNT_PRICE_FORMATED}}

			<div class="basket-checkout-section">
				<div class="basket-checkout-section-inner">			
					<div class="basket-checkout-block basket-checkout-block-total-price">
						<div class="basket-checkout-block-total-price-inner">
							<div class="yw-basket-checkout-row basket-coupon-block-total-price">
								<div class="yw-basket-checkout-title">{{#DISCOUNT_PRICE_FORMATED}}<?=Loc::getMessage('SBB_TOTAL_DISCOUNT')?>{{/DISCOUNT_PRICE_FORMATED}}{{^DISCOUNT_PRICE_FORMATED}}<?=Loc::getMessage('SBB_TOTAL')?>{{/DISCOUNT_PRICE_FORMATED}}</div>
								<div class="yw-basket-checkout-value basket-coupon-block-total-price-current" data-entity="basket-total-price">
									{{{PRICE_FORMATED}}}
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

        <? if ($arParams['STATIC_BASKET'] !== 'Y') { ?>
            <div class="basket-checkout-block basket-checkout-block-btn">
                <a href="<?= $arParams['PATH_TO_ORDER'] ?>"
                   class="button secondary upper basket-btn-checkout{{#DISABLE_CHECKOUT}} disabled{{/DISABLE_CHECKOUT}}">
                    <?=Loc::getMessage('SBB_ORDER')?>
                </a>
            </div>
        <? } ?>
	</div>
</script>