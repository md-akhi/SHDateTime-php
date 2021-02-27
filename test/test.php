<meta charset="utf-8" />
<?php
\error_reporting(E_ALL | E_STRICT);
\ini_set('display_errors', true);
require dirname(__DIR__) . '/vendor/autoload.php';

mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');
mb_http_input('UTF-8');
mb_language('uni');
mb_regex_encoding('UTF-8');
setlocale(LC_TIME, 'en_US.UTF8');//fa-IR.UTF8
require_once(dirname(__DIR__)."/SHDate.php");
//require_once "../SHCalendar.php";
//require_once "../SHDateTime.php";
//require_once "kint.php";

	  function startTimer() {
		  return floatval(sprintf('%.10f'/* max %.22f */,microtime(1)));
	  }
	 function stopTimer($start,$round = 6){
		  return 'Elapsed time: ' . round(startTimer()-$start,$round,PHP_ROUND_HALF_UP) .' seconds';
	  }
	$startTimer = startTimer();

//$jyyy = 1396;$jmmm = 12;$jddd = 29;
	//$SDateTime = new SDateTime();
	//$DateTime = new DateTime('2021W01');
	//$jmktime = jmktime(3,30,0,1348,10,11);
	//$jgmmktime = jgmmktime(0,0,0,1348,10,11);
	//$unix=JDate_BASE::date_to_time('','','','','','');
	//$rgmunix=JDate_BASE::time_to_gmdate($unix);
	//$runix=JDate_BASE::time_to_date($unix);
	var_dump( 
	time()
	//,$jmktime
	//,$jgmmktime
	//jdate(' c | r',
	//jstrtotime('1397w01-1 last day of')),
	//date('r',strtotime('2020W01-1 next week')),
	//$unix
	//,$runix
	//time(),
	,sdate('c|U|B')//,$unix
	//,$JDateTime->format('u|v|B')
	// ,getdate($jmktime)
	// ,jgetdate($jmktime)
	//,JDate_BASE::getdate($unix)
	//,JDate_BASE::gmgetdate($unix)
	// ,$unix
	//,$rgmunix
	//,$runix
	// ,JDate_BASE::gmgetdate()
	// ,JDate_BASE::time()-JDate_BASE::gmtime()
	// ,date('c|U')
	// ,jdate('c|U')
	// ,gmdate('c|U')
	// ,jgmdate('c|U')
	//,JDate_BASE::debug()
	);
	
	
	/* 		365.24219895		day in year
		->	6761905 => -1		6761905 years => -1 day
	
			2082 * 365.24219895 = 1029983.0005878
		-> 	0.0005878 * 24*60*60 = 50.78592		in 2082 years �51 second
	 */
	
	
	

echo stopTimer($startTimer);


















