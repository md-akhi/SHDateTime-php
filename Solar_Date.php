<?php
    /**
    * In The Name Of God
    * @package Date and Time Related Extensions { Hijri Shamsi, Solar }
    * @author  Mohammad Amanalikhani ( MD AKHI )
    * @link    http://akhi.ir/
    * @copyright   Copyright (C) 2014 - 2030 Open Source Matters,Inc. All right reserved.
    * @license http://www.php.net/license/3_0.txt  PHP License 3.0
    * @version Release: 0.5.0
    */
	include_once "Solar_Base.php";
	
	class SolarDate extends SolarDate_CALC{
		
		private static function dateBase($format,$timestamp=false,$is_gmt=false/* ,DateTime $DateTime=null */){
			$tformat = 'Y=n=j=w=H';
			/* $is_datetime = is_object($DateTime);
			if($is_datetime)
				$strdate = $DateTime->format($tformat);
			else */if($is_gmt){
				$timestamp = self::time($timestamp);
				$strdate = gmdate($tformat,$timestamp);
			}else{
				$timestamp = self::time($timestamp);
				$strdate = date($tformat,$timestamp);
			}
			sscanf($strdate,'%d=%d=%d=%d=%d',$gy,$gm,$gd,$gdow,$H);
			list($jy,$jm,$jd)=self::gregorianToSolar($gm,$gd,$gy);
			$jdow = self::GDowToSDow($gdow);//self::dayOfWeek($jy,$jm,$jd);
			$flen=strlen($format);
			$string='';
			for ($i=0; $i < $flen; $i++){
				switch ($format[$i]){
					case '\\':$string .= $format[++$i];break;
					/* day */
					case 'd':$string .= sprintf("%02d",$jd);break;
					case 'D':$string .= self::dayShortNames($jdow);break;
					case 'j':$string .= $jd;break;
					case 'l'/* lowercase 'L' */:$string .= self::dayFullNames($jdow);break;
					/* ISO-8601 numeric representation of the day of the week */
					case 'N':$string .= $jdow+1;/* added in PHP 5.1.0 */break;
					case 'S':$string .= self::suffixNames($jd);break;
					case 'w':$string .= $jdow;break;
					case 'z':$string .= self::dayOfYear($jy,$jm,$jd);break;
					/* week */
					case 'W':/* ISO-8601 week number of year, weeks starting on Saturday */
						if(!$iso_week)
							list($iso_year,$iso_week) = self::weekOfYear($jy,$jm,$jd);
						$string .= $iso_week;/* added in PHP 4.1.0 */break;
					/* month */
					case 'F':$string .= self::monthFullNames($jm);break;
					case 'm':$string .= sprintf("%02d",$jm);break;
					case 'M':$string .= self::monthShortNames($jm);break;
					case 'n':$string .= $jm;break;
					case 't':$string .= self::daysInMonth($jy,$jm);break;
					/* year */
					case 'L':$string .= self::isLeapBase($jy);break;
					case 'y':$string .= sprintf('%02d',$jy % 100);break;
					case 'Y':$string .= $jy;break;
					case 'o':/* ISO-8601 week numbering year. @see W */
						if(!$iso_year)
							list($iso_year,$iso_week) = self::weekOfYear($jy,$jm,$jd);
						$string .= $iso_year;/* added in PHP 5.1.0 */break;
					/* time */
					case 'a':$string .= self::meridienShortNames($H);break;
					case 'A':$string .= self::meridienFullNames($H);break;
					case 'H':$string .= sprintf('%02d',$H);break;
					case 'G':$string .= $H;break;
					/* full date/time */
					case 'c':/* ISO 8601 date = JDATE_ATOM(Y-m-d\TH:i:sP) */
						/* if($is_datetime)
							list($m,$s,$P) = explode('=',$DateTime->format('i=s=P'));
						else */if($is_gmt)
							list($m,$s,$P) = explode('=',gmdate('i=s=P',$timestamp));
						else
							list($m,$s,$P) = explode('=',date('i=s=P',$timestamp));
						$string .= sprintf('%04d-%02d-%02dT%02d:%02d:%02d%s',$jy,$jm,$jd,$H,$m,$s,$P);break;
					case 'r':/* » RFC 2822 formatted date = JDATE_RFC2822(D, d M Y H:i:s O) */
						/* if($is_datetime)
							list($m,$s,$O) = explode('=',$DateTime->format('i=s=O'));
						else */if($is_gmt)
							list($m,$s,$O) = explode('=',gmdate('i=s=O',$timestamp));
						else
							list($m,$s,$O) = explode('=',date('i=s=O',$timestamp));
						$string .= sprintf('%s, %02d %s %04d %02d:%02d:%02d %s',self::dayShortNames($jdow),$jd,self::monthShortNames($jm),$jy,$H,$m,$s,$O);break;
					/* time */
					// case 'h':
					// case 'g':
					// case 'i':
					// case 's':
					// case 'B':
					// case 'u'/* added in PHP 5.2.2 */:
					// case 'v'/* added in PHP 7.0.0 */:
					/* timezone */
					// case 'O':
					// case 'P':/* added in PHP 5.1.3 */
					// case 'e'/* added in PHP 5.1.0 */:
					// case 'I'/* capital i */:
					// case 'T':
					// case 'Z':
					/* Time Stamp */
					// case 'U':
					default:
						/* if($is_datetime)
							$string .= $DateTime->format($format[$i]);
						else */if($is_gmt)
							$string .= gmdate($format[$i],$timestamp);
						else
							$string .= date($format[$i],$timestamp);
					break;
				}
			}
			return $string;
		}
		/* public static function __DateTime_format__($format,DateTime $DateTime){
			return SolarDate::dateBase($format,false,false,$DateTime);
		} */
		public static function date($format,$timestamp=false){
			return self::dateBase($format,$timestamp,false);
		}
		public static function gmdate($format,$timestamp=false){
			return self::dateBase($format,$timestamp,true);
		}

		private static function strFTimeBase($format,$timestamp=false,$is_gmt=false){
			$timestamp = self::time($timestamp);
			$tformat = '%Y=%m=%e=%w=%k';
			if($is_gmt)
				$strdate = gmstrftime($tformat,$timestamp);
			else
				$strdate = strftime($tformat,$timestamp);
			sscanf($strdate,'%d=%d=%d=%d=%d',$gy,$gm,$gd,$gdow,$k);
			list($jy,$jm,$jd)=self::gregorianToSolar($gm,$gd,$gy);
			$jdow = self::GDowToSDow($gdow);//self::dayOfWeek($jy,$jm,$jd);
			$flen = strlen($format);
			$string = '';
			for($i=0; $i < $flen; $i++){
				if($format[$i] == '%'){
					switch($format[++$i]){
						/* Day */
						case 'a':$string .= self::dayShortNames($jdow);break;
						case 'A':$string .= self::dayFullNames($jdow);break;
						case 'd':$string .= sprintf("%02d",$jd);break;
						case 'e':$string .= sprintf("%2d",$jd);break;
						case 'j':$string .= sprintf("%03d",self::dayOfYear($jy,$jm,$jd)+1);break;
						case 'u':$string .= $jdow+1;break;
						case 'w':$string .= $jdow;break;
						/* Week */
	                    case 'U':
							if(!$iso_week)
								list($iso_year,$iso_week) = self::weekOfYear($jy,$jm,$jd);
							$string .= $iso_week;break;
						case 'V':
							if(!$iso_week)
								list($iso_year,$iso_week) = self::weekOfYear($jy,$jm,$jd);
							$string .= sprintf("%02d",$iso_week);break;
	                    case 'W':
							if(!$iso_week)
								list($iso_year,$iso_week) = self::weekOfYear($jy,$jm,$jd);
							/*
							if($iso_week > 51 && $jd<4){$iso_week = 0;}
							*/
							$string .= $iso_week;break;
						/* Month */
						case 'b':
						case 'h':$string .= self::monthShortNames($jm);break;
						case 'B':$string .= self::monthFullNames($jm);break;
						case 'm':$string .= sprintf("%02d",$jm);break;
						/* Year */
						case 'C':$string .= (int)($jy/100)+1;break;
						case 'g':
							if(!$iso_year)
								list($iso_year,$iso_week) = self::weekOfYear($jy,$jm,$jd);
							$string .=  sprintf("%02d",$iso_year%100);break;
						case 'G':
							if(!$iso_year)
								list($iso_year,$iso_week) = self::weekOfYear($jy,$jm,$jd);
							$string .= sprintf("%04d",$iso_year);break;
						case 'y':$string .= sprintf("%02d",$jy%100);break;
						case 'Y':$string .= $jy;break;
						/* Time */
						case 'H':$string .= sprintf("%02d",$k);break;
						case 'k':$string .= $k;break;
						case 'p':$string .= self::meridienFullNames($k);break;
						case 'P':$string .= self::meridienShortNames($k);break;
						case 'r'://Same as "%I:%M:%S %p"
							if($is_gmt)
								list($I,$M,$S) = explode('=',gmstrftime('%I=%M=%S',$timestamp));
							else
								list($I,$M,$S) = explode('=',strftime('%I=%M=%S',$timestamp));
							$string .= sprintf('%02d:%02d:%02d %s',$I,$M,$S,self::meridienFullNames($k));
							break;
						/* Time and Date Stamps */
						case 'c':
							if($is_gmt)
								list($M,$S) = explode('=',gmstrftime('%M=%S',$timestamp));
							else
								list($M,$S) = explode('=',strftime('%M=%S',$timestamp));
							$string .= sprintf('%s %02d %s %04d %02d:%02d:%02d',self::dayShortNames($jdow),$jd,self::monthShortNames($jm),$jy,$k,$M,$S);
							break;
						case 'D':$string .= sprintf("%02d/%02d/%02d",$jy%100,$jm,$jd);break;
						case 'F':$string .= sprintf("%04d-%02d-%02d",$jy,$jm,$jd);break;
						case 'x':$string .= sprintf("%02d/%02d/%02d",$jd,$jm,$jy%100);break;
						// /* Time */
						// case 'I':
						// case 'l':
						// case 'M':
						// case 'S':
						// case 'R':
						// case 'T':
						// case 'X':
						// case 'z':
						// case 'Z':
						// case 's':
						// /* Miscellaneous */
						// case 'n':
						// case 't':
						// case '%':
						default:
							if($is_gmt)
								$string .= gmstrftime('%'.$format[$i],$timestamp);
							else
								$string .= strftime('%'.$format[$i],$timestamp);
						break;
					}
				}else{
					if($is_gmt)
						$string .= gmstrftime($format[$i],$timestamp);
					else
						$string .= strftime($format[$i],$timestamp);
				}
			}
			return $string;
		}
		/**
		* Format a local time/date according to locale settings
		* @param   string  $format  The following characters are recognized in the format parameter string.
		* @param   int  $timestamp  The optional timestamp parameter is an integer Unix timestamp that defaults to the current local time if a timestamp is not given.In other words,it defaults to the value of jtime().
		* @return  string  A string formatted according to the given format string using the given timestamp or the current local time if no timestamp is given.
		* @see self::strFTimeBase().
		* @since   1.0
		*/
		public static function strftime($format,$timestamp=false){
			return self::strFTimeBase($format,$timestamp,false);
		}
		/**
		* Format a GMT/UTC time/date according to locale settings
		* @param   string  $format  See description in strfjtime().
		* @param   int  $timestamp  See description in strfjtime().
		* @return  string  A string formatted according format using the given timestamp or the current local time if no timestamp is given.
		* @see JDate::php_strfjtime().
		* @since   1.0
		*/
		public static function gmstrftime($format,$timestamp=false){
			return self::strFTimeBase($format,$timestamp,true);
		}
		public static function strfgmtime($format,$timestamp=false){
			return self::strFTimeBase($format,$timestamp,true);
		}
		/**
		*	@see Doc method mktime|gmmktime
		*/
		private static function mkTimeBase($h=false,$i=false,$s=false,$jd=false,$jm=false,$jy=false,$is_gmt=false){//(!(is_numeric($h)||is_numeric($i)||is_numeric($s)||is_numeric($jy)||is_numeric($jm)||is_numeric($jd)))
			if(func_num_args() == 0){
				if($is_gmt)
					return gmmktime();
				return mktime();
			}
			list($h,$i,$s,$jy,$jm,$jd) = self::numsVal($h,$i,$s,$jy,$jm,$jd);
			if($is_gmt)
				$getdate = self::getgmdate();
			else
				$getdate = self::getdate();
			if(!is_numeric($h))//0-23
				$h = $getdate['hours'];
			if(!is_numeric($i))//0-59
				$i = $getdate['minutes'];
			if(!is_numeric($s))//0-59
				$s = $getdate['seconds'];
			if(!$jy)//1-*
				$jy = $getdate['year'];
			if(!$jm)//1-12
				$jm = $getdate['mon'];
			if(!$jd)//1-31
				$jd = $getdate['mday'];
			list($gm,$gd,$gy) = self::solarToGregorian($jy,$jm,$jd);
			if($is_gmt)
				return gmmktime($h,$i,$s,$gm,$gd,$gy);
			return mktime($h,$i,$s,$gm,$gd,$gy);
		}
		/**
		* Get Unix timestamp for a date
		* @param   int  $h  The number of the hour.
		* @param   int  $m  The number of the minute.
		* @param   int  $s  The number of seconds.
		* @param   int  $jd  The number of the day.
		* @param   int  $jm  The number of the month.
		* @param   int  $jy  The number of the year.
		* @return  int|bool  The Unix timestamp of the arguments given. If the arguments are invalid,the function returns FALSE .
		* @since   2.0
		*/
		public static function mktime($h=false,$i=false,$s=false,$jd=false,$jm=false,$jy=false){
			return self::mkTimeBase($h,$i,$s,$jd,$jm,$jy,false);
		}
		/**
		* Get Unix timestamp for a GMT date
		* @param   int  $h  The number of the hour.
		* @param   int  $m  The number of the minute.
		* @param   int  $s  The number of seconds.
		* @param   int  $jd  The number of the day.
		* @param   int  $jm  The number of the month.
		* @param   int  $jy  The number of the year.
		* @return  int|bool  The Unix timestamp of the arguments given. If the arguments are invalid,the function returns FALSE .
		* @since   2.0
		*/
		public static function gmmktime($h=false,$i=false,$s=false,$jd=false,$jm=false,$jy=false){
			return self::mkTimeBase($h,$i,$s,$jd,$jm,$jy,true);
		}
		public static function mkgmtime($h=false,$i=false,$s=false,$jd=false,$jm=false,$jy=false){
			return self::mkTimeBase($h,$i,$s,$jd,$jm,$jy,true);
		}

		public static function time($timestamp=false,$add_tserver=false){
			if(is_numeric($timestamp)){
				$timestamp = self::numsVal($timestamp);
				if($add_tserver)
					$timestamp += self::TSERVER;
				return $timestamp;
			}
			return time()+self::TSERVER;
		}
		
		/**
		* Format a local time/date as integer
		* @param   string  $format  The following characters are recognized in the format parameter string.
		* @param   int  $timestamp  The optional timestamp parameter is an integer Unix timestamp that defaults to the current local time if a timestamp is not given.
		*                                                In other words,it defaults to the value of jtime().@return int  an integer.
		* @since   2.0
		*/
		public static function idate($format,$timestamp=false){
			if(strlen($format)>1)return false;
			$getdate = self::getdate($timestamp);
			switch($format){
				/* time */
				case 'g':
				case 'h':
				case 'G':
				case 'H':
				case 'i':
				case 's':
				/* timezone */
				case 'I':
				case 'Z':
				/* Swatch Beat / Internet Time */
				case 'B':
				case 'U':
					return idate($format,$getdate[0]);
				/* day */
				case 'j':
				case 'd':return $getdate[mday];
				/* month */
				case 'n':
				case 'm':return $getdate[mon];
				/* time */
				case 'w':return $getdate[wday];
				case 'z':return $getdate[yday];
				/* week */
				case 'W':return self::weekOfYear($getdate[year],$getdate[mon],$getdate[mday])[1];
				case 't':return self::daysInMonth($getdate[year],$getdate[mon]);
				/* year */
				case 'L':return self::isLeapBase($getdate[year]);
				case 'y':return $getdate[year]%100;
				case 'Y':return $getdate[year];
				default:
					return false;
			}
		}

		/**
		* Get date/time information
		* @param   int  $timestamp  The optional timestamp parameter is an integer Unix timestamp that defaults to the current local time if a timestamp is not given. In other words,it defaults to the value of jtime().
		* @return  array  an associative array of information related to the timestamp.
		* @since   3.0
		*/
		private static function getDateBase($timestamp=false, $is_gmt=false){
			$timestamp = self::time($timestamp);
			if($is_gmt)
				sscanf(gmdate('n=j=Y=H=i=s=w',$timestamp),'%d=%d=%d=%d=%d=%d=%d',$gm,$gd,$gy,$H,$i,$s,$gdow);
			else
				list($s,$i,$H,$gd,$gdow,$gm,$gy) = array_values(getdate($timestamp));
			list($jy,$jm,$jd)=self::gregorianToSolar($gm,$gd,$gy);
			$jdow = self::GDowToSDow($gdow);//self::dayOfWeek($jy,$jm,$jd);
			return array(
			'seconds' => $s,
			'minutes' => $i,
			'hours' => $H,
			'mday' => $jd,
			'wday' => $jdow,
			'mon' => $jm,
			'year' => $jy,
			'yday' => self::dayOfYear($jy,$jm,$jd),
			'weekday' => self::dayFullNames($jdow),
			'month' => self::monthFullNames($jm),
			0 => $timestamp
			);
		}
		public static function getdate($timestamp=false){
			return self::getDateBase($timestamp,false);
		}
		public static function getgmdate($timestamp=false){
			return self::getDateBase($timestamp,true);
        }
		/*
		* Get the local time
		* @param   int  $timestamp  The optional timestamp parameter is an integer Unix timestamp that defaults to the current local time if a timestamp is not given. In other words,it defaults to the value of jtime().
		* @param   bool  $is_associative  If set to FALSE,numerically indexed array. If set to TRUE,associative array containing all.
		* @return  array  Numerically indexed array or associative array containing all.
		* @since   3.0
		*/
		public static function localtime($timestamp=false,$is_associative=false){
			list($tm_sec,$tm_min,$tm_hour,$tm_mday,$tm_mon,$tm_year,$tm_wday,$tm_yday,$tm_isdst) = localtime(self::time($timestamp));
			list($jy,$jm,$jd) = self::gregorianToSolar($tm_mon+1,$tm_mday,$tm_year+1900);
			if($is_associative)
				return array(
					'tm_sec' => $tm_sec,
					'tm_min' => $tm_min,
					'tm_hour' => $tm_hour,
					'tm_mday' => $jd,
					'tm_mon' => $jm-1,
					'tm_year' => $jy-1300,
					'tm_wday' => self::GDowToSDow($tm_wday),
					'tm_yday' => self::dayOfYear($jy,$jm,$jd),
					'tm_isdst' => $tm_isdst
				);
			return array(
				$tm_sec,
				$tm_min,
				$tm_hour,
				$jd,
				$jm-1,
				$jy-1300,
				self::GDowToSDow($tm_wday),
				self::dayOfYear($jy,$jm,$jd),
				$tm_isdst
			);
		}
		/*
		* Get current time
		* @param   bool  $return_float  When set to TRUE,a float instead of an array is returned.
		* @return  array  By default an array. If return_float is set,then a float.
		* @since   1.0
		*/
		public static function gettimeofday($return_float=false){
			if ($return_float)
				return gettimeofday(true)+self::TSERVER;
			$gettimeofday = gettimeofday(false);
			return array(
				'sec' => $gettimeofday['sec']+self::TSERVER,
				'usec' => $gettimeofday['usec'],
				'minuteswest' => $gettimeofday['minuteswest'],
				'dsttime' => $gettimeofday['dsttime'],
				'minuteseast' => -$gettimeofday['minuteswest']
			);
		}
		/*
		* Return current Unix timestamp with microseconds
		* @param   bool  $get_as_float  If used and set to TRUE, microtime() will return a float instead of a string, as described in the return values section below.
		* @return  array  If get_as_float is set to TRUE, then microtime() returns a float, which represents the current time in seconds since the Unix epoch accurate to the nearest microsecond.
		* @since   1.0
		*/
		public static function microtime($get_as_float=false){
			if($get_as_float)
				return microtime(true)+self::TSERVER;
			sscanf(microtime(false),'%f %f',$usec, $sec);
			return sprintf('%.8f %f',$usec,$sec+self::TSERVER);
		}
		/**
		* Validate a date
		* @param   int  $h  Year of the date.
		* @param   int  $i  Month of the date.
		* @param   int  $s  Day of the date.
		* @return  bool  TRUE if the date given is valid; otherwise returns FALSE.
		* @since   1.0
		*/
		public static function checkdate($jy,$jm,$jd){
			if(!self::isNums($jy,$jm,$jd)[0])return null;
			list($jy,$jm,$jd) = self::numsVal($jy,$jm,$jd);
			return !($jy<1||$jy>3500000||$jm<1||$jm>12||$jd<1||$jd>self::daysInMonth($jy,$jm));
		}
		/**
		* Validate a time
		* @param   int  $h  Hour of the time.
		* @param   int  $i  Minute of the time.
		* @param   int  $s  Second of the time.
		* @return  bool  TRUE if the time given is valid; otherwise returns FALSE.
		* @since   1.0
		*/
		private static function checkTimeBase($h,$i,$s,$is_H12 = false){
			if($is_H12)
				return !($h<0||$h>11||$i<0||$i>59||$s<0||$s>59);
			return !($h<0||$h>23||$i<0||$i>59||$s<0||$s>59);
		}
		public static function checktime($h24,$i,$s){
			if(!self::isNums($h24,$i,$s)[0])return null;
			list($h24,$i,$s) = self::numsVal($h24,$i,$s);
			return self::checkTimeBase($h24,$i,$s,false);
		}
		public static function checktime12($h12,$i,$s){
			if(!self::isNums($h12,$i,$s)[0])return null;
			list($h12,$i,$s) = self::numsVal($h12,$i,$s);
			return self::checkTimeBase($h12,$i,$s,true);
		}
		/**
		* Validate a week
		* @param   int  $h  ISO Year of the week.
		* @param   int  $i  ISO Week of the week.
		* @param   int  $s  ISO Day(dow) of the week.
		* @return  bool  TRUE if the week given is valid; otherwise returns FALSE.
		* @since   1.0
		*/
		public static function checkweek($iy,$iw,$id = 1){
			if(!self::isNums($iy,$iw,$id)[0])return null;
			list($iy,$iw,$id) = self::numsVal($iy,$iw,$id);
			return !($iy<1||$jy>3500000||$iw<1||$iw>self::weeksInYear($iy)||$id<1||$id>7);
		}
		
		/**
		* Whether it's a leap year
		* @param   int  $jy  The number of the year.
		* @param   int  $leaps  All leap years 1-now.
		* @return  int  1 if it is a leap year,0 otherwise.
		* @see | reference solar>    http://www.iranboom.ir/iranshahr/jostar/9707-dar-amad-bar-kabise-giri.html  Zya'aldyn Torabi
		* @since   4.0
		*/
		public static function isleap($jy){
			if(!self::isNums($jy)[0]||$jy<1)return null;
			$jy = self::numsVal($jy);
			return (bool)self::isLeapBase($jy);
		}
		
		private static function MkTime2Base($h=false,$i=false,$s=false,$jd=false,$jm=false,$jy=false,$is_gmt=false){//(!(is_numeric($h)||is_numeric($i)||is_numeric($s)||is_numeric($jy)||is_numeric($jm)||is_numeric($jd)))//(func_num_args() == 0)
			if(func_num_args() == 0){
				if($is_gmt)
					return gmmktime();
				return mktime();
			}
			list($h,$i,$s,$jy,$jm,$jd) = self::numsVal($h,$i,$s,$jy,$jm,$jd);
			if($is_gmt){
				$getdate = self::getgmdate();
			}else{
				$getdate = self::getdate();
				$gmmktime = self::gmmktime($h,$i,$s,$jd,$jm,(self::isLeapBase($jy)?1395:1394));
				$tz = date('Z',$gmmktime);
			}
			if(!is_numeric($h))
				$h = $getdate['hours'];
			if(!is_numeric($i))
				$i = $getdate['minutes'];
			if(!is_numeric($s))
				$s = $getdate['seconds'];
			if(!$jy)
				$jy = $getdate['year'];
			if(!$jm)
				$jm = $getdate['mon'];
			if(!$jd)
				$jd = $getdate['mday'];
			//	0	=	1348/10/11	00:00:00	=	1970/01/01	00:00:00
			return self::numsVal(
				(
					(
						($jy-1)*365
						+self::isLeapBase($jy,1)
						+self::dayOfYear($jy,$jm,$jd)
					)*86400 /* 24*60*60 */
				)
				+($h*3600 /* 60*60 */)+($i*60)+$s-42531868800-$tz
			);
		}
		public static function mktime2($h=false,$i=false,$s=false,$jd=false,$jm=false,$jy=false){
			return self::MkTime2Base($h,$i,$s,$jd,$jm,$jy,false);
		}
		public static function mkgmtime2($h=false,$i=false,$s=false,$jd=false,$jm=false,$jy=false){
			return self::MkTime2Base($h,$i,$s,$jd,$jm,$jy,true);
		}
		
		private static function MkDateBase($timestamp=false, $is_gmt=false){
			//	0	=	1348/10/11	00:00:00	=	1970/01/01	00:00:00
			/* $intval = function($num){
				if($num<0)
					return ceil($num);
				return floor($num);
			}; */
			$ts = $timestamp = self::time($timestamp);
			$timestamp += 42531868800;
			/*
			*	86400 = 24*60*60
			*	3600  = 60*60
			*/
			$doy = $timestamp/86400;
			$jy = floor($doy/365)+1;
			$doy = $doy%365-self::isLeapBase($jy,1);
			list($jy,$jm,$jd) = self::rDayOfYear($jy,$doy);
			if(!$is_gmt){
				$gmmktime = self::gmmktime(false,false,false,$jd,$jm,(self::isLeapBase($jy)?1395:1394));
				$tz = date('Z',$gmmktime);
				$timestamp += $tz;
				$ts += $tz;
				$doy = $timestamp/86400;
				$tst = $timestamp - $doy;
				$jy = floor($doy/365)+1;
				$doy = $doy%365-self::isLeapBase($jy,1);
				list($jy,$jm,$jd) = self::rDayOfYear($jy,$doy);
			}
			$h = $ts/3600%24;
			$i = $ts/60%60;
			$s = $ts%60;
			return self::numsVal($jy,$jm,$jd,$h,$i,$s,$ts-$tz,$timestamp-$tz,$tz);
		}
		public static function mkdate($timestamp=false){
			return self::MkDateBase($timestamp,false);
		}
		public static function mkgmdate($timestamp=false){
			return self::MkDateBase($timestamp,true);
		}

		protected static function sm_solstice($jdoy,$solstice=false){//    Summer Solstice        ||    winter Solstice    |    Midwinter
			if($jdoy == 245 &&!$solstice)return true;/* m=8,d=30 *///     طولاني ترين شب - یلدا
			elseif($jdoy == 93&&$solstice)return true;/* m=4,d=1 *///    طولاني ترين روز - تموز
			return false;
		}

		/* private static function millesimal($jy){//1000
			return (int)($jy/1000)+1;
		}
		private static function century($jy){//100
			return (int)($jy/100)+1;
		}
		private static function decade($jy){//10
			return (int)(($jy%100)/10);
		}
		private static function season($jm){//4
			return (int)($jm/3.1)+1;
		} */
		
	}
    
	class SDate extends SolarDate{}
	class JDate extends SolarDate{}
	
	function jdate($format,$timestamp=false){
        return SolarDate::date($format,$timestamp);
    }
    function sdate($format,$timestamp=false){
        return SolarDate::date($format,$timestamp);
    }
    function jgmdate($format,$timestamp=false){
        return SolarDate::gmdate($format,$timestamp);
    }
    function sgmdate($format,$timestamp=false){
        return SolarDate::gmdate($format,$timestamp);
    }
	/**
    * Get Unix timestamp for a date
    * @param   int  $h  The number of the hour.
    * @param   int  $m  The number of the minute.
    * @param   int  $s  The number of seconds.
    * @param   int  $jm  The number of the month.
    * @param   int  $jd  The number of the day.
    * @param   int  $jy  The number of the year.
    * @param   int  $is_dst  1 if the time is during daylight savings time (DST),0 if it is not,or -1 (the default) if it is unknown whether the time is within daylight savings time.
    * @return  int  The Unix timestamp of the arguments given. If the arguments are invalid,the function returns FALSE .
    * @since   1.0
    */
    function jmktime($h=false,$m=false,$s=false,$jd=false,$jm=false,$jy=false){
        return SolarDate::mktime($h,$m,$s,$jd,$jm,$jy);
    }
    function smktime($h=false,$m=false,$s=false,$jd=false,$jm=false,$jy=false){
        return SolarDate::mktime($h,$m,$s,$jd,$jm,$jy);
    }
	/**
    * Get Unix timestamp for a GMT date
    * @param   int  $h  The number of the hour.
    * @param   int  $m  The number of the minute.
    * @param   int  $s  The number of seconds.
    * @param   int  $jm  The number of the month.
    * @param   int  $jd  The number of the day.
    * @param   int  $jy  The number of the year.
    * @param   int  $is_dst  Parameters always represent a GMT date so is_dst doesn't influence the result.
    * @return  int  a integer Unix timestamp.
    * @since   1.0
    */
    function jgmmktime($h=false,$m=false,$s=false,$jd=false,$jm=false,$jy=false){
        return SolarDate::gmmktime($h,$m,$s,$jd,$jm,$jy);
    }
    function sgmmktime($h=false,$m=false,$s=false,$jd=false,$jm=false,$jy=false){
        return SolarDate::gmmktime($h,$m,$s,$jd,$jm,$jy);
    }
	function jstrftime($format,$timestamp=false){
		return SolarDate::strftime($format,$timestamp);
	}
	function sstrftime($format,$timestamp=false){
		return SolarDate::strftime($format,$timestamp);
	}
	function jgmstrftime($format,$timestamp=false){
		return SolarDate::gmstrftime($format,$timestamp);
	}
	function sgmstrftime($format,$timestamp=false){
		return SolarDate::gmstrftime($format,$timestamp);
	}
   /**
    * Get date/time information
    * @param   int  $timestamp  The optional timestamp parameter is an integer Unix timestamp that defaults to the current local time if a timestamp is not given. In other words,it defaults to the value of jtime().
    * @return  array  an associative array of information related to the timestamp.
    * @since   1.0
    */
	function jgetdate($timestamp=false){
		return SolarDate::getdate($timestamp);
    }
	function sgetdate($timestamp=false){
		return SolarDate::getdate($timestamp);
    }
	function jgetgmdate($timestamp=false){
		return SolarDate::getgmdate($timestamp);
    }
	function sgetgmdate($timestamp=false){
		return SolarDate::getgmdate($timestamp);
    }
	/*
    * Get the local time
    * @param   int  $timestamp  The optional timestamp parameter is an integer Unix timestamp that defaults to the current local time if a timestamp is not given. In other words,it defaults to the value of jtime().
    * @param   bool  $is_associative  If set to FALSE,numerically indexed array. If set to TRUE,associative array containing all.
    * @return  array  Numerically indexed array or associative array containing all.
    * @since   1.0
    */
	function jlocaltime($timestamp=false,$is_associative=false){
		return SolarDate::localtime($timestamp,$is_associative);
    }
	function slocaltime($timestamp=false,$is_associative=false){
		return SolarDate::localtime($timestamp,$is_associative);
    }
    /*
    * Get current time
    * @param   bool  $return_float  When set to TRUE,a float instead of an array is returned.
    * @return  array  By default an array. If return_float is set,then a float.
    * @since   1.0
    */
	function jgettimeofday($return_float=false){
		return SolarDate::gettimeofday($return_float);
    }
	function sgettimeofday($return_float=false){
		return SolarDate::gettimeofday($return_float);
    }
    function jcheckdate($jy,$jm,$jd){
		return SolarDate::checkdate($jy,$jm,$jd);
    }
    function scheckdate($jy,$jm,$jd){
		return SolarDate::checkdate($jy,$jm,$jd);
    }
	/**
    * Validate a time
    * @param   int  $h  Hour of the time.
    * @param   int  $i  Minute of the time.
    * @param   int  $s  Second of the time.
    * @return  bool  TRUE if the date given is valid; otherwise returns FALSE.
    * @since   1.0
    */
	function jchecktime($h,$i,$s){
		return SolarDate::checktime($h,$i,$s);
    }
	function schecktime($h,$i,$s){
		return SolarDate::checktime($h,$i,$s);
    }
	/**
    * Format a local time/date as integer
    * @param   string  $format  The following characters are recognized in the format parameter string.
    * @param   int  $timestamp  The optional timestamp parameter is an integer Unix timestamp that defaults to the current local time if a timestamp is not given.
    *                                                In other words,it defaults to the value of jtime().@return int  an integer.
    * @since   1.0
    */
	function jidate($format,$timestamp=false){
		return SolarDate::idate($format,$timestamp);
    }
	function sidate($format,$timestamp=false){
		return SolarDate::idate($format,$timestamp);
    }
    function jfdate($format,$timestamp=false){
        return SolarDate::fdate($format,$timestamp);
    }
/* 	function jstrptime($date,$format){
		return SolarDate::strptime($date,$format);
	} */
    function jtime($timestamp=false){
		return SolarDate::time($timestamp);
	}
    function stime($timestamp=false){
		return SolarDate::time($timestamp);
	}
    function jmicrotime($get_as_float=false){
		return SolarDate::microtime($get_as_float);
	}
    function smicrotime($get_as_float=false){
		return SolarDate::microtime($get_as_float);
	}
	
