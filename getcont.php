<?php
function curl_get_contents($page_url, $base_url, $pause_time, $retry){
    $error_page = array();
    $ch = curl_init();
	curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:38.0) Gecko/20100101 Firefox/38.0");
	curl_setopt($ch, CURLOPT_COOKIEJAR, str_replace("\\", "/", getcwd()).'/gearbest.txt');
	curl_setopt($ch, CURLOPT_COOKIEFILE, str_replace("\\", "/", getcwd()).'/gearbest.txt');
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); // Автоматом идём по редиректам
	curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0); // Не проверять SSL сертификат
	curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0); // Не проверять Host SSL сертификата
	curl_setopt($ch, CURLOPT_URL, $page_url); // Куда отправляем
	curl_setopt($ch, CURLOPT_REFERER, $base_url); // Откуда пришли
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // Возвращаем, но не выводим на экран результат
	$response['html'] = curl_exec($ch);
	$info = curl_getinfo($ch);
	if($info['http_code'] != 200 && $info['http_code'] != 404) {
        $error_page[] = array(1, $page_url, $info['http_code']);


        $response['code'] = $info['http_code'];
        $response['errors'] = $error_page;
        curl_close($ch);
        $response;
}
$page_url ="https://you-wanna.ru/kron.php";
$base_url ="https://you-wanna.ru/";
$pause_time ="2";
$retry ="1";
$ff = curl_get_contents($page_url, $base_url, $pause_time, $retry);
?>
