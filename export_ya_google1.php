<?php
set_time_limit(0);
date_default_timezone_set('Europe/Moscow');
//echo $_SERVER['DOCUMENT_ROOT']; exit;
$_SERVER['DOCUMENT_ROOT'] = '/home/bitrix/www';
//error_reporting(-1);
//print '<hr><xmp>'.print_r($ArrColor, 1).'</xmp><hr>'; exit;

function YouExportGoogle ($fileExport, $description)
{
	$title_site = 'YOU WANNA - Trending Clothes for Women';
	$data_update = date('Y-m-dTH:i:00');

	$ArrCategory = array(
		2 => 'Предметы одежды и принадлежности > Одежда > Верхняя одежда',
		82 => 'Предметы одежды и принадлежности > Одежда > Верхняя одежда > Пальто и куртки',
		83 => 'Предметы одежды и принадлежности > Одежда > Верхняя одежда > Пальто и куртки',
		84 => 'Предметы одежды и принадлежности > Одежда > Верхняя одежда > Пальто и куртки',
		107 => 'Предметы одежды и принадлежности > Одежда > Верхняя одежда > Пальто и куртки',

		8 => 'Предметы одежды и принадлежности > Одежда > Выходные костюмы',
		90 => 'Предметы одежды и принадлежности > Одежда > Выходные костюмы',
		91 => 'Предметы одежды и принадлежности > Одежда > Выходные костюмы > Брючные костюмы',
		102 => 'Предметы одежды и принадлежности > Одежда > Выходные костюмы',
		104 => 'Предметы одежды и принадлежности > Одежда > Выходные костюмы',
		105 => 'Предметы одежды и принадлежности > Одежда > Выходные костюмы > Юбочные костюмы',
		106 => 'Предметы одежды и принадлежности > Одежда > Выходные костюмы',

		12 => 'Предметы одежды и принадлежности > Одежда > Платья',
		85 => 'Предметы одежды и принадлежности > Одежда > Платья',
		86 => 'Предметы одежды и принадлежности > Одежда > Платья',
		87 => 'Предметы одежды и принадлежности > Одежда > Платья',
		88 => 'Предметы одежды и принадлежности > Одежда > Платья',

		111 => 'Предметы одежды и принадлежности > Одежда > Платья > Рубашки и топы',
		112 => 'Предметы одежды и принадлежности > Одежда > Платья > Рубашки и топы',
		113 => 'Предметы одежды и принадлежности > Одежда > Платья > Рубашки и топы',
		115 => 'Предметы одежды и принадлежности > Одежда > Платья > Рубашки и топы',
		117 => 'Предметы одежды и принадлежности > Одежда > Платья > Рубашки и топы',

		97 => 'Предметы одежды и принадлежности > Одежда > Юбки',
		98 => 'Предметы одежды и принадлежности > Одежда > Штаны',
		99 => 'Предметы одежды и принадлежности > Одежда > Юбки',
		101 => 'Предметы одежды и принадлежности > Одежда > Юбки',

		118 => 'Предметы одежды и принадлежности > Принадлежности для одежды > Аксессуары для волос'
	);

	$ArrType = array(
		2 => 'Главная > Верхняя одежда',
		82 => 'Главная > Верхняя одежда > Пальто',
		83 => 'Главная > Верхняя одежда > Тренчи',
		84 => 'Главная > Верхняя одежда > Куртки',
		107 => 'Главная > Верхняя одежда > Пуховики',

		8 => 'Главная > Комплекты',
		90 => 'Главная > Комплекты > Костюмы',
		91 => 'Главная > Комплекты > Брючные костюмы',
		102 => 'Главная > Комплекты > Пиджаки',
		104 => 'Главная > Комплекты > Пижамные костюмы',
		105 => 'Главная > Комплекты > Костюмы с юбками',
		106 => 'Главная > Комплекты > Костюмы с шортами',

		12 => 'Главная > Платья',
		85 => 'Главная > Платья > Платья',
		86 => 'Главная > Платья > Туники',
		87 => 'Главная > Платья > Сорочки',
		88 => 'Главная > Платья > Платья - Рубашки',

		111 => 'Главная > Топы и блузы',
		112 => 'Главная > Топы и блузы > Топы',
		113 => 'Главная > Топы и блузы > Блузы',
		115 => 'Главная > Топы и блузы > туники',
		117 => 'Главная > Топы и блузы > рубашки',

		97 => 'Главная > Брюки и Юбки',
		98 => 'Главная > Брюки и Юбки > Юбки',
		99 => 'Главная > Брюки и Юбки > Брюки',
		101 => 'Главная > Брюки и Юбки > Кюлоты',

		118 => 'Главная > Аксессуары'
	);

	$content = '<?xml version="1.0"?>
<feed xmlns="http://www.w3.org/2005/Atom" xmlns:g="http://base.google.com/ns/1.0">
	<title>'.$title_site.'</title>
	<link rel="self" href="https://you-wanna.ru"/>
	<updated>'.$data_update.'</updated> ';

	if (!$xmlstr =  file_get_contents($fileExport))
	{
		//$msg = $pdate.'Нет файла выгрузки'."\n";
		//fwrite($f_log, $msg);
		return 0;
	}
	$xml = new SimpleXMLElement($xmlstr);

	// экспорт товаров
	$amount_goods = count($xml->shop->offers->offer);
	//echo 'товаров /'.$amount_goods.'/'; exit;

	$entry = '';
	for ($i=0; $i<$amount_goods; $i++)
	{
		$offer = $xml->shop->offers->offer[$i];
		echo "<pre>";
		print_r($offer);
		echo "</pre>";
		$param_dop = '';
		$amount_param = count($offer->param);
		for ($j=0; $j<$amount_param; $j++)
		{
			$param = $offer->param[$j];

			if ($param['name']=='Артикул') $param_dop .= '		<g:mpn>'.$param.'</g:mpn>';
			if ($param['name']=='Цвет') $param_dop .= '		<g:color>'.$param.'</g:color>';
			if ($param['name']=='Размер') $param_dop .= '		<g:size>'.$param.'</g:size>';
			//if ($param['name']=='Состав') $param_dop .= '		<g:mpn>'.$param.'</g:mpn>';
			//if ($param['name']=='Уход за изделием') $param_dop .= '		<g:mpn>'.$param.'</g:mpn>';
			//if ($param['name']=='Обмеры') $param_dop .= '		<g:mpn>'.$param.'</g:mpn>';
		}

		$entry .= '	<entry>
		<g:id>'.$offer['id'].'</g:id>
		<g:title>'.$offer->name.'</g:title>
		<g:description>'.$description.'</g:description>
		<g:link>'.$offer->url.'</g:link>
		<g:image_link>'.$offer->picture.'</g:image_link>
		<g:condition>new</g:condition>
		<g:availability>in stock</g:availability>
		<g:price>'.$offer->price.'.00 RUB</g:price>
'.$param_dop.'

		<g:google_product_category>'.$ArrCategory[strval($offer->categoryId)].'</g:google_product_category>
		<g:product_type>'.$ArrType[strval($offer->categoryId)].'</g:product_type>
	</entry>';
	}

	$content .= $entry.'</feed>';

	$fp = fopen($_SERVER['DOCUMENT_ROOT'].'/export_gl.xml', 'w');
	fwrite($fp, $content);
	fclose($fp);

	return $content;
}

