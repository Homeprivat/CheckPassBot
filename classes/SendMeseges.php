<?php 



/**
 * Class Bot ifeedbackbot
 */
class SendMeseges
{
	
	function __construct($botToken, $chat_id, $message_id = false) 
	{
       	$this->botToken = $botToken;
		$this->chat_id = $chat_id;
      	$this->message_id = $message_id;
    }
	
	
	
	/** Отправляем запрос в Телеграмм
     * @param $data
     * @param string $type
     * @return mixed
     */
    public function sendMessageUser($messageText, $dataKeyboard = false, $inline = false, $remove_keyboard = false)
    {

		$chat = $this->chat_id;
		$replto = $this->message_id;
		
		if($inline)
		{
			$Keyboard = array('inline_keyboard' => $dataKeyboard);
		}
		if($dataKeyboard)
		{
			$Keyboard = array('keyboard' => $dataKeyboard,
			'resize_keyboard' => true);
		}
		if($remove_keyboard)
		{
			$Keyboard = array('remove_keyboard' => true);
		}
	
		$rm = json_encode($Keyboard);

		$args = array(
			'chat_id' => $chat,
			'parse_mode'=> 'HTML',
			'text' => $messageText
		);
		
		if($replyto) $args['reply_to_message_id'] = $this->message_id;
		if($dataKeyboard) $args['reply_markup'] = $rm;
       
		if($messageText) 
		{
			$r = new HttpRequest("post", "https://api.telegram.org/bot".$this->botToken."/sendmessage", $args);
			$rr = $r->getResponse();
			$ar = json_decode($rr, true);
        }
		
		return ($ar);
	}
	
	/**
	*@param $botToken
	*/
	public function ChatAction()
	{
		$chat = $this->chat_id;
		
		$args = array(
			'chat_id' => $chat,
			'action' => 'typing' 
		);

			$r = new HttpRequest("post", "https://api.telegram.org/bot".$this->botToken."/sendChatAction", $args);
			$rr = $r->getResponse();
			$ar = json_decode($rr, true);
			return ($ar);
	}
	
	/** Отправляем запрос в Телеграмм
     * @param $data
     * @param string $type
     * @return mixed
     */
    public function sendMessageAdmin($messageText, $dataKeyboard = false, $inline = false)
    {

		if($inline)
		{
		$Keyboard = array('inline_keyboard' => $dataKeyboard);
		} else {
		$Keyboard = array('keyboard' => $dataKeyboard,
		'resize_keyboard' => true);
		}
		
		$rm = json_encode($Keyboard);

		$args = array(
			'chat_id' => 1618426159,
			'parse_mode' => 'HTML',
			'text' => $messageText
		);
	
		if($replyto) $args['reply_to_message_id'] = $this->message_id;
		if($dataKeyboard) $args['reply_markup'] = $rm;
		
		if($messageText) 
		{
			$r = new HttpRequest("post", "https://api.telegram.org/bot".$this->botToken."/sendmessage", $args);
			$rr = $r->getResponse();
			$ar = json_decode($rr, true);
        }
		
		return ($ar);
	}
	
	
	public function sendAnswerCallbackQuery($callback_query_id, $text, $alert = false)
	{
		$chat = $this->chat_id;
		
		$args = array(
		    'callback_query_id' => $callback_query_id,
            'text' => $text,
          'cache_time' => 2,
		);
	  
		$r = new HttpRequest("post", "https://api.telegram.org/bot".$this->botToken."/answerCallbackQuery", $args);
		$rr = $r->getResponse();
		$ar = json_decode($rr, true);
		return ($ar);
	}
	
	public function sendMessageCallBackUser($chat , $messageText)
    {
		$args = array(
			'chat_id' => $chat,
			'parse_mode'=> 'HTML',
			'text' => $messageText
		);
       
		if($messageText) 
		{
			$r = new HttpRequest("post", "https://api.telegram.org/bot".$this->botToken."/sendmessage", $args);
			$rr = $r->getResponse();
			$ar = json_decode($rr, true);
        }
		
		$messageText = "*Отправлено*";
		self::sendMessageAdmin($messageText);
		return ($ar);
	}
	

}





?>