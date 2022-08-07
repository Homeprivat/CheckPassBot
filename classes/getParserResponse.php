<?php 


require_once 'SendMeseges.php';
require_once 'PDO.php';


/**
 * Class getParserResponse 
 */
class getParserResponse
{

    
	public function parser($message)
	{	
		
	    if(self::isStartBot($message)) 
		{
			//	Обнуляем команду проверки данных
			DB::$the->prepare("UPDATE tlg_passCheck_user SET commands=? WHERE chat_id=? ")->execute(array(NULL,  $this->chat_id));
			return  self::msgStart();
		} 
		
		if(self::isHelpInfo($message)) 
		{
			//	Обнуляем команду проверки данных
			DB::$the->prepare("UPDATE tlg_passCheck_user SET commands=? WHERE chat_id=? ")->execute(array(NULL,  $this->chat_id));
			return self::getHelpInfoToMsg($message);	
		}
		
		if(self::isBack($message)) 
		{
			//	Обнуляем команду проверки данных
			DB::$the->prepare("UPDATE tlg_passCheck_user SET commands=? WHERE chat_id=? ")->execute(array(NULL,  $this->chat_id));
			return self::msgStart();
		}
		
		if(self::isCheckPassInfo($message)) 
		{
		
			return self::getCheckPassInfoToMsg($message);
			
		}
		
		if(self::isFeedbackText($message)) 
		{
			DB::$the->prepare("UPDATE tlg_passCheck_user SET commands=? WHERE chat_id=? ")->execute(array(NULL,  $this->chat_id));
			return self::msgFeedback();
		}
		
		
		else
		{
			
			$messageText = "Неверная команда бота";
			$buttonKeyboard27[] = array("❔ Справка", "👉 Проверить паспорт");
			$buttonKeyboard27[] = array("Обратная связь 📫");	
			$this->SendTo->sendMessageUser($messageText, $buttonKeyboard27);
			exit;
		}
		exit;
	}
	

	public function msgStart ()
	{
		$messageText = "Приветствую Вас ".$this->persona."\nВаш \xF0\x9F\x86\x94 Телеграм: ".$this->chat_id."\n
		Я бот телеграм, который осуществит Вам проверку по базе Главного управления по вопросам миграции МВД 🇷🇺 на предмет действительности паспорта гражданина РФ.\n\n\nДля начала нажмите в меню кнопку \n\n ❔ Справка\n\n для ознакомления способа и принципа работы с ботом.\n\n";
		
		$buttonKeyboard2[] = array("❔ Справка", "👉 Проверить паспорт");
		$buttonKeyboard2[] = array("Обратная связь 📫");	
		return $this->SendTo->sendMessageUser($messageText, $buttonKeyboard2);
	} 
	
	
	public function msgFeedback()
	{
		$messageText = "📫 - Если с этим ботом есть проблемы или вы хотите что-то предложить, отправьте сообщение здесь:";
		$buttonKeyboard[] = array("Отмена ❌");
		DB::$the->prepare("UPDATE tlg_passCheck_user SET status=? WHERE chat_id=? ")->execute(array("feedback", $this->chat_id));
		return $this->SendTo->sendMessageUser($messageText, $buttonKeyboard);
		
	} 

	
	/** 
	* @param $chat_id
	* @return bool
	*/
	public function getHelpInfoToMsg()
	{
		$messageText = "В ответ на сообщение ✔️Жду Ваши данные✔️ пришлите паспортные данные, которые Вы желаете проверить на действительность.\n
		‼️‼️‼️Образец сообщения:\n
		<b>123456789;</b>
		(<b>где - '1234'- серия паспорта, 
		'567890'- номер паспорта</b>)\n\nВ ответ Вы получите данные с сайта <a href='http://services.fms.gov.ru/'>Перейти на сервис</a> о действительности указанного паспорта.\n➖➖➖➖➖➖➖➖\nНи какой капчи, ни чего не нужно. Быстро! Легко! Удобно!

		📌📌📌Для работы в дальнейшем с ботом, достаточно просто нажать ещё раз кнопку 👉 Проверить паспорт  и прислать новые данные.в данной переписке вновь указать серию и номер паспорта и в ответ ВЫ получите данные о его действительности.📌📌📌
		
		<b>‼️‼️‼️ВНИМАНИЕ‼️‼️‼️</b>. За одну проверку можно <b>проверить неболее 10 документов</b>.
		<b>⏰⏰⏰ Ограничение по времени: 1 проверка раз в 3 минуты</b>.
		
		‼️‼️‼️Образец сообщения для проверки нескольких паспортов разом:\n
				<b>123456789;123456789;123456789;123456789;</b>
				
		В данном случае как Вы видите будет проверено 4️⃣ документа на предмет действительности!!!
		‼️‼️‼️Ни каких пробелов между серией и номером паспорта ни в коем случае не ставить‼️‼️‼️!
		
		➖➖➖➖➖➖➖\n";
		
		$buttonKeyboard3[] = array("👉 Проверить паспорт");
		$buttonKeyboard3[] = array("Обратная связь 📫", "🔙 Назад");	
	
			
		return $this->SendTo->sendMessageUser($messageText, $buttonKeyboard3);
	}
	
