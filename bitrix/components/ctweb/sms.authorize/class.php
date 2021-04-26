<?

use Bitrix\Main;
use Bitrix\Main\Loader;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Localization\Loc;
use Ctweb\SMSAuth\Module;
use Ctweb\SMSAuth\Manager;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

Loc::loadMessages(__FILE__);

if (!Loader::includeModule("ctweb.smsauth")) {
    ShowError(Loc::getMessage("SOA_MODULE_NOT_INSTALL"));
    return;
}

class CtwebSMSAuthComponent extends \CBitrixComponent
{
    const ERROR_CODE_EMPTY = 'CODE_EMPTY';
    const ERROR_CODE_NOT_CORRECT = 'CODE_NOT_CORRECT';
    const ERROR_TIME_EXPIRED = 'TIME_EXPIRED';
    const ERROR_USER_NOT_FOUND = 'USER_NOT_FOUND';
    const ERROR_USER_NOT_CHOOSED = 'USER_NOT_CHOOSED';
    const ERROR_CAPTCHA_WRONG = 'CAPTCHA_WRONG';
    const ERROR_UNKNOWN_ERROR = 'UNKNOWN_ERROR';

    const RESULT_SUCCESS = 'SUCCESS';
    const RESULT_FAILED = 'FAILED';

    protected $manager;
    protected $moduleOptions;

    protected function getDefaultComponentParams()
    {
        return array(
            'REDIRECT_TIME' => -1,
            'REDIRECT_URL' => '/',
            'ALLOW_MULTIPLE_USERS' => 'N',
        );
    }

    public function onPrepareComponentParams($arParams)
    {
        global $APPLICATION;

        $this->manager = new Manager;
        $this->moduleOptions = Module::getOptions();
        $arParams = array_merge($this->getDefaultComponentParams(), $arParams);

        $this->arResult = array(
            'USER_VALUES' => array(),
            'ERRORS' => array(),
            'USE_CAPTCHA' => (Option::get("main", "captcha_registration", "N") == "Y"? "Y" : "N"),
            'FORM_ID' =>$this->getEditAreaId('form')
        );
        if ($this->arResult["USE_CAPTCHA"] == "Y")
            $this->arResult["CAPTCHA_CODE"] = htmlspecialcharsbx($APPLICATION->CaptchaGetCode());

        return $arParams;
    }

    protected function setSessionField($key, $value)
    {
        $_SESSION[self::class][$key] = $value;
    }

    protected function getSessionField($key)
    {
        return $_SESSION[self::class][$key];
    }

    protected function clearSession()
    {
        unset($_SESSION[self::class]);
    }

    private function isPost() {
        return $this->request->isPost() && check_bitrix_sessid() && $this->request->get('FORM_ID') === $this->arResult['FORM_ID'];
    }

