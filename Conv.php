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
    
class SHDATE_CONVERT{
	// Languages
	const fa_IR = 'fa_IR'; //Persion	fa
	const en_US = 'en_US'; //English	en
	const DEFAULT_LANG = self::fa_IR;
	const LANG_WORD = SSDATETIME_LANG_WORD;
	
	private static function set_lang($lang){
		switch($lang){
			case self::fa_IR:return self::fa_IR;
			case self::en_US:return self::en_US;
			default:return self::DEFAULT_LANG;
		}
	}
    /* public function __call($name, $arguments){
		foreach($arguments as $arg){
			if(strlen($arg)>1)
				return $this->$name($arg[1],$arg[2]);
			return $this->$name($arg);
		}
    }
    public function __set($name, $value){
    }
    public function __get($name) {
    } */

    public static function Cnum($str,$Convert=self::LANG_WORD){
        $en_US = SDATE_en_US::DIGIT;// array('0','1','2','3','4','5','6','7','8','9','.');
        $fa_IR = SDATE_fa_IR::DIGIT;// array('٠','١','٢','٣','۴','۵','۶','٧','٨','٩',',');
        // Languages available in the world
		$Convert = self::set_lang($Convert);
        foreach(array($en_US,$fa_IR,$fa_IR_2/* ,'Languages' */) as $lang){
            if($Convert == $lang)continue;
            $str=str_replace(${$lang}, ${$Convert},$str);
        }
        return $str;
    }
    public static function Cword($str,$Convert=self::LANG_WORD){
		// array_push()
		// EN   en_US
        $en_US=array(
		'',
		'Spring','Summer','Fall','Winter',
		'Saturday','Sunday','Monday','Tuesday','Wednesday','Thursday','Friday',
		'Sat','Sun','Mon','Tue','Wed','Thu','Fri',
		'Farvardin','Ordibehesht','Khordad','Tir','Amordad','Mordad','Shahrivar','Mehr','Aban','Azar','Dey','Bahman','Esfand',
		'Far','Ord','Kho','Tir','Amo','Mor','Sha','Meh','Aba','Aza','Bah','Esf',
		'th','st','nd','rd',
		'Aries','Taurus','Gemini','Cancer','Leo','Virgo','Libra','Scorpio','Sagittarius','Capricorn','Aquarius','Pisces',
		'Snake','Horse','Sheep','Monkey','Chicken','Dog','Pig','Mouse','Cow','Panther','Rabbit','Whale');
        $en_US_I=array('am','pm','AM','PM');
		// FA   fa_IR
        $fa_IR=array(
		'',
		'بهار','تابستان','پاييز','زمستان',
		'شنبه','يکشنبه','دوشنبه','سه شنبه','چهارشنبه','پنجشنبه','جمعه',
		'ش','ي','د','س','چ','پ','ج',
		'فروردين','ارديبهشت','خرداد','تير','امرداد','امرداد','شهريور','مهر','آبان','آذر','دي','بهمن','اسفند',
		'فر','ار','خر','تي','امر','امر','شه‍','مه','آب','آذ','به‍','اس‍',
		'ام','ام','ام','ام',
		'حمل','ثور','جوزا','سرطان','اسد','سنبله','ميزان','عقرب','قوس','جدي','دلو','حوت',
		'مار','اسب','گوسفند','ميمون','مرغ','سگ','خوک','موش','گاو','پلنگ','خرگوش','نهنگ');
        $fa_IR_I=array('ق.ظ','ب.ظ','قبل از ظهر','بعد از ظهر');
		// Languages available in the world
		$Convert = self::set_lang($Convert);
        foreach(array($en_US,$fa_IR/* ,'Languages' */) as $lang){
            if($Convert==$lang)continue;
            $str=str_ireplace(${$lang}, ${$Convert},str_replace(${$lang.'_I'}, ${$Convert.'_I'},$str));
        }
        return $str;
    }
}
    function jcnum($str,$Convert='fa_IR'){
        return SDATE_BASE_CONVERT::Cnum($str,$Convert,$dec);
    }
   /*  function Cwords($str,$Convert='EN'){
        return SDATE_BASE_CONVERT::Cwords($str,$Convert);
    } */