function YouExportYandex ($fileExport, $description)
{
	$dop = '<delivery>true</delivery>
<pickup>true</pickup>
<delivery-options>
<option cost="0" days="1"/>
</delivery-options>
<store>true</store>';

	$content = file_get_contents($fileExport);
	$content = str_replace('windows-1251', 'UTF-8', $content);
	$content = iconv('cp1251', 'UTF-8', $content);
	$content = str_replace('</offer>', $dop."\n".'</offer>', $content);
	$content = str_replace('<description>', '<description>'.$description.' ', $content);

	$fp = fopen($_SERVER['DOCUMENT_ROOT'].'/export_ya.xml', 'w');
	fwrite($fp, $content);
	fclose($fp);

	return $content;
}

$mtime = explode(" ", microtime());
$time1 = doubleval($mtime[1]) + doubleval($mtime[0]);

$description = 'Одежда для современной городской женщины. Собраны все актуальные сезонные тренды. Коллекции создаются с учетом тенденций fashion-индустрии.';
$fileExport = 'https://you-wanna.ru/bitrix/catalog_export/yandex_239216.php';
$content = YouExportYandex($fileExport, $description);
$content = YouExportGoogle($fileExport, $description);

//header("Content-type: text/xml");
//echo $content;
echo 'Экспорт закончен!';

$mtime = explode(" ", microtime());
$time2 = doubleval($mtime[1]) + doubleval($mtime[0]);
//echo "<br>Прошло: ".sprintf("%.2f", abs($time2-$time1))." секунд.<br>\n";
?>
