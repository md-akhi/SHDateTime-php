--TEST--
date suffixes test
--FILE--
<?php
require_once(dirname(__DIR__)."/SHDate.php");
date_default_timezone_set('UTC');
class test extends SHDate
{
    const array=[""=>time,
    ""=>time,
    ""=>time,
    ""=>time,
    ""=>time];
	public function __construct(){
        foreach(array as item=>time){
		$stt = $this->strtotime(item)
				var_dump($stt.' -- '.time);
		}}
	}
}
$tst = new test();

echo "Done\n";
?>
--EXPECT--