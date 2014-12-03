<?php
	/**
	* In The Name Of God 	به نام خداوند بخشنده و مهربان
	* @package Date and Time Related Extensions { Hijri_Shamsi,Solar(Jalali) }
	*
    * @author   MD Amanalikhani
	* @link    http://md-amanalikhani.github.io | http://md.akhi.ir
	* @copyright   Copyright (C) 2014 - 2020 Open Source Matters,Inc. All right timestamp reserved.
	* @license http://www.php.net/license/3_0.txt  PHP License 3.0
	* @version Release: 0.5.10
	*/	
	define("JDATE_Diff_Timeserver",0);///////		اختلاف زمان با سرور
	function jtime(){return time()+JDATE_Diff_Timeserver;}
	define("JDATE_TimeZone","Asia/Tehran");
	define("JDATE_LANG_NUM",strtoupper('en'));
	define("JDATE_LANG_WORD",strtoupper('FA'));
	$_SERVER['JDATE']=new DATE(JDATE_TimeZone,JDATE_LANG_WORD,JDATE_LANG_NUM);
	
	/* Constantimestamp */
	define("CAL_JALALI",4);
	define("CAL_MONTH_JALALI_SHORT",6);
	define("CAL_MONTH_JALALI_LONG",7);
	
	define("JDATE_ATOM","Y-m-d\TH:i:sP");
	define("JDATE_RFC3339",JDATE_ATOM);
	define("JDATE_W3C",JDATE_ATOM);
	define("JDATE_COOKIE",JDATE_LANG_WORD=='FA'?'T l,d-M-y H:i:s':"l,d-M-y H:i:s T");
	define("JDATE_RFC850",JDATE_COOKIE);
	define("JDATE_ISO8601","Y-m-d\TH:i:sO");
	define("JDATE_RSS",JDATE_LANG_WORD=='FA'?'O D,d M y H:i:s':"D,d M y H:i:s O");
	define("JDATE_RFC822",JDATE_RSS);
	define("JDATE_RFC1036",JDATE_RSS);
	define("JDATE_RFC1123",JDATE_RSS);
	define("JDATE_RFC2822",JDATE_RSS);
	
	function jjddayofweek($julianday,$mode=CAL_DOW_DAYNO){
		if($mode==1)
			return jword('+dfn',(jddayofweek($julianday,0)+1)%7);
		elseif($mode==2)
			return jword('+dsn',(jddayofweek($julianday,0)+1)%7);
		return (jddayofweek($julianday,0)+1)%7;
	}
	function jcal_from_jd($jd,$calendar){
		if($calendar==CAL_JALALI){
			list($jy,$jm,$jd,$jdow,$Sjdow,$sjdow,$sjm,$Sjm)=explode('=',jdate('Y=n=j=w=D=l=F=M',jdtounix($jd)));
			$array=array('date'=>sprintf("%04d/%02d/%02d",$jy,$jm,$jd),
			'month'=>(int)$jm,
			'day'=>(int)$jd,
			'year'=>(int)$jy,
			'dow'=>(int)$jdow,
			'abbrevdayname'=>$Sjdow,
			'dayname'=>$sjdow,
			'abbrevmonth'=>$Sjm,
			'monthname'=>$sjm);
		}else
			$array=cal_from_jd($jd,$calendar);
		return $array;
	}
	function jcal_to_jd($calendar,$m,$d,$y){
		return ($calendar==CAL_JALALI)?jalalitojd($y,$m,$d):cal_to_jd($calendar,$m,$d,$y);
	}
	function jcal_days_in_month($calendar,$m,$y){
		return ($calendar==CAL_JALALI)?DATE::days_in_month($y,$m):cal_days_in_month($calendar,$m,$y);
	}
	function jjdmonthname($julianday,$mode){
		return ($mode>=CAL_MONTH_JALALI_SHORT)? (($mode==CAL_MONTH_JALALI_SHORT)?jword("+mfn",explode('/',jdtojalali($julianday))[1]):jword("+msn",explode('/',jdtojalali($julianday))[1])):jdmonthname($julianday,$mode);
	}
	function jcal_info($calendar=-1){
		$array=cal_info();
		if($calendar==-1||$calendar==CAL_JALALI){
			$array[CAL_JALALI]=array(
			  'months' =>  explode('_',jword("_+mfn_+mfn_+mfn_+mfn_+mfn_+mfn_+mfn_+mfn_+mfn_+mfn_+mfn_+mfn",range(1,13))),
			  'abbrevmonths' => explode('_',jword("_+msn_+msn_+msn_+msn_+msn_+msn_+msn_+msn_+msn_+msn_+msn_+msn",range(1,13))),
			  'maxdaysinmonth' => jidate('t'),
			  'calname' =>  'Jalali',
			  'calsymbol' => 'CAL_JALALI');
			unset($array[CAL_JALALI]['months'][0],$array[CAL_JALALI]['abbrevmonths'][0]);	
		}
		return ($calendar<0) ? $array : $array[$calendar];
	}
	function jdtojalali($julianday){
		list($gm,$gd,$gy)=explode('/',jdtogregorian($julianday));
		list($jy,$jm,$jd)=gjalali($gy,$gm,$gd);
		return "$jy/$jm/$jd";
	}
	function jalalitojd($jy,$jm,$jd){
		list($gy,$gm,$gd)=jgregorian($jy,$jm,$jd);
		return gregoriantojd($gm,$gd,$gy);
	}
	function jgregorian($jy,$jm,$jd){
		return DATE::jgregorian($jy,$jm,$jd);
	}
	function gjalali($gy,$gm,$gd){
		return DATE::gjalali($gy,$gm,$gd);
	}
/**
	* JDateTime class
	*
	* Representation of date and time.
	*
	* @since       1.0
	*/
