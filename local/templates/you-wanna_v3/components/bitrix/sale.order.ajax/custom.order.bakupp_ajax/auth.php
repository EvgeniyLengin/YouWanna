<?php
/**
 * Авторизация/регистрация перед оформлением заказа
 *
 * @var array $arParams
 * @var array $arResult
 * @var CMain $APPLICATION
 */
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Localization\Loc;

?>
    <div class="row">
        <div class="col-sm4 col-md-4 col-lg-4 col-xs-12">
            <?php if ($arResult['AUTH']['new_user_registration'] === 'Y') : ?>
                <p>
                    <?= Loc::getMessage('STOF_2REG'); ?>
                </p>
            <?php endif; ?>

            <form method="post" action="" name="order_auth_form">
                <?= bitrix_sessid_post(); ?>

                <?php foreach ($arResult['POST'] as $key => $value) : ?>
                    <input type="hidden" name="<?= $key ?>" value="<?= $value ?>"/>
                <?php endforeach; ?>

                <div class="form-group has-feedback">
                    <label for="USER_LOGIN" class="control-label">
                        <?= Loc::getMessage('STOF_LOGIN') ?><sup class="text-danger">*</sup>
                    </label>
                    <input type="text" class="form-control" value="<?= $arResult['AUTH']['USER_LOGIN'] ?>"
                           name="USER_LOGIN" id="USER_LOGIN"/>
                    <i class="fa fa-user form-control-feedback"></i>
                </div>

                <div class="form-group has-feedback">
                    <label for="USER_PASSWORD" class="control-label">
                        <?= Loc::getMessage('STOF_PASSWORD') ?><sup class="text-danger">*</sup>
                    </label>
                    <input type="password" class="form-control"
                           name="USER_PASSWORD" id="USER_PASSWORD"/>
                    <i class="fa fa-lock form-control-feedback"></i>
                </div>

                <p>
                    <a href="/<?= LANGUAGE_ID ?>/personal/forgot-password/">
                        <?= Loc::getMessage('STOF_FORGET_PASSWORD') ?>
                    </a>
                </p>

                <input type="submit" value="<?= Loc::getMessage('STOF_NEXT_STEP') ?>" class="btn btn-group btn-default">
                <input type="hidden" name="do_authorize" value="Y">
            </form>
        </div>

        <div
            class="col-sm-7 col-md-7 col-lg-7 col-xs-12 col-sm-offset-1 col-md-offset-1 col-lg-offset-1 col-xs-offset-0">
            <?php if ($arResult['AUTH']['new_user_registration'] === 'Y') : ?>
                <p>
                    <?= Loc::getMessage('STOF_2NEW') ?>
                </p>
            <?php endif; ?>

            <?php if ($arResult['AUTH']['new_user_registration'] === 'Y'): ?>
                <form method="post" action="" name="order_reg_form">
                    <?= bitrix_sessid_post(); ?>

                    <?php foreach ($arResult['POST'] as $key => $value) : ?>
                        <input type="hidden" name="<?= $key ?>" value="<?= $value ?>"/>
                    <?php endforeach; ?>

                    <div class="form-group has-feedback">
                        <label for="NEW_NAME" class="control-label">
                            <?= Loc::getMessage('STOF_NAME') ?><sup class="text-danger">*</sup>
                        </label>
                        <input type="text" class="form-control" name="NEW_NAME" id="NEW_NAME"
                               value="<?= $arResult['AUTH']['NEW_NAME'] ?>"/>
                        <i class="fa fa-user form-control-feedback"></i>
                    </div>

                    <div class="form-group has-feedback">
                        <label for="NEW_LAST_NAME" class="control-label">
                            <?= Loc::getMessage('STOF_LASTNAME') ?><sup class="text-danger">*</sup>
                        </label>
                        <input type="text" class="form-control" name="NEW_LAST_NAME" id="NEW_LAST_NAME"
                               value="<?= $arResult['AUTH']['NEW_LAST_NAME'] ?>"/>
                        <i class="fa fa-user form-control-feedback"></i>
                    </div>

                    <div class="form-group has-feedback">
                        <label for="NEW_EMAIL" class="control-label">
                            E-Mail <sup class="text-danger">*</sup>
                        </label>
                        <input type="text" class="form-control" name="NEW_EMAIL" id="NEW_EMAIL"
                               value="<?= $arResult['AUTH']['NEW_EMAIL'] ?>"/>
                        <i class="fa fa-envelope form-control-feedback"></i>
                    </div>

                    <?php if (!$arParams['EMAIL_AS_LOGIN']): ?>
                        <div class="form-group has-feedback">
                            <label for="NEW_LOGIN" class="control-label">
                                <?= Loc::getMessage('STOF_LOGIN') ?> <sup class="text-danger">*</sup>
                            </label>
                            <input type="text" class="form-control" name="NEW_LOGIN" id="NEW_LOGIN"
                                   value="<?= $arResult['AUTH']['NEW_LOGIN'] ?>"/>
                            <i class="fa fa-user form-control-feedback"></i>
                        </div>
                    <?php else: ?>
                        <input type="hidden" class="form-control" name="NEW_LOGIN"
                               value="<?= $arResult['AUTH']['NEW_LOGIN'] ?>"/>
                    <?php endif ?>

                    <div class="form-group has-feedback">
                        <label for="NEW_PASSWORD" class="control-label">
                            <?= Loc::getMessage('STOF_PASSWORD') ?> <sup class="text-danger">*</sup>
                        </label>
                        <input type="password" class="form-control" name="NEW_PASSWORD" id="NEW_PASSWORD"/>
                        <i class="fa fa-lock form-control-feedback"></i>
                    </div>

                    <div class="form-group has-feedback">
                        <label for="NEW_PASSWORD_CONFIRM" class="control-label">
                            <?= Loc::getMessage('STOF_RE_PASSWORD') ?> <sup class="text-danger">*</sup>
                        </label>
                        <input type="password" class="form-control" name="NEW_PASSWORD_CONFIRM"
                               id="NEW_PASSWORD_CONFIRM"/>
                        <i class="fa fa-lock form-control-feedback"></i>
                    </div>

                    <?php if ($arResult['AUTH']['captcha_registration'] === 'Y') : ?>
                        <input type="hidden" name="captcha_sid" value="<?= $arResult['AUTH']['capCode'] ?>">

                        <div class="form-group has-feedback">
                            <label for="captcha_word" class="control-label">
                                <?= Loc::getMessage('CAPTCHA_REGF_PROMT') ?><sup class="text-danger">*</sup>
                            </label>
                            <input type="text" class="form-control" name="captcha_word" id="captcha_word"
                                   autocomplete="off"/>
                            <i class="fa fa-font form-control-feedback"></i>
                        </div>

                        <div class="form-group has-feedback">
                            <img src="/bitrix/tools/captcha.php?captcha_sid=<?= $arResult['AUTH']['capCode'] ?>"
                                 width="180" height="40" alt="CAPTCHA">
                        </div>
                    <?php endif; ?>

                    <input type="submit" value="<?= Loc::getMessage('STOF_NEXT_STEP') ?>"
                           class="btn btn-group btn-default">
                    <input type="hidden" name="do_register" value="Y">
                </form>
            <?php endif; ?>
        </div>
    </div>

<?= Loc::getMessage('STOF_REQUIED_FIELDS_NOTE') ?><br/><br/>
<?php if ($arResult['AUTH']['new_user_registration'] === 'Y'): ?>
    <?= Loc::getMessage('STOF_EMAIL_NOTE'); ?><br/><br/>
<?php endif; ?>
<?= Loc::getMessage('STOF_PRIVATE_NOTES');
