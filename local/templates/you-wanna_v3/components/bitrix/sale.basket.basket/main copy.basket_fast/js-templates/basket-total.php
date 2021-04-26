<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

/**
 * @var array $arParams
 */
?>
<script id="basket-total-template" type="text/html">
	<div class="basket-checkout-container" data-entity="basket-checkout-aligner">
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

			<div class="basket-checkout-section">
				<div class="basket-checkout-section-inner">			
					<div class="basket-checkout-block basket-checkout-block-total-price">
						<div class="basket-checkout-block-total-price-inner">
							<div class="yw-basket-checkout-row basket-coupon-block-total-price">
								<div class="yw-basket-checkout-title">	        {{#ITEMS_COUNT}}
	        <div class="yw-basket-checkout-value basket-checkout-block-items-count-value" data-entity="basket-items-count">{{{ITEMS_COUNT}}} товара</div>
	        {{/ITEMS_COUNT}} </div>
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