class JDateTime{
		/* @static  Predefined Constants */
		const ATOM=JDATE_ATOM;
		const COOKIE=JDATE_COOKIE;
		const ISO8601=JDATE_ISO8601;
		const RFC822=JDATE_RFC822;
		const RFC850=JDATE_RFC850;
		const RFC1036=JDATE_RFC1036;
		const RFC1123=JDATE_RFC1123;
		const RFC2822=JDATE_RFC2822;
		const RFC3339=JDATE_RFC3339;
		const RSS=JDATE_RSS;
		const W3C=JDATE_W3C;
		/**
		* @var    int
		* @since  1.0
		*/
		private $timestamp;
		/**
		* DateTimeZone object
		*
		* @var    object
		* @since  1.0
		*/
		private $DateTimeZone;
		/**
		* JDateTime object constructor
		*
		* @param   string  $time  A jdate/time string. Valid formats are explained in JDate and Time Formats.
		* @param   object  $timezone  A DateTimeZone object representing the desired time zone.
		*
		* @since   1.0
		*/
		public function __construct($time="now",DateTimeZone $timezone=null){
			$this->DateTimeZone=empty($timezone) ? new DateTimeZone(date_default_timezone_get()) : $timezone;
			$this->timestamp=$_SERVER['JDATE']->tzstrtotime($time,'',$this->DateTimeZone);
			$this->date=$_SERVER['JDATE']->tzdate('Y-m-d H:i:s',$this->timestamp,$this->DateTimeZone);
			$tz=(array)$this->DateTimeZone;
			$this->timezone_type=$tz['timezone_type'];
			$this->timezone=$tz['timezone'];
		}
		/**
		* Adds an amount of days,months,years,hours,minutes and seconds to a JDateTime object
		*
		* @param   object  $interval  A DateInterval object
		*
		* @return  object  The JDateTime object.
		*
		* @since   1.0
		*/
		public function add(DateInterval $interval){
			$h=(bool)$interval->h ? " +$interval->h hour" : "";
			$i=(bool)$interval->i ? " +$interval->i minute" : "";
			$s=(bool)$interval->s ? " +$interval->s second" : "";
			$y=(bool)$interval->y ? " +$interval->y year" : "";
			$m=(bool)$interval->m ? " +$interval->m month" : "";
			$d=(bool)$interval->d ? " +$interval->d day" : "";
			$this->timestamp=$_SERVER['JDATE']->tzstrtotime($s . $i . $h . $d . $m . $y,$this->timestamp,$this->DateTimeZone);
			$this->date=$_SERVER['JDATE']->tzdate('Y-m-d H:i:s',$this->timestamp,$this->DateTimeZone);
			return $this;
		}
		/**
		* Subtracts an amount of days,months,years,hours,minutes and seconds from a JDateTime object
		*
		* @param   object  $interval  A DateInterval object
		*
		* @return  object  The JDateTime object.
		*
		* @since   1.0
		*/
		public function sub(DateInterval $interval){
			$h=(bool)$interval->h ? " -$interval->h hour" : "";
			$i=(bool)$interval->i ? " -$interval->i minute" : "";
			$s=(bool)$interval->s ? " -$interval->s second" : "";
			$y=(bool)$interval->y ? " -$interval->y year" : "";
			$m=(bool)$interval->m ? " -$interval->m month" : "";
			$d=(bool)$interval->d ? " -$interval->d day" : "";
			$this->timestamp=$_SERVER['JDATE']->tzstrtotime($s . $i . $h . $d . $m . $y,$this->timestamp,$this->DateTimeZone);
			$this->date=$_SERVER['JDATE']->tzdate('Y-m-d H:i:s',$this->timestamp,$this->DateTimeZone);
			return $this;
		}
		/**
		* Alters the timestamp
		*
		* @param   string  $modify  A jdate/time string. Valid formats are explained in JDate and Time Formats.
		*
		* @return  object  The JDateTime object.
		*
		* @since   1.0
		*/
		public function modify($modify){
			$this->timestamp=$_SERVER['JDATE']->tzstrtotime($modify,$this->timestamp,$this->DateTimeZone);
			$this->date=$_SERVER['JDATE']->tzdate('Y-m-d H:i:s',$this->timestamp,$this->DateTimeZone);
			return $this;
		}
		/**
		* Return time zone relative to given JDateTime
		*
		* @return  object  A DateTimeZone object .
		*
		* @since   1.0
		*/
		public function getTimezone(){
			return $this->DateTimeZone;
		}
		/**
		* Sets the time zone for the JDateTime object
		*
		* @param   object  $timezone  A DateTimeZone object representing the desired time zone.
		*
		* @return  object  The JDateTime object.
		*
		* @since   1.0
		*/
		public function setTimezone(DateTimeZone $timezone){
			$this->DateTimeZone=$timezone;
			$tz=(array)$this->DateTimeZone;
			$this->timezone_type=$tz['timezone_type'];
			$this->timezone=$tz['timezone'];
			return $this;
		}
		/**
		* Gets the Unix timestamp
		*
		* @return  int  The Unix timestamp representing the jdate.
		*
		* @since   1.0
		*/
		public function getTimestamp(){
			return $this->timestamp*1;
		}
		/**
		* Sets the jdate and time based on an Unix timestamp
		*
		* @param   int  $unixtimestamp  Unix timestamp representing the jdate.
		*
		* @return  object  The JDateTime object.
		*
		* @since   1.0
		*/
		public function setTimestamp($unixtimestamp){
			$this->timestamp=$unixtimestamp;
			$this->date=$_SERVER['JDATE']->tzdate('Y-m-d H:i:s',$this->timestamp,$this->DateTimeZone);
			return $this;
		}
		/**
		* Returns jdate formatted according to given format
		*
		* @param   string  $format  Format accepted by jdate().
		*
		* @return  string  The formatted date string.
		*
		* @since   1.0
		*/
		public function format($format){
			return $_SERVER['JDATE']->tzdate($format,$this->timestamp,$this->DateTimeZone);
		}
		/**
		* Returns the timezone offset
		*
		* @return  int  The timezone offset in seconds from UTC.
		*
		* @since   1.0
		*/
		public function getOffset(){
			return $this->getTimezone()->getOffset(new JDateTime("now",$this->getTimezone()));
		}
		/**
		* Sets the jdate
		*
		* @param   int  $year  Year of the date.
		* @param   int  $month  Month of the date.
		* @param   int  $day  Day of the date.
		*
		* @return  object  The DateTime object.
		*
		* @since   1.0
		*/
		public function setDate($jy,$jm,$jd){
			$this->timestamp=$_SERVER['JDATE']->tzstrtotime("$jy-$jm-$jd",$this->timestamp,$this->DateTimeZone);
			$this->date=$_SERVER['JDATE']->tzdate('Y-m-d H:i:s',$this->timestamp,$this->DateTimeZone);
			return $this;
		}
		/**
		* Sets the time
		*
		* @param   int  $hour  Hour of the time.
		* @param   int  $minute  Minute of the time.
		* @param   int  $second  Second of the time.
		*
		* @return  object  The DateTime object.
		*
		* @since   1.0
		*/
		public function setTime($h,$m,$s=0){
			$this->timestamp=$_SERVER['JDATE']->tzstrtotime("$h:$m:$s",$this->timestamp,$this->DateTimeZone);
			$this->date=$_SERVER['JDATE']->tzdate('Y-m-d H:i:s',$this->timestamp,$this->DateTimeZone);
			return $this;
		}
		/**
		* Returns the difference between two JDateTime objects
		*
		* @param   object  $datetime2  The jdate to compare to.
		* @param   int  $absolute  Whether to return absolute difference.
		*
		* @return  object  The DateInterval object representing the difference between the two jdates.
		*
		* @since   1.0
		*/
		public function diff(jDateTime $datetime2,$absolute=false){
			$date= new DateTime(date('Y-n-j H:i:s',$this->getTimestamp(),$this->DateTimeZone),$this->DateTimeZone);
			$date2= new DateTime(date('Y-n-j H:i:s',$datetime2->getTimestamp(),$datetime2->DateTimeZone),$datetime2->DateTimeZone);
			return $date->diff($date2,$absolute);
		}
		public static function createFromFormat($format,$time ,DateTimeZone $timezone=null){
		$ms2i=function($str){
			$m=array_search($str,array('farvardin','ordibehesht','khordad','tir','mordad','shahrivar','mehr','aban','azar','dey','bahman','esfand','far','ord','kho','tir','mor','sha','meh','aba','aza','dey','bah','esf'));
			if($m<12)$m++;
			else $m-=11;
			return $m;
		};
		$masks=array(
		// '%a' => '(?P<a>tue|wed|thu|sat|sun|mon|fri)',
		// '%A' => '(?P<A>(?:tues|wednes|thurs|satur|sun|mon|fri)day)',
		'd' => '(?P<d>\d{1,2})',
		'j' => '(?P<d>\d{1,2})',
/* 		'D' => '(?P<D>sun|mon|tue|wed|thu|fri|sat)',
		'I' => '(?P<D>sunday|monday|tuesday|wednesday|thursday|friday|saturday)',*/
		//'S' => '(?P<S>st|nd|rd|th)',
		'z'=>'(?P<z>\d{1,3})',
		'F'=>'(?P<M>farvardin|ordibehesht|khordad|tir|mordad|shahrivar|mehr|aban|azar|dey|bahman|esfand)',
		'M'=>'(?P<M>far|ord|kho|tir|mor|sha|meh|aba|aza|dey|bah|esf)',
		'm'=>'(?P<m>\d{1,2})',
		'n'=>'(?P<m>\d{1,2})',
		'Y'=>'(?P<Y>\d{4})',
		'y'=>'(?P<Y>\d{2})'
		//''=>'(?P<>)'
    );
	 $regex=strtr(preg_quote($format),$masks);
      if(!preg_match("#$regex#i",$time,$out)) return false;
	   if(array_key_exists('d',$out))$d=$out['d'];
	   else {$d=1;$dd=1;}
	   if(array_key_exists('m',$out))$m=$out['m'];
	   if(array_key_exists('Y',$out))$y=strlen($out['Y']<4)?((int)$out['Y']+((jfdate('#c')-1)*100)):$out['Y'];
	   if(array_key_exists('z',$out)){
		   $doy=$out['z'];
				if ($doy < 187){
					$m=(int)(($doy - 1) / 31);
					$d=$doy - (31 * $m++);
				} else{
					$m=(int)(($doy - 187) / 30);
					$d=$doy - 186 - ($m * 30);
					$m += 7;
				}
		}
	   if(array_key_exists('M',$out))$m=$ms2i($out['M']);
	   list($gy,$gm,$gd)=jgregorian($y,$m,$d);
		if(array_key_exists('M',$out))
			$gm=array(1=>'jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec')[$gm];
		$dt=(array)DateTime::createFromFormat($format,str_replace(array((strlen($out['Y']<4)?'(?P<Y>\d{2})':'(?P<Y>\d{4})'),'(?P<m>\d{1,2})','(?P<d>\d{1,2})'),array($gy,$gm,$gd),$regex),$timezone);
		return new JDateTime($_SERVER['JDATE']->tzdate('Y-n-j H:i:s',strtotime($dt['date']),$timezone),$timezone);
		
		}
		/**
		* The __set_state handler
		*
		* @param   array  $array  Initialization array.
		*
		* @return  object  A new instance of a JDateTime object.
		*
		* @since   1.0
		*/
		public static function __set_state($array){
			if (in_array('timezone',$array)){
				if (gettype($array['timezone']) == "object")
					$JDateTime=new JDateTime($array['time'],$array['timezone']);
				else {
					$JDateTime=new JDateTime($array['time']);
					$JDateTime->timezone_type=in_array($array['timezone_type']) ? $array['timezone_type'] : 3;
					$JDateTime->timezone=in_array($array['timezone']) ? $array['timezone'] :
					date_default_timezone_get();
				}
			} else
				$JDateTime=new JDateTime($array['time']);
			return $JDateTime;
		}
		/**
		* The __wakeup handler
		*
		* @return  -  Initializes a JDateTime object.
		*
		* @since   1.0
		*/
		public function __wakeup(){
			$this->__construct($this->date,$this->DateTimeZone);
		}
	}
	function jdate_add(JDateTime $object,DateInterval $interval){
		return  $object->add($interval);
	}
	function jdate_create($time="now" ,DateTimeZone $timezone=NULL){
		return new JDateTime($time,$timezone);
	}
	function jdate_create_from_format($format,$time,DateTimeZone $timezone){
		return JDateTime::createFromFormat($format,$time,$timezone);
	}
	function jdate_diff(JDateTime $datetime1,JDateTime $datetime2,$absolute=false){
		return $datetime1->diff($datetime2,$absolute);
	}
	function jdate_format(JDateTime $object,$format){
		return $object->format($format);
	}
	function jdate_offset_get(JDateTime $object){
		return $object->getOffset();
	}
	function jdate_timestamp_get(JDateTime $object){
		return $object->getTimestamp();
	}
	function jdate_timezone_get(JDateTime $object){
		return $object->getTimezone();
	}
	function jdate_modify(JDateTime $object,$modify){
		return $object->modify($modify);
	}
	function jdate_date_set(JDateTime $object,$year,$month,$day){
		return $object->setDate($year,$month,$day);
	}
	function jdate_time_set(JDateTime $object,$hour,$minute,$second=0){
		return $object->setTime($hour,$minute,$second);
	}
	function jdate_timestamp_set(JDateTime $object,$unixtimestamp){
		return $object->setTimestamp($unixtimestamp);
	}
	function jdate_timezone_set(JDateTime $object,DateTimeZone $timezone){
		return $object->setTimezone($timezone);
	}
	function jdate_sub(JDateTime $object,DateInterval $interval){
		return $object->sub($interval);
	}
	/**
	* Date class
	*
	* Representation of function jdate and time.
	*
	* @since       1.0
	*/