	/** 
	* @param $chat_id
	* @return bool
	*/
	public function getCheckPassInfoToMsg()
	{
		$messageText = "✔️Жду Ваши данные✔️";
		$buttonKeyboard1[] = array("Отмена ❌");
		$buttonKeyboard1[] = array("❔ Справка", "🔙 Назад");
		DB::$the->prepare("UPDATE tlg_passCheck_user SET commands=? WHERE chat_id=? ")->execute(array("check", $this->chat_id));
		return $this->SendTo->sendMessageUser($messageText, $buttonKeyboard1);
	}
	
	
	/**
	* @param $chat_id
	* @return
	*/
	public function check_for_number($message) 
	{
		$string = $message;
		echo "<pre>";	
		print_r($string);
		echo "</pre>";
	
		return  $ar = self::is_num($string);
	}
	
	public function is_num($s)
	{
		return preg_match( '/^\d+[\;]?\d/', $s);
	}
	
	
	/** 
	* @param $arrData
	* @return bool
	*/
    public function isMessage_id($arrData) 
	{
		$result = $arrData['message']['message_id'];
        return $result;
	exit;
	}

	
	/** 
	* @param $arrData
	* @return bool
	*/
    public function isFeedbackText($message) 
	{
        return ($message == "Обратная связь 📫") ? true : false;
    	exit;
	
	}
	
	
	/** 
	* @param $arrData
	* @return bool
	*/
    public function isStartBot($message) 
	{
        return ($message == "/start") ? true : false;
    	exit;
	
	}

	/** проверяем на ❔ Справка
	* @param $data
	* @return bool
	*/
	public function isHelpInfo($message)
	{	
		return ($message == "❔ Справка") ? true : false;
		//	Обнуляем команду проверки данных
		DB::$the->prepare("UPDATE tlg_passCheck_user SET commands=? WHERE chat_id=? ")->execute(array(NULL,  $chat_id));
	
		exit;
	
	}
	
	/** проверяем на 🔙 Назад
	* @param $data
	* @return bool
	*/
	public function isBack($message)
	{	
		return ($message == "🔙 Назад") ? true : false;
		//	Обнуляем команду проверки данных
		DB::$the->prepare("UPDATE tlg_passCheck_user SET commands=? WHERE chat_id=? ")->execute(array(NULL,  $chat_id));
	
		exit;
	}
	
	/** проверяем на 👉 Проверить паспорт
	* @param $data
	* @return bool
	*/
	public function isCheckPassInfo($message)
	{	
		return ($message == "👉 Проверить паспорт") ? true : false;
		exit;
	}
	
	/** проверяем на 👉 Проверить паспорт
	* @param $data
	* @return bool
	*/
	public function SendTo($botToken, $chat_id, $message_id = false)
	{
		
		$SendTo = new SendMeseges($botToken, $chat_id, $message_id);
		$this->SendTo = $SendTo;
		return $SendTo;
	}


	/**
	* @return $this
	*/
	public function persona($chat_id)
	{
		$this->chat_id = $chat_id;	
		$user = DB::$the->query("SELECT chat_id, first_name, last_name, isAdmine FROM `tlg_passCheck_user` WHERE `chat_id` = ".$chat_id."");
		$user = $user->fetch(PDO::FETCH_ASSOC);
		
		$lname = $user['last_name'];
		$fname = $user['first_name'];   
		$persona = "".$fname."".$lname."";	
		$this->persona = $persona;	
		$isAdmine = $user['isAdmine'];	
		$this->isAdmine = $isAdmine;	
	}
	
	/**
	* @return $this
	*/
	public function isAdmine()
	{
		return ($this->isAdmine == "yes") ? true : false;
		exit;
	}
	
	public function msgfeedBackAdmin($call_id, $message, $name, $username, $id_user, $adminId)
	{
		$dateformat = "d-m-Y H:i:s";
		$menu1[] = array(array(
				"text" => "Reply",
				"callback_data" => "reply-" . $id_user));
				
		$feedback = "Для Вас новое сообщение!\n\nMessage: $message\nName: $name\nUsername: @$username\nUserID: $id_user\nDate: " . date($dateformat, time());
		
	
		
		$var=fopen("feedback.txt","a+");
		fwrite($var, "\n\n" . $feedback);
		fclose($var);
		
		$text = $this->SendTo->sendMessageAdmin($feedback, $menu1, true, true);
		$this->SendTo->sendAnswerCallbackQuery($call_id, $text, $alert = false);
		
		
		$messageText = "Спасибо за ваш отзыв! ✅";
		$buttonKeyboard[] = array("❔ Справка", "👉 Проверить паспорт");
		$buttonKeyboard[] = array("Обратная связь 📫");
		DB::$the->prepare("UPDATE tlg_passCheck_user SET status=? WHERE chat_id=? ")->execute(array(NULL, $this->chat_id));
		DB::$the->prepare("UPDATE tlg_passCheck_user SET status=? WHERE chat_id=? ")->execute(array(NULL,  $adminId));
		return $this->SendTo->sendMessageUser($messageText, $buttonKeyboard);

	}
	
}	
	
?>