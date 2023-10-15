<?php

require_once(dirname(__DIR__)."/SHDate.php");


class dev extends SHDate
{
	public function __construct(){
		for($y = 1401; $y <= 1401; $y++){
			$date = self::date("Y=m=d=M=D");
			
				var_dump($date);
		}
	}
}

$tst = new dev();

?>