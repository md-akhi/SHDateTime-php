--TEST--
date suffixes test
--FILE--
<?php
require_once(dirname(__DIR__)."/SHDate.php");
date_default_timezone_set('UTC');
class test extends SHDate
{
	public function __construct(){
		for($y = 1201; $y <= 1600; $y++){
			var_dump(self::getCentury($y).' --- '.$y);
			if(($y%100)==1)
				$y+=98;
		}
	}
}
$tst = new test();

echo "Done\n";
?>
--EXPECT--
string(11) "13 --- 1201"
string(11) "13 --- 1300"
string(11) "14 --- 1301"
string(11) "14 --- 1400"
string(11) "15 --- 1401"
string(11) "15 --- 1500"
string(11) "16 --- 1501"
string(11) "16 --- 1600"
string(11) "17 --- 1601"
Done