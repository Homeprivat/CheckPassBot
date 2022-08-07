<?php header('Content-type: text/html; charset=utf-8');
require_once 'classes/PDO.php';					// подключаем базу данных
require_once 'classes/function.php'; 			// функция логирования
require_once 'classes/getParserResponse.php';	// основная контроллер команд
require_once 'classes/FMSOut.php'; 	 			// контроллер проверки паспортов


$Innumber = "5";


$imagenumber = ParserSticker($Innumber);
print_r ($imagenumber);

	
	
	function ParserNumberSticker($data)
	{	
		$number =$data;
		
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

$string = "2504339183;2504 33 9183 25043391 83250433918325043391832504339183 2504339183;2504339183.2504339183250433918325043391832504339183";
	echo "<br />";
	echo "<br />";
		$Pass = preg_replace('/\s+/', '', $string); //убираем пробелы
	echo "<br />";
		echo "первыйц выход";echo "<br />";
		echo $Pass;	
		echo "<br />";
		echo "<br />";
		echo "<br />";
		$str = substr($Pass, 0, 10);
		echo $str;
		echo "<br />";
		echo "<br />";
		echo "Писичивтал";
		echo "<br />";
		echo preg_match_all("/[0-9]{10}+/", $Pass);
		echo "<br />";echo "<br />";
			preg_match_all("/[0-9]{10}+/", $Pass, $Passinf);
			foreach($Passinf as $item){
				$reault = $item;
			}
print_r ($reault);
		echo "<br />";
	//	$Passinfo = preg_match_all("/[0-9]+/",$string);
			echo "<br />";
	//	print_r($Passinfo);
		echo "<br />";
		echo "<br />";
		echo "<br />";
			echo "Писичивтал";
				echo "Писичивтал";
					echo "Писичивтал";
		echo "<br />";
		$PassinfoArr = preg_split("/[;\s]+/", $Passinfo, -1, PREG_SPLIT_NO_EMPTY);
	
		var_dump($PassinfoArr);
		
?>