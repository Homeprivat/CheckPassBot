<?php //header('Content-type: text/html; charset=utf-8');
set_error_handler('err_handler');
function err_handler($errno, $errmsg, $filename, $linenum) {
$date = date('Y-m-d H:i:s (T)');
$f = fopen('errors_bot.txt', 'a');
if (!empty($f)) {
$filename  =str_replace($_SERVER['DOCUMENT_ROOT'],'',$filename);
$err  = "$date = $errmsg = $filename = $linenum\r\n";
fwrite($f, $err);
fclose($f);
}
}

require_once 'classes/PDO.php';					// подключаем базу данных
require_once 'classes/function.php'; 			// функция логирования
require_once 'classes/getParserResponse.php';	// основная контроллер команд
require_once 'classes/FMSOut.php'; 	 			// контроллер проверки паспортов
require_once 'classes/getWebHook.php';					// Класс расшифровки ответа от телеграмм 


		/**
	* Обязательные параметры
	**/
	$botToken  = " "; // токен бота телеграмм
	$maxCheckPass = '10';  		// Максимально допустимое кол-во документов для проверки
	$timeOutCheck = '180'; 		// Временной тайм-аут между проверками
	$adminId = ;		//1618426159 -> id @kravchukk
	
	/**
	*
	*Начало работы бота
	*Принимаем данные от Тегерамм в виде WebHook
	*Настроку WebHook производить через файл SetWebHook указав там данные: токен бота телеграмм.
	**/
	$WebHook= new getWebHook();
	$action = $WebHook->init('php://input');
		
		
		
	//**********************************************************************************
	//****************** Задаём переменные данным от Телеграмм     *********************
	//**********************************************************************************

	$message	= $action["message"];
	$chat_id	= $action["chat_id"];
	
	if($action["replyid"]){$reply_id	= $action["replyid"];}
	if($action["data"]){$data = $action["data"];}
	if($action["call_id"]){$call_id	= $action["call_id"];}
	

	//**********************************************************************************
	//*********************** Начальные функции бота    ********************************
	//**********************************************************************************


	/**
	*	Собираем все данные по юзеру из базы данных
	**/
	$user = DB::$the->query("SELECT * FROM `tlg_passCheck_user` WHERE `chat_id` = ".$chat_id."");
	$user = $user->fetch(PDO::FETCH_ASSOC);

	
	/**
	*	Объявляем класс основных команд бота
	**/
	$ParserResponse = new getParserResponse();	

	
	/**
	* 	Команда отправки ответа в телеграмм
	**/
	$SenDTo = $ParserResponse->SendTo($botToken, $chat_id);

	
	/**
	*	Создаем имя пользователя для приветствия
	**/
	$ParserResponse->persona($chat_id);

	
	/**
	* 	Подсчитываем временной промежуток
	**/
	$start		= $user['startcheck'];
	$timeout 	= ($start + $timeOutCheck);
	
	
	/**
	* 	Подсчитываем кол-во проверенных документов юзера
	**/
	$LastCountCcheck = $user['count_check'];
	$Admine = $ParserResponse->isAdmine();
	$commands = $user['commands']; 
	//**********************************************************************************
	//*********************** Обратная связь на  отзыв  ********************************
	//**********************************************************************************
	
	
	/**
	* 	Задаем переменную статусу
	*/
	$status = $user['status']; 
	
	/**
	*	Получаем данные с callback_query непосредственно с нажатой кнопки
	* 	придёт: reply- - префикс
	*			id_user - кому будет передан ответ. Получено значение с функции msgfeedBackAdmin
	*
	*/
	$datain = explode("-", $data);
	
	if ($datain[0] == "reply" AND $status == NULL AND $chat_id == $adminId)
	{
		DB::$the->prepare("UPDATE tlg_passCheck_user SET status=? WHERE chat_id=? ")->execute(array("reply-". $datain[1]."",  $chat_id));
		$messageText = "*Написать ответ на отзыв:*";
		return $SenDTo->sendMessageAdmin($messageText);
		exit('ok');
	}
	
	/**
	*	Обрезаем данные с базы данных (статус) и считываем значение кому отвечать
	*/
	$sexploded = explode("-", $status);
	
	if($sexploded[0] == "reply" AND $chat_id == $adminId)
	{
		$chatuser = $sexploded[1];
	
		DB::$the->prepare("UPDATE tlg_passCheck_user SET status=? WHERE chat_id=? ")->execute(array(NULL,  $chat_id));
		$SenDTo->sendMessageCallBackUser($chatuser, $message);
		exit('ok');
	} 
	
	/**
	* 	После нажатия кнопки обратная связь! Пользователь получает сообщение msgFeedback
	*	После отправки отзыва присваивается статус feedback 
	*	По итогу отправка сообщений: 
	*			-- Юзеру с благодарностью
	*			-- С описанием отзыва и кнопкой Repley для ответа
	*/
	if($user['status'] == "feedback"){

		if($message == "Отмена ❌")
		{
			DB::$the->prepare("UPDATE tlg_passCheck_user SET status=? WHERE chat_id=? ")->execute(array(NULL,  $chat_id));		
			$messageText = "отменено ❌";
			$buttonKeyboard[] = array("❔ Справка", "👉 Проверить паспорт");	
			$buttonKeyboard[] = array("Обратная связь 📫");	
			return $SenDTo->sendMessageUser($messageText, $buttonKeyboard);

		}else{
			$name = $ParserResponse->persona($chat_id);
			return $ParserResponse->msgfeedBackAdmin($call_id, $message, $name, $username, $id_user, $adminId);
			
		}
		exit('ok');
	}
	
	//**********************************************************************************
	//**********************************************************************************
	//**********************************************************************************
	
	
	
	//******************   Для административной группы функции *************************
	//**********************************************************************************
	
	
	/**
	*	Работа для админ группы
	**/
	if($Admine == 'yes')
	{	
		if($commands == 'check')
		{
			if($message == "Отмена ❌" or $message == "🔙 Назад")
			{
				DB::$the->prepare("UPDATE tlg_passCheck_user SET commands=? WHERE chat_id=? ")->execute(array(NULL, $chat_id));
				
				$messageText 	 	= "отменено ❌";
				$buttonKeyboard[] 	= array("❔ Справка", "👉 Проверить паспорт");	
				$buttonKeyboard[] 	= array("Обратная связь 📫");	
				
				$SenDTo->sendMessageUser($messageText, $buttonKeyboard);
				exit;
			}

			$CheckPassMax = '20';  // Максимально допустимое кол-во документов для проверки
			DB::$the->prepare("UPDATE tlg_passCheck_user SET commands=? WHERE chat_id=? ")->execute(array(NULL,  $chat_id));
			$CheckPassisAdmine = new FMSOut($message, $botToken, $chat_id);		
			// Подсчитываем кол=во документов
			$CountPassAdmin = $CheckPassisAdmine->isCountPass();			
			
			
			/**
			* Проверяем не привышекно ли кол-во документов к проверке
			* если нет, то
			*/
			if($CountPassAdmin <= $CheckPassMax)
			{
				DB::$the->prepare("UPDATE tlg_passCheck_user SET commands=? WHERE chat_id=? ")->execute(array(NULL,  $chat_id));

				//прибавляем кол-во документов с нового запроса 
				$CountCcheckAdmine = $LastCountCcheck + $CountPassAdmin;
				
				//	Обноывляем данные о кол-ве проверенных паспортов
				DB::$the->prepare("UPDATE tlg_passCheck_user SET  count_check=? WHERE chat_id=? ")->execute(array($CountCcheckAdmine, $chat_id));	
				//	статус пользователя делвем 1
				
				//	Задаем время старта проверки паспортов				
				DB::$the->prepare("UPDATE tlg_passCheck_user SET startcheck=? WHERE chat_id=? ")->execute(array(time(), $chat_id));	
				
				// Клавиатура бота				
				
				// Тело сообщения для отправки				
				$messageTextAdmine = "Административная группа
				📋 На проверку предоставлено документов: <b>".$CountPassAdmin."</b> .\xE2\x8F\xB3 Подождите немного.\xE2\x8F\xB3";
				
				// отправляем в телеграмм ответ				
				$ToisAdmine = $SenDTo->sendMessageUser($messageTextAdmine, 	$remove_keyboard = false);
				$SenDTo->ChatAction();
				
				/**
				*	ЕСЛИ СООБЩЕНИЕ О КОЛ-ВО ДОКУМЕНТОВ ДОСТАВЛЕНО В ТЕЛЕГРАММ ТО ПРОВЕРЯЕМ ДАЛЬШЕ
				*/
				if($ToisAdmine['ok'] == 1)
				{
					
					// Создаем массив массивов содержащий каждый документ по отдельности
					$PassInfoArryAdmine = $CheckPassisAdmine->PassinfoArr();
					
					// 	Разбываем каждый массив на части: серия и номер паспорта
					$arrPassInAdmine = $CheckPassisAdmine->getPassCheckInfo();
			
					// 
					$SenDTo->ChatAction();	
					// Делаем непосредственно проверку паспортов и отсылаем пользователю данные о проведенноё проверке
					$TextisAdmine = $CheckPassisAdmine->isCheckPass($CountPassAdmin, $arrPassInAdmine);
				
					
				exit;
			
				}
				
			exit;
		
			}
		exit;
		}
	
	}


	
	//***********************************   Для ЮЗЕРОВ функции *************************
	//**********************************************************************************
	/**
	*	Проверяем была ли принята команда ожидания данных для проверки и записана в базу данных
	*
	**/
	if($commands == 'check')
	{	
	
		/**
		*	Проверяем временной промежуток. Если времени прошло меньше чем разрешено, то
		*	юзеру отсылаем отказ
		**/
		if($timeout > time())
		{
			//	Обнуляем команду проверки данных
			DB::$the->prepare("UPDATE tlg_passCheck_user SET commands=? WHERE chat_id=? ")->execute(array(NULL,  $chat_id));		
			// 	Высчитываем минуты из секунд
			$minuyes = $timeOutCheck/60;	
			// Тело сообщения для отправки
			$messageText = "Простите, но проверку можно совершать 1 раз в ".$minuyes." минут в кол-ве ".$maxCheckPass." пасспортов за запрос. Данные ограничения введены исключительно в целях технической безопасности сервера.";
			// Клавиатура бота
			$buttonKeyboard2[] = array("❔ Справка", "👉 Проверить паспорт");	
			// отправляем в телеграмм ответ			
			$SenDTo->sendMessageUser($messageText, $buttonKeyboard2);
			
			exit;
	
		}

		/**
		* Проверяем  прошло ли времени  больше чем таймаут
		*/
		if($timeout < time())
		{
			
			if($message == "Отмена ❌" or $message == "🔙 Назад")
			{
				DB::$the->prepare("UPDATE tlg_passCheck_user SET commands=? WHERE chat_id=? ")->execute(array(NULL,  $chat_id));
				$messageText = "отменено ❌";
				$buttonKeyboard[] = array("❔ Справка", "👉 Проверить паспорт");	
				$buttonKeyboard[] = array("Обратная связь 📫");	
				$SenDTo->sendMessageUser($messageText, $buttonKeyboard);
				exit;
			}
			else
			{			
				//	Обнуляем команду проверки данных
				DB::$the->prepare("UPDATE tlg_passCheck_user SET commands=? WHERE chat_id=? ")->execute(array(NULL,  $chat_id));
				// Обяъвляем класс проверки данных
				$CheckPass = new FMSOut($message, $botToken, $chat_id);		
				// Подсчитываем кол=во документов
				$CountPass = $CheckPass->isCountPass();						

				/**
				* Проверяем не привышекно ли кол-во документов к проверке
				* если нет, то
				*/
				if($CountPass <= $maxCheckPass)
				{
					//прибавляем кол-во документов с нового запроса 
					$CountCcheck = $LastCountCcheck + $CountPass;
					//	Обноывляем данные о кол-ве проверенных паспортов
					DB::$the->prepare("UPDATE tlg_passCheck_user SET  count_check=? WHERE chat_id=? ")->execute(array($CountCcheck, $chat_id));	
					//	Задаем время старта проверки паспортов				
					DB::$the->prepare("UPDATE tlg_passCheck_user SET startcheck=? WHERE chat_id=? ")->execute(array(time(), $chat_id));	
					
					// Тело сообщения для отправки				
					$messageText1 = "📋 На проверку предоставлено документов: <b>".$CountPass."</b> . Подождите немного.";
					
					// отправляем в телеграмм ответ				
					$To = $SenDTo->sendMessageUser($messageText1, false, false, false, true);
					$SenDTo->ChatAction();
					
				
					/**
					*	ЕСЛИ СООБЩЕНИЕ О КОЛ-ВО ДОКУМЕНТОВ ДОСТАВЛЕНО В ТЕЛЕГРАММ ТО ПРОВЕРЯЕМ ДАЛЬШЕ
					*/
					if($To['ok'] == 1)
					{
						
						// Создаем массив массивов содержащий каждый документ по отдельности
						$PassInfoArry = $CheckPass->PassinfoArr();
						// 	Разбываем каждый массив на части: серия и номер паспорта
						$arrPassIn = $CheckPass->getPassCheckInfo();
						// 
						$SenDTo->ChatAction();	
						// Делаем непосредственно проверку паспортов и отсылаем пользователю данные о проведенноё проверке
						$Text = $CheckPass->isCheckPass($CountPass, $arrPassIn);
						
					exit;
				
					}
					
				exit;
			
				}
				
			
				else //Если кол-во больше чем допустимое
				{
					// Тело сообщения для отправки	
					$messageText = "Простите, но проверку можно совершать 1 раз в ".$minuyes." минут в кол-ве ".$maxCheckPass." пасспортов за запрос. Данные ограничения введены исключительно в целях технической безопасности сервера.";
					
					// Клавиатура бота		
					$buttonKeyboard2[] = array("❔ Справка", "👉 Проверить паспорт");
					// отправляем в телеграмм ответ				
					$SenDTo->sendMessageUser($messageText, $buttonKeyboard2);
					exit;
			
				}
			
			exit;
			}
		
		exit;
	
		}
		
		
		
	
	}
	
	
	//	Обнуляем команду проверки данных
	DB::$the->prepare("UPDATE tlg_passCheck_user SET commands=? WHERE chat_id=? ")->execute(array(NULL,  $chat_id));
	
	
	/**
	*	Пасрсим все остальные сообщения
	*/
	$ParserResponse->parser($message);


teleToLog($action);	
exit;

?>