class DATE{
	public $Lang,$TimeZone;
	/**
	* Date object constructor
	*
	* @param   string  $timezone  Set DateTimeZone
	*
	* @since   1.0
	*/
	public function __construct($timezone=JDATE_TimeZone,$word='FA',$num='FA'){
		$this->TimeZone=new DateTimeZone($timezone);
		//	default timezone
		date_default_timezone_set($timezone);
		$this->Lang=new LANG($word,$num);
		$this->DateTime=new Datetime();
	}
	public function set_lang($word='FA',$num='FA'){
		$this->Lang->__construct($word,$num);
	}
	public function set_timezone($timezone=JDATE_TimeZone){
		$this->TimeZone->__construct($timezone);
	}
	/**
	* @see   DATE::date().
	* @see  DATE::gmdate().
	*
	* @since   1.0
	*/
	private function php_date($format,$timestamp='',$gmt=0,DateTimeZone $timezone=null){
		$timestamp=((empty($timestamp)||$timestamp=='now')?jtime():$timestamp);
		$this->DateTime->__construct('@'.$timestamp);
		$this->DateTime->setTimezone($gmt?new DateTimeZone("UTC"):(empty($timezone)?$this->TimeZone:$timezone));
		list($gy,$gm,$gd,$H,$m,$s,$O,$P)=explode('=',$this->DateTime->format('Y=n=j=H=i=s=O=P'));
		list($jy,$jm,$jd)=self::gregorian_jalali($gy,$gm,$gd);
		$jdow=self::day_of_week($jy,$jm,$jd);
		$jdoy=self::day_of_year($jy,$jm,$jd);
		$jleap=self::is_leap($jy);
		$flen=strlen($format);
		$string='';
		$iso=0;
		for ($i=0; $i < $flen; $i++){
			switch ($format{$i}){
				case 'e':
				case 'I':
				case 'T':
				case 'u':
				case 'Z':
					$string .= $this->DateTime->format($format{$i});
					break;
				/* day */
				case 'd':
					$string .= sprintf("%02d",$jd);
					break;
				case 'D':
					$string .= $this->word('+dsn',$jdow);
					break;
				case 'j':
					$string .= $jd;
					break;
				case 'l':
					$string .= $this->word('+dfn',$jdow);
					break;
				case 'S':
					$string .= $this->word('+pes',$jd);
					break;
				case 'w':
					$string .= $jdow;
					break;
				case 'N':
					$string .= $jdow+1;
					break;
				case 'z':
					$string .= $jdoy;
					break;
				/* week */
				case 'W':
					if(!$iso)
						self::week_of_year($jy,$jm,$jd,$iso_week,$iso_year,$iso+=1);
					$string .= $iso_week;
					break;
				/* month */
				case 'F':
					$string .= $this->word('+mfn',$jm);
					break;
				case 'm':
					$string .= sprintf("%02d",$jm);
					break;
				case 'M':
					$string .= $this->word('+msn',$jm);
					break;
				case 'n':
					$string .= $jm;
					break;
				case 't':
					$string .= self::days_in_month($jy,$jm);
					break;
				/* year */
				case 'L':
					$string .= (int)$jleap;
					break;
				case 'o':
					if(!$iso)
						self::week_of_year($jy,$jm,$jd,$iso_week,$iso_year,$iso+=1);
					$string .= $iso_year;
					break;
				case 'y':
					$string .= (int)($jy % 100);
					break;
				case 'Y':
					$string .= sprintf(($jy < 0 ? "-":'')."%04d",abs($jy));
					break;
				/* Swatch Beat a.k.a. Internet Time */
				case 'B':
					$retval = ((($timestamp-($timestamp - (($timestamp % 86400) + 3600))) * 10) / 864);			
					while ($retval < 0) {
						$retval += 1000;
					}
					$string .= $retval % 1000;
					break;
				/* time */
				case 'a':
					$string .= $this->word('+apm',(int)($H / 12));
					break;
				case 'A':
					$string .= $this->word('+apm',(int)($H / 12)+2);
					break;
				case 'H':
					$string .= $H;
					break;
				case 'i':
					$string .= $m;
					break;
				case 's':
					$string .= $s;
					break;
				case 'g':
					$string .= $H%12?:12;
					break;
				case 'G':
					$string .= (int)$H;
					break;
				case 'h':
					$string .= sprintf('%02d',$H%12?:12);
					break;
				/* full date/time */
				case 'c':
					$string .= sprintf("%04d-%02d-%02dT%02d:%02d:%02d%s",$jy,$jm,$jd,$H,$m,$s,$P);
					break;
				case 'r':
					$string .= $this->Lang->getword()!='FA'?sprintf($this->word('+dsn, %02d +mfn',$jdow,$jm) . " %04d %02d:%02d:%02d %s",$jd,$jy,$H,$m,$s,$O):sprintf("%s %02d:%02d:%02d " . $this->word('+dsn, %02d +mfn',$jdow,$jm) . " %04d",$O,$H,$m,$s,$jd,$jy);
					break;
				case 'U':
					$string .= $timestamp;
					break;
				/* timezone */
				case 'O':
					$string .= $O;
					break;
				case 'P':
					$string .= $P;
					break;
				case '\\':
					if ($i < $flen)$i++;
				default:
					$string .= $format{$i};
					break;
			}
		}
		return $string;
	}
	/**
	* @see   DATE::strftime().
	* @see  DATE::gmstrftime().
	*
	* @since   1.0
	*/
	private function php_strftime($format,$timestamp='',$gmt=0){
		list($jy,$jm,$jd,$H,$h,$m,$s,$jdow,$jdoy,$jleap,$O,$T,$P,$woy,$yoy,$timestamp)=explode('=',self::php_date('Y=n=j=H=h=i=s=w=z=L=O=T=P=W=o=U',$timestamp,$gmt));
		$flen=strlen($format);
		$string='';
		for ($i=0; $i < $flen; $i++){
			if ($format{$i} == '%'){
				$i++;
				if($format{$i} == '#'&&$format{$i+1} == 'd')
					$format{$i}='e';
				switch ($format{$i}){
					/* Day */
					case 'a':
						$string .= $this->word('+dsn',$jdow);
						break;
					case 'A':
						$string .= $this->word('+dfn',$jdow);
						break;
					case 'd':
						$string .= sprintf("%02d",$jd);
						break;
					case 'e':
						$string .= sprintf("%d",(int)$jd);
						break;
					case 'j':
						$string .= sprintf("%03d",$jdoy + 1);
						break;
					case 'u':
						$string .= $jdow+1;
						break;
					case 'w':
						$string .= $jdow;
						break;
					/* Week */
					case 'U':
						$avs=(($jdow < 6) ? $jdow + 3 : $jdow - 6) - ($jdoy % 7);
						if ($avs < 0)
						$avs += 7;
						$num=(int)(($jdoy + $avs) / 7) + 1;
						if ($avs > 3 or $avs == 1)
						$num--;
						$string .= sprintf("%02d",$num);
						break;
					case 'V':
						$string .= sprintf("%02d",$woy);
						break;
					case 'W':
						$avs=(($jdow < 6) ? $jdow + 3 : $jdow - 6) - ($jdoy % 7);
						if ($avs < 0)
						$avs += 7;
						$num=(int)(($jdoy + $avs) / 7) + 1;
						if ($avs > 3 or $avs == 1)
						$num--;
						$string .= sprintf("%02d",$num);
						break;
					/* Month */
					case 'b':
					case 'h':
						$string .= $this->word('+msn',$jm);
						break;
					case 'B':
						$string .= $this->word('+mfn',$jm);
						break;
					case 'm':
						$string .= sprintf("%02d",$jm);
						break;
					/* Year */
					case 'C':
						$string .= (int)($jy/100);
						break;
					case 'g':
						$string .=  sprintf("%02d",$yoy%100);
						break;
					case 'G':
						$string .= sprintf("%04d",$yoy);
						break;
					case 'y':
						$string .= $jy%100;
						break;
					case 'Y':
						$string .= $jy;
						break;
					/* Time */
					case 'H':
						$string .= $H;
						break;
					case 'I':
						$string .= $h;
						break;
					case 'l':
						$string .= sprintf("%2d",$h);
						break;
					case 'M':
						$string .= $m;
						break;
					case 'p':
						$string .= $this->word('+apm',$H + 24);
						break;
					case 'P':
						$string .= $this->word('+apm',$H);
						break;
					case 'r':
						$string .= $h . ':' . $m . ':' . $s . ' ' . $this->word('+apm',$H + 24);
						break;
					case 'R':
						$string .= $H . ':' . $m;
						break;
					case 'S':
						$string .= $s;
						break;
					case 'T':
						$string .= $H . ':' . $m . ':' . $s;
						break;
					case 'X':
						$string .= $h . ':' . $m . ':' . $s;
						break;
					case 'z':
						$string .= $O;
						break;
					case 'Z':
						$string .= $T;
						break;
					/* Time and Date Stamps */
					case 'c':
						$string .= $this->Lang->getword()!='FA'?sprintf($this->word('+dfn, %02d +mfn',$jdow,$jm) . " %04d %02d:%02d:%02d %s",$jd,$jy,$H,$m,$s,$P):sprintf("%s %02d:%02d:%02d " . $this->word('+dfn, %02d +mfn',$jdow,$jm) . " %04d",$P,$H,$m,$s,$jd,$jy);
						break;
					case 'D':
						$string .= sprintf("%02d/%02d/%02d",(int)$jy%100,$jm,$jd);
						break;
					case 'F':
						$string .= sprintf("%04d-%02d-%02d",$jy,$jm,$jd);
						break;
					case 's':
						$string .= $timestamp;
						break;
					case 'x':
						$string .= sprintf("%02d/%02d/%02d",(int)$jy % 100,$jm,$jd);
						break;
					/* Miscellaneous */
					case 'n':
						$string .= "\n";
						break;
					case 't':
						$string .= "\t";
						break;
					case '%':
						$string .= '%';
						break;
					default:
						return false;
				}
			} else
				$string .= $format{$i};
		}
		return $string;
	}
	/**
	*	Parse a time/date generated with strjtime() 
	*
	* @see   .
	* @see  .
	*
	* @since   1.0
	*/
	private static function php_strptime($date,$format){
	    $masks=array(
		// '%a' => '(?P<a>tue|wed|thu|sat|sun|mon|fri)',
		// '%A' => '(?P<A>(?:tues|wednes|thurs|satur|sun|mon|fri)day)',
		'%d' => '(?P<d>\d{2})',
		'%e' => '(?P<e>\d{1,2})',
		'%j' => '(?P<j>\d{3})',
		//  '%u' => '(?P<u>\d{1})',
		//  '%w' => '(?P<w>\d{1})',
		'%U' => '(?P<U>\d{1,2})',
		'%V' => '(?P<V>\d{2})',
		'%W' => '(?P<W>\d{1,2})',
		'%b' => '(?P<b>Far|Ord|Kho|Tir|Amo|Sha|Meh|Aba|Aza|Dey|Bah|Esf)',
		'%B' => '(?P<B>Farvardin|Ordibehesht|Khordad|Tir|Amordad|Shahrivar|Mehr|Aban|Azar|Dey|Bahman|Esfand)',
		'%h' => '(?P<h>Far|Ord|Kho|Tir|Amo|Sha|Meh|Aba|Aza|Dey|Bah|Esf)',
		'%m' => '(?P<m>\d{2})',
		'%C' => '(?P<C>\d{2})',
		 '%g' => '(?P<g>\d{2})',
		 '%G' => '(?P<G>\d{4})',
		 '%y' => '(?P<y>\d{2})',
		 '%Y' => '(?P<Y>\d{4})',
		 '%H' => '(?P<H>\d{2})',
		 '%I' => '(?P<I>\d{2})',
		 '%l' => '(?P<l>\d{1,2})',
		 '%M' => '(?P<M>\d{2})',
		 '%p' => '(?P<p>AM|PM)',
		 '%P' => '(?P<P>am|pm)',
		 '%r' => '(?P<r>\d{2}:\d{2}:\d{2} (?:AM|PM))',
		 '%R' => '(?P<R>\d{2}:\d{2})',
		 '%S' => '(?P<S>\d{2})',
		 '%T' => '(?P<T>\d{2})',
		 '%X' => '(?P<X>\d{2}:\d{2}:\d{2})',
		 '%z' => '(?P<z>[-+]?\d{4}|\w{3})',
		 '%Z' => '(?P<Z>[-+]?\d{4}|\w{3})',
		 '%c' => '(?P<c>\w{3} \w{3} \d{1,2} \d{2}:\d{2}:\d{2} \d{4})',
		 '%D' => '(?P<D>\d{4}/\d{2}/\d{2})',
		 '%F' => '(?P<F>\d{4}-\d{2}-\d{2})',
		 '%s' => '(?P<s>\d{8,12})',
		 '%x' => '(?P<x>\d{2}/\d{2}/\d{4})'
    );
	 $regex=strtr(preg_quote($format),$masks);
      if(!preg_match("#$regex#i",$date,$array)) return false;
	  if(array_key_exists('e',$array))$array['d']=$array['e'];
	  if(array_key_exists('b',$array)||array_key_exists('h',$array))$array['m']=array_search(($array['h']||$array['b']),array('Far','Ord','Kho','Tir','Amo','Sha','Meh','Aba','Aza','Dey','Bah','Esf'))+1;
	  if(array_key_exists('B',$array))$array['m']=array_search($array['B'],array('Farvardin','Ordibehesht','Khordad','Tir','Amordad','Shahrivar','Mehr','Aban','Azar','Dey','Bahman','Esfand'))+1;
	   //if(array_key_exists('C',$array))$array['Y']=$array['C'];
	   if(array_key_exists('y',$array)||array_key_exists('g',$array))$array['Y']=($array['y']||$array['g']);
	   if(array_key_exists('G',$array))$array['Y']=$array['G'];
	   // ......> soon ...
	   $tm=jlocaltime(jstrtotime("{$array['Y']}/{$array['m']}/{$array['d']} {$array['H']}:{$array['M']}:{$array['S']}"),1);
	   $tm['unparsed']=implode('',preg_replace('/[:\/ -]/','',preg_split("/%\w/",$format)));
	   unset($tm['tm_isdst']);
	   return $tm;
	}
	/**
	* @see   DATE::mkjtime().
	* @see  DATE::gmmkjtime().
	*
	* @since   1.0
	*/
	private static function php_mktime($h=0,$m=0,$s=0,$jm=0,$jd=0,$jy =0,$is_dst=-1,$gmt=0){
		if (!$h&&!$m&&!$s&&!$jm&&!$jd&&!$jy)
			return ($gmt ? gmmktime() : mktime());
		list($gy,$gm,$gd)=self::jalali_gregorian((int)$jy,(int)$jm,(int)$jd);
		$this->DateTime->__construct(sprintf('%04d/%02d/%02d %02d:%02d:%02d',$gy,$gm,$gd,$h,$m,$s));
		if($gmt){
			$this->DateTime->setTimezone(new DateTimeZone("UTC"));
		}
		return $this->DateTime->format('U')*1;
	}
	/**
	* Parse about any English textual datetime description into a Unix timestamp
	*
	* @param   string  $time  A date/time string. Valid formats are explained in Date and Time Formats.
	* @param   int  $now  The timestamp which is used as a base for the calculation of relative dates.
	*
	* @return int  A timestamp on success,FALSE otherwise.
	*
	* @since   1.0
	*/
	private function php_strtotime($time,$now='',DateTimeZone $timezone=null){
		$now=(empty($now) ? jtime() : $now);
		if ($time == 'now'){
			return $now;
		}
		$time=LANG::words($time,'fa|en');
		$g=$gg=$time;
		$gt=-1;
		$Symbols=array(
		'frac'=>'(\.\d+)',
		'hh'=>'(0?[1-9]|1[0-2])',
		'HH'=>'(2[0-4]|[01]?[0-9])',
		'meridian'=>'([apAP]\.?[mM]\.?[\0\t ]?)',
		'II'=>'([0-5]?[0-9])',
		'SS'=>'([0-5]?[0-9])',
		'tz'=>'(([\(]?[A-Z]{2,6}[\)]?)|(([A-Z][a-z]+)(?:[_\/]([A-Z][a-z]+))+))',
		'tzcorrection'=>"((gmt)?([+-][1-9]|1[0-3]:?[0-5][0-9])?)",
		'daysuf'=>'(th|st|rt|nd)',
		'space'=>'[ \t]',
		'dd'=>'(3[01]|[1-2][0-9]|[1-9])',
		'DD'=>'(3[01]|[1-2][0-9]|0[1-9])',
		'm'=>'(farvardin|ordibehesht|khordad|tir|mordad|shahrivar|mehr|aban|azar|dey|bahman|esfand|far|ord|kho|tir|mor|sha|meh|aba|aza|dey|bah|esf|i|ii|iii|iv|v|vi|vii|viii|ix|x|xi|xii)',
		'M'=>'(far|ord|kho|tir|mor|sha|meh|aba|aza|dey|bah|esf)',
		'mm'=>'(1[0-2]|0?[1-9])',
		'MM'=>'(0[1-9]|1[0-2])',
		'y'=>'([0-9]{1,4})',
		'yy'=>'([0-9]{2})',
		'YY'=>'([0-9]{4})',
		'doy'=>'(00[1-9]|0[1-9][0-9]|[1-2][0-9][0-9]|3[0-5][0-9]|36[0-6])',
		'W'=>'(0[1-9]|[1-4][0-9]|5[0-3])',
		'dayname'=>"(sunday|monday|tuesday|wednesday|thursday|friday|saturday|sun|mon|tue|wed|thu|fri|sat|sun)",
		'daytext'=>"(weekday|weekdays)",
		'number'=>"([+-]?[0-9]+)",
		'ordinal'=>"(first|second|third|fourth|fifth|sixth|seventh|eighth|ninth|tenth|eleventh|twelfth|next|last|previous|this)",
		'reltext'=>"(next|last|previous|this)",
		'unit'=>"((?:(?:sec|second|min|minute|hour|day|fortnight|forthnight|month|year)s?)(?:|weeks|weekday|weekdays))"
		);
		$Formats =array(
		//	START COMPOUND
		'Common_Log'=>"{$Symbols['dd']}\/{$Symbols['M']}\/{$Symbols['YY']}:{$Symbols['HH']}:{$Symbols['II']}:{$Symbols['SS']}{$Symbols['space']}{$Symbols['tzcorrection']}",
		//"{$Symbols['YY']}-{$Symbols['MM']}-{$Symbols['DD']}\s{$Symbols['HH']}:{$Symbols['II']}:{$Symbols['SS']}",
		'EXIF_MSQL'=>"{$Symbols['YY']}[-:]{$Symbols['MM']}[:-]{$Symbols['DD']}\s{$Symbols['HH']}:{$Symbols['II']}:{$Symbols['SS']}",
		'SOAP'=>"{$Symbols['YY']}-{$Symbols['MM']}-{$Symbols['DD']}T{$Symbols['HH']}:{$Symbols['II']}:{$Symbols['SS']}{$Symbols['frac']}{$Symbols['tzcorrection']}?",
		//"{$Symbols['YY']}{$Symbols['MM']}{$Symbols['DD']}t{$Symbols['HH']}{$Symbols['II']}{$Symbols['SS']}",
		'XMLRPC_Compact'=>"{$Symbols['YY']}{$Symbols['MM']}{$Symbols['DD']}T{$Symbols['HH']}:?{$Symbols['II']}:?{$Symbols['SS']}",
		'WDDX'=>"{$Symbols['YY']}-{$Symbols['mm']}-{$Symbols['dd']}T{$Symbols['HH']}:{$Symbols['II']}:{$Symbols['SS']}",
		//"{$Symbols['YY']}-?W{$Symbols['W']}",
		'ISO_YY-wW-Wdd'=>"{$Symbols['YY']}-?W{$Symbols['W']}(?:-?([0-6]))?",
		'Unix_Timestamp'=>"@-?\d+",
		'PSQL_YY.doy'=>"{$Symbols['YY']}\.?{$Symbols['doy']}",
		//	END COMPOUND
		//	START DATE
		//"{$Symbols['mm']}/{$Symbols['dd']}",
		'US_mm-dd-y'=>"{$Symbols['mm']}\/{$Symbols['dd']}(?:\/{$Symbols['y']})?",
		'YY-mm-dd'=>"{$Symbols['YY']}\/{$Symbols['mm']}\/{$Symbols['dd']}",
		'YY-mm'=>"{$Symbols['YY']}-{$Symbols['mm']}",
		'y-mm-dd'=>"{$Symbols['y']}-{$Symbols['mm']}-{$Symbols['dd']}",
	//	'dd-mm-yy'=>"{$Symbols['dd']}[\.\t ]{$Symbols['mm']}[\.\t ]{$Symbols['yy']}",
		'dd-mm-YY'=>"{$Symbols['dd']}[\.\t- ]{$Symbols['mm']}[\.\t- ](?:{$Symbols['YY']}|{$Symbols['yy']})",
		'm-YY'=>"{$Symbols['m']}[ \t\.-]*{$Symbols['YY']}",
		'y-M-DD'=>"{$Symbols['y']}-{$Symbols['M']}-{$Symbols['DD']}",
		'dd-m-y'=>"{$Symbols['dd']}[ \t\.-]*{$Symbols['m']}(?:[ \t\.-]*{$Symbols['y']})?",
	//	'dd-m'=>"{$Symbols['dd']}[ \.\t-]*{$Symbols['m']}",
		'm-dd-y'=>"{$Symbols['m']}[ \.\t-]*{$Symbols['dd']}[\.\t ]*(?:{$Symbols['y']})?",
	//	'm-dd'=>"{$Symbols['m']}[ \.\t-]*{$Symbols['dd']}[,\.\t ]*",
		'M-DD-y'=>"{$Symbols['M']}-{$Symbols['DD']}-{$Symbols['y']}",
		'YY-m'=>"{$Symbols['YY']}[ \t\.-]*{$Symbols['m']}",
		'm'=>"{$Symbols['m']}",
		'YY'=>"{$Symbols['YY']}",
		//iso
		'YY-MM-DD'=>"([+-])?({$Symbols['YY']}|{$Symbols['yy']})[-\/]{$Symbols['MM']}[-\/]{$Symbols['DD']}",
	//	'yy-MM-DD'=>"{$Symbols['yy']}-{$Symbols['MM']}-{$Symbols['DD']}",
	//	'YY.MM.DD'=>"([+-])?{$Symbols['YY']}-{$Symbols['MM']}-{$Symbols['DD']}",
		//	END DATE
		//	START TIME
/* 		//		Time 12
		//"({$Symbols['hh']}{$Symbols['space']}?{$Symbols['meridian']})",
		//"({$Symbols['hh']}[\.:]{$Symbols['II']}{$Symbols['space']}?{$Symbols['meridian']})",
		"{$Symbols['hh']}[\.:]?{$Symbols['II']}?[\.:]?{$Symbols['SS']}?{$Symbols['space']}?{$Symbols['meridian']}",
		"{$Symbols['hh']}:{$Symbols['II']}:{$Symbols['SS']}[\.:]?([0-9]+){$Symbols['meridian']}",
		//		Time 24
		//"(t?{$Symbols['HH']}[\.:]?{$Symbols['II']})",
		'H-i-s'=>"t?{$Symbols['HH']}[\.:]?{$Symbols['II']}[\.:]?{$Symbols['SS']}?",
		'H-i-s_tz'=>"t?{$Symbols['HH']}[\.:]?{$Symbols['II']}[\.:]?{$Symbols['SS']}{$Symbols['space']}?({$Symbols['tzcorrection']}|{$Symbols['tz']})",
		"t?{$Symbols['HH']}[\.:]{$Symbols['II']}[\.:]{$Symbols['SS']}{$Symbols['frac']}",
		'tz'=>"{$Symbols['tzcorrection']}|{$Symbols['tz']}" */
		// END TIME
		//	START RELATIVE
		'fld'=>"(first|last)\sday\s?(?:of\s)?(?:{$Symbols['reltext']}\s{$Symbols['unit']}|{$Symbols['m']}\s{$Symbols['YY']})",
		'fldn'=>"(first|last)\s{$Symbols['dayname']}\s?(?:of\s)?(?:(?:{$Symbols['reltext']}\s{$Symbols['unit']})|(?:{$Symbols['m']}\s{$Symbols['YY']}))"
		//	END RELATIVE
		);
		$time=preg_replace(array("/^\s+|\s+$/","/\s+|\\s/","/\r|\n/","/\t+|\\t/"),array('',' ','','	'),strtolower($time));
		foreach($Formats as $k=>$v){
			preg_match('/'.$v.'/i',$time,$out[$k]);
		}
		$pn='';$y=jdate('Y');$m=0;$d=1;$im='';$id='';
		$out=array_filter($out);
		//var_dump($out);
		$ms2i=function($str){
			$m=array_search($str,array('farvardin','ordibehesht','khordad','tir','mordad','shahrivar','mehr','aban','azar','dey','bahman','esfand','far','ord','kho','tir','mor','sha','meh','aba','aza','dey','bah','esf','I','II','III','IV','V','VI','VII','VIII','IX','X','XI','XII'));
			if($m<12)$m++;
			elseif($m<24)$m-=11;
			else $m-=23;
			return $m;
		};
		if(array_key_exists('m',$out)){
			if(array_key_exists('m-dd-y',$out)){$gt=1;$g=$out['m-dd-y'][0];$m=$ms2i($out['m-dd-y'][1]);$d=$out['m-dd-y'][2];$y=empty($out['m-dd-y'][3])?$y:$out['m-dd-y'][3];}
			if(array_key_exists('M-DD-y',$out)){$id='02';$gt=1;$g=$out['M-DD-y'][0];$m=$ms2i($out['M-DD-y'][1]);$d=$out['M-DD-y'][2];$y=$out['M-DD-y'][3];}
		//	if(array_key_exists('dd-m',$out)){$gt=2;$g=$out['dd-m'][0];$d=$out['dd-m'][1];$m=$ms2i($out['dd-m'][2]);}
			if(array_key_exists('dd-m-y',$out)){$gt=2;$g=$out['dd-m-y'][0];$d=$out['dd-m-y'][1];$m=$ms2i($out['dd-m-y'][2]);$y=empty($out['dd-m-y'][3])?$y:$out['dd-m-y'][3];}
			if(array_key_exists('y-M-DD',$out)){$id='02';$gt=3;$g=$out['y-M-DD'][0];$y=$out['y-M-DD'][1];$m=$ms2i($out['y-M-DD'][2]);$d=$out['y-M-DD'][3];}
			if(array_key_exists('YY',$out)){
				if(array_key_exists('m-YY',$out)){$gt=2;$g=$out['m-YY'][0];$d=1;$m=$ms2i($out['m-YY'][1]);$y=$out['m-YY'][2];}
				if(array_key_exists('YY-m',$out)){$gt=3;$g=$out['YY-m'][0];$y=$out['YY-m'][1];$m=$ms2i($out['YY-m'][2]);$d=1;}
			}
		}
		if(array_key_exists('US_mm-dd-y',$out)){$gt=1;$g=$out['US_mm-dd-y'][0];$m=$out['US_mm-dd-y'][1];$d=$out['US_mm-dd-y'][2];$y=empty($out['US_mm-dd-y'][3])?$y:$out['US_mm-dd-y'][3];}
	//	if(array_key_exists('dd-mm-yy',$out)){$gt=2;$g=$out['dd-mm-yy'][0];$d=$out['dd-mm-yy'][1];$m=$out['dd-mm-yy'][2];$y=$out['dd-mm-yy'][3];}
		if(array_key_exists('y-mm-dd',$out)){$gt=3;$g=$out['y-mm-dd'][0];$y=$out['y-mm-dd'][1];$m=$out['y-mm-dd'][2];$d=$out['y-mm-dd'][3];}
		//if(array_key_exists('yy-MM-DD',$out)){$im='02';$id='02';$gt=3;$g=$out['yy-MM-DD'][0];$y=$out['yy-MM-DD'][1];$m=$out['yy-MM-DD'][2];$d=$out['yy-MM-DD'][3];}
		if(array_key_exists('YY',$out)){
			if(array_key_exists('YY-mm-dd',$out)){$gt=3;$g=$out['YY-mm-dd'][0];$y=$out['YY-mm-dd'][1];$m=$out['YY-mm-dd'][2];$d=$out['YY-mm-dd'][3];}
			if(array_key_exists('YY-mm',$out)&&!array_key_exists('y-mm-dd',$out)){$gt=3;$g=$out['YY-mm'][0];$y=$out['YY-mm'][1];$m=$out['YY-mm'][2];$d=1;}
			if(array_key_exists('dd-mm-YY',$out)){$gt=2;$g=$out['dd-mm-YY'][0];$d=$out['dd-mm-YY'][1];$m=$out['dd-mm-YY'][2];$y=$out['dd-mm-YY'][3];}
			if(array_key_exists('YY-MM-DD',$out)){$im='02';$id='02';$gt=3;$g=$out['YY-MM-DD'][0];$y=$out['YY-MM-DD'][1];$m=$out['YY-MM-DD'][2];$d=$out['YY-MM-DD'][3];}
			//if(array_key_exists('YY.MM.DD',$out)){$im='02';$id='02';$gt=3;$g=$out['YY.MM.DD'][0];$pn=$out['YY.MM.DD'][1];$y=$out['YY.MM.DD'][2];$m=$out['YY.MM.DD'][3];$d=$out['YY.MM.DD'][4];}
		}
		if(array_key_exists('Common_Log',$out)){
			$gt=2;
			$g=explode(':',$out['Common_Log'][0])[0];
			$y=$out['Common_Log'][3];
			$m=$ms2i($out['Common_Log'][2]);
			$d=$out['Common_Log'][1];
		}
		elseif(array_key_exists('EXIF_MSQL',$out)){
			$gt=3;
			$g=explode(' ',$out['EXIF_MSQL'][0])[0];
			$y=$out['EXIF_MSQL'][1];
			$m=$out['EXIF_MSQL'][2];
			$d=$out['EXIF_MSQL'][3];
		}
		elseif(array_key_exists('XMLRPC_Compact',$out)){
			$gt=3;
			$g=preg_split('/t/i',$out['XMLRPC_Compact'][0])[0];
			$y=$out['XMLRPC_Compact'][1];
			$m=$out['XMLRPC_Compact'][2];
			$d=$out['XMLRPC_Compact'][3];
		}
		elseif(array_key_exists('ISO_YY-wW-Wdd',$out)){
			$gt=6;
			$g=$out['ISO_YY-wW-Wdd'][0];
			$y=$out['ISO_YY-wW-Wdd'][1];
			$w=$out['ISO_YY-wW-Wdd'][2];
			$wd=empty($out['ISO_YY-wW-Wdd'][3])?0:$out['ISO_YY-wW-Wdd'][3]+1;
			if($w<53){
				$doy=(int)$w*7;
				if ($doy<187){
					$m=(int)(($doy-1)/31);
					$d=$doy-(31*$m++);
				} else{
					$m=(int)(($doy-187)/30);
					$d=$doy-186-($m*30);
					$m +=7;
				}
				$dow=self::day_of_week($y,$m,$d);
				if($wd)
					$dw=self::day_of_week($y,$m,$d)>$wd?$dow-$wd:$wd-$dow;
				else
					$dw=0;
				$d-=$dw;
				if($d<1){
					$m--;
					$d=($m<6)?31-$d:30-$d;
				}elseif($d>30&&$m>6){
					$m++;
					$d=30-$d;
				}elseif($d>31&&$m<7){
					$m++;
					$d=31-$d;
				}
			}else {
				$y++;
				$m=1;
				$d=$w*7-364+self::is_leap(++$y);
				$dow=self::day_of_week($y,$m,$d);
				if($wd)
					$dw=self::day_of_week($y,$m,$d)>$wd?$dow-$wd:$wd-$dow;
				else
					$dw=0;
				$d-=$dw;
			}
		}
		elseif(array_key_exists('PSQL_YY.doy',$out)&&!array_key_exists('YY-MM-DD',$out)){
			$gt=5;
			$g=$out['PSQL_YY.doy'][0];
			$y=$out['PSQL_YY.doy'][1];
			$doy=$out['PSQL_YY.doy'][2];
			if ($doy < 187){
				$m=(int)(($doy - 1) / 31);
				$d=$doy - (31 * $m++);
			} else{
				$m=(int)(($doy - 187) / 30);
				$d=$doy - 186 - ($m * 30);
				$m += 7;
			}
		}
		elseif(array_key_exists('WDDX',$out)){
			$g=preg_split('/t/i',$out['WDDX'][0])[0];
			$y=$out['WDDX'][1];
			$m=$out['WDDX'][2];
			$d=$out['WDDX'][3];
		}
		elseif(array_key_exists('SOAP',$out)){
			$gt=3;
			$g=preg_split('/t/i',$out['SOAP'][0])[0];
			$y=$out['SOAP'][1];
			$m=$out['SOAP'][2];
			$d=$out['SOAP'][3];
		}
		if(array_key_exists('Unix_Timestamp',$out)){
			$gt=10;
			$gg=$g=$out['Unix_Timestamp'][0];
		}
		if(array_key_exists('fld',$out)){
			$gt=5;
			$g=$out['fld'][0];
			$time.=date(' H:i:s',$now);
			if(array_key_exists('m-YY',$out)){$y=$out['m-YY'][2];$m=$ms2i($out['m-YY'][1]);}
			else {$y=jdate('Y');$m=jdate('n');}
			if($out['fld'][2]=='next'){
				if($out['fld'][3]=='month')$m++;
				else $y++;
			}
			elseif(!empty($out['fld'][2])) {
				if($out['fld'][3]=='month')$m--;
				else $y--;
			}
			if($out['fld'][1]=='first')$d=1;
			else $d=self::days_in_month((int)$y,(int)$m);
		}
		if(array_key_exists('fldn',$out)){
// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		}
		$sly=strlen($y);
		if($sly<4){
			if($sly==1)$y=(int)(jdate('Y')/10)*10+$y;
			elseif($sly==2)$y=(($y<=(int)((jdate('Y')+40)%100))?jfdate('#c')*100+$y:(jfdate('#c')-1)*100+$y);
		}
		//if(!jcheckdate($m,$d,$y))return false;
		list($gy,$gm,$gd)=self::jalali_gregorian((int)$y,(int)$m,(int)$d);
		if($sly<4){
			if($sly==1)$gy=$gy%10;
			elseif($sly==2)$gy=sprintf("%02d",$gy%100);
		}$gy=sprintf("%04d",$gy);
		//if(!checkdate($gm,$gd,$gy))return false;
		if(array_key_exists('m',$out))
			$gm=array(1=>'jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec')[$gm];
		$match=array('','');
		preg_match("/(\t| |\/|-|\.|:)/",$g,$match);
		$match=empty($match)?array('',''):$match;
 		if($gt==1)$gg=sprintf((array_key_exists('m',$out)?'%s':"%{$im}d")."{$match[0]}%{$id}d{$match[1]}%d",$gm,$gd,$gy);
		elseif($gt==2)$gg=sprintf("%{$id}d{$match[0]}".(array_key_exists('m',$out)?'%s':"%{$im}d")."{$match[1]}%d",$gd,$gm,$gy);
		elseif($gt==3)$gg=sprintf("$pn%04d{$match[0]}".(array_key_exists('m',$out)?'%s':"%{$im}d")."{$match[1]}%{$id}d",$gy,$gm,$gd);
		elseif($gt==5)$gg=sprintf("$gy.%03d",date('z',strtotime("$gy-$gm-$gd"))+1);
		elseif($gt==6)$gg="$gy\W".date('W-w',strtotime("$gy/$gm/$gd"));
		if(empty($timezone))$timezone=$this->TimeZone;
		//var_dump($gt,$gg,str_replace($g,str_replace($g,$gg,$g),$time),strlen(array_key_exists('m',$out)));
		$this->DateTime->__construct($this->php_date('Y-n-j H:i:s',$now,0,$timezone),$timezone);
		$this->DateTime->modify(str_replace($g,str_replace($g,$gg,$g),$time));
		//$DateTime->setTimezone($timezone);
		return $this->DateTime->format('U')*1;//return strtotime(str_replace($g,str_replace($g,$gg,$g),$time),$now);
		
		/*
		* for ($i=0,$len=count($match); $i < $len; $i++) {
		* $splt=split(' ',$match[$i]); // Todo: Reconcile this with regex using \s,taking into account browser issues with split and regexes
		* $type=$splt[0];
		* $range=substr($splt[1],0,3);
		* $typeIsNumber=preg_match('/\d+/',$type);
		* $ago=($splt[2] == 'ago');
		* $num=($type == 'last' ? -1 : 1) * ($ago ? -1 : 1);
		* if ($typeIsNumber) {
		* $num *= (int)$type;
		* }
		* // in_array()
		* if (in_array($range,$ranges)&&!preg_match('/^mon(day|\.)?$/i',$splt[1])) {
		* return $date['set' + $ranges[$range]]($date['get' + $ranges[$range]]() + $num);
		* }
		* if ($range == 'wee') {
		* return $date.setDate(self::date('d') + ($num * 7));
		* }
		* if ($type == 'next' || $type == 'last') {
		* $day=$days[$range];
		* if ($day !== 'undefined') {
		* $diff=$day - self::date('d',$now);
		* if ($diff == 0) {
		* $diff=7 * $num;
		* } else if ($diff > 0&&$type == 'last') {
		* $diff -= 7;
		* } else if ($diff < 0&&$type == 'next') {
		* $diff += 7;
		* }
		* //$date.setDate(self::date('d',$now) + $diff);
		* }
		* } else if (!$typeIsNumber) {
		* return false;
		* }
		* }
		*/
	}
	/**
	* Format a local time/date as integer
	*
	* @param   string  $format  The following characters are recognized in the format parameter string.
	* @param   int  $timestamp  The optional timestamp parameter is an integer Unix timestamp that defaults to the current local time if a timestamp is not given.
	*												In other words,it defaults to the value of jtime().
	* @return int  an integer.
	*
	* @since   1.0
	*/
	public function idate($format,$timestamp=''){
		if(strlen($format)>1)return false;
		switch($format){
			/* day */
			case 'd':
			case 'j':
				return (int)self::php_date("j",$timestamp);
			case 'w':
				return (int)self::php_date("w",$timestamp);
			case 'z':
				return (int)self::php_date("z",$timestamp);
			/* week */
			case 'W':
				return (int)self::php_date("W",$timestamp);
			/* month */
			case 'm':
			case 'n':
				return (int)self::php_date("n",$timestamp);
			case 't':
				return (int)self::php_date("t",$timestamp);
			/* year */
			case 'L':
				return (int)self::php_date("L",$timestamp);
			case 'y':
				return (int)self::php_date("y",$timestamp);
			case 'Y':
				return (int)self::php_date("Y",$timestamp);
			/* Swatch Beat a.k.a. Internet Time */
			case 'B':
				return (int)self::php_date("B",$timestamp);
			/* time */
			case 'g':
			case 'h':
				return (int)self::php_date("h",$timestamp);
			case 'H':
			case 'G':
				return (int)self::php_date("G",$timestamp);
			case 'i':
				return (int)self::php_date("i",$timestamp);
			case 's':
				return (int)self::php_date("s",$timestamp);
			/* timezone */
			case 'I':
				return (int)self::php_date("I",$timestamp);
			case 'Z':
				return (int)self::php_date("Z",$timestamp);
			case 'U':
				return empty($timestamp) ? jtime() : $timestamp*1;
			default:
				return false;
		}
	}
	public function fdate($format,$timestamp=''){
		list($jy,$jm,$jd,$jleap,$jdoy)=explode("=",self::php_date("Y=n=j=L=z",$timestamp));
		$flen=strlen($format);
		$string='';
		for ($i=0; $i < $flen; $i++){
			switch ($format{$i}){
				case 'c':$string .= (int)($jy/100);break;
				case 'C':$string .= (int)($jy/100)+1;break;
				case 'e':$string .= (int)((($jy)%100)/10+1);break; // decade
				case 's':$string .= (int)($jm/3.1)+1;break; // decade%
				case 'S':$string .= $this->word('+sfn',(int)($jm/3.1));break;
				case 'a':$string .= $this->word('+asn',$jm);break;
				case 'A':$string .= $this->word('+afn',$jm);break;
				case 'B':$string .= $this->word('+bfn',$jy%12);break;
				case 'z':$string .= $jdoy+1;break;
				case 'Z':$string .= (int)(($jdoy/365)*100)+1;break;
				case 'r':$string .= $jleap+365-$jdoy;break;
				case 'R':$string .= (int)((($jleap+365-$jdoy)/365)*100);break;
				case 'l':$string .= $jleap;break;
				case 'L':$string .= $this->word('+icy',$jleap);break;
				case 'y':$string .= $jy;break;
				case 'Y':$string .= $this->word('+i2s',$jy);break;
				case 'm':$string .= $jm;break;
				case 'M':$string .= $this->word('+i2s',$jm);break;
				case 'd':$string .= $jd;break;
				case 'D':$string .= $this->word('+i2s',$jd);break;
				case 'n':$string .= self::new_year($jy);break;
				case 't':$string .= self::sm_solstice($jm,$jd);break;
				case 'U':$string .= (empty($timestamp)||$timestamp=='now')?jtime():$timestamp;break;
				case '\\':
				default:$string .= $format{$i};break;
			}
		}
		return ($this->Lang->getnum()!='FA')?$string:$this->Lang->nums($string);
	}
	/**
	* A formatted int to string
	*
	* @param   string  $format  The following characters are recognized in the format parameter string.
	* @param   mixed  $args  mixed.
	* @param   mixed  $...  mixed.
	*
	* @return  string  A string produced according to the formatting string format.
	*
	* @since   1.0
	*/
	private function word(){
		$args=func_get_args();
		$format=array_shift($args);
		if (gettype($args[0]) == 'array')$args=$args[0];
		$count=0;
		$flen=strlen($format);
		$string='';
		for ($i=0; $i < $flen; $i++){
			if ($format{$i} == '+'){
				$num=(int)$args[$count];
				$str=substr($format,++$i,3);
				switch ($str){
					case 'pes': // persian|english_suffix
						$english_suffix=function ($num) {
							if ($num >= 10&&$num <= 19)
								return 0;
							else
								switch ($num % 10){
									case 1:
									return 1;
									case 2:
									return 2;
									case 3:
									return 3;
								}
							return 0;
						};
						$string .= $this->Lang->getword()!='FA'?array('th','st','nd','rd')[$english_suffix($num)]:'ام';
						break;
					case 'apm': // ante|post_meridiem
						$string .= $this->Lang->getword()!='FA'?array('am','pm','AM','PM')[$num]:array('ﻗ.ظ','ﺑ.ظ','قبل از ظهر','بعد از ظهر')[$num];
						break;
					case 'i2s': // int_to_str
						include_once "num2word.class.php";
						new Num2Word($num,strtolower($this->Lang->getword()),1,$n2w);
						$string .= $n2w;
						break;
					case 'mfn': // month_full_names
						$string .= $this->Lang->getword()!='FA'?array(1=>'Farvardin','Ordibehesht','Khordad','Tir','Mordad','Shahrivar','Mehr','Aban','Azar','Dey','Bahman','Esfand')[$num]:array(1=>'فروردین','اردیبهشت','خرداد','تیر','مرداد','شهریور','مهر','آبان','آذر','دی','بهمن','اسفند')[$num];
						break;
					case 'msn': // month_short_names
						$string .= $this->Lang->getword()!='FA'?array(1=>'Far','Ord','Kho','Tir','Mor','Sha','Meh','Aba','Aza','Dey','Bah','Esf')[$num]:array(1=>'فر','ار','خر','ﺗﻳ','مر','ﺷﻬ‍','ﻣﻬ','آﺑ','آذ','ﺑﻬ‍','اﺳ‍')[$num];
						break;
					case 'dfn': // day_full_names
						$string .= $this->Lang->getword()!='FA'?array('Shanbeh','Yekshanbeh','Doshanbeh','Sehshanbeh','Chaharshanbeh','Panjshanbeh','Jomeh')[$num]:array('شنبه','یکشنبه','دوشنبه','سه شنبه','چهارشنبه','پنجشنبه','جمعه')[$num];
						break;
					case 'dsn': // day_short_names
						$string .= $this->Lang->getword()!='FA'?array('Sh','Ye','Do','Se','Ch','Pa','Jo')[$num]:array('ﺷ','ﻳ','د','ﺳ','ﭼ','ﭘ','ﺟ')[$num];
						break;
					case 'bfn': // bestial_full_names
						$string .= $this->Lang->getword()!='FA'?array('Snake','Horse','Sheep','Monkey','Chicken','Dog','Pig','Mouse','Cow','Panther','Rabbit','Whale')[$num]:array('مار','اسب','گوسفند','میمون','مرغ','سگ','خوک','موش','گاو','پلنگ','خرگوش','نهنگ')[$num];
						break;
					case 'afn': // asterism_full_names
						$string .= $this->Lang->getword()!='FA'?array('Aries','Taurus','Gemini','Cancer','Leo','Virgo','Libra','Scorpio','Sagittarius','Capricorn','Aquarius','Pisces')[$num]:array('حمل','ثور','جوزا','سرطان','اسد','سنبله','میزان','عقرب','قوس','جدی','دلو','حوت')[$num];
						break;
					case 'asn': // asterism_short_names
						$string .= $this->Lang->getword()!='FA'?array('Aries','Taurus','Gemini','Cancer','Leo','Virgo','Libra','Scorpio','Sagittarius','Capricorn','Aquarius','Pisces')[$num]:array('حمل','ثور','جوز','سرط','اسد','سنبل','میز','عقر','قوس','جدی','دلو','حوت')[$num];
						break;
					case 'sfn': // season_full_names
						$string .= ($this->Lang->getword()!='FA')?array('spring','summer','fall','winter')[$num]:array('بهار','تابستان','پاییز','زمستان')[$num];
						break;
					case 'icy': // season_full_names
						$string .= ($this->Lang->getword()!='FA')?array('','leap')[$num]:array('','کبیسه')[$num];
						break;
					default:
						$string .= "{unknown}";
				}
				$i += 2;
				$count++;
			} else
				$string .= $format{$i};
		}
		return $string;
	}
	/**
	* Convertor Gregorian to Jalali (Hijri_Shamsi,Solar)
	*
	* @param   int  $gy  The number of the year (Gregorian).
	* @param   int  $gm  The number of the month (Gregorian).
	* @param   int  $gd  The number of the day (Gregorian).
	*
	* @return  array  Solar date.
	*
	* @since   1.0
	*/
	private static function gregorian_jalali($gy,$gm,$gd){
		$jdoy=(($gy-1)*365+(array(1=>0,31,59,90,120,151,181,212,243,273,304,334)[$gm]+$gd))-(226745 /*621*365+80*/)+abs(self::is_leap($gy,'G',1)-self::is_leap($gy-621,'J',1));
		if(self::is_leap($gy,'G')&&$gm>2)$jdoy++;
		$jy=(int)($jdoy/365)+1;
		$jd=$jdoy%365;
		$jleap=self::is_leap($jy);
		if($gm<4&&$jleap&&$jy==$gy-622)$jd++;
		foreach(array(1=>31,31,31,31,31,31,30,30,30,30,30,($jleap+29)) as $jm=>$dims){
			if ($jd<=$dims)break;
			$jd-=$dims;
		}
		if($jd==0){$jy--;$jm=12;$jd=self::is_leap($jy)+29;}
		return array($jy,$jm,$jd);
	}
	/**
	* Convertor Jalali (Hijri_Shamsi,Solar) to Gregorian
	*
	* @param   int  $jy  The number of the year (Solar).
	* @param   int  $jm  The number of the month (Solar).
	* @param   int  $jd  The number of the day (Solar).
	*
	* @return  array  Gregorian date.
	*
	* @since   1.0
	*/
	private static function jalali_gregorian($jy,$jm,$jd){
		$gdoy=(($jy-1)*365+self::day_of_year($jy,$jm,$jd)+1)+(226745 /*621*365+80*/)-abs(self::is_leap($jy+621,'G',1)-self::is_leap($jy,'J',1));
		$gy=(int)($gdoy/365)+1;
		$gd=$gdoy%365;
		$prev_gleap=self::is_leap($gy-1,'G');
		$jleap=self::is_leap($jy);
		if(($prev_gleap&&$gy==$jy+622)||($jleap&&$prev_gleap&&$jm>11))$gd--;
		foreach(array(1=>31,(self::is_leap($gy,'G')+28),31,30,31,30,31,31,30,31,30,31) as $gm=>$dims){
			if ($gd<=$dims)break;
			$gd-=$dims;
		}
		if($gd==-1){$gy--;$gm=12;$gd=30;}
		if($gd==0){$gy--;$gm=12;$gd=31;}
		return array($gy,$gm,$gd);
	}
	/**
	* Numeric representation of the day of the week
	*
	* @param   int  $jy  The number of the year.
	* @param   int  $jm  The number of the month.
	* @param   int  $jd  The number of the day.
	*
	* @return  int  0 through 6
	*
	* @since   1.0
	*/
	private static function day_of_week($jy,$jm,$jd){
		return ((1127+$jy)*365.2422+self::day_of_year($jy,$jm,$jd)-1)%7;
	}
	public static  function dow($jy,$jm,$jd){
		return ($this->Lang->getnum()!='FA')?self::day_of_week($jy,$jm,$jd):$this->Lang->nums(self::day_of_week($jy,$jm,$jd));
	}
	/**
	* The day of the year
	*
	* @param   int  $jy  The number of the year.
	* @param   int  $jm  The number of the month.
	* @param   int  $jd  The number of the day.
	*
	* @return  int  0 through 365
	*
	* @since   1.0
	*/
	private static function day_of_year($jy,$jm,$jd){
		return ((($jm < 7)?(($jm-1)*31):(($jm-7)*30+186))+$jd-1);
	}
	public static function doy($jy,$jm,$jd){
		return ($this->Lang->getnum()!='FA')?self::day_of_year($jy,$jm,$jd):$this->Lang->nums(self::day_of_year($jy,$jm,$jd));
	}
	/**				000000000000000000
	*/
	function year_of_day($jdoy){
		if ($jdoy < 187){
			$jm=(int)(($jdoy - 1) / 31);
			$jd=$jdoy - (31 * $jm++);
		} else{
			$jm=(int)(($jdoy - 187) / 30);
			$jd=$jdoy - 186 - ($jm * 30);
			$jm += 7;
		}
		return array($jm,$jd);
	}
	/**
	* The week of the year
	*
	* @param   int  $jy  The number of the year.
	* @param   int  $jm  The number of the month.
	* @param   int  $jd  The number of the day.
	*
	* @return  int  0 through 365
	*
	* @since   1.0
	*/
	private static function week_of_year($jy,$jm,$jd,&$iw=0,&$iy=0){
		$doy = self::day_of_year($jy, $jm, $jd) + 1;
		$far1weekday = self::day_of_week($jy, 1, 1)+1;
		/* Find if Y M D falls in YearNumber Y-1, WeekNumber 52 or 53 */
		if (($doy<=8-$far1weekday) && $far1weekday>4) {
			$iy = --$jy;
			$iw = ($far1weekday==5||($far1weekday==6&&self::is_leap($iy)))?53:52;
		}else {
		/* Find if Y M D falls in YearNumber Y+1, WeekNumber 1 */
			$weekday = self::day_of_week($jy, $jm, $jd)+1;
			if (365-$doy+self::is_leap($jy) < 4-$weekday) {
				$iy = ++$jy;
				$iw = 1;
				return $iw;
			}
		/* Find if Y M D falls in YearNumber Y, WeekNumber 1 through 52|53 */
			$iy = $jy;
			$iw = ($doy+6-$weekday+$far1weekday) / 7;
			if ($far1weekday > 4) --$iw;
		}
		return $iw;
	}
	public static function woy($jy,$jm,$jd){
		return ($this->Lang->getnum()!='FA')?self::week_of_year($jy,$jm,$jd):$this->Lang->nums(self::week_of_year($jy,$jm,$jd));
	}
	/**
	* Number of days in the given month
	*
	* @param   int  $jy  The number of the year.
	* @param   int  $jm  The number of the month.
	*
	* @return  int  29 through 31
	* @since   1.0
	*/
	private static function days_in_month($jy,$jm){
		return ($jm > 11)? (self::is_leap($jy)+29):(($jm>6)?30:31);
	}
	public static function dims($jy,$jm){
		return ($this->Lang->getnum()!='FA')?self::days_in_month($jy,$jm):$this->Lang->nums(self::days_in_month($jy,$jm));
	}
	/**
	* Number of days in the given year
	*
	* @param   int  $jy  The number of the year.
	*
	* @return  int  365 through 366
	* @since   1.0
	*/
	private static function days_in_year($jy){
		return self::is_leap($jy)+365;
	}
	public static function diys($jy){
		return ($this->Lang->getnum()!='FA')?self::days_in_year($jy):$this->Lang->nums(self::days_in_year($jy));
	}
	private static function new_year($jy){//	norooz		next year
		$h=(float)('.'.explode('.',(1129+$jy)*365.2422059)[1])*24;
		$m=(float)('.'.explode('.',$h)[1])*60;
		$s=(float)('.'.explode('.',$m)[1])*60;
		return (($h*60)>=(12.1*60+1)?(--$jy).'/12/'.(29+self::is_leap($jy)):$jy.'/01/01')." ".(int)$h.':'.(int)$m.':'.(int)$s;
	}
	private static function sm_solstice($jm,$jd){//	Summer Solstice		||	winter Solstice	|	Midwinter
		if($jm==8&&$jd==30)
			return 2 .' yalda';
		if($jm==4&&$jd==1)
			return 1 .' temoz';
		return 0;
	}
	/**
	* it's a leap year
	*
	* @param   int  $jy  The number of the year.
	*
	* @return  int  1 if it is a leap year,0 otherwise.
	* @since   1.0
	*/
	public static function is_leap($y,$type='J',$all=0){
		if(ord(strtoupper($type))==74)
			return empty($all)?(bool)(((int)(($y+=1128)*365.2422)-(int)(--$y*365.2422))-365):(ceil((($y+=1127)*365.2422)-$y*365)-274);
			return empty($all)?(bool)((($y%4==0)&&!(($y%100==0)&&($y%400!= 0)))?1:0):(ceil((int)((--$y)/4)-(int)(($y)/100)+(int)(($y)/400))-150);
	}
	/**
	* Get Unix timestamp for a date
	*
	* @param   int  $h  The number of the hour.
	* @param   int  $m  The number of the minute.
	* @param   int  $s  The number of seconds.
	* @param   int  $jm  The number of the month.
	* @param   int  $jd  The number of the day.
	* @param   int  $jy  The number of the year.
	* @param   int  $is_dst  1 if the time is during daylight savings time (DST),0 if it is not,or -1 (the default) if it is unknown whether the time is within daylight savings time.
	*
	* @return  int  The Unix timestamp of the arguments given. If the arguments are invalid,the function returns FALSE .
	* @since   1.0
	*/
	public function mktime($h=0,$m=0,$s=0,$jm=0,$jd=0,$jy =0,$is_dst=-1){
		return self::php_mktime($h,$m,$s,$jm,$jd,$jy,$is_dst,0);
	}
	/**
	* Get Unix timestamp for a GMT date
	*
	* @param   int  $h  The number of the hour.
	* @param   int  $m  The number of the minute.
	* @param   int  $s  The number of seconds.
	* @param   int  $jm  The number of the month.
	* @param   int  $jd  The number of the day.
	* @param   int  $jy  The number of the year.
	* @param   int  $is_dst  Parameters always represent a GMT date so is_dst doesn't influence the result.
	*
	* @return  int  a integer Unix timestamp.
	*
	* @since   1.0
	*/
	public function gmmktime($h=0,$m=0,$s=0,$jm=0,$jd=0,$jy =0,$is_dst=-1){
		return self::php_mktime($h,$m,$s,$jm,$jd,$jy,$is_dst,1);
	}
	/**
	* Get date/time information
	*
	* @param   int  $timestamp  The optional timestamp parameter is an integer Unix timestamp that defaults to the current local time if a timestamp is not given. In other words,it defaults to the value of jtime().
	*
	* @return  array  an associative array of information related to the timestamp.
	*
	* @since   1.0
	*/
	public function getdate($timestamp=''){
		list($jy,$jm,$jd,$h,$m,$s,$sjm,$sjdow,$jdow,$jdoy)=explode('=',$this->php_date('Y=n=j=G=i=s=F=l=w=z',$timestamp));
		return array(
		'seconds' => (int)$s,
		'minutes' => (int)$m,
		'hours' => (int)$h,
		'mday' => (int)$jd,
		'wday' => (int)$jdow,
		'mon' => (int)$jm,
		'year' => (int)$jy,
		'yday' => (int)$jdoy,
		'weekday' => $sjdow,
		'month' => $sjm,
		0 => $timestamp*1
		);
	}
	/**
	* Get current time
	*
	* @param   bool  $return_float  When set to TRUE,a float instead of an array is returned.
	*
	* @return  array  By default an array. If return_float is set,then a float.
	*
	* @since   1.0
	*/
	public function gettimeofday($return_float=false){
		if (!$return_float){
			$array=gettimeofday(0);
			$array['minuteseast']=$array['minuteswest']*-1;
		}
		return $return_float ? gettimeofday(1): $array;
	}
	/**
	* Get the local time
	*
	* @param   int  $timestamp  The optional timestamp parameter is an integer Unix timestamp that defaults to the current local time if a timestamp is not given. In other words,it defaults to the value of jtime().
	* @param   bool  $is_associative  If set to FALSE,numerically indexed array. If set to TRUE,associative array containing all.
	*
	* @return  array  Numerically indexed array or associative array containing all.
	* @since   1.0
	*/
	public function localtime($timestamp='',$is_associative=false){
		list($jy,$jm,$jd,$h,$m,$s,$isdst,$jdow,$jdoy)=explode('=',self::php_date('Y=n=j=G=i=s=I=w=z',$timestamp));
		$array=array(
		'tm_sec' => (int)$s,
		'tm_min' => (int)$m,
		'tm_hour' => (int)$h,
		'tm_mday' => (int)$jd,
		'tm_mon' => $jm - 1,
		'tm_year' => $jy - 1300,
		'tm_wday' => (int)$jdow,
		'tm_yday' => (int)$jdoy,
		'tm_isdst' => (int)$isdst
		);
		return $is_associative?$array:array_values($array);
	}
	/**
	* Validate a Solar(Jalali) date
	*
	* @param   int  $jm  Month of the date(Jalali).
	* @param   int  $jd  Day of the date(Jalali).
	* @param   int  $jy  Year of the date(Jalali).
	*
	* @return  bool  TRUE if the date given is valid; otherwise returns FALSE.
	* @since   1.0
	*/
	public function checkdate($jm,$jd,$jy){
		return (bool)(($jy<1||$jy>3500000||$jm<1||$jm>12||$jd<1||$jd>self::days_in_month($jy,$jm))?0:1);
	}
	/**
	* Validate a Solar(Jalali) time
	*
	* @param   int  $h  Hour of the time.
	* @param   int  $i  Minute of the time.
	* @param   int  $s  Second of the time.
	*
	* @return  bool  TRUE if the date given is valid; otherwise returns FALSE.
	* @since   1.0
	*/
	public function checktime($h,$i,$s){
		return (bool)(($h<0||$h>23||$i<0||$i>59||$s<0||$s>59)?0:1);
	}
	/**
	* Format a local time/date according to locale settings
	*
	* @param   string  $format  The following characters are recognized in the format parameter string.
	* @param   int  $timestamp  The optional timestamp parameter is an integer Unix timestamp that defaults to the current local time if a timestamp is not given.In other words,it defaults to the value of jtime().
	*
	* @return  string  A string formatted according to the given format string using the given timestamp or the current local time if no timestamp is given.
	* @see DATE::php_strftime().
	* @since   1.0
	*/
	public function strftime($format,$timestamp=''){
		return ($this->Lang->getnum()!='FA')?self::php_strftime($format,$timestamp,0):$this->Lang->nums(self::php_strftime($format,$timestamp,0));
	}
	/**
	* Format a GMT/UTC time/date according to locale settings
	*
	* @param   string  $format  See description in strfjtime().
	* @param   int  $timestamp  See description in strfjtime().
	*
	* @return  string  A string formatted according format using the given timestamp or the current local time if no timestamp is given.
	* @see DATE::php_strfjtime().
	* @since   1.0
	*/
	public function gmstrftime($format,$timestamp=''){
		return ($this->Lang->getnum()!='FA')?self::php_strftime($format,$timestamp,1):$this->Lang->nums(self::php_strftime($format,$timestamp,1));
	}
	/**
	* Parse about any English textual datetime description into a Unix timestamp
	*
	* @param   string  $time  A date/time string. Valid formats are explained in Date and Time Formats.
	* @param   int  $now  The timestamp which is used as a base for the calculation of relative dates.
	*
	* @return int  A timestamp on success,FALSE otherwise.
	*
	* @since   1.0
	*/
	public function strtotime($format,$timestamp=''){
		return self::php_strtotime($format,$timestamp);
	}
	public function tzstrtotime($format,$timestamp='',DateTimeZone $timezone=null){
		return self::php_strtotime($format,$timestamp,$timezone);
	}
	/**
	* Format a local time/date
	*
	* @param   string  $format  The following characters are recognized in the format parameter string.
	* @param   int  $timestamp  The optional timestamp parameter is an integer Unix timestamp that defaults to the current local time if a timestamp is not given.
	*												In other words,it defaults to the value of jtime().
	* @return  string  A formatted date string.
	* @see DATE::php_date().
	* @since   1.0
	*/
	public function date($format,$timestamp=''){
		return ($this->Lang->getnum()!='FA')?self::php_date($format,$timestamp,0):$this->Lang->nums(self::php_date($format,$timestamp,0));
	}
	public function tzdate($format,$timestamp='',DateTimeZone $timezone=null){
		return self::php_date($format,$timestamp,0,$timezone);
	}
	/**
	* Format a GMT/UTC date/time
	*
	* @param   string  $format  See description in DATE::date().
	* @param   int  $timestamp  See description in DATE::date().
	*
	* @return  string  A formatted date string.
	* @see DATE::php_date().
	* @since   1.0
	*/
	public function gmdate($format,$timestamp=''){
		return ($this->Lang->getnum()!='FA')?self::php_date($format,$timestamp,1):$this->Lang->nums(self::php_date($format,$timestamp,1));
	}
	/**
	* Convertor Jalali (Hijri_Shamsi,Solar) to Gregorian
	*
	* @param   int  $jy  Year of the date(Jalali).
	* @param   int  $jm  Month of the date(Jalali).
	* @param   int  $jd  Day of the date(Jalali).
	*
	* @return  array  Gregorian date.
	*
	* @link  http://date.akhi.ir
	*/
	public static function jgregorian($jy,$jm,$jd){
		return self::jalali_gregorian($jy,$jm,$jd);
	}
	/**
	* Convertor Gregorian to Jalali (Hijri_Shamsi,Solar)
	*
	* @param   int  $gy  Year of the date(Gregorian).
	* @param   int  $gm  Month of the date(Gregorian).
	* @param   int  $gd  Day of the date(Gregorian).
	*
	* @return  array  Jalali date.
	*
	* @link  http://date.akhi.ir
	*/
	public static function gjalali($gy,$gm,$gd){
		return self::gregorian_jalali($gy,$gm,$gd);
	}
	/* public static function date_parse($date){
	//		NOT Support meh mordad farvardin       1393/2/30
		return date_parse($date);
		/* array(
			'year',
			'month',
			'day',
			'hour',
			'minute',
			'second',
			'fraction',
			'warning_count',
			'warnings',
			'error_count',
			'errors',
			'is_localtime'
		); *\/
	} */
/* 	public static function date_parse_from_format($format,$date){
		//		NOT Support meh mordad farvardin       1393/2/30
		//return JDateTime::createFromFormat($format,$date,$this->TimeZone);
		return date_parse_from_format($format,$date);
	} */

}
	function jtimezone_set($timezone=JDATE_TimeZone){
		return $_SERVER['JDATE']->set_timezone($timezone);
	}
	function jlang_set($word='FA',$num='FA'){
		return $_SERVER['JDATE']->set_lang($word,$num);
	}
	function jdate($format,$timestamp=''){
		return $_SERVER['JDATE']->date($format,$timestamp);
	}
	function jgmdate($format,$timestamp=''){
		return $_SERVER['JDATE']->gmdate($format,$timestamp);
	}
	function jmktime($h=0,$m=0,$s=0,$jm=0,$jd=0,$jy =0,$is_dst =-1){
		return DATE::mktime($h,$m,$s,$jm,$jd,$jy,$is_dst);
	}
	function jgmmktime($h=0,$m=0,$s=0,$jm=0,$jd=0,$jy =0,$is_dst =-1){
		return DATE::gmmktime($h,$m,$s,$jm,$jd,$jy,$is_dst);
	}
	function jgetdate($timestamp=''){
		return $_SERVER['JDATE']->getdate($timestamp);
	}
	function jstrftime($format,$timestamp=''){
		return $_SERVER['JDATE']->strftime($format,$timestamp);
	}
	function jgmstrftime($format,$timestamp=''){
		return $_SERVER['JDATE']->gmstrftime($format,$timestamp);
	}
	function jlocaltime($timestamp='',$is_associative=false){
		return $_SERVER['JDATE']->localtime($timestamp,$is_associative);
	}
	function jgettimeofday($return_float=false){
		return DATE::gettimeofday($return_float);
	}
	function jcheckdate($jm,$jd,$jy){
		return DATE::checkdate($jm,$jd,$jy);
	}
	function jchecktime($h,$i,$s){
		return DATE::checktime($h,$i,$s);
	}
	function jstrtotime($time,$now=''){
		return $_SERVER['JDATE']->strtotime($time,$now);
	}
	function jidate($format,$timestamp=''){
		return $_SERVER['JDATE']->idate($format,$timestamp);
	}
	function jfdate($format,$time='now'){
		return $_SERVER['JDATE']->fdate($format,$time);
	}

class LANG{
	public $num;
	public $word;
	public function __construct($word='FA',$num='FA'){
		$this->num=empty($num)?'FA':$num;
		$this->word=strtoupper(empty($word)?'FA':$word);
	}
	public function setnum($num='FA'){
		$this->num=$num;
	}
	public function getnum(){
		return $this->num;
	}
	public function setword($word='FA'){
		$this->word=strtoupper($word);
	}
	public function getword(){
		return $this->word;
	}
	public static function nums($str,$rel='en|fa',$dec=','){
		$split=range(0,2);
		preg_match('/(\w{2,3})?\|?(\w{2,3})?/',(string)$rel,$split);
		$L=empty($rel) ? 'en': (empty($split[1]) ? 'en': $split[1]);
		$L2=empty($rel) ? 'fa': (empty($split[2])? 'fa': $split[2]);
		$en=array('0','1','2','3','4','5','6','7','8','9','.');
		$fa=array('۰','۱','۲','۳','۴','۵','۶','۷','۸','۹',$dec);
		$fa2=array('٠','١','٢','٣','٤','٥','٦','٧','٨','٩',$dec);
		return str_replace( ${$L}, ${$L2},$str);
	}
	public static function words($str,$rel='en|fa',$type=0){
		$split=range(0,2);
		preg_match('/(\w{2,3})?\|?(\w{2,3})?/',(string)$rel,$split);
		$L=empty($rel) ? 'en': (empty($split[1]) ? 'en': $split[1]);
		$L2=empty($rel) ? 'fa': (empty($split[2])? 'fa': $split[2]);
		$en=array('spring','summer','fall','winter','shanbeh','yekshanbeh','doshanbeh','seshanbeh','chaharshanbeh','panjshanbeh','jomeh','sh','ye','do','se','ch','pa','jo','farvardin','ordibehesht','khordad','tir','mordad','shahrivar','mehr','aban','azar','dey','bahman','esfand','far','ord','kho','tir','mor','sha','meh','aba','aza','bah','esf','th','st','nd','rd','aries','taurus','gemini','cancer','leo','virgo','libra','scorpio','sagittarius','capricorn','aquarius','pisces','snake','horse','sheep','monkey','chicken','dog','pig','mouse','cow','panther','rabbit','whale');
		$fa=array('بهار','تابستان','پاییز','زمستان','شنبه','یکشنبه','دوشنبه','سه شنبه','چهارشنبه','پنجشنبه','جمعه','ﺷ','ﻳ','د','ﺳ','ﭼ','ﭘ','ﺟ','فروردین','اردیبهشت','خرداد',($type?'ﺗﻳ':'تیر'),'مرداد','شهریور','مهر','آبان','آذر','دی','بهمن','اسفند','فر','ار','خر','ﺗﻳ','مر','ﺷﻬ‍','ﻣﻬ','آﺑ','آذ','ﺑﻬ‍','اﺳ‍','ام','ام','ام','ام','حمل','ثور','جوزا','سرطان','اسد','سنبله','میزان','عقرب','قوس','جدی','دلو','حوت','مار','اسب','گوسفند','میمون','مرغ','سگ','خوک','موش','گاو','پلنگ','خرگوش','نهنگ');
		$en2=array('am','pm','AM','PM');
		$fa2=array('ﻗ.ظ','ﺑ.ظ','قبل از ظهر','بعد از ظهر');
		return str_ireplace( ${$L},${$L2},str_replace( ${$L."2"},${$L2."2"},$str));
	}
}
	function jnums($str,$rel='en|fa',$dec=','){
		return LANG::nums($str,$rel,$dec);
	}
	function jwords($str,$rel='en|fa',$type=0){
		return LANG::words($str,$rel,$type);
	}
   // $f='first day of mehr 1393';
   // $fd='last day of next year';
//$strf=jstrftime($f);
	


 	function shamsi_ghamari($jy,$jm,$jd,$tcs=1){
		list($c,$c2)=explode(".",(((int)(--$jy)*365.2422+($tcs+((($jm<7)?(($jm-1)*31):(($jm-7)*30+186))+$jd-1))-119)/354.3670));	
		$hy=++$c;
		list($m,$m2)=explode(".",(".".$c2)*12);
		$hm=++$m;
		$hd=(".".$m2)*29.530;
		return "$hy/$hm/$hd";
	}

