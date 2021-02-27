
<?php
    /**
	* 	In the name of Allah
	*
    * @package		Date and Time Related Extensions SH{ Shamsi Hijri, Solar Hijri, Iranian Hijri }
    * @author		Mohammad Amanalikhani (MD Amanalikhani, MD Akhi)
    * @link			http://git.akhi.ir/php/SHDateTime.php 	(Git)
    * @link			http://help.akhi.ir/php/SHDateTime.php 	(Help)
    * @license		https://www.gnu.org/licenses/agpl-3.0.en.html AGPL-3.0 License
    * @version		Release: 2.0.0-alpha.1
    */
	
	require_once("src/SHBase.php");

    /* Predefined Constants */
    define("CAL_SOLAR",CAL_NUM_CALS);
    define("CAL_MONTH_SOLAR_SHORT",(CAL_NUM_CALS-1)*2);
    define("CAL_MONTH_SOLAR_LONG",CAL_MONTH_SOLAR_SHORT+1);
    define("SCAL_NUM_CALS",CAL_NUM_CALS+1);// count cals add solar
    define("SHCAL_NUM_CALS",SCAL_NUM_CALS);
	
	class SHCalendar extends SHDateBase {
		
		const CAL_SOLAR = CAL_SOLAR;
		const CAL_MONTH_SOLAR_SHORT = CAL_MONTH_SOLAR_SHORT;
		const CAL_MONTH_SOLAR_LONG = CAL_MONTH_SOLAR_LONG;
		const CAL_NUM_CALS = SCAL_NUM_CALS;
		
		public static function jddayofweek($julianday,$mode=CAL_DOW_DAYNO){
			if($mode==CAL_DOW_LONG)//1
				return self::getDayFullNames((jddayofweek($julianday,0)+1)%7);
			elseif($mode==CAL_DOW_SHORT)//2
				return self::getDayShortNames((jddayofweek($julianday,0)+1)%7);
			return (jddayofweek($julianday,0)+1)%7;//0
		}

		public static function cal_from_jd($jd,$calendar=self::CAL_SOLAR){
			if($calendar==self::CAL_SOLAR){
				$getdate = self::getdate(jdtounix($jd));
				return array(
					'date'=>sprintf("%04d/%02d/%02d",$getdate['year'],$getdate['mon'],$getdate['mday']),
					'day'=>$getdate['mday'],
					'month'=>$getdate['mon'],
					'year'=>$getdate['year'],
					'dow'=>$getdate['wday'],
					'abbrevdayname'=>self::getDayShortNames($getdate['wday']),
					'dayname'=>$getdate['weekday'],
					'abbrevmonth'=>self::getMonthShortNames($getdate['mon']),
					'monthname'=>$getdate['month']
					);
			}
			return cal_from_jd($jd,$calendar);
		}

		public static function cal_to_jd($calendar=self::CAL_SOLAR,$y,$m,$d){
			if($calendar==self::CAL_SOLAR)
				return self::solartojd($y,$m,$d);
			return cal_to_jd($calendar,$m,$d,$y);
		}

		public static function cal_days_in_month($calendar=self::CAL_SOLAR,$y,$m){
			if($calendar==self::CAL_SOLAR)
				return self::getDaysInMonth($y,$m);
			return cal_days_in_month($calendar,$m,$y);
		}

		public static function jdmonthname($julianday,$mode=self::CAL_MONTH_SOLAR_LONG){
			if($mode==self::CAL_MONTH_SOLAR_LONG)
				return self::getMonthFullNames(explode('/',self::jdtosolar($julianday))[1]);
			if($mode==self::CAL_MONTH_SOLAR_SHORT)
				return self::getMonthShortNames(explode('/',self::jdtosolar($julianday))[1]);
			return jdmonthname($julianday,$mode);
		}

		public static function cal_info($calendar=-1){
			$cal_info=cal_info();
			if($calendar<0||$calendar==self::CAL_SOLAR){
				$class = self::getClassLanguage(self::LANG_WORD);
				$getdate = self::getdate();
				$cal_info[self::CAL_SOLAR]=array(
				  'months' =>  $class::MONTH_FULL_NAMES,
				  'abbrevmonths' => $class::MONTH_SHORT_NAMES,
				  'maxdaysinmonth' => self::getDaysInMonth($getdate["year"],$getdate["mon"]),
				  'calname' =>  'Solar',
				  'calsymbol' => 'CAL_SOLAR');
			}
			if($calendar<0)
				return $cal_info;
			return $cal_info[$calendar];
		}

		public static function jdtosolar($julianday){
			sscanf(jdtogregorian($julianday),'%d/%d/%d',$gm,$gd,$gy);
			list($shYear,$shMonth,$shDay)=self::gregoriantosolar($gm,$gd,$gy);
			return sprintf("%04d/%02d/%02d",$shYear,$shMonth,$shDay);
			/* $sdoy = $julianday-1948321;
			$shYear = (int)($sdoy/365)+1;
			$sdoy %= 365-self::is_leap_($shYear,1);
			return self::rday_of_year($shYear,$sdoy); */
		}

		public static function solartojd($shYear,$shMonth,$shDay){
			list($gm,$gd,$gy)=self::solartogregorian($shYear,$shMonth,$shDay);
			return gregoriantojd($gm,$gd,$gy);
			//return ($shYear-1)*365+self::is_leap_($shYear,1)+self::day_of_year($shYear,$shMonth,$shDay)+1948321;
		}
	}

    class_alias("SHCalendar","SCalendar");

    function sjddayofweek($julianday,$mode=CAL_DOW_DAYNO){
        return SHCalendar::jddayofweek($julianday,$mode);
    }
    function shjddayofweek($julianday,$mode=CAL_DOW_DAYNO){
        return SHCalendar::jddayofweek($julianday,$mode);
    }
    function scal_from_jd($jd,$calendar){
        return SHCalendar::cal_from_jd($jd,$calendar=CAL_SOLAR);
    }
    function shcal_from_jd($jd,$calendar){
        return SHCalendar::cal_from_jd($jd,$calendar=CAL_SOLAR);
    }
    function scal_to_jd($calendar,$y,$m,$d){
		return SHCalendar::cal_to_jd($calendar=CAL_SOLAR,$m,$d,$y);
    }
    function shcal_to_jd($calendar,$y,$m,$d){
		return SHCalendar::cal_to_jd($calendar=CAL_SOLAR,$m,$d,$y);
    }
    function scal_days_in_month($calendar=CAL_SOLAR,$y,$m){
		return SHCalendar::cal_days_in_month($calendar,$m,$y);
    }
    function shcal_days_in_month($calendar=CAL_SOLAR,$y,$m){
		return SHCalendar::cal_days_in_month($calendar,$m,$y);
    }
    function sjdmonthname($julianday,$mode=CAL_MONTH_SOLAR_LONG){
		return SHCalendar::jdmonthname($julianday,$mode);
    }
    function shjdmonthname($julianday,$mode=CAL_MONTH_SOLAR_LONG){
		return SHCalendar::jdmonthname($julianday,$mode);
    }
    function scal_info($calendar=-1){
        return SHCalendar::cal_info($calendar);
    }
    function shcal_info($calendar=-1){
        return SHCalendar::cal_info($calendar);
    }
    function jdtosolar($julianday){
        return SHCalendar::jdtosolar($julianday);
    }
    function solartojd($shYear,$shMonth,$shDay){
        return SHCalendar::solartojd($shYear,$shMonth,$shDay);
    }
