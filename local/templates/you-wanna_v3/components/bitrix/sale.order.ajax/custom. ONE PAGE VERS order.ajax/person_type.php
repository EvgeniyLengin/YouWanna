<?php
/**
 * Тип плательщика
 *
 * @var array $arParams
 * @var array $arResult
 */
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
} ?>
<?php if (count($arResult['PERSON_TYPE']) > 1) : ?>
    <div class="section" style="visibility:collapse;display: none;">
        <h4><?= GetMessage('SOA_TEMPL_PERSON_TYPE') ?></h4>
        <?php foreach ($arResult['PERSON_TYPE'] as $v): ?>
            <div class="label left">
                <input type="radio" id="PERSON_TYPE_<?= $v['ID'] ?>" name="PERSON_TYPE"
                       value="<?= $v['ID'] ?>"<?= ($v['CHECKED'] === 'Y') ? " checked=\"checked\"" : '' ?>
                       onclick="submitForm()">
                <label for="PERSON_TYPE_<?= $v['ID'] ?>">
                    <?= $v['NAME'] ?>
                </label>
                <br/>
            </div>
        <?php endforeach; ?>
        <div class="clear"></div>
        <input type="hidden" name="PERSON_TYPE_OLD" value="<?= $arResult['USER_VALS']['PERSON_TYPE_ID'] ?>"/>
    </div>
<?php elseif ((int)$arResult['USER_VALS']['PERSON_TYPE_ID'] > 0) : ?>
    <span style="display:none;">
		<label><input type="text" name="PERSON_TYPE"
                      value="<?= (int)$arResult['USER_VALS']['PERSON_TYPE_ID'] ?>"/></label>
		<label><input type="text" name="PERSON_TYPE_OLD" value="<?= (int)$arResult['USER_VALS']['PERSON_TYPE_ID'] ?>"/></label>
	</span>
<?php else : ?>
    <?php foreach ($arResult['PERSON_TYPE'] as $v) : ?>
        <input type="hidden" id="PERSON_TYPE" name="PERSON_TYPE" value="<?= $v['ID'] ?>"/>
        <input type="hidden" name="PERSON_TYPE_OLD" value="<?= $v['ID'] ?>"/>
    <?php endforeach; ?>
<?php endif;