    private function actionStepUserWaiting() {
        global $APPLICATION;
        $arUsers = $this->manager->GetUsersByPhone($this->getSessionField('PHONE'));
        $this->arResult['USER_LIST'] = $arUsers;
        if ($this->isPost()) {
            if ($arUsers) {
                if (strlen($this->request->get('USER_ID'))) {
                    if (($chosedUser = intval($this->request->get('USER_ID')))) {
                        $arUser = reset(array_filter($arUsers, function ($e) use ($chosedUser) {
                            return $e['ID'] == $chosedUser;
                        }));
                        if ($arUser['ID']) {
                            $res = $this->manager->StartUserAuth($arUser['ID']);
                            if (!$res) {
                                $this->arResult['ERRORS'][] = self::ERROR_UNKNOWN_ERROR;
                            }
                        }
                    }
                } else {
                    $this->arResult['ERRORS'][] = self::ERROR_USER_NOT_CHOOSED;
                }
            } else {
                $this->arResult['ERRORS'][] = self::ERROR_USER_NOT_FOUND;

            }
            $this->setSessionField('ERRORS', $this->arResult['ERRORS']);
            LocalRedirect($APPLICATION->GetCurPageParam());
        }
    }
    private function actionStepCodeWaiting() {
        global $APPLICATION;
        if ($this->manager->isTimeExpired()) {
            $this->arResult['ERRORS'][] = self::ERROR_TIME_EXPIRED;
            $this->clearSession();
            $this->manager->clearSession();
        } else {
            if ($this->isPost()) {
                if (strlen($this->request->get('CODE'))) {
                    if (!$this->manager->AuthByCode($this->request->get('CODE'), $this->getSessionField('SAVE_SESSION'))) {
                        $this->arResult['AUTH_RESULT'] = self::RESULT_FAILED;
                        $this->arResult['ERRORS'][] = self::ERROR_CODE_NOT_CORRECT;
                    }
                } else {
                    $this->arResult['ERRORS'][] = self::ERROR_CODE_EMPTY;
                }
                $this->setSessionField('ERRORS', $this->arResult['ERRORS']);
                LocalRedirect($APPLICATION->GetCurPageParam());
            }
        }
    }
    private function actionStepPhoneWaiting() {
        global $APPLICATION;
        if ($this->isPost()) {
            // check captcha
            if ($this->arResult["USE_CAPTCHA"] == "Y")
                if (!$APPLICATION->CaptchaCheckCode($this->request->get("captcha_word"), $this->request->get("captcha_sid")))
                    $this->arResult['ERRORS'][] = self::ERROR_CAPTCHA_WRONG;

            if (strlen($this->request->get('PHONE')))
                $this->setSessionField('PHONE', $this->request->get('PHONE'));

            if (strlen($this->request->get('SAVE_SESSION')))
                $this->setSessionField('SAVE_SESSION', $this->request->get('SAVE_SESSION'));

            if (empty($this->arResult['ERRORS'])) {
                if ($this->getSessionField('PHONE')) {
                    $arUsers = $this->manager->GetUsersByPhone($this->getSessionField('PHONE'));
                    if ($arUsers) {
                        if ($this->arParams['ALLOW_MULTIPLE_USERS'] === 'Y' && count($arUsers) > 1) {
                            $this->manager->setStep(Manager::STEP_USER_WAITING);
                        } else {
                            $arUser = reset($arUsers);

                            if ($arUser['ID']) {
                                $res = $this->manager->StartUserAuth($arUser['ID']);
                                if (!$res) {
                                    $this->arResult['ERRORS'][] = self::ERROR_UNKNOWN_ERROR;
                                }
                            }
                        }
                    } else {
                        // Not found

                        $this->arResult['ERRORS'][] = self::ERROR_USER_NOT_FOUND;
                    }
                } else {
                    $this->clearSession();
                }
            }

            $this->setSessionField('ERRORS', $this->arResult['ERRORS']);
            LocalRedirect($APPLICATION->GetCurPageParam());
        }
    }
    private function actionStepSuccess() {
        $this->arResult['AUTH_RESULT'] = self::RESULT_SUCCESS;
        $this->clearSession();
        $this->manager->clearSession();
    }

    public function executeComponent()
    {
        global $APPLICATION, $USER;

        $this->setFrameMode(false);
        $this->context = Main\Application::getInstance()->getContext();
        $this->isRequestViaAjax = $this->isPost() && $this->request->get('via_ajax') == 'Y';
        $isAjaxRequest = $this->request["is_ajax_post"] == "Y";

        if ($this->isPost() && $this->request->get('RESET')) {
            $this->manager->clearSession();
            LocalRedirect($APPLICATION->GetCurPageParam());
        }

        if (!$USER->isAuthorized()) {
            switch ($this->manager->getStep()) {
                case Manager::STEP_SUCCESS : // all ok, redirect waiting
                    $this->actionStepSuccess();
                    break;

                case Manager::STEP_USER_WAITING :
                    $this->actionStepUserWaiting();
                    break;

                case Manager::STEP_CODE_WAITING : // user found, code waiting for auth
                    $this->actionStepCodeWaiting();
                    break;

                case Manager::STEP_PHONE_WAITING: // no action, phone waiting
                default: // no action, phone waiting
                    $this->actionStepPhoneWaiting();
            }

            $this->arResult['ERRORS'] = $this->getSessionField('ERRORS');
            $this->arResult['USER_VALUES']['SAVE_SESSION'] = $this->getSessionField('SAVE_SESSION');
            $this->arResult['USER_VALUES']['PHONE'] = $this->getSessionField('PHONE');
            $this->arResult['EXPIRE_TIME'] = $this->manager->getExpireTime();
            ($this->arResult['STEP'] = $this->manager->getStep()) || ($this->arResult['STEP'] = Manager::STEP_PHONE_WAITING);
        } else {
            $this->arResult['AUTH_RESULT'] = self::RESULT_SUCCESS;
            $this->clearSession();
        }

        if ($isAjaxRequest)
            $APPLICATION->RestartBuffer();


//        if (!$isAjaxRequest)
//        {
//            CJSCore::Init(array('fx', 'popup', 'window', 'ajax', 'date'));
//        }

        $this->includeComponentTemplate();

        if ($isAjaxRequest) {
            $APPLICATION->FinalActions();
            die();
        }
    }
}