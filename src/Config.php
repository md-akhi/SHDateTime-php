<?php
    /**
	* In the name of Allah, the Beneficent, the Merciful.
	*
    * @package		Date and Time Related Extensions - SH(Solar Hijri, Shamsi Hijri, Iranian Hijri)
    * @author		Mohammad Amanalikhani <md.akhi.ir@gmail.com>
    * @link			http://git.akhi.ir/php/SHDateTime/		(Git)
    * @link			http://git.akhi.ir/php/SHDateTime/docs/	(wiki)
    * @license		https://www.gnu.org/licenses/agpl-3.0.en.html AGPL-3.0 License
    * @version		Release: 1.0.0
    */
	
    /**
    *	The time difference with the server
    */
    define("SHDATE_TSERVER", 0);
    /**
    *    Timezone identifier
    */
    define("SHDATE_TZONE","Asia/Tehran");//DateTimeZone
	// Set Default TimeZone
    date_default_timezone_set(SHDATE_TZONE);
	
	$_SERVER['REQUEST_SHTIME']= $_SERVER['REQUEST_TIME']+SHDATE_TSERVER;
	$_SERVER['REQUEST_SHTIME_FLOAT'] = $_SERVER['REQUEST_TIME_FLOAT']+SHDATE_TSERVER;
    /**
    *    Language words Software
    */
    define("SHDATE_LWORD",'en_US');
    
    define("SHDATE_FIRST_DAY_OF_WEEK",0); // 0 = Saturday | 6 = Friday
	
	