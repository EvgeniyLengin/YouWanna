<?php
const THIS_MODULE_ID = 'webfly.instagram';
use Bitrix\Main\Localization\Loc,
    Bitrix\Main\Config\Option,
    Bitrix\Main\Web\Json,
    Bitrix\Main\Loader,
    Bitrix\Main\Type\DateTime,
    Webfly\Instagram\InstagramTable,
    Webfly\Instagram\Request,
    Webfly\Instagram\File;

global $APPLICATION;
Loc::loadMessages(__FILE__);

class CWebflyInstagramRights{
    //1.4.5 function don't use
    /*public static function canRead(){
		global $APPLICATION;
		return ($APPLICATION->GetGroupRight(THIS_MODULE_ID) != "D" && !empty($APPLICATION->GetGroupRight(THIS_MODULE_ID)));
	}*/
	public static function canWrite(){
		global $APPLICATION;
		return ($APPLICATION->GetGroupRight(THIS_MODULE_ID) == "W" || $APPLICATION->GetGroupRight(THIS_MODULE_ID) == "X");
	}
}
class CWebflyInstagramHelper{
    const MODULE_ID = "webfly.instagram";
    const PREVIEW_SIZE = 70;
    const PREVIEW_SIZE_LARGE = 150;
    private $hashTagPropertyCode = null;
    private $hashTagProperty = null;
    private static $instance;

    public function __construct(){
        $this->hashTagPropertyCode = Option::get(self::MODULE_ID,"hashtag_prop_id");
    }

    public static function getInstance(){
		if (!isset(self::$instance)) {
			self::$instance = new CWebflyInstagramHelper();
		}
		return self::$instance;
	}
    /**
     * Gets hash tag property array
     * @return array
     */
    public function getHashTagProperty(){
        if(empty($this->hashTagProperty)){
            Loader::includeModule("iblock");
            $arPropExploded = explode("|",$this->hashTagPropertyCode);
            $arFilter = array("ID" => $arPropExploded[0], "VERSION" => $arPropExploded[1], "MULTIPLE" => $arPropExploded[2]);
            $this->hashTagProperty = CIBlockProperty::GetList(array("ID" => "ASC"),$arFilter)->Fetch();
            if(!empty($this->hashTagProperty)){
                $iblockInfo = CIBlock::GetByID($this->hashTagProperty["IBLOCK_ID"])->Fetch();
                $this->hashTagProperty["IBLOCK_ID"] = $iblockInfo["ID"];
                $this->hashTagProperty["IBLOCK_CODE"] = $iblockInfo["IBLOCK_TYPE_ID"];
                $this->hashTagProperty["IS_CATALOG"] = false;
                if(Loader::includeModule("catalog")){
                    if(CCatalog::GetByID($iblockInfo["ID"])){
                        $this->hashTagProperty["IS_CATALOG"] = true;
                    }
                }
            }
        }
        return $this->hashTagProperty;
    }
    /**
     * Gets resource of wares list
     * @param array $arrFilter
     * @return object
     */
    public function getDBWaresList($arrFilter = false, $arOrder = false){
        Loader::includeModule("iblock");
        $arProp = $this->getHashTagProperty();
        if(!$arProp) return false;
        else{
            $sPropCode = "PROPERTY_{$arProp["ID"]}";
            $arFilter = array("!$sPropCode" => false, "ACTIVE" => "Y", "IBLOCK_ID" => $arProp["IBLOCK_ID"]);
            if(!empty($arrFilter)) $arFilter = array_merge($arFilter,$arrFilter);
            if(!empty($arFilter[$sPropCode])) $arFilter[$sPropCode] .= "%";
            if(empty($arOrder)){
                $arOrder = array("ID" => "ASC");
            }
            $arSelect = array("ID","NAME","IBLOCK_ID","PREVIEW_PICTURE");
            if($arProp["MULTIPLE"] != "Y"){
                $arSelect[] = $sPropCode;
            }
            $dbWares = CIBlockElement::GetList($arOrder,$arFilter,false,false,$arSelect);
            return $dbWares;
        }
    }
    /**
     * Gets hash tags
     * @param array $arrFilter
     * @return array
     */
    public function getHashTags($arrFilter = false){
        Loader::includeModule("iblock");
        $arHashTags = array();
        $arProp = $this->getHashTagProperty();
        if($arProp){
            $sPropCode = "PROPERTY_{$arProp["ID"]}";
            $arFilter = array("!$sPropCode" => false, "ACTIVE" => "Y", "IBLOCK_ID" => $arProp["IBLOCK_ID"]);
            if(!empty($arrFilter)) $arFilter = array_merge($arFilter,$arrFilter);
            $arSelect = array("ID");
            if($arProp["MULTIPLE"] != "Y"){
                $arSelect[] = $sPropCode;
            }
            $dbWares = CIBlockElement::GetList(array("ID" => "ASC"),$arFilter,false,false,$arSelect);
            while($arWare = $dbWares->Fetch()){
                $arHashTags[$arWare["ID"]] = $arWare[$sPropCode."_VALUE"];
            }
        }
        return empty($arHashTags)?false:$arHashTags;
    }
    /**
     * Gets property value for ware
     * @param array $arWare
     * @return array
     */
    public function getHashTagPropertyValue($arWare){
        Loader::includeModule("iblock");
        $arProp = $this->getHashTagProperty();
        $arPropValue = array();
        $wareId = $arWare["ID"];
        $iblockId = $arWare["IBLOCK_ID"];
        $arOrder = array("ID" => "ASC");
        $arFilter = array("CODE" => $arProp["CODE"]);
        $dbPropValue = CIBlockElement::GetProperty($iblockId,$wareId,$arOrder,$arFilter);
        while($arValue = $dbPropValue->Fetch()){
            $arPropValue[] = $arValue["VALUE"];
        }
        return $arPropValue;
    }
    /**
     * Gets scaled preview of photo
     * @param int $iPicID
     * @return array
     */
    public function getWarePhotoPreview($iPicID){
        return CFile::ResizeImageGet($iPicID,array("width" => self::PREVIEW_SIZE, "height" => self::PREVIEW_SIZE),BX_RESIZE_IMAGE_PROPROTIONAL,true);
    }
    /**
     * Gets scaled preview of photo
     * @param int $iPicID
     * @return array
     */
    public function getPostPhotoPreview($iPicID){
        return CFile::ResizeImageGet($iPicID,array("width" => self::PREVIEW_SIZE_LARGE, "height" => self::PREVIEW_SIZE_LARGE),BX_RESIZE_IMAGE_PROPROTIONAL,true);
    }
    /** Deprecated
     * Get post ids
     * @param array $filter
     * @param array $order
     * @return array
     */
    public function getPostIds($filter = array(), $order = array("DATE" => "DESC")){
        $result = array();
        $select = array("POST_ID");
        if(!empty($filter["WARE"])) $select[] = "WARE";
        $arPostIds = InstagramTable::getList(array("select" => $select, "filter" => $filter, "order" => $order))->fetchAll();
        foreach($arPostIds as $item){
            $result[intval($item["WARE"])][] = $item["POST_ID"];
        }
        return $result;
    }
    public function getStoredPosts($limit = 30,$productId = ''){
        $result = [];
        $filter = ["WARE" => $productId];
        $select = ["ID","POST_ID"];
        $order = ["DATE" => "DESC"];
        $arPostIds = InstagramTable::getList(array("select" => $select, "filter" => $filter, "order" => $order,"limit"=>$limit))->fetchAll();
        return $arPostIds;
    }
    public function getUserInfo(){
        $jsonUserInfo = Request::getInstance()->getUserInfo();
        if(empty($jsonUserInfo)) return false;
        $arUserInfo = array("NAME" => $jsonUserInfo['user']['full_name'], "TEXT" => $jsonUserInfo['user']['biography']);
        $arUserInfo["PIC"] = $jsonUserInfo['user']['profile_pic_url'];
        $arUserInfo["PIC_HD"] = $jsonUserInfo['user']['profile_pic_url_hd'];
        $arUserInfo["FOLLOWERS_COUNT"] = $jsonUserInfo['user']['followed_by']['count'];
        $arUserInfo["URL"] = $jsonUserInfo['user']['external_url'];
        return $arUserInfo;
    }
}
class CWebflyInstagramTools{

