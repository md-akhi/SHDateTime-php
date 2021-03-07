<?php
    /**
	* In the name of Allah, the Beneficent, the Merciful.
	*
    * @package		Date and Time Related Extensions - SH(Solar Hijri, Shamsi Hijri, Iranian Hijri)
    * @author		Mohammad Amanalikhani <md.akhi.ir@gmail.com>
    * @link			http://git.akhi.ir/php/SHDateTime/		(Git)
    * @link			http://git.akhi.ir/php/SHDateTime/docs/	(wiki)
    * @license		https://www.gnu.org/licenses/agpl-3.0.en.html AGPL-3.0 License
    * @version		Release: 2.0.0-alpha.5
    */
	//require (__DIR__ . '/../vendor/autoload.php');
	require_once(__DIR__."/Config.php");
	require_once(__DIR__."/i18n/Word.php");

	class SHDateBase extends SHDateWord{
		//	Default Server
		const TSERVER = SHDATE_TSERVER;
		//	Default Time Zone
		const TZONE = SHDATE_TZONE;

		const DAYS_IN_MONTH = array(
			0,
			31,	// far
			31,	// ord
			31,	// kho
			31,	// tir
			31,	// amo
			31,	// sha
			30,	// meh
			30,	// aba
			30,	// aza
			30,	// dey
			30,	// bah
			29,	// esf
		);
		const DAYS_IN_MONTH_LEAP = array(
			0,
			31,	// far
			31,	// ord
			31,	// kho
			31,	// tir
			31,	// amo
			31,	// sha
			30,	// meh
			30,	// aba
			30,	// aza
			30,	// dey
			30,	// bah
			30,	// esf
		);        
		const DAY_OF_YEAR = array(
			0,
			0,		// far
			31,		// ord
			62,		// kho
			93,		// tir
			124,	// amo
			155,	// sha
			186,	// meh
			216,	// aba
			246,	// aza
			276,	// dey
			306,	// bah
			336		// esf
		);
		const DAYS_IN_YEAR = 365;
		const DAYS_IN_YEAR_LEAP = 366;

		const WEEKS_IN_YEAR = 52;
		const WEEKS_IN_YEAR_LEAP = 53;
		
		const FIRST_DAY_OF_WEEK = SHDATE_FIRST_DAY_OF_WEEK;	//	0 = Saturday | 6 = Friday
		
		/**
		* @see SHDate::date
		* @see SHDate::gmdate
		**/
		protected static function dates($format,$timestamp=false,$gmt=false,?DateTime $DateTime=null){
			if(!is_string($format)){
				throw new Exception("The value is not string");
			}
			$timestamp = self::time($timestamp);
			$ftemp = 'Y=n=j=w=H=h=i=s=O=P';
			if(is_object($DateTime))
				$ftstr = $DateTime->format($ftemp);
			elseif($gmt)
				$ftstr = gmdate($ftemp,$timestamp);
			else
				$ftstr = date($ftemp,$timestamp);
			sscanf($ftstr,'%d=%d=%d=%d=%s',$gYear,$gMonth,$gDay,$gdow,$ftstr);
			list($Hours,$hours,$minute,$second,$O,$P)=explode('=',$ftstr);
			list($shYear,$shMonth,$shDay)=self::gregoriantosolar($gMonth,$gDay,$gYear);
			$shdow = self::gDayOfWeeks($gdow);//self::getDayOfWeek($shYear,$shMonth,$shDay);
			$flen=strlen($format);
			$string='';
			$shLeap=$shdoy=$shdiy=$iso_year=$iso_week=false;
			for ($i=0; $i < $flen; $i++){
				switch ($format[$i]){
					case 'B':case 'u':case 'v':case 'e':case 'I':case 'T':case 'Z':
						if(is_object($DateTime))
							$string .= $DateTime->format($format[$i]);
						elseif($gmt)
							$string .= gmdate($format[$i],$timestamp);
						else
							$string .= date($format[$i],$timestamp);
						break;
					/* day */
					case 'd':$string .= sprintf("%02d",$shDay);break;
					case 'D':$string .= self::getDayShortNames($shdow);break;
					case 'j':$string .= $shDay;break;
					case 'l'/* lowercase 'L' */:$string .= self::getDayFullNames($shdow);break;
					/* ISO-8601 numeric representation of the day of the week */
					case 'N':$string .= $shdow+1;/* added in PHP 5.1.0 */break;
					case 'S':$string .= self::getSuffixNames($shDay);break;
					case 'w':$string .= $shdow;break;
					case 'z':$string .= self::getDayOfYear($shYear,$shMonth,$shDay);break;
					/* week */
					case 'W':/* ISO-8601 week number of year, weeks starting on Saturday */
						if(!$iso_week)
							list($iso_year,$iso_week) = self::getWeekOfYear($shYear,$shMonth,$shDay);
						$string .= $iso_week;/* added in PHP 4.1.0 */break;
					case 'o':/* ISO-8601 week-numbering year. @see W */
						if(!$iso_year)
							list($iso_year,$iso_week) = self::getWeekOfYear($shYear,$shMonth,$shDay);
						$string .= $iso_year;/* added in PHP 5.1.0 */break;
					/* month */
					case 'F':$string .= self::getMonthFullNames($shMonth);break;
					case 'm':$string .= sprintf("%02d",$shMonth);break;
					case 'M':$string .= self::getMonthShortNames($shMonth);break;
					case 'n':$string .= $shMonth;break;
					case 't':$string .= self::getDaysInMonth($shYear,$shMonth);break;
					/* year */
					case 'L':$string .= self::isLeaps($shYear);break;
					case 'y':$string .= sprintf('%02d',$shYear % 100);break;
					case 'Y':$string .= $shYear;break;
					/* time */
					case 'a':$string .= self::getMeridienShortNames((int)$Hours);break;
					case 'A':$string .= self::getMeridienFullNames((int)$Hours);break;
					case 'h':$string .= $hours;break;
					case 'g':$string .= (int)$hours;break;
					case 'H':$string .= $Hours;break;
					case 'G':$string .= (int)$Hours;break;
					case 'i':$string .= $minute;break;
					case 's':$string .= $second;break;
					//case 'B':$string .= $B;break;
					//case 'u':$string .= $u;/* added in PHP 5.2.2 */break;
					//case 'v':$string .= $v;/* added in PHP 7.0.0 */break;
					/* timezone */
					//case 'e':$string .= $e;/* added in PHP 5.1.0 */break;
					//case 'I'/* capital i */:$string .= $I;break;
					case 'O':$string .= $O;break;
					case 'P':$string .= $P;/* added in PHP 5.1.3 */break;
					//case 'T':$string .= $T;break;
					//case 'Z':$string .= $Z;break;
					/* full date/time */
					case 'c':/* ISO 8601 date = SDATE_ATOM(Y-m-d\TH:i:sP) */
						$string .= sprintf('%04d-%02d-%02dT%02d:%02d:%02d%s',$shYear,$shMonth,$shDay,$Hours,$minute,$second,$P);break;
					case 'r':/* » RFC 2822 formatted date = SDATE_RFC2822(D, d M Y H:i:s O) */
						$string .= sprintf('%s, %02d %s %04d %02d:%02d:%02d %s',self::getDayShortNames($shdow),$shDay,self::getMonthShortNames($shMonth),$shYear,$Hours,$minute,$second,$O);break;
					case 'U':$string .= $timestamp;break;
					/* Add	*/
					case '?':
						$i++;
						if(!$shLeap)$shLeap = self::isLeaps($shYear);
						if(!$shdoy)$shdoy = self::getDayOfYear($shYear,$shMonth,$shDay);
						if(!$shdiy)$shdiy = self::getDaysInYear($shYear);
						switch($format[$i]){
							case 'm':$string .= self::getMillesimal($shYear);break; // getMillesimal
							case 'c':$string .= self::getCentury($shYear);break; // getCentury
							case 'd':$string .= self::getDecade($shYear);break; // getDecade
							case 's':$string .= self::getSeason($shMonth);break;
							case 'z':$string .= (int)(($shdoy/$shdiy)*100)+1;break;
							case 'r':$string .= $shdiy-$shdoy;break;
							case 'R':$string .= (int)((($shdiy-$shdoy)/$shdiy)*100);break;
							case 'S':$string .= self::getSeasonFullNames($shMonth);break;
							case 'C':$string .= self::getConstellationsFullNames($shMonth);break;
							case 'A':$string .= self::getAnimalsFullNames($shYear);break;
							case 'L':$string .= self::getLeapFullNames($shLeap);break;

							case 'Y':$string .= self::toWord('+its',$shYear);break;
							case 'M':$string .= self::toWord('+its',$shMonth);break;
							case 'D':$string .= self::toWord('+its',$shDay);break;

							//case 'n':$string .= self::new_year(date('Y',$timestamp),$this->TimeZone->getOffset($this->DateTime));break;
							case 't':$string .= self::getMidSolstice($shMonth,$shDay);break;
							default:$string .= $format[$i];break;
						}
						break;
					case '\\':$i++;
					default:$string .= $format[$i];break;
				}
			}
			return $string;
		}

		/*
		* @see SHDate::strftime
		* @see SHDate::gmstrftime
		**/
		protected static function strftimes($format,$timestamp=false,$gmt=false){
			if(!is_string($format)){
				throw new Exception("The value is not string");
			}
			$timestamp = self::time($timestamp);
			$ftemp = '%Y=%m=%e=%w=%H=%k=%L=%l=%M=%R=%S=%T=%X=%z=%Z';
			if($gmt)
				$ftstr = gmstrftime($ftemp,$timestamp);
			else
				$ftstr = strftime($ftemp,$timestamp);
			sscanf($ftstr,'%d=%d=%d=%d=%s',$gYear,$gMonth,$gDay,$gdow,$ftstr);
			list($Hours,$k,$L,$l,$M,$R,$S,$T,$X,$z,$Z)=explode('=',$ftstr);
			list($shYear,$shMonth,$shDay)=self::gregoriantosolar($gMonth,$gDay,$gYear);
			$shdow = self::gDayOfWeeks($gdow);//self::getDayOfWeek($shYear,$shMonth,$shDay);
			$flen=strlen($format);
			$string='';
			$iso_year=$iso_week=false;
			for ($i=0; $i < $flen; $i++){
				if ($format[$i] == '%'){
					$i++;
					switch ($format[$i]){
						/* Day */
						case 'a':$string .= self::getDayShortNames($shdow);break;
						case 'A':$string .= self::getDayFullNames($shdow);break;
						case 'd':$string .= sprintf("%02d",$shDay);break;
						case 'e':$string .= (int)$shDay;break;
						case 'j':$string .= sprintf("%03d",self::getDayOfYear($shYear,$shMonth,$shDay)+1);break;
						case 'u':$string .= $shdow+1;break;
						case 'w':$string .= $shdow;break;
						/* Week */
	                    case 'U':
							if(!$iso_week)
								list($iso_week,$iso_year) = self::getWeekOfYear($shYear,$shMonth,$shDay);
							$string .= $iso_week-1;break;
						case 'V':
							if(!$iso_week)
								list($iso_week,$iso_year) = self::getWeekOfYear($shYear,$shMonth,$shDay);
							$string .= sprintf("%02d",$iso_week);break;
	                    case 'W':
							if(!$iso_week)
								list($iso_week,$iso_year) = self::getWeekOfYear($shYear,$shMonth,$shDay);
							$string .= $iso_week;break;
						case 'g':
							if(!$iso_year)
								list($iso_week,$iso_year) = self::getWeekOfYear($shYear,$shMonth,$shDay);
							$string .=  sprintf("%02d",$iso_year%100);break;
						case 'G':
							if(!$iso_year)
								list($iso_week,$iso_year) = self::getWeekOfYear($shYear,$shMonth,$shDay);
							$string .= sprintf("%04d",$iso_year);break;
						/* Month */
						case 'b':
						case 'h':$string .= self::getMonthShortNames($shMonth);break;
						case 'B':$string .= self::getMonthFullNames($shMonth);break;
						case 'm':$string .= sprintf("%02d",$shMonth);break;
						/* Year */
						case 'C':$string .= self::getCentury($shYear);break;
						case 'y':$string .= $shYear%100;break;
						case 'Y':$string .= $shYear;break;
						/* Time */
						case 'p':$string .= self::getMeridienFullNames((int)($k > 11));break;
						case 'P':$string .= self::getMeridienShortNames((int)($k > 11));break;
						case 'r':
							$format=substr_replace($format,'I:%M:%S %p',$i,1);
							$i--;$flen = strlen($format);break;
						case 'H':$string .= $Hours;break;
						case 'k':$string .= $k;break;
						case 'L':$string .= $L;break;
						case 'l':$string .= $l;break;
						case 'M':$string .= $M;break;
						case 'R':$string .= $R;break;
						case 'S':$string .= $S;break;
						case 'T':$string .= $T;break;
						case 'X':$string .= $X;break;
						case 'z':$string .= $z;break;
						case 'Z':$string .= $Z;break;
						/* Time and Date Stamps */
						case 'c':
							$format=substr_replace($format,'a %b %e %H:%i:%M %Y',$i,1);
							$i--;$flen = strlen($format);
							//$string .= sprintf('%s, %02d %s %04d %02d:%02d:%02d %s',self::getDayFullNames($shdow),$shDay,self::getMonthFullNames($shMonth),$shYear,$Hours,$minute,$second,$P);
							break;
						case 'x':$string .= sprintf("%02d/%02d/%02d",$shDay,$shMonth,$shYear%100);break;
						case 'D':$string .= sprintf("%02d/%02d/%02d",$shYear%100,$shMonth,$shDay);break;
						case 'F':$string .= sprintf("%04d-%02d-%02d",$shYear,$shMonth,$shDay);break;
						case 's':$string .= $timestamp;break;
						/* Miscellaneous */
						case 'n'://$string .= "\n";break;
						case 't'://$string .= "\t";break;
						case '%'://$string .= '%';break;
						default:$string .= '%'.$format[$i];break;
					}
				}
				else $string .= $format[$i];
			}
			return $gmt?gmstrftime($string,$timestamp):strftime($string,$timestamp);
		}
		
		/**
		* Parse about any English textual datetime description into a Unix timestamp
		* @param   string  $time  A date/time string. Valid formats are explained in Date and Time Formats.
		* @param   int  $now  The timestamp which is used as a base for the calculation of relative dates.
		* @return int  A timestamp on success,FALSE otherwise.
		* @since   2.0.0-alpha.5
		*/
		public static function strtotime($time_,$now=false){
			//	time_ = by del str step to step
			//	time = convert jalali to gregorian
			$time=$time_=preg_replace(array("/^\s+|\s+$/","/\s{2,}/","/[\t\r\n]/"),array('',' ','	'),strtolower($time_));
			$USymbols=array(
			'daysuf'=>'[sr]t|nd|th',
			'Dd'=>"sat|saturday|sun|sunday|mon|monday|tue|tuesday|wed|wednesday|thu|thursday|fri|friday",
			//'dd'=>'[0-2]?[0-9]|3[01]',
			'DD'=>'3[01]|[1-2][0-9]|0?[1-9]',// 02|2 - 31
			// find by serach array and str%12 OR|| return array(0...11)[str]
			'Mm'=>'far|farvardin|ord|ordibehesht|kho|khordad|tir|amo|mor|a?mordad|sha|shahrivar|mehr?|azar?|aban?|dey|bah|bahman|esf|esfand|i[vx]|viii|vii|vi|xii|xi|iii|ii|x|i|v',
			//'mm'=>'0?\d|1[0-2]',
			'MM'=>'1[0-2]|0?[1-9]',// 01|1 m
			'doy'=>'36[0-6]|3[0-5]\d|[1-2]\d{2}|0[1-9]\d|00[1-9]',// 365 doy
			'W'=>'5[0-3]|[1-4][0-9]|0[1-9]',// 52 w
			'y'=>'1[34]\d{2}|[34]\d{2}|\d{2}|\d',//\d{1,4}
			'yy'=>'\d{2}',// 96 y
			'YY'=>'1[34]\d{2}',// 1396 y
			'frac'=>'\.?(\d+)',// .123
			'meridian'=>'[ap]\.?m\.?',
			//'ss'=>'[0-5]?\d',
			'SS'=>'[0-5]?\d',// 8|08 s
			//'ii'=>'[0-5]?\d',
			'II'=>'[0-5]?\d',// 9|09 i
			'hh'=>'1[0-2]|0?[1-9]',// 12
			'HH'=>'2[0-4]|[01]?\d',// 24
			'space'=>'[ \t]',
			'tz'=>'[a-z]+(?:[_\/]([a-z]+))+|\(?([a-z]{4,6})\)?',
			'tzcorrection'=>"(gmt)?([+-])(1[0-2]|0?[1-9]):?([0-5]?\d)?",
			'number'=>"[+-]?\d+",// +12 | -12
			'dot'=>'[:\.-\/]',// :|.|/
			'daytext'=>"weekdays?",
			'ordinal'=>"first|second|third|fourth|fifth|sixth|seventh|eighth|ninth|tenth|eleventh|twelfth|next|last|previous|this",
			'reltext'=>"next|last|previous|this",
			'unit'=>"(?:(?:sec|second|min|minute|hour|day|fortnight|forthnight|month|year)s?)(?:week(?:day)?s?)"
			);

			$FPCOMPOUND=array(
				// COMPOUND
				'CLFormat'=>"({$USymbols["DD"]})\/({$USymbols["Mm"]})\/({$USymbols["YY"]}):({$USymbols["HH"]}):({$USymbols["II"]}):({$USymbols["SS"]}){$USymbols["space"]}({$USymbols["tzcorrection"]})",
				'EXIF'=>"({$USymbols["YY"]}):({$USymbols["MM"]}):({$USymbols["DD"]}) ({$USymbols["HH"]}):({$USymbols["II"]}):({$USymbols["SS"]})",
				//'ISOyearISOweek'=> "({$USymbols["YY"]})-?[w]({$USymbols["W"]})",
				'IyIwday'=>"({$USymbols["YY"]})(-?)[w]({$USymbols["W"]})(-?)([0-7]?)",
				'MySQL'=>"({$USymbols["YY"]})-({$USymbols["MM"]})-({$USymbols["DD"]}) ({$USymbols["HH"]}):({$USymbols["II"]}):({$USymbols["SS"]})",
				'MSSQL'=>"({$USymbols["hh"]}):({$USymbols["II"]}):({$USymbols["SS"]})[:\.](\d+)({$USymbols["meridian"]})", // TIME 12 Hour
				'WDDX'=>"({$USymbols["YY"]})-({$USymbols["MM"]})-({$USymbols["DD"]})[t]({$USymbols["HH"]}):({$USymbols["II"]}):({$USymbols["SS"]})",
				'SOAP'=>"({$USymbols["YY"]})-({$USymbols["MM"]})-({$USymbols["DD"]})[t]({$USymbols["HH"]}):({$USymbols["II"]}):({$USymbols["SS"]}){$USymbols["frac"]}?({$USymbols["tzcorrection"]})?",
				//'XMLRPCCompact'=>"({$USymbols["YY"]})({$USymbols["MM"]})({$USymbols["DD"]})[tT]({$USymbols["HH"]})({$USymbols["II"]})({$USymbols["SS"]})",
				'XMLRPC'=>"({$USymbols["YY"]})({$USymbols["MM"]})({$USymbols["DD"]})[t]({$USymbols["HH"]}):?({$USymbols["II"]}):?({$USymbols["SS"]})",
				//'UTs'=>"@(-?\d+)",
				'PostgreSQL'=>"({$USymbols["YY"]})\.?({$USymbols["doy"]})"
			);
			$inttostr_gm = function($num,$type = 1){
				return $type?array(1=>'jan','feb','mar','apr','may','jun','jul','aug','sep','sept','oct','nov','dec')[$num]:array(1=>'january','february','march','april','may','june','july','august','september','october','november','december')[$num];
			};
			$strtoint_jm = function($str){
				return array('far'=>1,'ord'=>2,'kho'=>3,'tir'=>4,'mor'=>5,'amo'=>5,'sha'=>6,'meh'=>7,'aba'=>8,'aza'=>9,'dey'=>10,'bah'=>11,'esf'=>12,'farvardin'=>1,'ordibehesht'=>2,'khordad'=>3,'tir'=>4,'mordad'=>5,'amordad'=>5,'shahrivar'=>6,'mehr'=>7,'aban'=>8,'azar'=>9,'dey'=>10,'bahman'=>11,'esfand'=>12)[$str];
			};

			foreach($FPCOMPOUND as $NPATTERN=>$VPATTERN)
				if(preg_match('#'.$VPATTERN.'#',$time_,$preg_match))
					$PMatchCOMPOUND[$NPATTERN]=array_filter($preg_match);
			if($PMatchCOMPOUND){
				$setCOMPOUND = 1;
				switch(array_keys($PMatchCOMPOUND)[0]){
					case 'CLFormat':
						$shYear=$PMatchCOMPOUND["CLFormat"][3];
						$shMonth=$strtoint_jm($PMatchCOMPOUND["CLFormat"][2]);
						$shDay=$PMatchCOMPOUND["CLFormat"][1];
						list($gYear,$gMonth,$gDay)=self::solartogregorian($shYear,$shMonth,$shDay);
						$time_=str_replace($PMatchCOMPOUND["CLFormat"][0],'',$time_);
						$time=str_replace($shDay.'/'.$PMatchCOMPOUND["CLFormat"][2].'/'.$shYear,$gDay.'/'.$gMonth.'/'.$gYear,$time);
						break;
					case 'EXIF':
						$shYear=$PMatchCOMPOUND["EXIF"][1];
						$shMonth=$PMatchCOMPOUND["EXIF"][2];
						$shDay=$PMatchCOMPOUND["EXIF"][3];
						list($gYear,$gMonth,$gDay)=self::solartogregorian($shYear,$shMonth,$shDay);
						$time_=str_replace($PMatchCOMPOUND["EXIF"][0],'',$time_);
						$time=str_replace($shYear.':'.$shMonth.':'.$shDay,$gYear.':'.sprintf('%02d',$gMonth).':'.sprintf('%02d',$gDay),$time);
						break;
					case 'IyIwday':
						$shYear=$PMatchCOMPOUND["IyIwday"][1];
						$jwoy=$PMatchCOMPOUND["IyIwday"][3];
						$shdow=isset($PMatchCOMPOUND["IyIwday"][5])?$PMatchCOMPOUND["IyIwday"][5]:1;
						list($shYear,$shMonth,$shDay)=self::getWeekOfDay($shYear,$jwoy,$shdow);
						list($gMonth,$gDay,$gYear)=self::solartogregorian($shYear,$shMonth,$shDay);
						$time_=str_replace($PMatchCOMPOUND["IyIwday"][0],'',$time_);
						$time=str_replace($PMatchCOMPOUND["IyIwday"][0],$gYear.'/'.$gMonth.'/'.$gDay,$time);
						break;
					case 'MySQL':
						$shYear=$PMatchCOMPOUND["MySQL"][1];
						$shMonth=$PMatchCOMPOUND["MySQL"][2];
						$shDay=$PMatchCOMPOUND["MySQL"][3];
						list($gMonth,$gDay,$gYear)=self::solartogregorian($shYear,$shMonth,$shDay);
						$time_=str_replace($PMatchCOMPOUND["MySQL"][0],'',$time_);
						$time=str_replace($shYear.'-'.$shMonth.'-'.$shDay,$gYear.'/'.$gMonth.'/'.$gDay,$time);
						break;
					/* case 'MSSQL': //////  go to time by do not syntax
						$hour=$PMatchCOMPOUND[MSSQL][1];
						$minute=$PMatchCOMPOUND[MSSQL][2];
						$second=$PMatchCOMPOUND[MSSQL][3];
						$frac=$PMatchCOMPOUND[MSSQL][5];
						$meridian=$PMatchCOMPOUND[MSSQL][6];
						$time=str_replace($PMatchCOMPOUND[MSSQL][0],'',$time);
						// change Y M D jalali to Y M D gregorian and set to time
						break; */
					case 'SOAP':
						list($date,$time)=explode('t',$PMatchCOMPOUND["SOAP"][0]);
						$shYear=$PMatchCOMPOUND["SOAP"][1];
						$shMonth=$PMatchCOMPOUND["SOAP"][2];
						$shDay=$PMatchCOMPOUND["SOAP"][3];
						list($gMonth,$gDay,$gYear)=self::solartogregorian($shYear,$shMonth,$shDay);
						$time_=str_replace($PMatchCOMPOUND["SOAP"][0],'',$time_);
						$time=str_replace($date.'t',$gYear.'-'.sprintf('%02d',$gMonth).'-'.sprintf('%02d',$gDay).'T',$time);
						break;
					case 'WDDX':
						list($date,$time)=explode('t',$PMatchCOMPOUND["WDDX"][0]);
						$shYear=$PMatchCOMPOUND["WDDX"][1];
						$shMonth=$PMatchCOMPOUND["WDDX"][2];
						$shDay=$PMatchCOMPOUND["WDDX"][3];
						list($gMonth,$gDay,$gYear)=self::solartogregorian($shYear,$shMonth,$shDay);
						$time_=str_replace($PMatchCOMPOUND["WDDX"][0],'',$time_);
						$time=str_replace($date.'t',$gYear.'-'.$gMonth.'-'.$gDay.'T',$time);
						break;
					case 'XMLRPC':
						list($date,$time)=explode('t',$PMatchCOMPOUND["XMLRPC"][0]);
						$shYear=$PMatchCOMPOUND["XMLRPC"][1];
						$shMonth=$PMatchCOMPOUND["XMLRPC"][2];
						$shDay=$PMatchCOMPOUND["XMLRPC"][3];
						list($gMonth,$gDay,$gYear)=self::solartogregorian($shYear,$shMonth,$shDay);
						$time_=str_replace($PMatchCOMPOUND["XMLRPC"][0],'',$time_);
						$time=str_replace($date.'t',$gYear.sprintf('%02d',$gMonth).sprintf('%02d',$gDay).'T',$time);
						break;
					case 'PostgreSQL':
						$shYear=$PMatchCOMPOUND["PostgreSQL"][1];
						$shdoy=$PMatchCOMPOUND["PostgreSQL"][2];
						list($shYearn,$shMonth,$shDay)=self::getDaysOfDay($shYear,$shdoy-1);
						list($gMonth,$gDay,$gYear)=self::solartogregorian($shYearn,$shMonth,$shDay);
						$time_=str_replace($PMatchCOMPOUND["PostgreSQL"][0],'',$time_);
						$time=str_replace($PMatchCOMPOUND["PostgreSQL"][0],$gYear.'/'.$gMonth.'/'.$gDay,$time);
						break;
					/* case 'UTs':
						$now=$PMatchCOMPOUND[UTs][1];
						$time=str_replace($PMatchCOMPOUND[UTs][0],'',$time);
						// change Y M D jalali to Y M D gregorian and set to time
						break; */
				}
			}


			$FPDATE=array(
				// DATE
				//Localized
				//'AMMDD'=>"({$USymbols["MM"]})(\/)({$USymbols["DD"]})",
				//'YYMMDD'=>"({$USymbols["YY"]})?(\/)?({$USymbols["MM"]})(\/)({$USymbols["DD"]})",
				//'FDYYMM'=>"({$USymbols["YY"]})(-)({$USymbols["MM"]})",
				//'yMD'=>"({$USymbols["y"]})(-)({$USymbols["MM"]})(-)({$USymbols["DD"]})",
				//'YYMMDD'=>"({$USymbols["YY"]})({$USymbols["MM"]})({$USymbols["DD"]})",
				//'YYyyMMDD'=>"({$USymbols["YY"]}|{$USymbols["yy"]})([\-\/])?({$USymbols["MM"]})([\-\/])?({$USymbols["DD"]})?",
				'signYYyyMMDD'=>"([+-]?)((?:{$USymbols["YY"]}|{$USymbols["yy"]}|{$USymbols["y"]})?)([\-\/]?)({$USymbols["MM"]})([\-\/]?)((?:{$USymbols["DD"]})?)",

				//'DDMM.yy'=>"({$USymbols["DD"]})([\.\t])({$USymbols["MM"]})(\.)({$USymbols["yy"]})",
				'DDMMYYyy'=>"({$USymbols["DD"]})([\.\t-])({$USymbols["MM"]})([\.-])({$USymbols["YY"]}|{$USymbols["yy"]})",

				//'DDMm'=> "({$USymbols["DD"]})([ \.\t-])({$USymbols["Mm"]})",
				//'MmYY'=>"({$USymbols["Mm"]})([ \.\t-])({$USymbols["YY"]})",
				//'DDMmy'=>"({$USymbols["DD"]})([ \.\t-]?)({$USymbols["Mm"]})([ \.\t-]?)({$USymbols["y"]})",
				'DDMmYY'=>"({$USymbols["DD"]})?([ \.\t-]?)({$USymbols["Mm"]})([ \.\t-]?)({$USymbols["YY"]}|{$USymbols["y"]})?",

				//'YYMm'=>"({$USymbols["YY"]})([ \.\t-])({$USymbols["Mm"]})",
				//'MmDD'=>"({$USymbols["Mm"]})([ \.\t-])({$USymbols["DD"]})(({$USymbols["daysuf"]}|[,\.\t ])+)?",
				'YYyMmD'=>"({$USymbols["YY"]}|{$USymbols["y"]})?([ \.\t-]?)({$USymbols["Mm"]})([ \.\t-]?)({$USymbols["DD"]})?[\,\.\t ]?({$USymbols["daysuf"]})?[\,\.\t ]?",

				//'YY'=>"({$USymbols["YY"]})",
				//'Mm'=>"({$USymbols["Mm"]})"
			);
			foreach($FPDATE as $NPATTERN=>$VPATTERN)
				if(preg_match('#'.$VPATTERN.'#',$time_,$preg_match))
					$PMatchDATE[$NPATTERN]=array_filter($preg_match);
			if($PMatchDATE&&!$setCOMPOUND){
				list($jsy,$jfy,$jfm,$jfd)=explode('=',self::dates("y=Y=m=d"));
				$SDATEcentury=(int)($jfy/100);
				$maxjyear=($jfy+30)%100;
				$minjyear=($jfy-69)%100;
				/*	strlen(y) == 2
				if	1410 result ?
				''
				(min <= y)?SDATEcentury*100+y:(SDATEcentury+1)*100+y
				*/
				$PMatchDATEkeys=array_keys($PMatchDATE);
				for($IDATE=0;$IDATE<count($PMatchDATE);$IDATE++)
					switch($PMatchDATEkeys[$IDATE]){
						case 'signYYyyMMDD':
							if(strlen($PMatchDATE["signYYyyMMDD"][2])<4&&$PMatchDATE["signYYyyMMDD"][3]=='-'&&$PMatchDATE["signYYyyMMDD"][5]=='-'){
								if(strlen($PMatchDATE["signYYyyMMDD"][2])>2)// strlen($shYear)==3
									$shYear=1000+$PMatchDATE["signYYyyMMDD"][2];
								elseif(strlen($PMatchDATE["signYYyyMMDD"][2])==2)// strlen($shYear)==2
									$shYear=$minjyear<=$PMatchDATE["signYYyyMMDD"][2]?$SDATEcentury*100+$PMatchDATE["signYYyyMMDD"][2]:($SDATEcentury+1)*100+$PMatchDATE["signYYyyMMDD"][2];
								elseif(strlen($PMatchDATE["signYYyyMMDD"][2])<2)// strlen($shYear)==1
									$shYear=(int)($jfy/10)*10+$PMatchDATE["signYYyyMMDD"][2];
								$shMonth=$PMatchDATE["signYYyyMMDD"][4];
								$shDay=$PMatchDATE["signYYyyMMDD"][6];
								list($gMonth,$gDay,$gYear)=self::solartogregorian($shYear,$shMonth,$shDay);
								$time_=str_replace($PMatchDATE["signYYyyMMDD"][0],'',$time_);
								$time=str_replace($PMatchDATE["signYYyyMMDD"][0],$gYear.'-'.$gMonth.'-'.$gDay,$time);
							}
							elseif($PMatchDATE["signYYyyMMDD"][3]=='-'&&$PMatchDATE["signYYyyMMDD"][5]=='-'){
								if($PMatchDATE["signYYyyMMDD"][1])
									$sign=$PMatchDATE["signYYyyMMDD"][1];
								$shYear=$PMatchDATE["signYYyyMMDD"][2];
								$shMonth=$PMatchDATE["signYYyyMMDD"][4];
								if($PMatchDATE["signYYyyMMDD"][6])
									$shDay=$PMatchDATE["signYYyyMMDD"][6];
								else
									$shDay=$jfd;
								list($gMonth,$gDay,$gYear)=self::solartogregorian($shYear,$shMonth,$shDay);
								$time_=str_replace($PMatchDATE["signYYyyMMDD"][0],'',$time_);
								$time=str_replace($PMatchDATE["signYYyyMMDD"][0],$sign.sprintf('%04d',$gYear).'-'.$gMonth.'-'.$gDay,$time);
						// change Y M D jalali to Y M D gregorian and set to time
							}
							elseif($PMatchDATE["signYYyyMMDD"][3]=='/'&&$PMatchDATE["signYYyyMMDD"][5]=='/'){
								$shYear=$PMatchDATE["signYYyyMMDD"][2];
								$shMonth=$PMatchDATE["signYYyyMMDD"][4];
								$shDay=$PMatchDATE["signYYyyMMDD"][6];
								list($gMonth,$gDay,$gYear)=self::solartogregorian($shYear,$shMonth,$shDay);
								$time_=str_replace($PMatchDATE["signYYyyMMDD"][0],'',$time_);
								$time=str_replace($PMatchDATE["signYYyyMMDD"][0],$gYear.'/'.$gMonth.'/'.$gDay,$time);
						// change Y M D jalali to Y M D gregorian and set to time
							}
							elseif(!$PMatchDATE["signYYyyMMDD"][1]&&!$PMatchDATE["signYYyyMMDD"][3]&&!$PMatchDATE["signYYyyMMDD"][5]&&strlen($PMatchDATE["signYYyyMMDD"][2])>3 /* &&strlen($PMatchDATE["signYYyyMMDD"][4])>1&&strlen($PMatchDATE["signYYyyMMDD"][6])>1 */){
								$shYear=$PMatchDATE["signYYyyMMDD"][2];
								$shMonth=$PMatchDATE["signYYyyMMDD"][4];
								$shDay=$PMatchDATE["signYYyyMMDD"][6];
								list($gMonth,$gDay,$gYear)=self::solartogregorian($shYear,$shMonth,$shDay);
								$time_=str_replace($PMatchDATE["signYYyyMMDD"][0],'',$time_);
								$time=str_replace($PMatchDATE["signYYyyMMDD"][0],$gYear.$gMonth.$gDay,$time);
						// change Y M D jalali to Y M D gregorian and set to time
							}
							elseif($PMatchDATE["signYYyyMMDD"][3]&&!$PMatchDATE["signYYyyMMDD"][5]){
								$shYear=$jfy;
								$shMonth=$PMatchDATE["signYYyyMMDD"][2];
								$shDay=$PMatchDATE["signYYyyMMDD"][4].''.$PMatchDATE["signYYyyMMDD"][6];
								list($gMonth,$gDay,$gYear)=self::solartogregorian($shYear,$shMonth,$shDay);
								$time_=str_replace($PMatchDATE["signYYyyMMDD"][0],'',$time_);
								$time=str_replace($PMatchDATE["signYYyyMMDD"][0],$gMonth.'/'.$gDay,$time);
							}else
								$errors='signYYyyMMDD';
							break;
						case 'DDMMYYyy':
							if(strlen($PMatchDATE["DDMMYYyy"][5])==4){
								$shYear=$PMatchDATE["DDMMYYyy"][5];
								$shMonth=$PMatchDATE["DDMMYYyy"][3];
								$shDay=$PMatchDATE["DDMMYYyy"][1];
								list($gMonth,$gDay,$gYear)=self::solartogregorian($shYear,$shMonth,$shDay);
								$time_=str_replace($PMatchDATE["DDMMYYyy"][0],'',$time_);
								$time=str_replace($PMatchDATE["DDMMYYyy"][0],$gYear.'/'.$gMonth.'/'.$gDay,$time);
							}
							elseif($PMatchDATE["DDMMYYyy"][1]>24&&$PMatchDATE["DDMMYYyy"][5]>60&&$PMatchDATE["DDMMYYyy"][2]=='.'&&$PMatchDATE["DDMMYYyy"][4]=='.'||strlen($PMatchDATE["DDMMYYyy"][5])==2&&$PMatchDATE["DDMMYYyy"][4]=='.'){
								$shYear=$minjyear<=$PMatchDATE["DDMMYYyy"][5]?$SDATEcentury*100+$PMatchDATE["DDMMYYyy"][5]:($SDATEcentury+1)*100+$PMatchDATE["DDMMYYyy"][5];
								$shMonth=$PMatchDATE["DDMMYYyy"][3];
								$shDay=$PMatchDATE["DDMMYYyy"][1];
								list($gMonth,$gDay,$gYear)=self::solartogregorian($shYear,$shMonth,$shDay);
								$time_=str_replace($PMatchDATE["DDMMYYyy"][0],'',$time_);
								$time=str_replace($PMatchDATE["DDMMYYyy"][0],$gYear.'/'.$gMonth.'/'.$gDay,$time);
							}
							else
								$errors='DDMMYYyy';
							break;
						case 'YYyMmD':
							if($PMatchDATE["YYyMmD"][2]=='-'&&$PMatchDATE["YYyMmD"][4]=='-'){
								if(strlen($PMatchDATE["YYyMmD"][1])<2){
									$shYear=(int)($SDATEfyear/10)*10+$PMatchDATE["YYyMmD"][1];
						// change Y M D jalali to Y M D gregorian and set to time
								}
								elseif(strlen($PMatchDATE["YYyMmD"][1])==2){
									$shYear=$minjyear<=$PMatchDATE["YYyMmD"][1]?$SDATEcentury*100+$PMatchDATE["YYyMmD"][1]:($SDATEcentury+1)*100+$PMatchDATE["YYyMmD"][1];
						// change Y M D jalali to Y M D gregorian and set to time
								}
								elseif(strlen($PMatchDATE["YYyMmD"][1])>3){
									$shYear=$PMatchDATE["YYyMmD"][1];
						// change Y M D jalali to Y M D gregorian and set to time
								}
								elseif(strlen($PMatchDATE["YYyMmD"][1])>2){
									$shYear=1000+$PMatchDATE["YYyMmD"][1];
						// change Y M D jalali to Y M D gregorian and set to time
								}
								$shMonthonth=$strtoint_jm($PMatchDATE["YYyMmD"][3]);
								$jday=$PMatchDATE["YYyMmD"][5];
								$time_=str_replace($PMatchDATE["YYyMmD"][0],'',$time_);
								$time=str_replace($PMatchDATE["YYyMmD"][0],'',$time);
							}
							elseif(strlen($PMatchDATE["YYyMmD"][1])==4&&!$PMatchDATE["YYyMmD"][5]){
								if($PMatchDATE["YYyMmD"][1])
									$shYear=$PMatchDATE["YYyMmD"][1];
								$shMonthonth=$strtoint_jm($PMatchDATE["YYyMmD"][3]);
								if($PMatchDATE["YYyMmD"][5])
									$jday=$PMatchDATE["YYyMmD"][5];
								$time_=str_replace($PMatchDATE["YYyMmD"][0],'',$time_);
								$time=str_replace($PMatchDATE["YYyMmD"][0],'',$time);
						// change Y M D jalali to Y M D gregorian and set to time
							}
							elseif(!$PMatchDATE["YY"][1]&&!$PMatchDATE["YYyMmD"][1]&&$PMatchDATE["YYyMmD"][5]){
								$shMonthonth=$strtoint_jm($PMatchDATE["YYyMmD"][3]);
								$jday=$PMatchDATE["YYyMmD"][5];
								if($PMatchDATE["YYyMmD"][6])
									$daysuf=$PMatchDATE["YYyMmD"][6];
								$time_=str_replace($PMatchDATE["YYyMmD"][0],'',$time_);
								$time=str_replace($PMatchDATE["YYyMmD"][0],'',$time);
						// change Y M D jalali to Y M D gregorian and set to time
							}
							else
								$errors='YYyMmD';
							break;
						case 'DDMmYY':
							if(strlen($PMatchDATE["DDMmYY"][5])==4&&!$PMatchDATE["DDMmYY"][2]){
								$shMonth=$strtoint_jm($PMatchDATE["DDMmYY"][3]);
								$shYear=$PMatchDATE["DDMmYY"][5];
								list($gMonth,$gDay,$gYear)=self::solartogregorian($shYear,$shMonth,$jfd);
								$time_=str_replace($PMatchDATE["DDMmYY"][0],'',$time_);
								$time=str_replace($PMatchDATE["DDMmYY"][0],$gYear.'/'.$gMonth.'/'.$gDay,$time);
							}
							elseif(!$PMatchDATE["YY"][1]&&!$PMatchDATE["DDMmYY"][5]&&!$PMatchDATE["DDMmYY"][4]){
								$shDay=$PMatchDATE["DDMmYY"][1];
								$shMonth=$strtoint_jm($PMatchDATE["DDMmYY"][3]);
								list($gMonth,$gDay,$gYear)=self::solartogregorian($shYear,$shMonth,$shDay);
								$time_=str_replace($PMatchDATE["DDMmYY"][0],'',$time_);
								$time=str_replace($PMatchDATE["DDMmYY"][0],$gYear.'/'.$gMonth.'/'.$gDay,$time);
							}
							elseif($PMatchDATE["DDMmYY"][5]&&$PMatchDATE["DDMmYY"][3]&&$PMatchDATE["DDMmYY"][1]){
								$shDay=$PMatchDATE["DDMmYY"][1];
								$shMonth=$strtoint_jm($PMatchDATE["DDMmYY"][3]);
								$shYear=$PMatchDATE["DDMmYY"][5];
								list($gMonth,$gDay,$gYear)=self::solartogregorian($shYear,$shMonth,$shDay);
								$time_=str_replace($PMatchDATE["DDMmYY"][0],'',$time_);
								$time=str_replace($PMatchDATE["DDMmYY"][0],$gYear.'/'.$gMonth.'/'.$gDay,$time);
							}
							else
								$errors='DDMmYY';
							break;
						case 'YY':
							$shYear=$PMatchDATE["YY"][1];
							list($gMonth,$gDay,$gYear)=self::solartogregorian($shYear,$jfm,$jfd);
							$time_=str_replace($PMatchDATE["YY"][0],'',$time_);
							$time=str_replace($PMatchDATE["YY"][0],$gYear,$time);
							break;
						case 'Mm':
							$shMonth=$strtoint_jm($PMatchDATE["Mm"][1]);
							list($gMonth,$gDay,$gYear)=self::solartogregorian($jfy,$shMonth,$jfd);
							$time_=str_replace($PMatchDATE["Mm"][0],'',$time_);
							$time=str_replace($PMatchDATE["Mm"][0],strlen($shMonth)<4?$inttostr_gm($gMonth):$inttostr_gm($gMonth,0),$time);
							break;
					}
			}

 			$FPTIME=array(
				// TIME
				//12 Hour
				//'hhmeridian'=>"({$USymbols["hh"]})({$USymbols["space"]})?({$USymbols["meridian"]})",
				//'hhIImeridian'=>"({$USymbols["hh"]})[\.:]({$USymbols["II"]})({$USymbols["space"]})?({$USymbols["meridian"]})",
				'hhIISSmeridian'=>"({$USymbols["hh"]})([\.:]?)({$USymbols["II"]})?([\.:]?)({$USymbols["SS"]})?{$USymbols["space"]}?({$USymbols["meridian"]})",
				//24 Hour
				//'HHII'=>"[tT]?({$USymbols["HH"]})[\.:]?({$USymbols["II"]})",
				//'HHIISS'=>"[tT]?({$USymbols["HH"]})[\.:]?({$USymbols["II"]})[\.:]?({$USymbols["SS"]})?",
				//'HHIISStz'=>"[tT]?({$USymbols["HH"]})[\.:]({$USymbols["II"]})[\.:]({$USymbols["SS"]}){$USymbols["space"]}?({$USymbols["tz"]}|{$USymbols["tzcorrection"]})",
				//'HHIISSfrac'=>"[tT]?({$USymbols["HH"]})[\.:]({$USymbols["II"]})[\.:]({$USymbols["SS"]})({$USymbols["frac"]})",
				'HHIISSfractz'=>"[t]?({$USymbols["HH"]})([\.:]?)({$USymbols["II"]})([\.:]?)({$USymbols["SS"]})?{$USymbols["frac"]}?{$USymbols["space"]}?({$USymbols["tz"]}|{$USymbols["tzcorrection"]})?",
				'tz'=>"({$USymbols["tz"]}|{$USymbols["tzcorrection"]})"
			);
			foreach($FPTIME as $NPATTERN=>$VPATTERN)
				if(preg_match('#'.$VPATTERN.'#',$time_,$preg_match)){
					$PMatchTIME[$NPATTERN]=array_filter($preg_match);
				}
			if($PMatchTIME&&!$setCOMPOUND){
				$PMatchTIMEkeys=array_keys($PMatchTIME);
				for($ITIME=0;$ITIME<count($PMatchTIME);$ITIME++)
					switch($PMatchTIMEkeys[$ITIME]){
						case 'hhIISSmeridian':
						/* 	$hour=$PMatchTIME["hhIISSmeridian"][1];
							if($PMatchTIME["hhIISSmeridian"][3])
								$minute=$PMatchTIME["hhIISSmeridian"][3];
							if($PMatchTIME["hhIISSmeridian"][5])
								$second=$PMatchTIME["hhIISSmeridian"][5];
							$meridian=$PMatchTIME["hhIISSmeridian"][6]; */
							$time_=str_replace($PMatchDATE["hhIISSmeridian"][0],'',$time_);
							break;
						case 'HHIISSfractz':
							//if(!$meridian){
								/* $hour=$PMatchTIME["HHIISSfractz"][1];
								$minute=$PMatchTIME["HHIISSfractz"][3];
								if($PMatchTIME["HHIISSfractz"][5])
									$second=$PMatchTIME["HHIISSfractz"][5];
								if($PMatchTIME["HHIISSfractz"][7])
									$frac=$PMatchTIME["HHIISSfractz"][7];
								if($PMatchTIME["HHIISSfractz"][8])
									$tz=$PMatchTIME["HHIISSfractz"][8]; */
								$time_=str_replace($PMatchDATE["HHIISSfractz"][0],'',$time_);
							//}
							break;
						/* case 'tz':
							$tz=$PMatchTIME["tz"][1];
							//$time_=str_replace($PMatchTIME["tz"][0],'',$time_);
							break; */
					}
			}

			return strtotime($time);

			$FPDAYBASED=array(
				// DAY-BASED
				//'yesterday'=>"yesterday",// Midnight of yesterday	"yesterday 14:00"
				//'midnighttoday'=>"midnight|today",// The time is set to 00:00:00
				//'now'=>"now",// Now - this is simply ignored
				//'noon'=>"noon",//The time is set to 12:00:00	"yesterday noon"
				//'tomorrow'=>"tomorrow",// Midnight of tomorrow

				//'backof'=>"back of",
				//'frontof'=>"front of",
				'backfrontof'=>"(back|front) of",//H | h meridian

				//'firstdayof'=>"first day of",
				//'lastdayof'=>"last day of",
				'firstlastdayof'=>"(first|last) day of",

				'lastdaynameof'=>"({$USymbols["ordinal"]}){$USymbols["space"]}{$USymbols["Dd"]}{$USymbols["space"]}of",
				'numberunit'=>"({$USymbols["number"]})({$USymbols["space"]})?({$USymbols["unit"]}|week)",
				'ordinalunit'=>"({$USymbols["ordinal"]})({$USymbols["unit"]})",
				'ago'=>"(ago)",
				'dayname'=>"({$USymbols["Dd"]})",
				'reltextweek'=>"({$USymbols["Dd"]}){$USymbols["space"]}({$USymbols["reltext"]}){$USymbols["space"]}week",


				'unit'=>"({$USymbols["unit"]})",
				'ordinal'=>"({$USymbols["ordinal"]})",
				'reltext'=>"({$USymbols["reltext"]})"
			);
			//$now=self::time($now); // set in DAY-BASED
			//The timestamp which is used as a base for the calculation of relative dates.

			/* 'daytext'=>"weekdays?",
			'ordinal'=>"first|second|third|fourth|fifth|sixth|seventh|eighth|ninth|tenth|eleventh|twelfth|next|last|previous|this",
			'reltext'=>"next|last|previous|this",
			'unit'=>"(?:(?:sec|second|min|minute|hour|day|fortnight|forthnight|month|year)s?)(?:week(?:day)?s?)" */

 			foreach($FPDAYBASED as $NPATTERN=>$VPATTERN)
				if(preg_match('#'.$VPATTERN.'#',$time_,$preg_match)){
					$PMatchDAYBASED[$NPATTERN]=array_filter($preg_match);
				}
			if($PMatchDAYBASED){
				$PMatchDAYBASEDkeys=array_keys($PMatchDAYBASED);
				for($IDAYBASED=0;$IDAYBASED<count($PMatchDAYBASED);$IDAYBASED++)
					switch($PMatchDAYBASEDkeys[$IDAYBASED]){
						case 'backfrontof':
							break;
						case 'firstlastdayof':
							break;
						case 'lastdaynameof':
							break;
						case 'numberunit':
							break;
						case 'ago':
							break;
						case 'reltextweek':
							break;
						case 'dayname':
							break;
						case 'unit':
							break;
						case 'ordinal':
							break;
						case 'reltext':
							break;

					}
				return strtotime($time,self::time($now));
			}
		}

		/**
		* @see SHDate::mktime
		* @see SHDate::gmmktime
		*/
		protected static function mktimes($hours=false,$minute=false,$second=false,$shDay=false,$shMonth=false,$shYear=false,$gmt=false){
			if(!(is_int($hours)||is_int($minute)||is_int($second)||is_int($shYear)||is_int($shMonth)||is_int($shDay)))
				if($gmt)
					return gmmktime();
				else
					return mktime();
			if($gmt)
				$getdate = self::getdates(false, true);
			else
				$getdate = self::getdates();
			if(!is_int($hours))
				$hours = $getdate["hours"];
			if(!is_int($minute))
				$minute = $getdate["minutes"];
			if(!is_int($second))
				$second = $getdate["seconds"];
			if(!is_int($shDay))
				$shDay = $getdate["mday"];
			if(!is_int($shMonth))
				$shMonth = $getdate["mon"];
			if(!is_int($shYear))
				$shYear = $getdate["year"];
			list($gMonth,$gDay,$gYear) = self::solartogregorian($shYear,$shMonth,$shDay);
			if($gmt)
				return gmmktime($hours,$minute,$second,$gMonth,$gDay,$gYear);
			return mktime($hours,$minute,$second,$gMonth,$gDay,$gYear);
		}

		/**
		* Return current Unix timestamp
		* @param int $timestamp
		* @param bool $tserver
		* @return int the current time measured in the number of seconds since the Unix Epoch (January 1 1970 00:00:00 GMT).
		*/
		public static function time($timestamp=false,$tserver=false){
			if(!(is_int($timestamp)||is_bool($timestamp))){
				throw new Exception("The value is not integer");
			}
			if(is_int($timestamp)){
				if($tserver)
					$timestamp += self::TSERVER;
				return $timestamp;
			}
			return time()+self::TSERVER;
		}
		
		/**
		* Format a local time/date as integer
		* @param   string  $format  The following characters are recognized in the format parameter string.
		* @param   int  $timestamp  The optional timestamp parameter is an integer Unix timestamp that defaults to the current local time if a timestamp is not given. In other words,it defaults to the value of shtime().
		* @return int  an integer.
		* @since   1.0.0
		*/
		public static function idates($format,$timestamp=false){
			if(!is_string($format)){
				throw new Exception("The value is not string");
			}
			if(strlen($format)>1)return false;
			$getdate = self::getdates($timestamp);
			switch($format){
				/* day */
				case 'j':
				case 'd':return $getdate["mday"];
				/* month */
				case 'n':
				case 'm':return $getdate["mon"];
				/* time */
				case 'w':return $getdate["wday"];
				case 'z':return $getdate["yday"];
				/* week */
				case 'W':return self::getWeekOfYear($getdate["year"],$getdate["mon"],$getdate["mday"])[1];
				case 't':return self::getDaysInMonth($getdate["year"],$getdate["mon"]);
				/* year */
				case 'L':return self::isLeaps($getdate["year"]);
				case 'y':return $getdate["year"]%100;
				case 'Y':return $getdate["year"];
				/* time */
				case 'g':
				case 'h':return $getdate["hours"]%12?:12;
				case 'G':
				case 'H':return $getdate["hours"];
				case 'i':return $getdate["minutes"];
				case 's':return $getdate["seconds"];
				/* timezone */
				case 'I':
				case 'Z':
				/* Swatch Beat / Internet Time */
				case 'B':
					return idate($format,$getdate[0]);
				case 'U':return $getdate[0];
				default:
					return false;
			}
		}

		/**
		* Get date/time information
		* @param   int  $timestamp  The optional timestamp parameter is an integer Unix timestamp that defaults to the current local time if a timestamp is not given. In other words,it defaults to the value of jtime().
		* @return  array  an associative array of information related to the timestamp.
		* @since   1.0.0
		*/
		public static function getdates($timestamp=false, $gmt=false){
			if($gmt)
				sscanf(gmdate('n=j=Y=H=i=s=w=U',self::time($timestamp)),'%d=%d=%d=%d=%d=%d=%d=%d',$gMonth,$gDay,$gYear,$Hours,$minute,$second,$gdow,$timestamp);
			else
				list($second,$minute,$Hours,$gDay,$gdow,$gMonth,$gYear,$gdoy,$gdfn,$gmfn,$timestamp) = array_values(getdate(self::time($timestamp)));
			list($shYear,$shMonth,$shDay)=self::gregoriantosolar($gMonth,$gDay,$gYear);
			$shdow = self::gDayOfWeeks($gdow);//self::getDayOfWeek($shYear,$shMonth,$shDay);
			return array(
			'seconds' => $second,
			'minutes' => $minute,
			'hours' => $Hours,
			'mday' => $shDay,
			'wday' => $shdow,
			'mon' => $shMonth,
			'year' => $shYear,
			'yday' => self::getDayOfYear($shYear,$shMonth,$shDay),
			'weekday' => self::getDayFullNames($shdow),
			'month' => self::getMonthFullNames($shMonth),
			0 => $timestamp
			);
		}

		/**
		* Get the local time
		* @param   int  $timestamp  The optional timestamp parameter is an integer Unix timestamp that defaults to the current local time if a timestamp is not given. In other words,it defaults to the value of shtime().
		* @param   bool  $is_associative  If set to FALSE,numerically indexed array. If set to TRUE,associative array containing all.
		* @return  array  Numerically indexed array or associative array containing all.
		* @since   1.0.0
		*/
		public static function localtimes($timestamp=false,$is_associative=false){
			$localtime = localtime(self::time($timestamp),true);
			list($shYear,$shMonth,$shDay) = self::gregoriantosolar($localtime['tm_mon']+1,$localtime['tm_mday'],$localtime['tm_year']+1900);
			if($is_associative)
				return array(
				'tm_sec' => $localtime['tm_sec'],
				'tm_min' => $localtime['tm_min'],
				'tm_hour' => $localtime['tm_hour'],
				'tm_mday' => $shDay,
				'tm_mon' => $shMonth-1,
				'tm_year' => $shYear-1300,
				'tm_wday' => self::gDayOfWeeks($localtime['tm_wday']),
				'tm_yday' => self::getDayOfYear($shYear,$shMonth,$shDay),
				'tm_isdst' => $localtime['tm_isdst']
				);
			return array(
			$localtime['tm_sec'],
			$localtime['tm_min'],
			$localtime['tm_hour'],
			$shDay,
			$shMonth-1,
			$shYear-1300,
			self::gDayOfWeeks($localtime['tm_wday']),
			self::getDayOfYear($shYear,$shMonth,$shDay),
			$localtime['tm_isdst']
			);
		}

		/**
		* Get current time
		* @param   bool  $return_float  When set to TRUE,a float instead of an array is returned.
		* @return  array  By default an array. If return_float is set,then a float.
		* @since   1.0.0
		*/
		public static function gettimeofdays($return_float=false){
			if (!$return_float){
				$gettimeofday = gettimeofday();
				return array(
				'sec' => $gettimeofday['sec']+self::TSERVER,
				'usec' => $gettimeofday['usec'],
				'minuteswest' => $gettimeofday['minuteswest'],
				'minuteseast' => -$gettimeofday['minuteswest'],
				'dsttime' => $gettimeofday['dsttime'],
				);
			}
			return gettimeofday($return_float)+self::TSERVER;
		}

		/**
		* Return current Unix timestamp with microseconds
		* @param   bool  $get_as_float  If used and set to TRUE, microtime() will return a float instead of a string, as described in the return values section below.
		* @return  array  If get_as_float is set to TRUE, then microtime() returns a float, which represents the current time in seconds since the Unix epoch accurate to the nearest microsecond.
		* @since   1.0.0
		*/
		public static function microtimes($get_as_float=false){
			if(!$get_as_float){
				sscanf(microtime(),'%f %f',$usec, $sec);
				return sprintf('%.8f %f',$usec,$sec+self::TSERVER);
			}
			return microtime($get_as_float)+self::TSERVER;
		}

		/**
		* Validate a date
		* @param   int  $shYear  Year of the date.
		* @param   int  $shMonth  Month of the date.
		* @param   int  $shDay  Day of the date.
		* @return  bool  TRUE if the date given is valid; otherwise returns FALSE.
		* @since   1.0.0
		*/
		public static function checkdates($shYear,$shMonth,$shDay){
			if(!(is_int($shYear)&&is_int($shMonth)&&is_int($shDay))){
				throw new Exception("The value is not integer");
			}
			return !($shYear<1||$shYear>3500000||$shMonth<1||$shMonth>12||$shDay<1||$shDay>self::getDaysInMonth($shYear,$shMonth));
		}

		/**
		* Validate a time
		* @param   int  $hours  Hour of the time.
		* @param   int  $minute  Minute of the time.
		* @param   int  $second  Second of the time.
		* @return  bool  TRUE if the time given is valid; otherwise returns FALSE.
		* @since   1.0.0
		*/
		public static function checktime($hours,$minute,$second){
			if(!(is_int($hours)&&is_int($minute)&&is_int($second))){
				throw new Exception("The value is not integer");
			}
			return !($hours<0||$hours>23||$minute<0||$minute>59||$second<0||$second>59);
		}
		

		/**
		*
		*
		*/
		public static function dateToTimes($hours=false,$minute=false,$second=false,$shDay=false,$shMonth=false,$shYear=false, $gmt=false){
			if(!(is_int($hours)||is_int($minute)||is_int($second)||is_int($shYear)||is_int($shMonth)||is_int($shDay)))
				if($gmt)
					return gmmktime();
				else
					return mktime();
			$getdate = self::getdates(false, true);
			if(!is_int($hours))
				$hours = $getdate['hours'];
			if(!is_int($minute))
				$minute = $getdate['minutes'];
			if(!is_int($second))
				$second = $getdate['seconds'];
			if(!is_int($shYear))
				$shYear = $getdate['year'];
			if(! ($shMonth))
				$shMonth = $getdate['mon'];
			if(!is_int($shDay))
				$shDay = $getdate['mday'];
			/*
			*	86400 = 24*60*60
			*	3600  = 60*60
			*/
			//	0	=	1348/10/11	00:00:00	=	1970/01/01	00:00:00
			return array(((($shYear-1)*365+self::isLeaps($shYear,1)+self::getDayOfYear($shYear,$shMonth,$shDay))*86400 /* 24*60*60 */)+($hours*3600 /* 60*60 */)+($minute*60)+$second-42531868800);
		}
		
		/**
		*
		*
		*/
		public static function timeToDates($timestamp=false, $gmt=false){
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
			$shYear = floor($doy/365)+1;
			$doy = $doy%365-self::isLeaps($shYear,1);
			list($shYear,$shMonth,$shDay) = self::getDaysOfDay($shYear,$doy);
			$hours = $ts/3600%24;
			$minute = $ts/60%60;
			$second = $ts%60;
			$gmmktime = self::mktimes($hours,$minute,$second,$shDay,$shMonth,(self::isLeaps($shYear)?1375:1371),true);
			if($gmt)
				return array($shYear,$shMonth,$shDay,$hours,$minute,$second,$timestamp,$gmmktime);
			$tz = date('Z',$gmmktime);
			$timestamp += $tz;
			$ts += $tz;
			$doy = $timestamp/86400;
			$shYear = floor($doy/365)+1;
			$doy = $doy%365-self::isLeaps($shYear,1);
			list($shYear,$shMonth,$shDay) = self::getDaysOfDay($shYear,$doy);
			$hours = $ts/3600%24;
			$minute = $ts/60%60;
			$second = $ts%60;
			return array($shYear,$shMonth,$shDay,$hours,$minute,$second,$timestamp,$gmmktime);
		}

		/**
		* Convertor Gregorian to Solar Hijri(Shamsi Hijri)
		* @param   int  $gYear  The number of the year (Gregorian).
		* @param   int  $gMonth  The number of the month (Gregorian).
		* @param   int  $gDay  The number of the day (Gregorian).
		* @return  array  Solar date.
		* @since   1.0.0
		*/
		protected static function gregoriantosolar($gMonth,$gDay,$gYear){
			// 0622/03/22 = 0001/01/01
			if(!(is_int($gMonth)&&is_int($gDay)&&is_int($gYear))){
				throw new Exception("The value is not integer");
			}
			if($gMonth<1||$gDay<1||$gYear<622||($gMonth<3&&$gDay<22&&$gYear==622)) return NULL;
			$gdoy = ($gYear-1)*365+array(0,0,31,59,90,120,151,181,212,243,273,304,334)[$gMonth]+$gDay-226745; // -0622/03/22 = 0001/01/01
			if(self::gIsLeap($gYear)&&$gMonth>2)$gdoy++;
			$shYear = (int)($gdoy/365)+1;
			$shdoy = $gdoy%365+self::gIsLeap($gYear,1)-self::isLeaps($shYear,1);
			return self::getDaysOfDay($shYear,$shdoy-1);
		}

		/**
		* Convertor Solar Hijri(Shamsi Hijri) to Gregorian
		* @param   int  $shYear  The number of the year (Solar).
		* @param   int  $shMonth  The number of the month (Solar).
		* @param   int  $shDay  The number of the day (Solar).
		* @return  array  Gregorian date.
		* @since   1.0.0
		*/
		protected static function solartogregorian($shYear,$shMonth,$shDay){
			// 0001/01/01 = 0622/03/22
			if(!(is_int($shYear)&&is_int($shMonth)&&is_int($shDay))){
				throw new Exception("The value is not integer");
			}
			$shdoy = ($shYear-1)*365+self::getDayOfYear($shYear,$shMonth,$shDay)+226746; // +0622/03/22 = 0001/01/01
			$gYear = (int)($shdoy/365)+1;
			$gdoy = $shdoy%365+self::isLeaps($shYear,1)-self::gIsLeap($gYear,1);
			return self::gDaysOfDay($gYear,$gdoy);
		}
		
		/**
		*
		*
		*/
		private static function gDaysOfDay($gYear,$gdoy){
			$gdiy = self::gDaysInYear($gYear,self::gIsLeap($gYear));
			if($gdoy<1)
				do{
					$gYear--;
					$leap = self::gIsLeap($gYear);
					$gdiy = self::gDaysInYear($gYear,$leap);
					$gdoy += $gdiy;
				}while($gdoy<1);
			elseif($gdoy>$gdiy)
				do{
					$gdoy -= $gdiy;
					$gYear++;
					$leap = self::gIsLeap($gYear);
					$gdiy = self::gDaysInYear($gYear,$leap);
				}while($gdoy>$gdiy);
			else
				$leap = self::gIsLeap($gYear);
			foreach(array(0,31,($leap?29:28),31,30,31,30,31,31,30,31,30,31) as $gMonth=>$dim){
				if ($gdoy<=$dim)break;
				$gdoy -= $dim;
			}
			return array($gMonth,(int)$gdoy,$gYear);
		}

		/**
		*
		*
		*/
		private static function gIsLeap($gYear,$leaps=false){
			if($leaps)return (ceil((int)((--$gYear)/4)-(int)(($gYear)/100)+(int)(($gYear)/400))-150);
				return (int)(($gYear%4==0)&&!(($gYear%100==0)&&($gYear%400!= 0)));
		}
		
		/**
		*
		*
		*/
		private static function gDaysInYear($gYear,$leap=false){
			if($leap===false)
				$leap = self::gIsLeap($gYear);
			return $leap?366:365;
		}

		/**
		* Whether it's a leap year
		* @param   int  $shYear  The number of the year.
		* @param   int  $leaps  leap years.
		* @return  int  1 if it is a leap year,0 otherwise.
		* @copyright Zya'aldyn Torabi
		* @since   1.5.0
		*/
		protected static function isLeaps($shYear,$leaps=false){//private
			if(!is_int($shYear)){
				throw new Exception("The value is not integer");
			}
			if($leaps)
				return (int)(ceil((($shYear+=1127)*365.2422)-$shYear*365)-274);
			return (bool)(((int)(($shYear+=1128)*365.2422)-(int)(--$shYear*365.2422))-365);
		}
		
		/**
		* Numeric representation of the day of the week
		* @param   int  $shYear  The number of the year.
		* @param   int  $shMonth  The number of the month.
		* @param   int  $shDay  The number of the day.
		* @return  int  0 through 6
		* @since   1.0.0
		*/
		protected static function getDayOfWeek($shYear,$shMonth,$shDay,$FDOW = self::FIRST_DAY_OF_WEEK){
			if(!(is_int($shYear)&&is_int($shMonth)&&is_int($shDay))){
				throw new Exception("The value is not integer");
			}
			//new and best version
			//return ($shYear+self::isLeaps($shYear,1)+self::getDayOfYear($shYear,$shMonth,$shDay)+5)%7;
			return (5+$shYear+self::isLeaps($shYear,1)+self::getDayOfYear($shYear,$shMonth,$shDay)-$FDOW)%7;
		}
		
		/**
		*
		*
		*/
		protected static function gDayOfWeeks($gdow){
			// 7+(gdow+1)-FIRST_DAY_OF_WEEK%7
			return (8+$gdow-self::FIRST_DAY_OF_WEEK)%7;// shdow
		}

		/**
		* The day of the year
		* @param   int  $shYear  The number of the year.
		* @param   int  $shMonth  The number of the month.
		* @param   int  $shDay  The number of the day.
		* @return  int  0 through 364|365
		* @since   1.0.0
		*/
		protected static function getDayOfYear($shYear,$shMonth,$shDay){
			if(!(is_int($shYear)&&is_int($shMonth)&&is_int($shDay))){
				throw new Exception("The value is not integer");
			}
			return self::DAY_OF_YEAR[$shMonth]+$shDay-1;
		}

		/**
		*
		*
		*/
		protected static function getDaysOfDay($shYear,$doy){
			if(!(is_int($shYear)&&is_int($doy))){
				throw new Exception("The value is not integer");
			}
			$doy++;
			$diy = self::getDaysInYear($shYear);
			if($doy<1)
				do{
					$shYear--;
					$doy += self::getDaysInYear($shYear);
				}while($doy<1);
			elseif($doy>$diy)
				do{
					$doy -= $diy;
					$shYear++;
					$diy = self::getDaysInYear($shYear);
				}while($doy>$diy);
			if($doy<187){
				$shMonth = (int)(($doy-1)/31)+1;
				$shDay = $doy%31?:31;
			}
			else{
				$doy-=186;
				$shMonth = (int)(($doy-1)/30)+7;
				$shDay = $doy%30?:30;
			}
			return array($shYear,$shMonth,$shDay);
		}

		/**
		* The week of the year
		* @param   int  $shYear  The number of the year.
		* @param   int  $shMonth  The number of the month.
		* @param   int  $shDay  The number of the day.
		* @return  int  1 through 52|53
		* @since   1.0.0
		*/
		protected static function getWeekOfYear($shYear,$shMonth,$shDay,$FDOW=self::FIRST_DAY_OF_WEEK){
			if(!(is_int($shYear)&&is_int($shMonth)&&is_int($shDay))){
				throw new Exception("The value is not integer");
			}
			$doy = self::getDayOfYear($shYear,$shMonth,$shDay)+1; // 1 through 365-6
			$far1dow = self::getDayOfWeek($shYear,1,1,$FDOW)+1; // 1 through 7
			/* Find if Y M D falls in YearNumber --Y, WeekNumber 52 or 53 */
			if($doy<=(8-$far1dow)&&$far1dow>4){
				$shYear--;
				$isoYear = $shYear;
				//$isoWeek = ($far1dow == 5||($far1dow == 6&&self::isLeaps($shYear)))?53:52;
				if($far1dow == 5||($far1dow == 6&&self::isLeaps($shYear)))
					$isoWeek = 53;
				else
					$isoWeek = 52;
				return array($isoYear,$isoWeek);
			}
			/* 8. Find if Y M D falls in YearNumber ++Y, WeekNumber 1 */
			$esf29dow = self::getDayOfWeek($shYear,12,self::getDaysInMonth($shYear,12),$FDOW)+1; // 1 through 7
			if($doy>(self::getDaysInYear($shYear)-$esf29dow)&&$esf29dow<4){
				$shYear++;
				$isoYear = $shYear;
				$isoWeek = 1;
				return array($isoYear,$isoWeek);
			}
			/* 9. Find if Y M D falls in YearNumber Y, WeekNumber 1 through 52|53 */
			$isoYear = $shYear;
			//(doy+(7-(self::getDayOfWeek($shYear,$shMonth,$shDay,$FDOW)+1))+(far1dow-1))/7
			$isoWeek = (5+$doy+$far1dow-self::getDayOfWeek($shYear,$shMonth,$shDay,$FDOW))/7;
			if($far1dow>4)
				$isoWeek--;
			return array($isoYear,$isoWeek);
		}

		/**
		* Validate a week
		* @param   int  $isoYear  Year of the weeks.
		* @param   int  $isoWeek  Week of the weeks.
		* @param   int  $isoDay  Day of the week.
		* @return  bool  TRUE if the week given is valid; otherwise returns FALSE.
		* @since   1.0.0
		*/
		public static function checkweek($isoYear,$isoWeek,$isoDay=false){
			if(!(is_int($isoYear)&&is_int($isoWeek))){
				throw new Exception("The value is not integer");
			}
			if(!is_int($isoDay)||!is_bool($isoDay)){
				throw new Exception("The value is not integer");
			}
			if(!is_int($isoDay))
				return !($isoYear<1||$isoYear>3500000||$isoWeek<1||$isoWeek>self::getWeeksInYear($isoYear));
			return !($isoYear<1||$isoYear>3500000||$isoWeek<1||$isoWeek>self::getWeeksInYear($isoYear)||$isoDay<1||$isoDay>7);
		}

		/**
		*
		*
		*/
		protected static function getWeekOfDay($isoYear,$isoWeek,$isoDay=1){
			if(!(is_int($shIsoYear)&&is_int($isoWeek)&&is_int($isoDay))){
				throw new Exception("The value is not integer");
			}
			if(self::checkweek($isoYear,$isoWeek,$isoDay))
				throw new Exception("Validation of weekly values is incorrect");
			$doy = ($isoWeek-1)*7+$isoDay-self::getDayOfWeek($isoYear,1,4)+2;
			return self::getDaysOfDay($isoYear,$doy);
		}
		
		/**
		*
		*
		*/
		protected static function getWeeksInYear($shYear){
			if(!is_int($shYear)){
				throw new Exception("The value is not integer");
			}
			$far1dow = self::getDayOfWeek($shYear,1,1)+1;
			if($far1dow==4||($far1dow==3&&self::isLeaps($shYear)))
				return 53; // self::Weeks_In_Year_LEAP
			return 52; // self::Weeks_In_Year
		}
		/**
		* Number of days in the given month
		* @param   int  $shYear  The number of the year.
		* @param   int  $shMonth  The number of the month.
		* @return  int  29 through 31
		* @since   1.0.0
		*/
		protected static function getDaysInMonth($shYear,$shMonth){
			if(!(is_int($shYear)&&is_int($shMonth))){
				throw new Exception("The value is not integer");
			}
			if($shYear<1||$shMonth<1||$shMonth>12) return NULL;
			if(self::isLeaps($shYear))
				return self::DAYS_IN_MONTH_LEAP[$shMonth];
			return self::DAYS_IN_MONTH[$shMonth];
		}

		/**
		* Number of days in the given year
		* @param   int  $shYear  The number of the year.
		* @return  int  365 through 366
		* @since   1.0.0
		*/
		protected static function getDaysInYear($shYear){
			if(!is_int($shYear)){
				throw new Exception("The value is not integer");
			}
			if(self::isLeaps($shYear))
				return self::DAYS_IN_YEAR_LEAP;
			return self::DAYS_IN_YEAR;
		}

		/**
		*
		*
		*/
		protected static function getMidSolstice($shdoy,$solstice=false){  
			if($shdoy == 245 &&!$solstice)return true;/* m=8,d=30 */// winter Solstice | Midwinter     طولاني ترين شب - یلدا
			elseif($shdoy == 93&&$solstice)return true;/* m=4,d=1 */// Summer Solstice    طولاني ترين روز - تموز
			return false;
		}

		/**
		*
		*
		*/
		protected static function getMillesimal($shYear){//1000
			if(!is_int($shYear)){
				throw new Exception("The value is not integer");
			}
			if(!($shYear%1000))
				return (int)($shYear/1000);
			return (int)($shYear/1000)+1;
		}

		/**
		*
		*
		*/
		protected static function getCentury($shYear){//100
			if(!is_int($shYear)){
				throw new Exception("The value is not integer");
			}
			if(!($shYear%100))
				return (int)($shYear/100);
			return (int)($shYear/100)+1;
		}
		
		/**
		*
		*
		*/
		protected static function getDecade($shYear){//10
			if(!is_int($shYear)){
				throw new Exception("The value is not integer");
			}
			if(!($shYear%10))
				return (int)((($shYear-1)%100)/10+1);
			return (int)(($shYear%100)/10);
		}
		
		/**
		*
		*
		*/
		protected static function getSeason($shMonth){//4
			return (int)($shMonth/3.1)+1;
		}

	}
    class_alias("SHDateBase","SDateBase");

