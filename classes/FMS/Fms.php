<? 

require_once 'simple_html_dom.php';
/*
*
**/
class Fms{
	
	/*
	*
	**/
	function __construct($series, $no) 
	{
		$this->setSeries($series);
		$this->setNo($no);
	}

	/*
	*
	**/
	function getStatus() 
	{
		if (isset($this->status)) 
		{
			return $this->status;
		}
		
		$url = 'http://services.fms.gov.ru/services/captcha.jpg';
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_NOBODY, 1);
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$response = curl_exec($ch);
		curl_close($ch);

		preg_match('/^Set-Cookie:\s*([^;]*)/mi', $response, $matches);
		$matches = explode('=', $matches[1]);
		$session_id = $matches[1];
		$timestamp = time();
		$captcha_code = array();

		for ($i = 1; $i <= 6; $i++) 
		{
			$url = "http://services.fms.gov.ru/services/captcha-audio/{$session_id}/{$i}.mp3?timestamp={$timestamp}";
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_COOKIE, "JSESSIONID=$session_id");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$response = curl_exec($ch);
			curl_close($ch);
			$captcha_code[] = self::decodeCaptchaResponse($response);
		}
		$captcha_code = implode('', $captcha_code);
		$url = "http://services.fms.gov.ru/info-service.htm";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_COOKIE, "JSESSIONID=$session_id");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, "sid=2000&form_name=form&DOC_SERIE={$this->series}&DOC_NUMBER={$this->no}&captcha-input={$captcha_code}");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$contents = curl_exec($ch);
		curl_close($ch);
		$html = str_get_html($contents);
		if(count($html->find('<h4 class=\"ct-h4\">')))
		foreach($html->find('<h4 class=\"ct-h4\">') as $div)
		return $div->innertext;
	}

	/*
	*
	**/
	private function decodeCaptchaResponse($response) 
	{
		switch (md5($response)) 
		{
			case '669cf3532495afd40304eab33106bc94':
			return 1;
			break;
			case 'e9d71c9ff6d2f5de0ba6b50633947d16':
			return 2;
			break;
			case 'c66bb3ca19adff2903fa8731291d6b6b':
			return 3;
			break;
			case 'ea77c2f378cdc327f07b84c311b88782':
			return 4;
			break;
			case '1e44921e41455c19ef3dbbe442798aba':
			return 5;
			break;
			case 'c948e5bd536655405b3a5abd47f964c3':
			return 6;
			break;
			case 'f50c5a35d604e18c2d47e34383b782b7':
			return 7;
			break;
			case '51282edec22d43b8e0681f2ddd7d8e78':
			return 8;
			break;
			case 'fbd9f8f9fed8274cd18d59e4697e5359':
			return 9;
			break;
			default:
			return 0;
			break;
		}							
	}

	/*
	*
	**/
	private function setSeries($value) 
	{
		if (!preg_match("/^\d{4}$/", $value)) 
		{
			throw new Exception("Серия должна содержать только 4 цифры");
		}
		
		$this->series = $value;
	}

	/*
	*
	**/
	private function setNo($value)
	{
		if (!preg_match("/^\d{6}$/", $value))
		{
			throw new Exception("No должен содержать только 6 цифр");
		}

		$this->no = $value;
	}
}
