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
	
	require_once(__DIR__."/en_US.php");
	require_once(__DIR__."/fa_IR.php");

	class SHDateWord{
		// Languages
		const fa_IR = 'fa_IR';
		const en_US = 'en_US';//
		const LANG_WORD = SHDATE_LWORD;
		const CLASS_LANG = "SHDATE_";
		const DEFAULT_LANG = self::en_US;

		const FIRST_DAY_OF_WEEK = SHDATE_FIRST_DAY_OF_WEEK;

		/**
		 * 
		 * 
		 * 
		 */
		private static function getLanguage($lang=self::LANG_WORD){
			switch($lang){
				case self::fa_IR:
				case self::en_US:
					return $lang;
				default:
					return self::getLanguage(self::DEFAULT_LANG);
			}
		}
		
		/**
		 * 
		 * 
		 * 
		 */
		protected static function getClassLanguage($lang=self::LANG_WORD,&$class = false){
			return $class = self::CLASS_LANG.self::getLanguage($lang);
		}
		
		/**
		* Ordinal suffix for the day of the month
		* @param   int  $num    numeric the day of the month
		* @return  string  Ordinal suffix for the day of the month
		* @since   1.0.0
		*/
		protected static function getSuffixNames($num, $LW =self::LANG_WORD){
			self::getClassLanguage($LW,$class);
			if(is_array($class::SUFFIX)){
				//Calculate Ordinal suffix for the day of the month
				if ($num >= 10&&$num <= 19)
					return $class::SUFFIX[0];
				switch ($num%10){
					case 1:return $class::SUFFIX[1];
					case 2:return $class::SUFFIX[2];
					case 3:return $class::SUFFIX[3];
					default:return $class::SUFFIX[0];
				}
			}
			return $class::SUFFIX;
		}
		
		/**
		* Uppercase Ante meridiem and Post meridiem
		* @param   int  $num    numeric
		* @param   string  $LW    language    word
		* @return  string  Ante/Post meridiem
		* @since   1.0.0
		*/
		protected static function getMeridienFullNames($H24, $LW =self::LANG_WORD){
			self::getClassLanguage($LW,$class);
			return $class::MERIDIEN_FULL_NAMES[$H24>11];
		}
		
		/**
		* Lowercase Ante meridiem and Post meridiem, two letters
		* @param   int  $num    numeric
		* @param   string  $LW    language    word
		* @return  string  Ante/Post meridiem, two letters
		* @since   1.0.0
		*/
		protected static function getMeridienShortNames($H24, $LW =self::LANG_WORD){
			self::getClassLanguage($LW,$class);
			return $class::MERIDIEN_SHORT_NAMES[$H24>11];
		}
		
		/**
		* A full textual representation of a month
		* @param   int  $num    numeric of a month
		* @return  string  A full textual of a month
		* @since   1.0.0
		*/
		protected static function getMonthFullNames($jm, $LW =self::LANG_WORD){
			self::getClassLanguage($LW,$class);
			return $class::MONTH_FULL_NAMES[$jm];
		}
		
		/**
		* A short textual representation of a month, three letters
		* @param   int  $num    numeric of a month
		* @return  string  A short textual of a month, three letters
		* @since   1.0.0
		*/
		protected static function getMonthShortNames($jm, $LW =self::LANG_WORD){
			self::getClassLanguage($LW,$class);
			return $class::MONTH_SHORT_NAMES[$jm];
		}
		
		/**
		* A full textual representation of the day of the week
		* @param   int  $num    numeric of the day of the week
		* @return  string  A full textual the day of the week
		* @since   1.0.0
		*/
		protected static function getDayFullNames($jdow, $LW =self::LANG_WORD){
			self::getClassLanguage($LW,$class);
			return $class::DAY_FULL_NAMES[($jdow+self::FIRST_DAY_OF_WEEK)%7];
		}
		
		/**
		* A short textual representation of the day of the week, three letters
		* @param   int  $num    numeric of the day of the week
		* @return  string  A short textual of a day, three letters
		* @since   1.0.0
		*/
		protected static function getDayShortNames($jdow, $LW =self::LANG_WORD){
			self::getClassLanguage($LW,$class);
			return $class::DAY_SHORT_NAMES[($jdow+self::FIRST_DAY_OF_WEEK)%7];
		}
		
		/**
		 * 
		 * 
		 * 
		 */
		protected static function getConstellationsFullNames($jm, $LW =self::LANG_WORD){//Constellations
			self::getClassLanguage($LW,$class);
			return $class::CONSTELLATIONS_FULL_NAMES[$jm];
		}
		
		/**
		 * 
		 * 
		 * 
		 */
		protected static function getAnimalsFullNames($jy, $LW =self::LANG_WORD){
			self::getClassLanguage($LW,$class);
			return $class::ANIMALS_FULL_NAMES[$jy%12];
		}
		
		/**
		 * 
		 * 
		 * 
		 */
		protected static function getSeasonFullNames($jm, $LW =self::LANG_WORD){
			self::getClassLanguage($LW,$class);
			return $class::SEASON_FULL_NAMES[(int)($jm/3.1)];
		}
		
		/**
		* A textual representation a leap year
		* @param   int  $num    numeric of the year
		* @return  string  A textual a leap year
		* @since   1.0.0
		*/
		protected static function getLeapFullNames($jleap, $LW =self::LANG_WORD){
			self::getClassLanguage($LW,$class);
			return $class::LEAP_FULL_NAMES[$jleap];
		}
		
		/**
		 * 
		 * 
		 * 
		 */
		protected static function getSolsticeFullNames($num, $solstice=false, $LW =self::LANG_WORD){
			self::getClassLanguage($LW,$class);
			return $class::SOLSTICE_FULL_NAMES[$num+$solstice];
		}
		
	}