    public function updateImages($pid = 0){
        try{
            $iTimeLimit = intval(Option::get(THIS_MODULE_ID,"php_time_limit",0));
            set_time_limit($iTimeLimit);
            $arPosts = array();
            $oRequest = Request::getInstance();
            $oFile = File::getInstance();
            $oHelper = CWebflyInstagramHelper::getInstance();
            $defaultActive = $oRequest->getDefaultActive();
            $arHashTags = $oHelper->getHashTags();


            if(!empty($arHashTags)){
                if ($pid > 0 && $arHashTags[$pid]) {
                    $arHashTags = [$pid => $arHashTags[$pid]];
                }

                $iAgentTagLimit = $oRequest->getTagsAgentLimit();
                $iPostLimit = $oRequest->getPostAgentLimit();
                $arAllPosts = [];
                $updated_count = 0;
                $processed_count = 0;
                foreach($arHashTags as $productId => $tag){
                    $storedPosts = $oHelper->getStoredPosts($iAgentTagLimit, $productId);
                    $arPostIds = [];
                    foreach ($storedPosts as $storedPost) {
                        $arPostIds[] = explode("_",$storedPost["POST_ID"])[0];
                    }
                    $arAllPosts[$productId] = [];
                        $arPosts = $oRequest->getPostsByTag($tag);

                        foreach($arPosts['nodes'] as $post){
                            $post['ware'] = $productId;
                            if(!in_array($post["pid"],$arPostIds)){
                                $arAllPosts[$productId][] = $post;
                            } else {
                                //$post["file"] = $oFile->save($post['pic_url']);
                                
                                InstagramTable::updateByPostId($post);
                                $updated_count++;
                            }
                            $processed_count++;
                            if ( $processed_count >= $iAgentTagLimit ) break;
                        }
                }

                $arDataToInsert = [];
                foreach($arAllPosts as $productId => $posts){
                    foreach($posts as $post){
                        $post['file'] = $oFile->save($post['pic_url']);
                        if(empty($post["caption"])){
                            $post["caption"] = " ";
                        }
                        if (Option::get(CWebflyInstagramHelper::MODULE_ID, "get_owner") == 'Y') {
                            $post['params']['user'] = $oRequest->getUserDataById($post["params"]["oid"]);
                        }
                        $post['params'] = Json::encode($post["params"]);
                        $post['active'] = $defaultActive;
                        $arDataToInsert[] = $post;
                    }
                }

                if(!empty($arDataToInsert)){
                    //$res = InstagramTable::massInsert($arDataToInsert);
                }
                $msg = Loc::getMessage("WII_ADDED_POSTS",array("#NUM#" => count($arDataToInsert)));
                $msg .= ' '.Loc::getMessage("WII_UPDATED_POSTS",array("#NUM#" => $updated_count));
                echo $msg;
            }
        }catch(Exception $e){
            File::getInstance()->log($e->getMessage());
            $_SESSION[THIS_MODULE_ID][__FUNCTION__] = Loc::getMessage("WII_ERROR",array("#ERROR#" => $e->getMessage()));
        }
        return __CLASS__."::".__FUNCTION__."({$pid});";
    }
    public function updateOne($pid){
        CWebflyInstagramTools::updateImages($pid);
    }
    public function getUserDataById($uid){
        $oRequest = Request::getInstance();
        $data = $oRequest->getUserDataById($uid);
        return $data;
    }
    public function updatePosts(){
        try{
            $iTimeLimit = intval(Option::get(THIS_MODULE_ID,"php_time_limit",0));
            set_time_limit($iTimeLimit);
            $arPosts = [];
            $oRequest = Request::getInstance();
            $defaultActive = $oRequest->getDefaultActive();
            if(!empty($oRequest->sUserName)){
                $oHelper = CWebflyInstagramHelper::getInstance();
                $oFile = File::getInstance();
                $iPostLimit = $oRequest->getPostAgentLimit();
                if ($iPostLimit > 30) $iPostLimit = 30;

                $storedPosts = $oHelper->getStoredPosts(100);
                $arPostIds = [];
                foreach ($storedPosts as $storedPost) {
                    $arPostIds[] = explode("_",$storedPost["POST_ID"])[0];
                }
                $sMaxId = '';
                $userID = $oRequest->getAccountID();

                $count = 5; // 1 step
                if ($count > $iPostLimit) $count = $iPostLimit;
                if ($count < 1) $count = 1;

                $variables = [
                    'id' => (string) $userID,
                    'first' => (string) $count
                ];
                /** LOOP first step get last post */

                $updated_count = 0;
                $processed_count = 0;
                do{
                    if ($sMaxId) {
                        $variables['after'] = $sMaxId;
                    }

                    $arImages = $oRequest->getPostsForUser($variables);

                    $sMaxId = $arImages["max_id"];



                    if(!empty($arImages['images'])){
                        $newImages = [];
                        foreach($arImages['images'] as $post){
                            $post['ware'] = 0;
                            if(!in_array($post["pid"],$arPostIds)){
                                $newImages[] = $post;
                            } else {
                                //$post["file"] = $oFile->save($post['pic_url']);
                                InstagramTable::updateByPostId($post);
                                $updated_count++;
                            }
                            $processed_count++;
                        }
                        $arPosts = array_merge($arPosts,$newImages);
                    } else {
                        throw new Exception(Loc::getMessage("WII_NO_PROCESSED_POST",array("#METHOD_NAME#" => __FUNCTION__)));
                    }
                }while(!empty($sMaxId) && ($processed_count < $iPostLimit));
                /** LOOP END */
                $arDataToInsert = array();
                foreach($arPosts as $post){
                    $post["file"] = $oFile->save($post['pic_url']);
                    if(empty($post["caption"])){
                        $post["caption"] = " ";
                    }
                    $post["active"] = $defaultActive;
                    $post['params'] = Json::encode($post["params"]);
                    $arDataToInsert[] = $post;
                }
                if(!empty($arDataToInsert)){
                    $res = InstagramTable::massInsert($arDataToInsert);
                }
                $_SESSION[THIS_MODULE_ID][__FUNCTION__] = Loc::getMessage("WII_ADDED_POSTS",array("#NUM#" => count($arDataToInsert)));
                $_SESSION[THIS_MODULE_ID][__FUNCTION__] .= ' '.Loc::getMessage("WII_UPDATED_POSTS",array("#NUM#" => $updated_count));
            }
        }catch(Exception $e){
            $_SESSION[THIS_MODULE_ID][__FUNCTION__] = Loc::getMessage("WII_ERROR",array("#ERROR#" => $e->getMessage()));
            File::getInstance()->log($e->getMessage());
        }
        return __CLASS__."::".__FUNCTION__."();";
    }

}