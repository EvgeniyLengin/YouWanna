<?php
/**
 * Вывод платежных систем
 *
 * @var array $arParams
 * @var array $arResult
 *
 */
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

$checkedPaySystem = false;
?>

<?php if (count($arResult['PAY_SYSTEM']) > 0) : ?>
    <div class="form__section">
        <div class="form__section-title">
            <?= GetMessage('SOA_TEMPL_PAY_SYSTEM') ?>
        </div>
        <div class="payment_note">
            * <?= GetMessage('PAYMENT_NOTE') ?>
        </div>
        <div class="form-item checkboxes-inline end pay-system-container">
            <?php foreach ((array)$arResult['PAY_SYSTEM'] as $paySystem) : ?>
                <?php $checked = '';

                if (!$checkedPaySystem) {
                    $checked = 'checked = "checked"';
                    $checkedPaySystem = true;
                } ?>
                <label>
                    <input type="radio" name="PAY_SYSTEM_ID"
                           data-is-cash="<?= $paySystem['IS_CASH'] ?>"
                           id="ID_PAY_SYSTEM_ID_<?= $paySystem['ID'] ?>"
                           value="<?= $paySystem['ID'] ?>" <?= $checked ?>>
                    <span class="pay-system-block">
                        <span class="pay-system-block-inner">
                            <img src="<?= $paySystem['PSA_LOGOTIP']['SRC'] ?>" alt="<?= $paySystem['NAME'] ?>">
                        </span>
                        <span class="pay-system-block-name">
                            <?= CYouWanna::multiTranslate($paySystem['NAME'], LANGUAGE_ID) ?>
                        </span>
                    </span>
                </label>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif;
