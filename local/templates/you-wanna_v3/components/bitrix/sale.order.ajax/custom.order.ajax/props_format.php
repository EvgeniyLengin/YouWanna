<?php
/**
 *
 */
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Localization\Loc;

?><?php
/*if (!function_exists('showFilePropertyField')) {
    function showFilePropertyField($name, $property_fields, $values, $max_file_size_show = 50000)
    {
        if ($property_fields['MULTIPLE'] === 'N') {
            $res = "<label for=\"\"><input type=\"file\" size=\"" . $max_file_size_show . "\" value=\""
                . $property_fields['VALUE'] . "\" name=\"" . $name . "[0]\" id=\"" . $name . "[0]\"></label>";
        } else {

            $res = '
<script type="text/javascript">
				function addControl(item)
				{
					var br;
					var br2;
					var current_name = item.id.split("[")[0],
						current_id = item.id.split("[")[1].replace("[", "").replace("]", ""),
						next_id = parseInt(current_id) + 1;

					var newInput = document.createElement("input");
					newInput.type = "file";
					newInput.name = current_name + "[" + next_id + "]";
					newInput.id = current_name + "[" + next_id + "]";
					newInput.onchange = function() { addControl(this); };

					 br = document.createElement("br");
					 br2 = document.createElement("br");

					BX(item.id).parentNode.appendChild(br);
					BX(item.id).parentNode.appendChild(br2);
					BX(item.id).parentNode.appendChild(newInput);
				}
			</script>
			';

            $res .= "<label for=\"\"><input type=\"file\" size=\"" . $max_file_size_show . "\" value=\"" . $property_fields['VALUE'] . "\" name=\"" . $name . "[0]\" id=\"" . $name . "[0]\"></label>";
            $res .= '<br/><br/>';
            $res .= "<label for=\"\"><input type=\"file\" size=\"" . $max_file_size_show . "\" value=\"" . $property_fields['VALUE'] . "\" name=\"" . $name . "[1]\" id=\"" . $name . "[1]\" onChange=\"javascript:addControl(this);\"></label>";
        }

        return $res;
    }
}*/


