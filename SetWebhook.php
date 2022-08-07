<?php  require ("classes/HttpRequest.php");


/**
*	Во второй строчке указать Ваш токен бота телеграмм
*/
$api = "bot";
$api .=" "; //Replace with your bot's token.

/**
* 	Далее ни чего не менять. В строке браузера набрать : https:// ВАШ ДОМЕН/путь до данного файла/SetWebhook.php 
*	Перейти по набранному адресу. Если Всё впорядке Вы увидите ответ от телеграмм с данными в которых будет указана ссылка
*	https:// ВАШ ДОМЕН/путь до бота/webhook.php 
*
*
*
*
*/
$url = "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
$url = str_ireplace("SetWebhook.php", "webhook.php", $url);
$args = array(
  'url' => $url
);

$result = new HttpRequest("post", "https://api.telegram.org/$api/setWebhook", $args);

$result->getResponse();


echo "<pre>";	
print_r ($result);
echo "</pre>";	
?>
