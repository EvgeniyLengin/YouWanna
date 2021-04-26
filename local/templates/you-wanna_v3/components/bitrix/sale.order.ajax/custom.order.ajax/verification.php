<?php
/**
 * Вывод способов доставки
 *
 * @var array  $arParams
 * @var array  $arResult
 * @var string $templateFolder
 */
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Localization\Loc;

//$deliveryService = \Bitrix\Sale\Delivery\Services\Manager::getActiveList();
?>
<div class="form__section cls-order-box" data-step=2>
	
	<div class="form__section">
	    <div class="form__section-title">
	        <?= Loc::getMessage('SOA_TEMPL_SUM_COMMENTS') ?>
	    </div>
	    <div class="form-item end">
	        <textarea
	                class="textarea"
	                name="ORDER_DESCRIPTION"
	                id="ORDER_DESCRIPTION"
	                rows="3"
	                placeholder="<?= Loc::getMessage('COMMENT_TO_ORDER') ?>"><?=
	            $arResult['USER_VALS']['ORDER_DESCRIPTION'] ?></textarea>
	    </div>
	</div>
</div>