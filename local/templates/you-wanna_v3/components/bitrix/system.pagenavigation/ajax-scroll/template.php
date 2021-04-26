<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->createFrame()->begin("Загрузка навигации");
use Bitrix\Main\Application;
use Bitrix\Main\Web\Uri;
$request = Application::getInstance()->getContext()->getRequest();
$uriString = $request->getRequestUri();
?>
<?if($arResult["NavPageCount"] > 1):?>
<div class="b-pagination b-pagination--bottom" style="display: none">
	<div class="b-pagination__block text-center" data-ajax="btn-container">				
	    <?if ($arResult["NavPageNomer"]+1 <= $arResult["nEndPage"]):?>
	        <?
	            $plus = $arResult["NavPageNomer"]+1;
	            $url = $arResult["sUrlPathParams"] . "PAGEN_".$arResult["NavNum"]."=".$plus;
	        ?>
        <div class="btn-loadmore btn btn-rounded transition-all" data-url="<?=$url?>" data-ajax="btn">Показать еще</div>	        
	    <?endif?>
	</div>
</div>
<?endif?>