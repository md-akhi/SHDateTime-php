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
	
    require_once("SHBase.php");
        
    class SHDateInterval extends DateInterval{
        /* Properties */
    /* 	public $y;
        public $m;
        public $d;
        public $h;
        public $i;
        public $s;
        public $invert;
        public $days; */

        /* Methods */
    /* 	public function __construct($interval_spec){
            parent::__construct($interval_spec);
        }

        public static function createFromDateString($time){
            return parent::createFromDateString($time);
        }

        public function format($format){
            return parent::format($format);
        }  */
    }
    class SDateInterval extends SHDateInterval{}

    class SHDateTimeZone extends DateTimeZone{}
    class SDateTimeZone extends SHDateTimeZone{}

    interface SHDateTimeInterface {
        /* Methods */
        public function diff(SHDateTimeInterface $datetime2,$absolute=false);
        public function format($format);
        public function getOffset();
        public function getTimestamp();
        public function getTimezone();
        //public function __wakeup();
    }
    interface SDateTimeInterface extends SHDateTimeInterface {}

    class SHDatePeriod extends DatePeriod {
        /* Constants */
        const EXCLUDE_START_DATE = 1 ;

        /* Methods */
        //public function __construct(SHDateTimeInterface $start,SHDateInterval $interval,SHDateTimeInterface $end,$options=self::EXCLUDE_START_DATE)
        //public function __construct($isostr,$options=self::EXCLUDE_START_DATE)
        public function __construct( $start, $interval_options,$end_recurrences=self::EXCLUDE_START_DATE,$options=self::EXCLUDE_START_DATE){
        //parent::__construct($start,$interval,$recurrences,$options);
        //parent::__construct($start,$interval,$end,$options);
        //parent::__construct($isostr,$options);
        }

        public function getDateInterval(){
            return parent::getDateInterval();
        }

        public function getEndDate(){
            return parent::getEndDate();
        }

        public function getStartDate(){
            return parent::getStartDate();
        }
    }
    class SDatePeriod extends SHDatePeriod {}

    /*
    DateTimeImmutable

    DateTimeImmutable implements DateTimeInterface {
        /* Methods *\/
        public __construct ([ string $time = "now" [, DateTimeZone $timezone = NULL ]] )
        public DateTimeImmutable add ( DateInterval $interval )
        public static DateTimeImmutable createFromFormat ( string $format , string $time [, DateTimeZone $timezone ] )
        public static DateTimeImmutable createFromMutable ( DateTime $datetime )
        public static array getLastErrors ( void )
        public DateTimeImmutable modify ( string $modify )
        public static DateTimeImmutable __set_state ( array $array )
        public DateTimeImmutable setDate ( int $year , int $month , int $day )
        public DateTimeImmutable setISODate ( int $year , int $week [, int $day = 1 ] )
        public DateTimeImmutable setTime ( int $hour , int $minute [, int $second = 0 [, int $microseconds = 0 ]] )
        public DateTimeImmutable setTimestamp ( int $unixtimestamp )
        public DateTimeImmutable setTimezone ( DateTimeZone $timezone )
        public DateTimeImmutable sub ( DateInterval $interval )
        public DateInterval diff ( DateTimeInterface $datetime2 [, bool $absolute = false ] )
        public string format ( string $format )
        public int getOffset ( void )
        public int getTimestamp ( void )
        public DateTimeZone getTimezone ( void )
        public __wakeup ( void )
    }
    */
    if(!defined(DATE_RFC3339_EXTENDED))
        define(DATE_RFC3339_EXTENDED,'');
    /**
    * SHDateTime class
    * Representation of date and time.
    * @since       1.0
    */
	class SHDateTime extends SHDateBase implements SHDateTimeInterface{
        /* Predefined Constants */
        const ISO8601=DATE_ISO8601;
        const RFC822=DATE_RFC822;
        const RFC850=DATE_RFC850;
        const RFC1036=DATE_RFC1036;
        const RFC1123=DATE_RFC1123;
        const RFC2822=DATE_RFC2822;
        const RFC3339=DATE_RFC3339;
		const RFC3339_EXTENDED=DATE_RFC3339_EXTENDED;// Added php 7.0.0
        const COOKIE=DATE_COOKIE;
        const ATOM=DATE_ATOM;
        const RSS=DATE_RSS;
        const W3C=DATE_W3C;
        /**
        * @var    int
        * @since  1.0
        */
        private $timestamp;
       // private $Default_word;
        /**
        * DateTimeZone object
        *
        * @var    object
        * @since  1.0
        */
        private $DateTimeZone;
        /**
        * SDATE object
        *
        * @var    object
        * @since  1.0
        */
       // private $SDATE;
        /**
        * SHDateTime object constructor
        *
        * @param   string  $time  A SDATE/time string. Valid formats are explained in SDATE and Time Formats.
        * @param   object  $timezone  A DateTimeZone object representing the desired time zone.
        *
        * @since   1.0
        */
        public function __construct($time="now",DateTimeZone $timezone=null){
			$this->DateTime = new DateTime("now",$timezone);
			$this->setTimezone($timezone)->setTimestamp(self::strtotime($time));
            //$this->date = $this->format('Y-m-d H:i:s');
        }

		/**
        * Sets the time zone for the SHDateTime object
        *
        * @param   object  $timezone  A DateTimeZone object representing the desired time zone.
        *
        * @return  object  The SHDateTime object.
        *
        * @since   1.0
        */
        public function setTimezone(DateTimeZone $timezone=null){
			if($timezone)
				$this->DateTimeZone = $timezone;
			else
				$this->DateTimeZone = new DateTimeZone(date_default_timezone_get());
			$this->timezone_type = $this->DateTimeZone->timezone_type;
			$this->timezone = $this->DateTimeZone->getName();
			$this->DateTime->setTimezone($this->DateTimeZone);
            return $this;
        }
        /**
        * Return time zone relative to given SHDateTime
        *
        * @return  object  A DateTimeZone object .
        *
        * @since   1.0
        */
        public function getTimezone(){
            return $this->DateTimeZone;
        }
        /**
        * Sets the SDATE and time based on an Unix timestamp
        *
        * @param   int  $unixtimestamp  Unix timestamp representing the SDATE.
        *
        * @return  object  The SHDateTime object.
        *
        * @since   1.0
        */
        public function setTimestamp($unixtimestamp){
            $this->timestamp = self::time($unixtimestamp);
			$this->DateTime->setTimestamp($this->timestamp);
            //$this->date = $this->format('Y-m-d H:i:s');
            return $this;
        }
        /**
        * Gets the Unix timestamp
        *
        * @return  int  The Unix timestamp representing the SDATE.
        *
        * @since   1.0
        */
        public function getTimestamp(){
            return $this->timestamp;
        }
	    /**
        * Alters the timestamp
        *
        * @param   string  $modify  A SDATE/time string. Valid formats are explained in SDATE and Time Formats.
        *
        * @return  object  The SHDateTime object.
        *
        * @since   1.0
        */
        public function modify($modify){
            $this->setTimestamp(self::strtotime($modify));
            //$this->date = $this->format('Y-m-d H:i:s');
            return $this;
        }
        /**
        * Adds an amount of days,months,years,hours,minutes and seconds to a SHDateTime object
        *
        * @param   object  $interval  A DateInterval object
        *
        * @return  object  The SHDateTime object.
        *
        *
        *
        * @since   1.0
        */
        public function add(SHDateInterval $interval){
            $y=(bool)$interval->y ? " +$interval->y year" : "";
            $m=(bool)$interval->m ? " +$interval->m month" : "";
            $d=(bool)$interval->d ? " +$interval->d day" : "";
            $h=(bool)$interval->h ? " +$interval->h hour" : "";
            $i=(bool)$interval->i ? " +$interval->i minute" : "";
            $s=(bool)$interval->s ? " +$interval->s second" : "";
			//$this->timestamp+($y*365+day_of_year($y,$m,$d))*24*60*60+$h*60*60+$i*60+$s;
            $this->setTimestamp(strtotime($s . $i . $h . $d . $m . $y,$this->timestamp));
            //$this->date = $this->format('Y-m-d H:i:s');
            return $this;
        }
        /**
        * Subtracts an amount of days,months,years,hours,minutes and seconds from a SHDateTime object
        *
        * @param   object  $interval  A DateInterval object
        *
        * @return  object  The SHDateTime object.
        *
        * @since   1.0
        */
        public function sub(SHDateInterval $interval){
            $y=(bool)$interval->y ? " -$interval->y year" : "";
            $m=(bool)$interval->m ? " -$interval->m month" : "";
            $d=(bool)$interval->d ? " -$interval->d day" : "";
            $h=(bool)$interval->h ? " -$interval->h hour" : "";
            $i=(bool)$interval->i ? " -$interval->i minute" : "";
            $s=(bool)$interval->s ? " -$interval->s second" : "";
			//$this->timestamp-($y*365+day_of_year($y,$m,$d))*24*60*60+$h*60*60+$i*60+$s;
            $this->setTimestamp(strtotime($s . $i . $h . $d . $m . $y,$this->timestamp));
            //$this->date = $this->format('Y-m-d H:i:s');
            return $this;
        }
        /**
        * Returns SDATE formatted according to given format
        *
        * @param   string  $format  Format accepted by SDATE().
        *
        * @return  string  The formatted date string.
        *
        * @since   2.0
        */
        public function format($format){
			return self::date($format,false,false,$this->DateTime);
		}

        /**
        * Returns the timezone offset
        *
        * @return  int  The timezone offset in seconds from UTC.
        *
        * @since   1.0
        */
        public function getOffset(){// conver difference UTC
            return $this->DateTime->getOffset();
        }
        /**
        * Sets the SDATE
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
			$this->setTimestamp(self::strtotime("$jy/$jm/$jd",$this->getTimestamp()));
            //$this->date = $this->format('Y-m-d H:i:s');
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
            $this->setTimestamp(self::strtotime("$h:$m:$s",$this->getTimestamp()));
            //$this->date = $this->format('Y-m-d H:i:s');
            return $this;
        }
        /**
        * Returns the difference between two SHDateTime objects
        *
        * @param   object  $datetime2  The SDATE to compare to.
        * @param   int  $absolute  Whether to return absolute difference.
        *
        * @return  object  The DateInterval object representing the difference between the two SDATEs.
        *
        * @since   1.0
        */
        public function diff(SHDateTimeInterface $datetime2,$absolute=false){
            $date= new DateTime(date('Y-n-j H:i:s',$this->getTimestamp()),$this->DateTimeZone);
            $date2= new DateTime(date('Y-n-j H:i:s',$datetime2->getTimestamp()),$datetime2->DateTimeZone);
            return $date->diff($date2,$absolute);
        }
        public static function createFromFormat($format,$time ,DateTimeZone $timezone=null){
        $ms2i=function($str){
            $m=array_search($str,array('farvardin','ordibehesht','khordad','tir','mordad','shahrivar','mehr','aban','azar','dey','bahman','esfand','far','ord','kho','tir','mor','sha','meh','aba','aza','dey','bah','esf'));
            if($m<12)$m++;
            else $m-=11;
            return $m;
        };
		$USymbols=array(
			'daysuf'=>'[sr]t|nd|th',
			// find by serach array and str%7 OR|| return array(0...6)[str]
			'Dd'=>"(?:sat|sun|mon|tue|wed|thu|fri)(?:[ursn][rse]?s?)?(?:day)?",
			//'dd'=>'[0-2]?[0-9]|3[01]',
			'DD'=>'3[01]|[1-2][0-9]|0?[1-9]',// 02|2 - 31
			// find by serach array and str%12 OR|| return array(0...11)[str]
			'Mm'=>'far(?:vardin)?|ord(?:ibehesht)?|kho(?:rdad)?|tir|a?mor?(?:dad)?|sha(?:hrivar)?|meh(?:r)?|aza(?:r)?|aba(?:n)?|dey|bah(?:man)?|esf(?:and)?|i[vx]|viii|vii|vi|xii|xi|iii|ii|x|i|v',
			//'mm'=>'0?\d|1[0-2]',
			'MM'=>'1[0-2]|0?[1-9]',// 01|1 m
			'doy'=>'36[0-6]|3[0-5]\d|[1-2]\d{2}|0[1-9]\d|00[1-9]',// 365 doy
			//'W'=>'5[0-3]|[1-4][0-9]|0[1-9]',// 52 w
			//'y'=>'1[34]\d{2}|[34]\d{2}|\d{2}|\d',//\d{1,4}
			'yy'=>'\d{2}',// 96 y
			'YY'=>'1[34]\d{2}',// 1396 y
			//'frac'=>'\.?(\d+)',// .123
			//'meridian'=>'[ap]\.?m\.?',//
			//'ss'=>'[0-5]?\d',
			'SS'=>'[0-5]?\d',// 8|08 s
			//'ii'=>'[0-5]?\d',
			'II'=>'[0-5]?\d',// 9|09 i
			'hh'=>'1[0-2]|0?[1-9]',// 12
			'HH'=>'2[0-4]|[01]?\d',// 24
			//'space'=>'[ \t]',
			'tz'=>'[a-z]+(?:[_\/]([a-z]+))+|\(?([a-z]{4,6})\)?',
			'tzcorrection'=>"(gmt)?([+-])(1[0-2]|0?[1-9]):?([0-5]?\d)?",
			//'number'=>"[+-]?\d+",// +12 | -12
			//'dot'=>'[:\.-\/]',// :|.|/
			//'daytext'=>"weekday|weekdays",
			//'ordinal'=>"first|second|third|fourth|fifth|sixth|seventh|eighth|ninth|tenth|eleventh|twelfth|next|last|previous|this",
			//'reltext'=>"next|last|previous|this",
			//'unit'=>"(?:(?:sec|second|min|minute|hour|day|fortnight|forthnight|month|year)s?)(?:|weeks|weekday|weekdays)"
			);
        $masks=array(
        // '%a' => '(?P<a>tue|wed|thu|sat|sun|mon|fri)',
        // '%A' => '(?P<A>(?:tues|wednes|thurs|satur|sun|mon|fri)day)',
        'd' => '(?P<d>\d{1,2})',
        'j' => '(?P<d>\d{1,2})',
/*         'D' => '(?P<D>sun|mon|tue|wed|thu|fri|sat)',
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
       list($gy,$gm,$gd)=self::solartogregorian($y,$m,$d);
        if(array_key_exists('M',$out))
            $gm=array(1=>'jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec')[$gm];
        $dt=(array)DateTime::createFromFormat($format,str_replace(array((strlen($out['Y']<4)?'(?P<Y>\d{2})':'(?P<Y>\d{4})'),'(?P<m>\d{1,2})','(?P<d>\d{1,2})'),array($gy,$gm,$gd),$regex),$timezone);
        return new SHDateTime(self::date('Y-n-j H:i:s',strtotime($dt['date']),$timezone),$timezone);
        }
        /**
        * The __set_state handler
        *
        * @param   array  $array  Initialization array.
        *
        * @return  object  A new instance of a SHDateTime object.
        *
        * @since   1.0
        */
/*         public static function __set_state($array){
            if (in_array('timezone',$array)){
                if (gettype($array['timezone']) == "object")
                    $SHDateTime=new SHDateTime($array['time'],$array['timezone']);
                else {
                    $SHDateTime=new SHDateTime($array['time']);
                    $SHDateTime->timezone_type=in_array($array['timezone_type']) ? $array['timezone_type'] : 3;
                    $SHDateTime->timezone=in_array($array['timezone']) ? $array['timezone'] :
                    date_default_timezone_get();
                }
            } else
                $SHDateTime=new SHDateTime($array['time']);
            return $SHDateTime;
        } */
        /**
        * The __wakeup handler
        *
        * @return  -  Initializes a SHDateTime object.
        *
        * @since   1.0
        */
/*         public function __wakeup(){
            $this->__construct($this->date,$this->DateTimeZone);
        } */



    }
    class_alias("SHDateTime","SDateTime");

    function sdate_add(SHDateTimeInterface $object,SHDateInterval $interval){
        return  $object->add($interval);
    }
    function shdate_add(SHDateTimeInterface $object,SHDateInterval $interval){
        return  $object->add($interval);
    }
    function sdate_create($time="now" ,DateTimeZone $timezone=NULL){
        return new SHDateTime($time,$timezone);
    }
    function shdate_create($time="now" ,DateTimeZone $timezone=NULL){
        return new SHDateTime($time,$timezone);
    }
    function sdate_create_from_format($format,$time,DateTimeZone $timezone){
        return SHDateTime::createFromFormat($format,$time,$timezone);
    }
    function shdate_create_from_format($format,$time,DateTimeZone $timezone){
        return SHDateTime::createFromFormat($format,$time,$timezone);
    }
    function sdate_diff(SHDateTimeInterface $datetime1,SHDateTimeInterface $datetime2,$absolute=false){
        return $datetime1->diff($datetime2,$absolute);
    }
    function shdate_diff(SHDateTimeInterface $datetime1,SHDateTimeInterface $datetime2,$absolute=false){
        return $datetime1->diff($datetime2,$absolute);
    }
    function sdate_format(SHDateTimeInterface $object,$format){
        return $object->format($format);
    }
    function shdate_format(SHDateTimeInterface $object,$format){
        return $object->format($format);
    }
    function sdate_offset_get(SHDateTimeInterface $object){
        return $object->getOffset();
    }
    function shdate_offset_get(SHDateTimeInterface $object){
        return $object->getOffset();
    }
    function sdate_timestamp_get(SHDateTimeInterface $object){
        return $object->getTimestamp();
    }
    function shdate_timestamp_get(SHDateTimeInterface $object){
        return $object->getTimestamp();
    }
    function sdate_timezone_get(SHDateTimeInterface $object){
        return $object->getTimezone();
    }
    function shdate_timezone_get(SHDateTimeInterface $object){
        return $object->getTimezone();
    }
    function sdate_modify(SHDateTimeInterface $object,$modify){
        return $object->modify($modify);
    }
    function shdate_modify(SHDateTimeInterface $object,$modify){
        return $object->modify($modify);
    }
    function sdate_date_set(SHDateTimeInterface $object,$year,$month,$day){
        return $object->setDate($year,$month,$day);
    }
    function shdate_date_set(SHDateTimeInterface $object,$year,$month,$day){
        return $object->setDate($year,$month,$day);
    }
    function sdate_time_set(SHDateTimeInterface $object,$hour,$minute,$second=0){
        return $object->setTime($hour,$minute,$second);
    }
    function shdate_time_set(SHDateTimeInterface $object,$hour,$minute,$second=0){
        return $object->setTime($hour,$minute,$second);
    }
    function sdate_timestamp_set(SHDateTimeInterface $object,$unixtimestamp){
        return $object->setTimestamp($unixtimestamp);
    }
    function shdate_timestamp_set(SHDateTimeInterface $object,$unixtimestamp){
        return $object->setTimestamp($unixtimestamp);
    }
    function sdate_timezone_set(SHDateTimeInterface $object,DateTimeZone $timezone){
        return $object->setTimezone($timezone);
    }
    function shdate_timezone_set(SHDateTimeInterface $object,DateTimeZone $timezone){
        return $object->setTimezone($timezone);
    }
    function sdate_sub(SHDateTimeInterface $object,SHDateInterval $interval){
        return $object->sub($interval);
    }
    function shdate_sub(SHDateTimeInterface $object,SHDateInterval $interval){
        return $object->sub($interval);
    }

