<?

//require_once 'PDO.php';	
	//**********************************************************************************
	//*********************** Класс приём данных от Телеграмм **************************
	//**********************************************************************************
	
	/**
	*	 Класс расшифровки ответа от телеграмм формата json
	**/
	class getWebHook
	{
		public function init($json)
		{
			$inputUpdate = $this->getData($json);
			return $this->ParserInti($inputUpdate);
		}
		
	
		public function ParserInti($inputUpdate)	
		{	
			if(array_key_exists('message', $inputUpdate)) 
			{
				$update = array(
					"update_id"		=> $inputUpdate['update_id'],
					"message_id"	=> $inputUpdate['message']['message_id'],
					"date"			=> $inputUpdate['message']['date'],
					"message"		=> $inputUpdate['message']['text'],
					"chat_id"		=> $this->getChatId($inputUpdate),
					"replyid" 		=> $inputUpdate['message']['reply_to_message'],
				);
			}
			elseif(array_key_exists('callback_query', $inputUpdate)) 
			{
				$update = array(
					"data" 		=> $inputUpdate['callback_query']['data'],
					"call_id" 	=> $inputUpdate['callback_query']['id'],
					"chat_id" 	=> $inputUpdate['callback_query']['message']['chat']['id'],
					"id_user"	=> $inputUpdate['callback_query']['from']['id'],
					"message_id"=> $inputUpdate['callback_query']['message']['message_id'],
				);
			}	
			$this->inputUpdate = $inputUpdate;
			$this->update = $update;
			return $update;
		
		}
		
		/**
		* 	Converter Json
		**/
		private function getData($json)
		{
			return json_decode(file_get_contents($json), 1);
		}
		
		/**
		* 	Проверка на наличие first_name
		**/
		private function inFirst_name($data)
		{
			$datas = [
				"" => "",
				"$data" => "$data",
			];
			
			return ($datas[$data])? $datas[$data] : '';
		}
		
		/**
		* 	Проверка на наличие last_name
		**/
		private function inLast_name($data)
		{
			$datas = [
				"" => "",
				"$data" => "$data",
			];
			
			return ($datas[$data])? $datas[$data] : '';
		}
		
		/**
		* 	Проверка на наличие callback_query
		**/
		public function getChatId($datatUpdate)
		{
			$chat_id = $datatUpdate['message']['chat']['id'];
			
			/**
			* 	Проверка на наличие юзера
			**/
			$vsego = DB::$the->query("SELECT chat_id FROM `tlg_passCheck_user` WHERE `chat_id` = ".$chat_id."");
			$vsego = $vsego->fetchAll();
		
			/**
			*	Если юзера нет, то записываем его в базу данных
			*/
			if(count($vsego) == 0)
			{ 
				$params = array(
				'chat_id' 		=> $chat_id,
				'id_user' 		=> $datatUpdate['message']['from']['id'],
				'first_name' 	=>  $this->inFirst_name($datatUpdate['message']['from']['first_name']),
				'last_name' 	=> $this->inLast_name($datatUpdate['message']['from']['last_name']), 
				'username' 		=> $datatUpdate['message']['from']['username'],
				'status' 		=> NULL,
				);  

				$q = DB::$the->prepare("INSERT INTO `tlg_passCheck_user` (chat_id, id_user, first_name, last_name, username, status) VALUES (:chat_id, :id_user, :first_name, :last_name, :username, :status)"); 
				$q->execute($params);	
				return $chat_id;
				exit;
			}
			
			return $chat_id;
		}
		
		/**
		* 	Проверка на наличие callback_query
		**/
		public function dataTO()
		{
			$data = $this->inputUpdate;
			return explode("-", $data['callback_query']['data']);
		}
		
		
	}




?>