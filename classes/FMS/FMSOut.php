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
		$this->masg = $message;
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
		$PassinfoArr = preg_replace("/\s/", " ", $this->masg); //убираем пробелы
		$PassinfoArr =  preg_split("/[;\s]+/", $this->masg, -1, PREG_SPLIT_NO_EMPTY);
		$this->PassInfoArry = $PassinfoArr;
		
	}
	
	/**
	* Функция считает кол-во массивов (паспортов на проверку)
	* params 
	* return CountPass 
	*/
	public function isCountPass()
	{
		$CountPass =  preg_match_all("/[0-9]+/", $this->masg);
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
				
				
	
		for ($d = 1; $d <= $resmod;)
		{
			$passport = $isFmsCheck[$d]['passport'];
			$status = $isFmsCheck[$d]['status'];
			$setseries = $isFmsCheck[$d]['series'];
			$setnomer = $isFmsCheck[$d]['nomer'];
			$getpassport = strtolower($passport);
		
			echo $status;
				echo "<pre>";
				print_r($status);
				echo "</pre>";
				
				require_once 'PassportStatusFactory.php';
				$StatuFactory = new PassportStatusFactory();
				$Realtaties = $StatusFactory ->getStatus($status);
			//	$ParseErrorResponse = $StatusFactory ->getErrorStatus($status);
				
				if($Realtaties == "YES")
				{
					$resul = "✅ документ под № <b>".$getpassport ."</b>\nПаспорт серии <b>".$setseries ."</b> номер <b>".$setnomer ."</b>\n<b>ДЕЙСТВИТЕЛЬНЫЙ</b> ☑️☑️☑️\n➖➖➖➖➖➖➖➖\n";
					
				}
				if($Realtaties == "NO")
				{			
					
					$resul = "🆘🆘🆘 документ под № <b>".$getpassport ."</b>\nПаспорт серии <b>".$setseries ."</b> номер <b>".$setnomer ."</b>\n<b>НЕДЕЙСТВИТЕЛЬНЫЙ</b>";
				}
				
				$result = $resul;	
				$Text[] = "\n".$result."";				
			$d++;
		
		}
		
		
		$dateformat = "d-m-Y";
		$datain = date($dateformat);
		
		$messageText  = "Вы запросили проверку паспортов в кол-ве:  ".$CountPass."\n";
		$messageText .= "Проверка произведена по базе ГУ по вопросам миграции МВД России.\nВся информация актуальная на  <b>".$datain."</b> \nВаши данные:\n";
		$messageText .= implode(" ", $Text);
		$messageText .= "\nOбращайтесь к нам ещё! Всего доброго!\n\n\n®Данный бот предоставлен онлайн магазином https://shopvw.pw/™";
		$buttonKeyboard2[] = array("❔ Справка", "👉 Проверить паспорт");
		
		$messageTex = mb_convert_encoding($messageText, "UTF-8");
		return $this->sendMessage($messageTex, $buttonKeyboard2);
		
	}
	

	
	public function sendMessage($messageText, $dataKeyboard = false, $reply_to = false)
    {

		$chat = $this->chatid;
		
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
	
	
	
	
}
