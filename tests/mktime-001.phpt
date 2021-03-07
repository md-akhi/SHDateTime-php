--TEST--
date suffixes test
--FILE--
<?php
require_once(dirname(__DIR__)."/SHDate.php");
date_default_timezone_set('UTC');
class test extends SHDate
{
	public function __construct(){
		for($y = 1300; $y < 1500; $y+=6){
			for($m = 1; $m < 13; $m++){
				for ($d = 1; $d < 32; $d++) {
					$date = self::dates('Y-n-j', self::mktimes(0,0,0, $d, $m, $y));
					$dateb = $y.'-'.$m.'-'.$d;
					if($date!==$dateb)
						var_dump($date.' == '.$dateb.' -- false');
					if($d<5)
						$d=25;
				}
				if($m<3)
					$m = 6;
				elseif($m<8)
					$m = 11;
			}
		}
	}
}
$tst = new test();

echo "Done\n";
?>
--EXPECT--
string(30) "1300-8-1 == 1300-7-31 -- false"
string(31) "1301-1-1 == 1300-12-31 -- false"
string(30) "1306-8-1 == 1306-7-31 -- false"
string(31) "1307-1-1 == 1306-12-30 -- false"
string(31) "1307-1-2 == 1306-12-31 -- false"
string(30) "1312-8-1 == 1312-7-31 -- false"
string(31) "1313-1-1 == 1312-12-30 -- false"
string(31) "1313-1-2 == 1312-12-31 -- false"
string(30) "1318-8-1 == 1318-7-31 -- false"
string(31) "1319-1-1 == 1318-12-30 -- false"
string(31) "1319-1-2 == 1318-12-31 -- false"
string(30) "1324-8-1 == 1324-7-31 -- false"
string(31) "1325-1-1 == 1324-12-30 -- false"
string(31) "1325-1-2 == 1324-12-31 -- false"
string(30) "1330-8-1 == 1330-7-31 -- false"
string(31) "1331-1-1 == 1330-12-30 -- false"
string(31) "1331-1-2 == 1330-12-31 -- false"
string(30) "1336-8-1 == 1336-7-31 -- false"
string(31) "1337-1-1 == 1336-12-30 -- false"
string(31) "1337-1-2 == 1336-12-31 -- false"
string(30) "1342-8-1 == 1342-7-31 -- false"
string(31) "1343-1-1 == 1342-12-31 -- false"
string(30) "1348-8-1 == 1348-7-31 -- false"
string(31) "1349-1-1 == 1348-12-30 -- false"
string(31) "1349-1-2 == 1348-12-31 -- false"
string(30) "1354-8-1 == 1354-7-31 -- false"
string(31) "1355-1-1 == 1354-12-31 -- false"
string(30) "1360-8-1 == 1360-7-31 -- false"
string(31) "1361-1-1 == 1360-12-30 -- false"
string(31) "1361-1-2 == 1360-12-31 -- false"
string(30) "1366-8-1 == 1366-7-31 -- false"
string(31) "1367-1-1 == 1366-12-31 -- false"
string(30) "1372-8-1 == 1372-7-31 -- false"
string(31) "1373-1-1 == 1372-12-30 -- false"
string(31) "1373-1-2 == 1372-12-31 -- false"
string(30) "1378-8-1 == 1378-7-31 -- false"
string(31) "1379-1-1 == 1378-12-30 -- false"
string(31) "1379-1-2 == 1378-12-31 -- false"
string(30) "1384-8-1 == 1384-7-31 -- false"
string(31) "1385-1-1 == 1384-12-30 -- false"
string(31) "1385-1-2 == 1384-12-31 -- false"
string(30) "1390-8-1 == 1390-7-31 -- false"
string(31) "1391-1-1 == 1390-12-30 -- false"
string(31) "1391-1-2 == 1390-12-31 -- false"
string(30) "1396-8-1 == 1396-7-31 -- false"
string(31) "1397-1-1 == 1396-12-30 -- false"
string(31) "1397-1-2 == 1396-12-31 -- false"
string(30) "1402-8-1 == 1402-7-31 -- false"
string(31) "1403-1-1 == 1402-12-30 -- false"
string(31) "1403-1-2 == 1402-12-31 -- false"
string(30) "1408-8-1 == 1408-7-31 -- false"
string(31) "1409-1-1 == 1408-12-31 -- false"
string(30) "1414-8-1 == 1414-7-31 -- false"
string(31) "1415-1-1 == 1414-12-30 -- false"
string(31) "1415-1-2 == 1414-12-31 -- false"
string(30) "1420-8-1 == 1420-7-31 -- false"
string(31) "1421-1-1 == 1420-12-31 -- false"
string(30) "1426-8-1 == 1426-7-31 -- false"
string(31) "1427-1-1 == 1426-12-30 -- false"
string(31) "1427-1-2 == 1426-12-31 -- false"
string(30) "1432-8-1 == 1432-7-31 -- false"
string(31) "1433-1-1 == 1432-12-31 -- false"
string(30) "1438-8-1 == 1438-7-31 -- false"
string(31) "1439-1-1 == 1438-12-30 -- false"
string(31) "1439-1-2 == 1438-12-31 -- false"
string(30) "1444-8-1 == 1444-7-31 -- false"
string(31) "1445-1-1 == 1444-12-30 -- false"
string(31) "1445-1-2 == 1444-12-31 -- false"
string(30) "1450-8-1 == 1450-7-31 -- false"
string(31) "1451-1-1 == 1450-12-30 -- false"
string(31) "1451-1-2 == 1450-12-31 -- false"
string(30) "1456-8-1 == 1456-7-31 -- false"
string(31) "1457-1-1 == 1456-12-30 -- false"
string(31) "1457-1-2 == 1456-12-31 -- false"
string(30) "1462-8-1 == 1462-7-31 -- false"
string(31) "1463-1-1 == 1462-12-30 -- false"
string(31) "1463-1-2 == 1462-12-31 -- false"
string(30) "1468-8-1 == 1468-7-31 -- false"
string(31) "1469-1-1 == 1468-12-30 -- false"
string(31) "1469-1-2 == 1468-12-31 -- false"
string(30) "1474-8-1 == 1474-7-31 -- false"
string(31) "1475-1-1 == 1474-12-31 -- false"
string(30) "1480-8-1 == 1480-7-31 -- false"
string(31) "1481-1-1 == 1480-12-30 -- false"
string(31) "1481-1-2 == 1480-12-31 -- false"
string(30) "1486-8-1 == 1486-7-31 -- false"
string(31) "1487-1-1 == 1486-12-31 -- false"
string(30) "1492-8-1 == 1492-7-31 -- false"
string(31) "1493-1-1 == 1492-12-30 -- false"
string(31) "1493-1-2 == 1492-12-31 -- false"
string(30) "1498-8-1 == 1498-7-31 -- false"
string(31) "1499-1-1 == 1498-12-31 -- false"
Done