if (!function_exists('PrintPropsForm')) {
    /** @noinspection MoreThanThreeArgumentsInspection */
    /**
     * @param array  $arSource
     * @param string $locationTemplate
     * @param null   $hideParams
     * @param null   $homeIcon
     * @param array  $errors
     */
    function PrintPropsForm(
        array $arSource,
        $locationTemplate = '.default',
        $hideParams = null,
        $homeIcon = null,
        $errors = array()
    ) {

        # карта соответствия описания ошибки и символьного кода свойства заказа
        $errorsMap = array(
            Loc::getMessage('ERROR_TEXT_LOCATION')        => 'LOCATION',
            Loc::getMessage('ERROR_TEXT_ADDRESS')         => 'ADDRESS',
            Loc::getMessage('ERROR_TEXT_FIO')             => 'FIO',
            Loc::getMessage('ERROR_TEXT_EMAIL')           => 'EMAIL',
            Loc::getMessage('ERROR_TEXT_EMAIL_INCORRECT') => 'EMAIL_INCORRECT',
            Loc::getMessage('ERROR_TEXT_PHONE')           => 'PHONE',
            Loc::getMessage('ERROR_TEXT_SMS_ERROR')       => 'SMS_ERROR',
        );

        $errorsMap = array_flip($errorsMap);

        if (!empty($arSource)) { ?>
            <div class="js-check-sms-code-form">
                <?php foreach ($arSource as $arProperties) { ?>
                    <?php
                    $isError = false;
                    $isErrorSms = false;
                    $errorMessage = '';
                    $errorMessageSms = '';
                    $locError = false;
                    if (0 !== count($errors)) {
                        foreach ($errors as $error) {
                            if (0 === strpos($error, $arProperties['NAME'])) {
                                if ($arProperties['CODE'] === 'LOCATION'){
                                    $locError = true;
                                }
                                $isError = true;
                                $errorMessage = $errorsMap[$arProperties['CODE']];
                                break;
                            }/* else if ($error === 'SMS_ERROR' && $arProperties['CODE'] === 'PHONE') {
                                $isErrorSms = true;
                                $errorMessageSms = $errorsMap['SMS_ERROR'];
                                break;
                            }*/
                        }
                    }

                    //$isError = in_array($errorsMap[$arProperties['CODE']], $errors, false);
                    //$errorMessage = $errorsMap[$arProperties['CODE']];
                    if ($arProperties['REQUIRED'] === 'Y') {
                        $arProperties['NAME'] .= ' *';
                    }
					 // $arProperties['NAME'] = $arProperties['NAME'];
                   // $arProperties['NAME'] = CYouWanna::multiTranslate($arProperties['NAME'], LANGUAGE_ID);
					?>
                    <?php if (in_array($arProperties['CODE'], $hideParams, true)) : ?>
                        <input type="hidden"
                               value="<?= $arProperties['VALUE'] ?>"
                               name="<?= $arProperties['FIELD_NAME'] ?>"
                               id="<?= $arProperties['CODE'] ?>">
                        <?php continue; ?>
                    <?php endif; ?>
                        <?php if ($arProperties['CODE'] === 'DELIVERY_COST') : ?>
                            <?php continue; ?>
                        <?php endif; ?>

                    <div class="form-item<?/* if ($arProperties['CODE'] === 'PHONE') {?> row<?}*/ ?>"
                         data-property-id-row="<?= (int)((int)$arProperties['ID']) ?>">

                        <?php
                        if ($arProperties['TYPE'] === 'CHECKBOX') {
                            ?>
                            <input type="hidden"
                                   name="<?= $arProperties['FIELD_NAME'] ?>"
                                   value="">

                            <div class="bx_block r1x3 pt8">
                                <input type="checkbox"
                                       name="<?= $arProperties['FIELD_NAME'] ?>"
                                       id="<?= $arProperties['FIELD_NAME'] ?>"
                                       value="Y"<?php if ($arProperties['CHECKED'] === 'Y') {
                                    echo ' checked';
                                } ?>>
                                <span class="checkbox-name">
                                    <?= $arProperties['NAME'] ?>
                                    <?php if ($arProperties['REQUIED_FORMATED'] === 'Y') : ?>
                                        <span class="bx_sof_req">*</span>
                                    <?php endif; ?>
                                </span>
                                <? if (strlen(trim($arProperties['DESCRIPTION'])) > 0) { ?>
                                    <div class="bx_description">
                                        <?= $arProperties['DESCRIPTION'] ?>
                                    </div>
                                <? } ?>
                            </div>

                            <div style="clear: both;"></div>
                            <?php
                        } elseif ($arProperties['TYPE'] === 'TEXT') {
                            /*if (in_array($arProperties['CODE'], $homeIcon, true)) {
                                //skip
                            } else*/
                            if ($arProperties['CODE'] === 'FIO') {
                                //$arProperties['NAME'] = GetMessage('SOA_TEMPL_PROP_NAME');

                                # подставить текщее имя пользователя из профиля, если оно не было заполнено ранее
                                if (null === $arProperties['VALUE'] || '' === trim($arProperties['VALUE'])) {
                                    global $USER;
                                    $arProperties['VALUE'] = $USER->GetFullName();
                                }
                            } // Электронная почта BASKET MODIFY
                            // elseif ($arProperties['CODE'] === 'EMAIL') {
                            //     # подставить текущий email пользователя из профиля, если оно не было заполнено ранее
                            //     if (null === $arProperties['VALUE'] || '' === trim($arProperties['VALUE'])) {
                            //         global $USER;
                            //         $arProperties['VALUE'] = $USER->GetEmail();
                            //     }
                            //     if ((!empty($arProperties['VALUE'])) && (!filter_var($arProperties['VALUE'],
                            //             FILTER_VALIDATE_EMAIL))) {
                            //         $isError = 'Y';
                            //         $errorMessage = $errorsMap['EMAIL_INCORRECT'];
                            //     }
                            // }
                            ?>

                            <div class="form-item-inner<? /*global $USER; if ($arProperties['CODE'] === 'PHONE') {
                                ?> col col-6<?= !$USER->IsAuthorized() ? ' phone-input-wrapper' : '';
                            } */ ?>">
                                <label for=""><?= $arProperties['NAME']; ?><?php if($arProperties['NAME'] == "Email *") {echo " мы вышлем чек";} ?></label>
                                <input type="<?= $arProperties['CODE'] === 'PHONE' ? 'tel' : 'text'; ?>"
                                       value="<?= $arProperties['VALUE'] ?>"
                                       name="<?= $arProperties['FIELD_NAME'] ?>"
                                       id="<?= $arProperties['CODE'] ?>"
                                       class="width-100 <?= $isError ? 'error' : '' ?>"
                                       placeholder="<?= $arProperties['DESCRIPTION'] ?> "/>
                                <? if ($arProperties['CODE'] === 'EMAIL') { ?>
                                <div class="form-item form-item-inner">
                                    <div class="pt8">
                                        <input type="checkbox" name="pseudomail" id="pseudomail">
                                        <span class="checkbox-name"> я хочу подписаться на рассылку, чтобы первым получать новости о скидках и новых поступлениях </span>
                                    </div>
                                    <script>
                                        var PseudoCheck = document.getElementById('pseudomail');

                                        PseudoCheck.addEventListener('click', function (event) {
                                            document.getElementById('ORDER_PROP_10').click();
                                        });
                                    </script>
                                    <div style="clear: both;"></div>
                                </div>
                        <?}?>
                                <? /*global $USER; if ($arProperties['CODE'] === 'PHONE' && !$USER->IsAuthorized()) { ?>
                                        <div class="js-send-sms-code button secondary upper">
                                            <?= !empty($_SESSION['SALE_ACTIONS']['SMS_CODE'])
                                                ? Loc::getMessage('SEND_NEW_CODE') : Loc::getMessage('SEND_CODE') ?>
                                        </div>
                                    </div>
                                    <div class="form-item-inner<?= $arProperties['CODE'] === 'PHONE' ? ' col col-6' : '' ?>">
                                        <input type="text"
                                               value="<?= $_REQUEST['SMS_CODE'] ?>"
                                               name="SMS_CODE"
                                               id="SMS_CODE"
                                               class="width-100 <?= $isErrorSms ? 'error' : '' ?>"
                                               placeholder=" <?= Loc::getMessage('INPUT_SMS_CODE') ?>" />
                                        <div class="js-send-sms-code-text"
                                             style="display: none;"><?= Loc::getMessage('SEND_CODE_SUCCESS') ?></div>
                                <? }*/ ?>
                                <? if ($isError) { ?>
                                    <div class="error">
                                        <?= $errorMessage; ?>
                                    </div>
                                <? } /*else if ($isErrorSms) { ?>
                                    <div class="error">
                                        <?= $errorMessageSms; ?>
                                    </div>
                                <? }*/ ?>
                            </div>

                            <?php
                        } elseif ($arProperties['TYPE'] === 'SELECT') {
                            ?>
                            <br/>
                            <div class="bx_block r1x3 pt8">
                                <?= $arProperties['NAME'] ?>
                                <?php if ($arProperties['REQUIED_FORMATED'] === 'Y') : ?>
                                    <span class="bx_sof_req">*</span>
                                <?php endif; ?>
                            </div>

                            <div class="bx_block r3x1">
                                <select name="<?= $arProperties['FIELD_NAME'] ?>"
                                        id="<?= $arProperties['FIELD_NAME'] ?>"
                                        size="<?= $arProperties['SIZE1'] ?>">
                                    <?php
                                    foreach ($arProperties['VARIANTS'] as $arVariants):
                                        ?>
                                        <option
                                                value="<?= $arVariants['VALUE'] ?>"<?php if ($arVariants['SELECTED'] === 'Y') {
                                            echo ' selected';
                                        } ?>><?= $arVariants['NAME'] ?></option>
                                        <?php
                                    endforeach;
                                    ?>
                                </select>

                                <?php
                                if (strlen(trim($arProperties['DESCRIPTION'])) > 0):
                                    ?>
                                    <div class="bx_description">
                                        <?= $arProperties['DESCRIPTION'] ?>
                                    </div>
                                    <?php
                                endif;
                                ?>
                            </div>
                            <div style="clear: both;"></div>
                            <?php
                        } elseif ($arProperties['TYPE'] === 'MULTISELECT') {
                            ?>
                            <br/>
                            <div class="bx_block r1x3 pt8">
                                <?= $arProperties['NAME'] ?>
                                <?php if ($arProperties['REQUIED_FORMATED'] === 'Y') : ?>
                                    <span class="bx_sof_req">*</span>
                                <?php endif; ?>
                            </div>

                            <div class="bx_block r3x1">
                                <select multiple
                                        name="<?= $arProperties['FIELD_NAME'] ?>"
                                        id="<?= $arProperties['FIELD_NAME'] ?>"
                                        size="<?= $arProperties['SIZE1'] ?>">
                                    <?php
                                    foreach ($arProperties['VARIANTS'] as $arVariants):
                                        ?>
                                        <option
                                                value="<?= $arVariants['VALUE'] ?>"<?php if ($arVariants['SELECTED'] === 'Y') {
                                            echo ' selected';
                                        } ?>><?= $arVariants['NAME'] ?></option>
                                        <?php
                                    endforeach;
                                    ?>
                                </select>

                                <?php
                                if (strlen(trim($arProperties['DESCRIPTION'])) > 0):
                                    ?>
                                    <div class="bx_description">
                                        <?= $arProperties['DESCRIPTION'] ?>
                                    </div>
                                    <?php
                                endif;
                                ?>
                            </div>
                            <div style="clear: both;"></div>
                            <?php
                        } elseif ($arProperties['TYPE'] === 'TEXTAREA') {
                            $rows = ($arProperties['SIZE2'] > 10) ? 4 : $arProperties['SIZE2'];
                            ?>
                            <br/>
                            <div class="bx_block r1x3 pt8">
                                <?= $arProperties['NAME'] ?>
                                <?php if ($arProperties['REQUIED_FORMATED'] === 'Y') : ?>
                                    <span class="bx_sof_req">*</span>
                                <?php endif; ?>
                            </div>

                            <div class="bx_block r3x1">
                            <textarea rows="<?= $rows ?>"
                                      cols="<?= $arProperties['SIZE1'] ?>"
                                      name="<?= $arProperties['FIELD_NAME'] ?>"
                                      id="<?= $arProperties['FIELD_NAME'] ?>"><?= $arProperties['VALUE'] ?></textarea>

                                <?php
                                if (strlen(trim($arProperties['DESCRIPTION'])) > 0):
                                    ?>
                                    <div class="bx_description">
                                        <?= $arProperties['DESCRIPTION'] ?>
                                    </div>
                                    <?php
                                endif;
                                ?>
                            </div>
                            <div style="clear: both;"></div>
                            <?php
                        } elseif ($arProperties['TYPE'] === 'LOCATION') {
                            ?>
                            <? /*?><div class="bx_block r1x3 pt8">
                                <?= $arProperties['NAME'] ?>
                                <?php if ($arProperties['REQUIED_FORMATED'] === 'Y') : ?>
                                    <span class="bx_sof_req">*</span>
                                <?php endif; ?>
                            </div><?*/ ?>

                            <div class="bx_block r3x1">

                                <?php
                                $value = 0;
                                if (is_array($arProperties['VARIANTS']) && count($arProperties['VARIANTS']) > 0) {
                                    foreach ($arProperties['VARIANTS'] as $arVariant) {
                                        if ($arVariant['SELECTED'] === 'Y') {
                                            $value = $arVariant['ID'];
											$locationValue = $value;
                                            break;
                                        }
                                    }
                                }
                                $locationTemplateP = null;
                                // here we can get '' or 'popup'
                                // map them, if needed
                                if (CSaleLocation::isLocationProMigrated()) {
                                    $locationTemplateP = $locationTemplate === 'popup' ? 'search' : 'steps';
                                    $locationTemplateP = (int)$_REQUEST['PERMANENT_MODE_STEPS'] === 1 ? 'steps' : $locationTemplateP; // force to "steps"
                                }
                                ?>
                                <?php
                                if (strlen(trim($arProperties['DESCRIPTION'])) > 0):
                                    ?>
                                    <label for=""><?= $arProperties['DESCRIPTION'] ?></label>
                                    <?php
                                endif;
                                ?>
                                <?php if ($locationTemplateP === 'steps') : ?>
                                    <input type="hidden"
                                           id="LOCATION_ALT_PROP_DISPLAY_MANUAL[<?= (int)$arProperties['ID'] ?>]"
                                           name="LOCATION_ALT_PROP_DISPLAY_MANUAL[<?= (int)$arProperties['ID'] ?>]"
                                           value="<?= ($_REQUEST['LOCATION_ALT_PROP_DISPLAY_MANUAL'][(int)$arProperties['ID']] ? '1' : '0') ?>"/>
                                <?php endif ?>

                                <?php CSaleLocation::proxySaleAjaxLocationsComponent(array(
                                    'AJAX_CALL'          => 'N',
                                    'COUNTRY_INPUT_NAME' => 'COUNTRY',
                                    'REGION_INPUT_NAME'  => 'REGION',
                                    'CITY_INPUT_NAME'    => $arProperties['FIELD_NAME'],
                                    'CITY_OUT_LOCATION'  => 'Y',
                                    'LOCATION_VALUE'     => $value,
                                    'ORDER_PROPS_ID'     => $arProperties['ID'],
                                    'ONCITYCHANGE'       => ($arProperties['IS_LOCATION'] === 'Y' || $arProperties['IS_LOCATION4TAX'] === 'Y') ? 'submitForm()' : '',
                                    'SIZE1'              => $arProperties['SIZE1']
                                ),
                                    array(
                                        'ID'                       => $value,
                                        'CODE'                     => '',
                                        'SHOW_DEFAULT_LOCATIONS'   => 'Y',

                                        // function called on each location change caused by user or by program
                                        // it may be replaced with global component dispatch mechanism coming soon
                                        'JS_CALLBACK'              => 'submitFormProxy',

                                        // function window.BX.locationsDeferred['X'] will be created and lately called on each form re-draw.
                                        // it may be removed when sale.order.ajax will use real ajax form posting with BX.ProcessHTML() and other stuff instead of just simple iframe transfer
                                        'JS_CONTROL_DEFERRED_INIT' => (int)$arProperties['ID'],

                                        // an instance of this control will be placed to window.BX.locationSelectors['X'] and lately will be available from everywhere
                                        // it may be replaced with global component dispatch mechanism coming soon
                                        'JS_CONTROL_GLOBAL_ID'     => (int)$arProperties['ID'],

                                        'DISABLE_KEYBOARD_INPUT' => 'Y',
                                        'PRECACHE_LAST_LEVEL'    => 'Y',
                                        'PRESELECT_TREE_TRUNK'   => 'Y',
                                        'SUPPRESS_ERRORS'        => 'Y',
                                        'ERROR'                  => ($locError === true) ? $errorsMap['LOCATION'] : ''
                                    ),
                                    $locationTemplateP,
                                    true,
                                    'location-block-wrapper'
                                ) ?>



                            </div>
                            <div style="clear: both;"></div>
                            <?php
                        } elseif ($arProperties['TYPE'] === 'RADIO') {
                            ?>
                            <div class="bx_block r1x3 pt8">
                                <?= $arProperties['NAME'] ?>
                                <?php if ($arProperties['REQUIED_FORMATED'] === 'Y') : ?>
                                    <span class="bx_sof_req">*</span>
                                <?php endif; ?>
                            </div>

                            <div class="bx_block r3x1">
                                <?php
                                if (is_array($arProperties['VARIANTS'])) {
                                    foreach ($arProperties['VARIANTS'] as $arVariants):
                                        ?>
                                        <input
                                                type="radio"
                                                name="<?= /** @noinspection DisconnectedForeachInstructionInspection */
                                                $arProperties['FIELD_NAME'] ?>"
                                                id="<?= /** @noinspection DisconnectedForeachInstructionInspection */
                                                $arProperties['FIELD_NAME'] ?>_<?= $arVariants['VALUE'] ?>"
                                                value="<?= $arVariants['VALUE'] ?>" <?php if ($arVariants['CHECKED'] === 'Y') {
                                            echo ' checked';
                                        } ?> />

                                        <label for="<?= /** @noinspection DisconnectedForeachInstructionInspection */
                                        $arProperties['FIELD_NAME'] ?>_<?= $arVariants['VALUE'] ?>">
                                            <?= $arVariants['NAME'] ?>
                                        </label></br>
                                        <?php
                                    endforeach;
                                }
                                ?>

                                <?php
                                if (strlen(trim($arProperties['DESCRIPTION'])) > 0):
                                    ?>
                                    <div class="bx_description">
                                        <?= $arProperties['DESCRIPTION'] ?>
                                    </div>
                                    <?php
                                endif;
                                ?>
                            </div>
                            <div style="clear: both;"></div>
                            <?php
                        } elseif ($arProperties['TYPE'] === 'FILE') {
                            ?>
                            <br/>
                            <div class="bx_block r1x3 pt8">
                                <?= $arProperties['NAME'] ?>
                                <?php if ($arProperties['REQUIED_FORMATED'] === 'Y') : ?>
                                    <span class="bx_sof_req">*</span>
                                <?php endif; ?>
                            </div>

                            <div class="bx_block r3x1">
                                <?= showFilePropertyField('ORDER_PROP_' . $arProperties['ID'], $arProperties,
                                    $arProperties['VALUE'], $arProperties['SIZE1']) ?>

                                <?php
                                if (strlen(trim($arProperties['DESCRIPTION'])) > 0):
                                    ?>
                                    <div class="bx_description">
                                        <?= $arProperties['DESCRIPTION'] ?>
                                    </div>
                                    <?php
                                endif;
                                ?>
                            </div>

                            <div style="clear: both;"></div><br/>
                            <?php
                        }
                        ?>
                    </div>

                <?php if (CSaleLocation::isLocationProEnabled()) : ?>

                <?php
                $propertyAttributes = array(
                    'type'        => $arProperties['TYPE'],
                    'valueSource' => $arProperties['SOURCE'] === 'DEFAULT' ? 'default' : 'form'
                    // value taken from property DEFAULT_VALUE or it`s a user-typed value?
                );

                if ((int)$arProperties['IS_ALTERNATE_LOCATION_FOR']) {
                    $propertyAttributes['isAltLocationFor'] = (int)$arProperties['IS_ALTERNATE_LOCATION_FOR'];
                }

                if ((int)$arProperties['CAN_HAVE_ALTERNATE_LOCATION']) {
                    $propertyAttributes['altLocationPropId'] = (int)$arProperties['CAN_HAVE_ALTERNATE_LOCATION'];
                }

                if ($arProperties['IS_ZIP'] === 'Y') {
                    $propertyAttributes['isZip'] = true;
                }
                ?>

                    <script>

                        <?// add property info to have client-side control on it?>
                        (window.top.BX || BX).saleOrderAjax.addPropertyDesc(<?=CUtil::PhpToJSObject(array(
                            'id'         => (int)$arProperties['ID'],
                            'attributes' => $propertyAttributes
                        ))?>);

                    </script>
                <?php endif ?>

                    <?php
                }//endforeach
                ?>
            </div>
            <?php
        }
    }
}
