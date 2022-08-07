<?php header('Content-type: text/html; charset=utf-8');
mb_internal_encoding("UTF-8");

require_once 'FMS/Fms.php';// контроллер непосредственно FMS паспортов
require 'HttpRequest.php';// контроллер HTTP запроса

/**
* Класс обработка запроса и ответа от сайта FMS
* Class FMSOut 
*/
class FMSOut
{
	
	/**
	* Создаем экземпляр класса с параметрами
	* params message, botToken, chat_id
	* return this 
	*/
	public function __construct($message, $botToken, $chat_id) 
	{
		
		$this->masg = preg_replace("/\s+/", "", $message);
		$this->Token = $botToken;
		$this->chatid = $chat_id;
	}
	
	

	
	/**
	* Функция создает макссив данных состящих из массивов
	* params 
	* return this 
	*/
	public function PassinfoArr()
	{
		
		preg_match_all("/[0-9]{10}+/", $this->masg, $Passinf);
			foreach($Passinf as $item){
				$PassinfoArr = $item;
			}
		$this->PassInfoArry = $PassinfoArr;
	}
	
	/**
	* Функция считает кол-во массивов (паспортов на проверку)
	* params 
	* return CountPass 
	*/
	public function isCountPass()
	{
		$CountPass =  preg_match_all("/[0-9]{10}+/", $this->masg); 
		$this->CountPass = $CountPass;
		return ($CountPass);
	}
	
	/**
	* Функция разбивает строку цифр на серию и номер
	* params 
	* return resultate 
	*/
	public function getPassCheckInfo()
	{
	
		for ($d = 0; $d <= $this->CountPass;	)
		{
			foreach($this->PassInfoArry as $value)
			{
				$series = mb_substr($value,0,4);
				$no = substr($value,strlen($series));
				$resultate[] = array(
					'series'=>$series,
					'no'=>$no
				);
				
				$d++;
			
			}
		
		$this->arrPassIn = $resultate;
			
		return($resultate);	
		
		exit;
		}

	}
	
	/**
	* Функция запрос к файлу FMS и в ответ приходяд данные о проверке
	* params arrPassIn
	* return result 
	*/
	public function isFmsCheck($arrPassIn)
	{
		
		$mask = $arrPassIn;
		$i=1;
		
			foreach ($mask as $value) {
				$passport = $i++;
				$result[$passport] = [
					'passport'=>$passport,
					'series'=>$series = $value['series'],
					'nomer'=>$no = $value['no'],
					'fms'=>$fms = new Fms($series, $no),
					'status'=>$getStatus = $fms->getStatus(),
				];
				
				echo $passport;
				echo $series;
				echo $no;
				echo $status;
			
			}

		$this->FMSResponseSata = $result;
		
		return ($result);
		
		exit;
	}
	
	/**
	*	Фкнция обработки ответа от файла и Распарсировка, подготовка и отправка ответа в телеграмм
	*	 params CountPass, CountPass
	* 	@return bool
	*/
	public function isCheckPass($CountPass, $arrPassIn)
	{	
		$resmod = $CountPass;
		$isFmsCheck = $this->isFmsCheck($arrPassIn);
				echo "isFmsCheck";
				echo "<pre>";
				print_r($isFmsCheck);
				echo "</pre>";
		
		
		for ($d = 1; $d <= $this->CountPass;)
		{
			$passport = $isFmsCheck[$d]['passport'];
			$status = $isFmsCheck[$d]['status'];
			$setseries = $isFmsCheck[$d]['series'];
			$setnomer = $isFmsCheck[$d]['nomer'];
			$getpassport = strtolower($passport);
		
				echo "getstatus";
				echo "<pre>";
				print_r($status);
				echo "</pre>";
				//$this->sendMessage($status);
				$StatusCorrect = $this->PassportStatusCorrect($status);
				//	$this->sendMessage($StatusCorrect);
				$PassporForNumber = $this->ParserNumberSticker($getpassport);

				if($StatusCorrect == "YES")
				{
					$resul = "📘 документ под № <b>".$PassporForNumber ."</b>\nПаспорт  🇷🇺:
					серии <b>".$setseries ."</b> номер <b>".$setnomer ."</b>\n\xF0\x9F\x86\x97 <b>ДЕЙСТВИТЕЛЬНЫЙ</b> \n➖➖➖➖➖➖➖➖\n";

				}
				if($StatusCorrect == "NO")
				{	
					$StatusError = $this->PassportParseStatusError($status);
				
					$resul = "📘 документ под № <b>".$PassporForNumber ."</b>\nПаспорт  🇷🇺: 
					серии <b>".$setseries ."</b> номер <b>".$setnomer ."</b>\n<b>\xF0\x9F\x86\x98 \xF0\x9F\x86\x98 \xF0\x9F\x86\x98 НЕДЕЙСТВИТЕЛЬНЫЙ</b>
					Причина: <b>".$StatusError."</b>\n➖➖➖➖➖➖➖➖\n";
				}
				
				$result = $resul;	
				$Text[] = "\n".$result."";				
			$d++;
		
		}
		
		$dateformat = "d-m-Y";
		$datain = date($dateformat);
		
		$messageText  = "Вы запросили проверку паспортов в кол-ве:  ".$CountPass."\n";
		$messageText .= "Проверка произведена по базе 🗄 ГУ по вопросам миграции МВД 🇷🇺.\nПредоставленная информация актуальная на 📅  <b>".$datain."</b> \nВаши данные:\n⬇️⬇️⬇️\n";
		$messageText .= implode(" ", $Text);
		$messageText .= "\nOбращайтесь к нам ещё! Всего доброго!\n\n\n®";
		
		
		$buttonKeyboard2[] = array("❔ Справка", "👉 Проверить паспорт");
		$buttonKeyboard2[] = array("Обратная связь 📫");
	
		$messageTex = mb_convert_encoding($messageText, "UTF-8");
		return $this->sendMessage($messageTex, $buttonKeyboard2);
	}
	
