<?php
    /**
	* 	In the name of Allah
	*
    * @package		Date and Time Related Extensions - SH(Solar Hijri, Shamsi Hijri, Iranian Hijri)
    * @author		Mohammad Amanalikhani (MD Amanalikhani, MD Akhi)
    * @link			http://git.akhi.ir/php/SHDateTime/		(Git)
    * @link			http://git.akhi.ir/php/SHDateTime/help/	(Help)
    * @license		https://www.gnu.org/licenses/agpl-3.0.en.html AGPL-3.0 License
    * @version		Release: 2.0.0-alpha.1
    */
	
    /**
    *	The time difference with the server
	*	اختلاف زمان با سرور ±
    */
    define("SHDATE_TSERVER", 0);
    /**
    *    Timezone identifier
    */
    define("SHDATE_TZONE","Asia/Tehran");//DateTimeZone
	// Set Default TimeZone
    date_default_timezone_set(SHDATE_TZONE);
	
	$_SERVER['REQUEST_STIME']= $_SERVER['REQUEST_SHTIME']= $_SERVER['REQUEST_TIME']+SHDATE_TSERVER;
	$_SERVER['REQUEST_STIME_FLOAT'] = $_SERVER['REQUEST_SHTIME_FLOAT'] = $_SERVER['REQUEST_TIME_FLOAT']+SHDATE_TSERVER;
    /**
    *    Language words Software
    */
    define("SHDATE_LWORD",'en_US');
    
    define("SHDATE_FIRST_DAY_OF_WEEK",0); // 0 = Saturday | 6 = Friday
	
	