	/**
	*	Фкнция проверяет статус на прдмет действия
	*	 params status
	* 	@return bool
	// */
	public function PassportStatusCorrect($data)
	{
		$str = mb_convert_encoding($data, "UTF-8");;
		// подстрока
		$substr = "не значится";
		if (stristr($data, $substr) === FALSE){
			return "NO";
		}
		else
		{
		return "YES";
		}
	}	
	
	
	
	
	public function PassportParseStatusError($status)
	{	
		
		$dataStatus = $status;
		
		if(stristr($dataStatus, "«Не действителен (ЧИСЛИТСЯ В РОЗЫСКЕ)»")) 
		{
			return  "Числится в розыске";
		} 
		
		if(stristr($dataStatus,  "«Не действителен (ИСТЕК СРОК ДЕЙСТВИЯ)»"))
		{
			return  "Истек срок действия";	
		}
		
		if(stristr($dataStatus,  "«Не действителен (ТЕХНИЧЕСКИЙ БРАК)»"))
		{
			return  "Технический брак";
		}
		
		if(stristr($dataStatus, "«Не действителен (В СВЯЗИ СО СМЕРТЬЮ ВЛАДЕЛЬЦА)»")) 
		{
			return   "«Не действителен (В связи со смертью владельца";
		}
		
		if(stristr($dataStatus,  "«Не действителен (ЗАМЕНЕН НА НОВЫЙ)»"))
		{
			return  "Заменен на новый";
		}
		
		if(stristr($dataStatus,  "«Не действителен (ВЫДАН С НАРУШЕНИЕМ)»")) 
		{
			return  "Выдан с нарушением";
		}
		
		if(stristr($dataStatus,  "«Не действителен (ИЗЬЯТ, УНИЧТОЖЕН)»")) 
		{
			return  "Изъят и уничтожен!!!";
		}
		
		else
		{
			return  'Сведениями по заданным реквизитам не располагаем';
		}
	
	}
	
	public function ParserSticker($data)
	{	
		
		$Kol_vo =  	preg_match_all("/[0-9]{1}+/", $data, $Kol_vol); 
		
		if($Kol_vo == "1")
		{
			return $this->ParserNumberSticker($data);
			
		}
		else
		{
		
			foreach($Kol_vol as $item)
			{
				$reaultKol_vo = $item;
			}
			
			$x = $Kol_vo;
			
			for($i=0; $i <= $x; $i++)
			{
				foreach($reaultKol_vo as $item2)
				{
					$respos[] = $this->ParserNumberSticker($item2[$i]);
				
				}
				
				return implode($respos);
			}
	
		}
	
	}
	
	public function ParserNumberSticker($data)
	{	
		$number =  $data;
		
		
	   if($number == '0') 
		{
			return  "\x30\xE2\x83\xA3";
		} 
		
		if($number == '1')
		{
			return  "\x31\xE2\x83\xA3";	
		}
		
		if($number == '2')
		{
			return  "\x32\xE2\x83\xA3";
		}
		
		if($number == '3') 
		{
			return   "\x33\xE2\x83\xA3 ";
		}
		
		if($number == '4')
		{
			return  "\x34\xE2\x83\xA3 ";
		}
		
		if($number == '5') 
		{
			return   "\x35\xE2\x83\xA3";
		}
		
		if($number == '6') 
		{
			return   "\x36\xE2\x83\xA3";
		}
		if($number == '7') 
		{
			return   "\x37\xE2\x83\xA3";
		}
		if($number == '8') 
		{
			return  "\x38\xE2\x83\xA3";
		}
		if($number == '9') 
		{
			return  "\x39\xE2\x83\xA3";
		}
	
	
	}
	
	public function sendMessage($messageText, $dataKeyboard = false, $reply_to = false,  $inline = false)
    {

		$chat = $this->chatid;
		if($inline)
		{
			$Keyboard = array('inline_keyboard' => $dataKeyboard);
		} 
		if($dataKeyboard)
		{
			$Keyboard = array('keyboard' => $dataKeyboard,
			'resize_keyboard' => true);
		}else
		{
			$Keyboard = array('remove_keyboard' => true);
		}
	
		$rm = json_encode($Keyboard);

		$args = array(
			'chat_id' => $chat,
			'parse_mode'=> 'HTML',
			'text' => $messageText
		);
		
		if($reply_to) $args['reply_to_message'] = $reply_to;
		if($dataKeyboard) $args['reply_markup'] = $rm;
       
		if($messageText) 
		{
			$r = new HttpRequest("post", "https://api.telegram.org/bot".$this->Token."/sendmessage", $args);
			$rr = $r->getResponse();
			$ar = json_decode($rr, true);
        }
		return($ar);
	
	}
	
	/**
	*@param $botToken
	*/
	public function sendChatAction()
	{
		$chat = $this->chat_id;
		
		$args = array(
			'chat_id' => $chat,
			'action' => 'typing' 
		);

		$r = new HttpRequest("post", "https://api.telegram.org/bot".$this->botToken."/sendChatAction", $args);
		$rr = $r->getResponse();
		$ar = json_decode($rr, true);
		
	}
}
