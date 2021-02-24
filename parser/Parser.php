<?php


require_once __DIR__.'/Lexer.php';


class Parser
{

	private $Lexer;

	function __construct($srt){
		$this->Lexer = new Lexer($srt);
		do{
			if($this->CompoundFormats());
			elseif($this->RelativeFormats());
			elseif($this->DateFormats());
			elseif($this->TimeFormats());
		}while($this->nextToken());
		//return $this->LibDateTime();
	}
	
	function isToken($token){
		if(!is_null($this->Lexer->getLookahead())){
			return $this->Lexer->getLookahead()->is($token);
		}
		return false;
	}
	
	function nextToken(){
		return $this->Lexer->moveNext();
	}

	function getPosition(){
		return $this->Lexer->getPosition();
	}

	function resetPosition($pos){
		return $this->Lexer->resetPosition($pos);
	}

	// ==============================================================================
	// =================================   Compound   ===============================
	// ==============================================================================
	function CompoundFormats(){// Localized Notations
		if($this->CommonLogFormat()){ // dd/M/Y:HH:II:SS tspace tzcorrection
			return true;
		}
		elseif($this->EXIF()){ //  YY:MM:DD HH:II:SS
			return true;
		}
		elseif($this->IsoYearWeekDay()){ //  YY-?"W"W-?[0-7]
			return true;
		}
		elseif($this->MySQL()){//  YY-MM-DD HH:II:SS
			return true;
		}
		elseif($this->PostgreSQL()){ // YY .? doy
			return true;
		}
		elseif($this->SOAP()){ //  YY "-" MM "-" DD "T" HH ":" II ":" SS frac tzcorrection?
			return true;
		}
		elseif($this->Unix_Timestamp()){ // "@" "-"? [0-9]+	
			return true;
		}
		elseif($this->XMLRPC()){ // & (Compact) YY MM DD "T" hh :? II :? SS
			return true;
		}
		elseif($this->WDDX()){ // YY "-" mm "-" dd "T" hh ":" ii ":" ss
			return true;
		}
		elseif($this->MS_SQL()){// time
			return true;
		}
		return false;
	}

	function CommonLogFormat(){
		$pos = $this->getPosition();
		if($this->Day_OptionalPrefix($day)){
			if($this->isToken('SLASH')){
				$this->nextToken();
				if($this->Month_TextualShort($month)){
					if($this->isToken('SLASH')){
						$this->nextToken();
						if($this->Year4_MandatoryPrefix($year)){
							if($this->isToken('COLON')){
								$this->nextToken();
								if($this->HH24($h24)){
									if($this->isToken('COLON')){
										$this->nextToken();
										if($this->Minute_MandatoryPrefix($min)){
											if($this->isToken('COLON')){
												$this->nextToken();
												if($this->Second_MandatoryPrefix($sec)){
													if($this->Space()){
														if($this->TZCorrection()){
															$this->tokens['YEAR_OF_Century'] = $year;
															$this->tokens['MONTH_OF_YEAR'] = $month;
															$this->tokens['DAY_OF_MONTH'] = $day;
															$this->tokens['HOURS_OF_DAY'] = $h24;
															$this->tokens['MINUTES_OF_HOUR'] = $min;
															$this->tokens['SECONDS_OF_MINUTE'] = $sec;
															return true;
														}
													}
												}
											}
										}
									}
								}
							}
						}
					}
				}
			}
		}
		$this->resetPosition($pos);
		return false;
	}

	function EXIF(){
		$pos = $this->getPosition();
		if($this->Year4_MandatoryPrefix($year)){
			if($this->isToken('COLON')){
				$this->nextToken();
				if($this->Month_MandatoryPrefix($month)){
					if($this->isToken('COLON')){
						$this->nextToken();
						if($this->Day_MandatoryPrefix($day)){
							if($this->Space()){
								if($this->HH24($h24)){
									if($this->isToken('COLON')){
										$this->nextToken();
										if($this->Minute_MandatoryPrefix($min)){
											if($this->isToken('COLON')){
												$this->nextToken();
												if($this->Second_MandatoryPrefix($sec)){
													$this->tokens['YEAR_OF_Century'] = $year;
													$this->tokens['MONTH_OF_YEAR'] = $month;
													$this->tokens['DAY_OF_MONTH'] = $day;
													$this->tokens['HOURS_OF_DAY'] = $h24;
													$this->tokens['MINUTES_OF_HOUR'] = $min;
													$this->tokens['SECONDS_OF_MINUTE'] = $sec;
													return true;
												}
											}
										}
									}
								}
							}
						}
					}
				}
			}
		}
		$this->resetPosition($pos);
		return false;
	}

	function IsoYearWeekDay(){
		$pos = $this->getPosition();
		if($this->Year4_MandatoryPrefix($year)){
			if($this->isToken('DASH')){
				$this->nextToken();
			}
			if($this->isToken('SIGN_WEEK')){
				$this->nextToken();
				if($this->W($week)){
					if($this->isToken('DASH')){
						$this->nextToken();
					}
					if($this->int_1_to_7($dow)||$this->int_0($dow)){
						$this->tokens['DAY_OF_WEEK'] = $dow;
					}
					$this->tokens['WEEK_OF_YEAR'] = $week;
					$this->tokens['YEAR_OF_Century'] = $year;
					return true;
				}
			}
		}
		$this->resetPosition($pos);
		return false;
	}

	function MySQL(){
		$pos = $this->getPosition();
		if($this->Year4_MandatoryPrefix($year)){
			if($this->isToken('DASH')){
				$this->nextToken();
				if($this->Month_MandatoryPrefix($month)){
					if($this->isToken('DASH')){
						$this->nextToken();
						if($this->Day_MandatoryPrefix($day)){
							if($this->Space()){
								if($this->HH24($h24)){
									if($this->isToken('COLON')){
										$this->nextToken();
										if($this->Minute_MandatoryPrefix($min)){
											if($this->isToken('COLON')){
												$this->nextToken();
												if($this->Second_MandatoryPrefix($sec)){
													$this->tokens['YEAR_OF_Century'] = $year;
													$this->tokens['MONTH_OF_YEAR'] = $month;
													$this->tokens['DAY_OF_MONTH'] = $day;
													$this->tokens['HOURS_OF_DAY'] = $h24;
													$this->tokens['MINUTES_OF_HOUR'] = $min;
													$this->tokens['SECONDS_OF_MINUTE'] = $sec;
													return true;
												}
											}
										}
									}
								}
							}
						}
					}
				}
			}
		}
		$this->resetPosition($pos);
		return false;
	}

	function PostgreSQL(){
		$pos = $this->getPosition();
		if($this->Year4_MandatoryPrefix($year)){
			if($this->isToken('DOT')){
				$this->nextToken();
			}
			if($this->dayOfYear($doy)){
				$this->tokens['YEAR_OF_Century'] = $year;
				$this->tokens['DAY_OF_YEAR'] = $doy;
				return true;
			}
		}
		$this->resetPosition($pos);
		return false;
	}

	function SOAP(){
		$pos = $this->getPosition();
		if($this->Year4_MandatoryPrefix($year)){
			if($this->isToken('DASH')){
				$this->nextToken();
				if($this->Month_MandatoryPrefix($month)){
					if($this->isToken('DASH')){
						$this->nextToken();
						if($this->Day_MandatoryPrefix($day)){
							if($this->isToken('SIGN_TIME')){
								$this->nextToken();
								if($this->HH24($h24)){
									if($this->isToken('COLON')){
										$this->nextToken();
										if($this->Minute_MandatoryPrefix($min)){
											if($this->isToken('COLON')){
												$this->nextToken();
												if($this->Second_MandatoryPrefix($sec)){
													if($this->fraction($frac)){
														$this->TZCorrection();
														$this->tokens['YEAR_OF_Century'] = $year;
														$this->tokens['MONTH_OF_YEAR'] = $month;
														$this->tokens['DAY_OF_MONTH'] = $day;
														$this->tokens['HOURS_OF_DAY'] = $h24;
														$this->tokens['MINUTES_OF_HOUR'] = $min;
														$this->tokens['SECONDS_OF_MINUTE'] = $sec;
														$this->tokens['FRAC'] = $frac;
														return true;
													}
												}
											}
										}
									}
								}
							}
						}
					}
				}
			}
		}
		$this->resetPosition($pos);
		return false;
	}

	function Unix_Timestamp(){
		$pos = $this->getPosition();
		if($this->isToken('AT')){
			$this->nextToken();
			if($this->sign_number($sign)){
				$this->tokens['Sign_Timestamp'] = $sign;
			}
			if($this->number($int)){
				$this->tokens['Timestamp'] = $int;
				return true;
			}
		}
		$this->resetPosition($pos);
		return false;
	}

	function XMLRPC(){
		$pos = $this->getPosition();
		if($this->Year4_MandatoryPrefix($year)){
			if($this->Month_MandatoryPrefix($month)){
				if($this->Day_MandatoryPrefix($day)){
					if($this->isToken('SIGN_TIME')){
						$this->nextToken();
						if($this->hh12($h1t2)||$this->HH24($h1t2)){
							if($this->isToken('COLON')){
								$this->nextToken();
							}
							if($this->Minute_MandatoryPrefix($min)){
								if($this->isToken('COLON')){
									$this->nextToken();
								}
								if($this->Second_MandatoryPrefix($sec)){
									$this->tokens['YEAR_OF_Century'] = $year;
									$this->tokens['MONTH_OF_YEAR'] = $month;
									$this->tokens['DAY_OF_MONTH'] = $day;
									$this->tokens['HOURS_OF_DAY'] = $h1t2;
									$this->tokens['MINUTES_OF_HOUR'] = $min;
									$this->tokens['SECONDS_OF_MINUTE'] = $sec;
									return true;
								}
							}
						}
					}
				}
			}
		}
		$this->resetPosition($pos);
		return false;
	}

	function WDDX(){
		$pos = $this->getPosition();
		if($this->Year4_MandatoryPrefix($year)){
			if($this->isToken('DASH')){
				$this->nextToken();
				if($this->Month_OptionalPrefix($month)){
					if($this->isToken('DASH')){
						$this->nextToken();
						if($this->Day_OptionalPrefix($day)){
							if($this->isToken('SIGN_TIME')){
								$this->nextToken();
								if($this->hh12($h12)){
									if($this->isToken('COLON')){
										$this->nextToken();
										if($this->Minute_OptionalPrefix($min)){
											if($this->isToken('COLON')){
												$this->nextToken();
												if($this->Second_OptionalPrefix($sec)){
													$this->tokens['YEAR_OF_Century'] = $year;
													$this->tokens['MONTH_OF_YEAR'] = $month;
													$this->tokens['DAY_OF_MONTH'] = $day;
													$this->tokens['HOURS_OF_DAY'] = $h12;
													$this->tokens['MINUTES_OF_HOUR'] = $min;
													$this->tokens['SECONDS_OF_MINUTE'] = $sec;
													return true;
												}
											}
										}
									}
								}
							}
						}
					}
				}
			}
		}
		$this->resetPosition($pos);
		return false;
	}
	
	function MS_SQL(){ //hh ":" II ":" SS [.:] [0-9]+ meridian  |  in Time Formats
		$pos = $this->getPosition();
		if($this->hh12($h12)){
			$this->tokens['HOURS_OF_DAY'] = $h12;
			if($this->isToken('COLON')){
				$this->nextToken();
				if($this->Minute_MandatoryPrefix($min)){
					$this->tokens['MINUTES_OF_HOUR'] = $min;
					if($this->isToken('COLON')){
						$this->nextToken();
						if($this->Second_MandatoryPrefix($sec)){
							$this->tokens['SECONDS_OF_MINUTE'] = $sec;
							if($this->isToken('DOT')||$this->isToken('COLON')){
								$this->nextToken();
								if($this->number($frac)){
									$this->tokens['FRAC'] = $frac;
									if($this->meridian($str)){
										$this->tokens['AM_PM'] = $str;
										return true;
									}
								}
							}
						}
					}
				}
			}
		}
		$this->resetPosition($pos);
		return false;
	}



	// ==============================================================================
	// =================================   Relative   ===============================
	// ==============================================================================
	function RelativeFormats(){
		
		//Day-based Notations

		if($this->isToken('NOW')){ // Now - this is simply ignored
			time();
		}	

		if($this->isToken('TODAY')||$this->isToken('MIDNIGHT')){ // The time is set to 00:00:00	  
			$hours = 0;
			$min = 0;
			$sec = 0;
		}
		

		if($this->isToken('NOON')){ // The time is set to 12:00:00	
			$hours = 12;
			$min = 0;
			$sec = 0;
		}	
		
		 

		if($this->isToken('YESTERDAY')){ // Midnight of yesterday	
			$day .= -1;
			$hours = 0;
			$min = 0;
			$sec = 0;
		}
		
		if($this->isToken('TOMORROW')){ // Midnight of tomorrow	
			$day .= +1;
			$hours = 0;
			$min = 0;
			$sec = 0;
		}	 
		/*
		'back of' hour	
		15 minutes past the specified hour	"back of 7pm", "back of 15"

		'front of' hour	
		15 minutes before the specified hour	"front of 5am", "front of 23"

		'first day of'	
		Sets the day of the first of the current month. This phrase is best used together with a month name following it.	"first day of January 2008"

		'last day of'	
		Sets the day to the last day of the current month. This phrase is best used together with a month name following it.	"last day of next month"

		$this->ordinal() $this->space() $this->dayname() $this->space() 'of'	
		Calculates the x-th week day of the current month.	"first sat of July 2008"

		'last' space $this->dayname() $this->space() 'of'	
		Calculates the last week day of the current month.	"last sat of July 2008"

		$this->number() space? ($this->unit() | 'week')	
		Handles relative time items where the value is a number.	"+5 weeks", "12 day", "-7 weekdays"

		$this->ordinal() $this->space() $this->unit()	
		Handles relative time items where the value is text.	"fifth day", "second month"

		'ago'	
		Negates all the values of previously found relative time items.	"2 days ago", "8 days ago 14:00", "2 months 5 days ago", "2 months ago 5 days", "2 days ago"

		if($this->dayname()){ // Moves to the next day of this name.	"Monday"

		}
		

		$this->reltext() space 'week'	
		Handles the special format "weekday + last/this/next week".	"Monday next week"
		*/

	}


	// ==============================================================================
	// ==================================   TIME   ==================================
	// ==============================================================================
	function TimeFormats(){// hh [.:]? II? [.:]? SS? space? meridian
		if($this->Hour12Notation()){
			return true;
		}
		elseif($this->Hour24Notation()){
			return true;
		}
		return false;
	}

	function Hour12Notation(){
		$pos = $this->getPosition();
		if($this->hh12($h12)){
			$this->tokens['HOURS_OF_DAY'] = $h12;
			if($this->isToken('COLON')||$this->isToken('DOT')){
				$this->nextToken();
				if($this->Minute_MandatoryPrefix($min)){
					$this->tokens['MINUTES_OF_HOUR'] = $min;
					if($this->isToken('COLON')||$this->isToken('DOT')){
						$this->nextToken();
						if($this->Second_MandatoryPrefix($sec)){
							$this->tokens['SECONDS_OF_MINUTE'] = $sec;
						}
					}
				}
			}
			if($this->Space()){
				$this->nextToken();
			}
			if($this->meridian($str)){
				$this->tokens['AM_PM'] = $str;
				return true;
			}
		}
		$this->resetPosition($pos);
		return false;
	}

	function Hour24Notation(){// 't'? HH [.:] II [.:]? SS? (frac | (space? ( tzcorrection | tz )))
		$pos = $this->getPosition();
		if($this->isToken('SIGN_TIME')){
			$this->nextToken();
		}
		if($this->HH24($h24)){
			if($this->isToken('DOT')||$this->isToken('COLON')){
				$this->nextToken();
				if($this->Minute_MandatoryPrefix($min)){
					if($this->isToken('DOT')||$this->isToken('COLON')){
						$this->nextToken();
						if($this->Second_MandatoryPrefix($sec)){
							$this->tokens['SECONDS_OF_MINUTE'] = $sec;
							if($this->fraction($frac)){
								$this->tokens['FRAC'] = $frac;
							}
							$this->Space();
							$this->TZCorrection();
							$this->TimeZone();
						}
					}
					$this->tokens['HOURS_OF_DAY'] = $h24;
					$this->tokens['MINUTES_OF_HOUR'] = $min;
					return true;
				}
			}
			elseif($this->Minute_MandatoryPrefix($min)){
				if($this->Second_MandatoryPrefix($sec)){
					$this->tokens['SECONDS_OF_MINUTE'] = $sec;
				}
				$this->tokens['HOURS_OF_DAY'] = $h24;
				$this->tokens['MINUTES_OF_HOUR'] = $min;
				return true;
			}
		}
		elseif($this->TZCorrection()||$this->TimeZone()){
			return true;
		}
		$this->resetPosition($pos);
		return false;
	}

	// ==============================================================================
	// ==================================   Date   ==================================
	// ==============================================================================
	function DateFormats(){
		// Localized Notations
		
		if($this->AmericanDate()){ // mm / dd /? y?
			return true;
		}
		elseif($this->DateYear4_MandatoryPrefix()){ 
			return true;
		}
		elseif($this->DateYear_OptionalPrefix()){
				
		}
		elseif($this->DateYear2_MandatoryPrefix()){
				
		}
		elseif($this->DateDay_OptionalPrefix()){
				
		}
		elseif($this->Year4_MandatoryPrefix($year)){
			$this->tokens['YEAR_OF_Century'] = $year;
			return true;
		}
		elseif($this->Month_TextualFull($month)){
			$this->tokens['MONTH_OF_YEAR'] = $month;
			return true;
		}

	}

	function AmericanDate(){
		$pos = $this->getPosition();
		if($this->Month_OptionalPrefix($month)){
			if($this->isToken('SLASH')){
				$this->nextToken();
				if($this->Day_OptionalPrefix($day)){
					if($this->isToken('SLASH')){
						$this->nextToken();
						if($this->Year_OptionalPrefix($year)){
							$this->tokens['YEAR_OF_Century'] = $year;
						}
					}
					$this->tokens['MONTH_OF_YEAR'] = $month;
					$this->tokens['DAY_OF_MONTH'] = $day;
					return true;
				}
			}
		}
		$this->resetPosition($pos);
		return false;
	}

	function DateYear4_MandatoryPrefix(){
		if($this->DateYear4_Month_OptionalPrefix()){ // YY "/" mm "/" dd
			return true;
		}
		if($this->DateYear4_Month_MandatoryPrefix()){//ISO  YY "/"? MM "/"? DD
			return true;
		}
		if($this->DateYear4_DASH()){// YY "-" mm
			return true;
		}
		if($this->DateYear4_Month_TextualFull()){ // YY ([ \t.-])* m    Day reset to 1
			return true;
		}
		if($this->DateYear4_sign()){ // [+-]? YY "-" MM "-" DD
			return true;
		}
		return false;
	}

	function DateYear4_Month_OptionalPrefix(){ // YY "/" mm "/" dd
		$pos = $this->getPosition();
		if($this->Year4_MandatoryPrefix($year)){
			if($this->isToken('SLASH')){
				$this->nextToken();
				if($this->Month_OptionalPrefix($month)){
					if($this->isToken('SLASH')){
						$this->nextToken();
						if($this->Day_OptionalPrefix($day)){
							$this->tokens['YEAR_OF_Century'] = $year;
							$this->tokens['MONTH_OF_YEAR'] = $month;
							$this->tokens['DAY_OF_MONTH'] = $day;
							return true;
						}
					}
				}
			}
		}
		$this->resetPosition($pos);
		return false;
	}

	function DateYear4_Month_MandatoryPrefix(){ // YY "/"? MM "/"? DD
		$pos = $this->getPosition();
		if($this->Year4_MandatoryPrefix($year)){
			if($this->isToken('SLASH')){
				$this->nextToken();
			}
			if($this->Month_MandatoryPrefix($month)){
				if($this->isToken('SLASH')){
					$this->nextToken();
				}
				if($this->Day_MandatoryPrefix($day)){
					$this->tokens['YEAR_OF_Century'] = $year;
					$this->tokens['MONTH_OF_YEAR'] = $month;
					$this->tokens['DAY_OF_MONTH'] = $day;
					return true;
				}
			}
		}
		$this->resetPosition($pos);
		return false;
	}

	function DateYear4_DASH(){ // YY "-" mm
		$pos = $this->getPosition();
		if($this->Year4_MandatoryPrefix($year)){
			if($this->isToken('DASH')){
				$this->nextToken();
				if($this->Month_OptionalPrefix($month)){
					$this->tokens['YEAR_OF_Century'] = $year;
					$this->tokens['MONTH_OF_YEAR'] = $month;
					return true;
				}
			}
		}
		$this->resetPosition($pos);
		return false;
	}

	function DateYear4_Month_TextualFull(){ // YY ([ \t.-])* m    Day reset to 1
		$pos = $this->getPosition();
		if($this->Year4_MandatoryPrefix($year)){
			while($this->Space()||$this->isToken('DOT')||$this->isToken('DASH')){
				if($this->isToken('DOT')||$this->isToken('DASH')){
					$this->nextToken();
				}
			}
			if($this->Month_TextualFull($month)){
				$this->tokens['YEAR_OF_Century'] = $year;
				$this->tokens['MONTH_OF_YEAR'] = $month;
				$this->tokens['DAY_OF_MONTH'] = 1;
				return true;
			}
		}
		$this->resetPosition($pos);
		return false;
	}

	function DateYear4_sign(){ // [+-]? YY "-" MM "-" DD
		$pos = $this->getPosition();
		if($this->sign_number($sign));{
			$this->tokens['SIGN_DATE'] = $sign;
		}
		if($this->Year4_MandatoryPrefix($year)){
			if($this->isToken('DASH')){
				$this->nextToken();
				if($this->Month_MandatoryPrefix($month)){
					if($this->isToken('DASH')){
						$this->nextToken();
						if($this->Day_MandatoryPrefix($day)){
							$this->tokens['YEAR_OF_Century'] = $year;
							$this->tokens['MONTH_OF_YEAR'] = $month;
							$this->tokens['DAY_OF_MONTH'] = $day;
							return true;
						}
					}
				}
			}
		}
		$this->resetPosition($pos);
		return false;
	}

	function DateYear_OptionalPrefix(){
		if($this->DateYear_Month_OptionalPrefix()){ // y "-" mm "-" dd
			return true;
		}
		elseif($this->DateYear_Month_TextualFull()){ // y "-" M "-" DD
			return true;
		}
		return false;
	}

	function DateYear_Month_OptionalPrefix(){ // y "-" mm "-" dd
		$pos = $this->getPosition();
		if($this->Year_OptionalPrefix($year)){
			if($this->isToken('DASH')){
				$this->nextToken();
				if($this->Month_OptionalPrefix($month)){
					if($this->isToken('DASH')){
						$this->nextToken();
						if($this->Day_OptionalPrefix($day)){
							$this->tokens['YEAR_OF_Century'] = $year;
							$this->tokens['MONTH_OF_YEAR'] = $month;
							$this->tokens['DAY_OF_MONTH'] = $day;
							return true;
						}
					}
				}
			}
		}
		$this->resetPosition($pos);
		return false;
	}

	function DateYear_Month_TextualFull(){ // y "-" M "-" DD
		$pos = $this->getPosition();
		if($this->Year_OptionalPrefix($year)){
			if($this->isToken('DASH')){
				$this->nextToken();
				if($this->Month_TextualShort($month)){
					if($this->isToken('DASH')){
						$this->nextToken();
						if($this->Day_MandatoryPrefix($day)){
							$this->tokens['YEAR_OF_Century'] = $year;
							$this->tokens['MONTH_OF_YEAR'] = $month;
							$this->tokens['DAY_OF_MONTH'] = $day;
							return true;
						}
					}
				}
			}
		}
		$this->resetPosition($pos);
		return false;
	}

	function DateYear2_MandatoryPrefix(){ // yy "-" MM "-" DD
		$pos = $this->getPosition();
		if($this->Year2_MandatoryPrefix($year)){
			if($this->isToken('DASH')){
				$this->nextToken();
				if($this->Month_MandatoryPrefix($month)){
					if($this->isToken('DASH')){
						$this->nextToken();
						if($this->Day_MandatoryPrefix($day)){
							$this->tokens['YEAR_OF_Century'] = $year;
							$this->tokens['MONTH_OF_YEAR'] = $month;
							$this->tokens['DAY_OF_MONTH'] = $day;
							return true;
						}
					}
				}
			}
		}
		$this->resetPosition($pos);
		return false;
	}


	function DateDay_OptionalPrefix(){
		if($this->DateDay_year4_MandatoryPrefix()){
			return true;
		}
		elseif($this->DateDay_year2_MandatoryPrefix()){
			return true;
		}
		elseif($this->DateDay_day_OptionalPrefix()){
			return true;
		}
		elseif($this->DateDay_Month_TextualFull()){
			return true;
		}
		elseif($this->DateDay_Month_TextualShort()){
			return true;
		}
		return false;
	}

	function DateDay_year4_MandatoryPrefix(){ // dd [.\t-] mm [.-] YY
		$pos = $this->getPosition();
		if($this->Day_OptionalPrefix($day)){
			if($this->Space()||$this->isToken('DOT')||$this->isToken('DASH')){
				if($this->isToken('DOT')||$this->isToken('DASH')){
					$this->nextToken();
				}
				if($this->Month_OptionalPrefix($month)){
					if($this->isToken('DOT')||$this->isToken('DASH')){
						$this->nextToken();
						if($this->Year4_MandatoryPrefix($year)){
							$this->tokens['YEAR_OF_Century'] = $year;
							$this->tokens['MONTH_OF_YEAR'] = $month;
							$this->tokens['DAY_OF_MONTH'] = $day;
							return true;
						}
					}
				}
			}
		}
		$this->resetPosition($pos);
		return false;
	}

	function DateDay_year2_MandatoryPrefix(){ //  dd [.\t] mm "." yy
		$pos = $this->getPosition();
		if($this->Day_OptionalPrefix($day)){
			if($this->Space()||$this->isToken('DOT')){
				if($this->isToken('DOT')){
					$this->nextToken();
				}
				if($this->Month_OptionalPrefix($month)){
					if($this->isToken('DOT')){
						$this->nextToken();
						if($this->Year2_MandatoryPrefix($year)){
							$this->tokens['YEAR_OF_Century'] = $year;
							$this->tokens['MONTH_OF_YEAR'] = $month;
							$this->tokens['DAY_OF_MONTH'] = $day;
							return true;
						}
					}
				}
			}
		}
		$this->resetPosition($pos);
		return false;
	}

	function DateDay_day_OptionalPrefix(){
		$pos = $this->getPosition();
		if($this->Day_OptionalPrefix($day)){ // dd ([ \t.-])* m ([ \t.-])* y
			while($this->Space()||$this->isToken('DOT')||$this->isToken('DASH')){
				if($this->isToken('DOT')||$this->isToken('DASH')){
					$this->nextToken();
				}
			}
			if($this->Month_TextualFull($month)){ // d ([ .\t-])* m
				while($this->Space()||$this->isToken('DOT')||$this->isToken('DASH')){
					if($this->isToken('DOT')||$this->isToken('DASH')){
						$this->nextToken();
					}
				}
				if($this->Year_OptionalPrefix($year)){
					$this->tokens['YEAR_OF_Century'] = $year;
				}
				$this->tokens['MONTH_OF_YEAR'] = $month;
				$this->tokens['DAY_OF_MONTH'] = $day;
				return true;
			}
			
		}
		$this->resetPosition($pos);
		return false;
	}

	function DateDay_Month_TextualFull(){
		$pos = $this->getPosition();
		if($this->Month_TextualFull($month)){ // m ([ \t.-])* YY         Day reset to 1
			while($this->Space()||$this->isToken('DOT')||$this->isToken('DASH')){
				if($this->isToken('DOT')||$this->isToken('DASH')){
					$this->nextToken();
				}
			}
			if($this->Year4_MandatoryPrefix($year)){
				$this->tokens['YEAR_OF_Century'] = $year;
				$this->tokens['MONTH_OF_YEAR'] = $month;
				$this->tokens['DAY_OF_MONTH'] = 1;
				return true;
			}
			elseif($this->Day_OptionalPrefix($day)){ // m ([ .\t-])* dd [,.stndrh\t ]+? y?
				while($this->Space()||$this->Day_Suffix()||$this->isToken('COMMA')||$this->isToken('DOT')){
					if($this->isToken('DOT')||$this->isToken('COMMA')){
						$this->nextToken();
					}
				}
				if($this->Year_OptionalPrefix($year)){
					$this->tokens['YEAR_OF_Century'] = $year;
					$this->tokens['MONTH_OF_YEAR'] = $month;
					$this->tokens['DAY_OF_MONTH'] = $day;
					return true;
				}
			}
		}
		$this->resetPosition($pos);
		return false;
	}

	function DateDay_Month_TextualShort(){ // M "-" DD "-" y
		$pos = $this->getPosition();
		if($this->Month_TextualShort($month)){
			if($this->isToken('DASH')){
				$this->nextToken();
				if($this->Day_MandatoryPrefix($day)){
					if($this->isToken('DASH')){
						$this->nextToken();
						if($this->Year_OptionalPrefix($year)){
							$this->tokens['YEAR_OF_Century'] = $year;
							$this->tokens['MONTH_OF_YEAR'] = $month;
							$this->tokens['DAY_OF_MONTH'] = $day;
							return true;
						}
					}
				}
			}
		}
		$this->resetPosition($pos);
		return false;
	}


	// ======================================================================================
	// ==================================   Used Symbols   ==================================
	// ======================================================================================

	function Space(){
		if($this->isToken('SPACE')){
			$this->nextToken();
			return true;
		}
		return false;
	}

	function hh12(&$int){
		if($this->int_01_to_09($int)||$this->int_1_to_9($int)||$this->int_10_to_12($int)){
			return true;
		}
		return false;
	}

	function HH24(&$int){
		if($this->int_01_to_09($int)||$this->int_10_to_24($int)){
			return true;
		}
		return false;
	}

	function meridian(&$str){
		$str = false;
		if($this->isToken('AM')){
			$str = 'am';
			$this->nextToken();
			return true;
		}
		elseif($this->isToken('PM')){
			$str = 'pm';
			$this->nextToken();
			return true;
		}
		return false;
	}

	function Minute_MandatoryPrefix(&$int){
		if($this->int_00($int)||$this->int_01_to_09($int)||$this->int_10_to_59($int)){
			return true;
		}
		return false;
	}

	function Minute_OptionalPrefix(&$int){
		if($this->int_00($int)||$this->int_0($int)||$this->int_1_to_9($int)||$this->int_01_to_09($int)||$this->int_10_to_59($int)){
			$this->tokens['MINUTES_OF_HOUR'] = $int;
			return true;
		}
		return false;
	}

	function Second_OptionalPrefix(&$int){
		if($this->int_00($int)||$this->int_0($int)||$this->int_1_to_9($int)||$this->int_01_to_09($int)||$this->int_10_to_59($int)){
			$this->tokens['SECONDS_OF_MINUTE'] = $int;
			return true;
		}
		return false;
	}

	function Second_MandatoryPrefix(&$int){
		if($this->int_00($int)||$this->int_01_to_09($int)||$this->int_10_to_59($int)){
			return true;
		}
		return false;
	}

	function TimeZone(){
		if($this->isToken('TZ')){
			$this->tokens['TZ_NAME'] = $this->Lexer->getLookahead()->getValue();
			return true;
		}
		return false;
	}

	function TZCorrection(){
		if($this->isToken('UTC')){
			$this->nextToken();
		}
		$PLUS_DASH = false;
		if($this->isToken('PLUS')){
			$this->tokens['TZ_SIGN'] = '+';
			$this->nextToken();
			$PLUS_DASH = true;
		}
		elseif($this->isToken('DASH')){
			$this->tokens['TZ_SIGN'] = '-';
			$this->nextToken();
			$PLUS_DASH = true;
		}
		if($PLUS_DASH&&$this->hh12($h12)){
			$this->tokens['TZ_HOURS'] = $h12;
			if($this->isToken('COLON')){
				$this->nextToken();
			}
			if($this->Minute_MandatoryPrefix($min)){
				$this->tokens['TZ_MINUTES'] = $min;
				return true;
			}
			return true;
		}
		return false;
	}

	function fraction(&$num){
		if($this->isToken('DOT')){
			$this->nextToken();
			$isInt = false;
			while($this->int_10_to_99($int)||$this->int_00($int)||$this->int_01_to_09($int)||$this->int_0($int)||$this->int_1_to_9($int)){
				$num .= $int;//sprintf('%s%s',$num,$int);
				$isInt = true;
			}
			if($isInt){
				return true;
			}
		}
		return false;
	}

// date
	function Day_Suffix(){
		if(is_null($this->Lexer->getLookahead())){
			return false;
		}
		switch($this->Lexer->getLookahead()->getName()){
			case "st": $this->nextToken(); return true;
			case "nd": $this->nextToken(); return true;
			case "rd": $this->nextToken(); return true;
			case "th": $this->nextToken(); return true;
			default: return false;
		}
	}

// a number between 0 and 31 inclusive, with an optional 0 prefix before numbers 0-9
	function Day_OptionalPrefix(&$int){ 
		if($this->int_00($int)||$this->int_0($int)||$this->int_1_to_9($int)||$this->int_01_to_09($int)||$this->int_10_to_31($int)){
			if($this->Day_Suffix()){
				return true;
			}
			return true;
		}
		return false;
	}

// a number between 00 and 31 inclusive, with a mandatory 0 prefix before numbers 0-9
	function Day_MandatoryPrefix(&$int){ 
		if($this->int_00($int)||$this->int_01_to_09($int)||$this->int_10_to_31($int)){
			return true;
		}
		return false;
	}

	function Month_TextualFull(&$int){
		if(is_null($this->Lexer->getLookahead())){
			return false;
		}
		switch($this->Lexer->getLookahead()->getName()){
			case 'JANUARY':	
				$this->tokens['MONTH_OF_YEAR'] = 1; $this->nextToken(); return true;
			case 'FEBRUARY':
				$this->tokens['MONTH_OF_YEAR'] = 2; $this->nextToken(); return true;
			case 'MARCH': 
				$this->tokens['MONTH_OF_YEAR'] = 3; $this->nextToken(); return true;
			case 'APRIL': 
				$this->tokens['MONTH_OF_YEAR'] = 4; $this->nextToken(); return true;
			case 'MAY': 
				$this->tokens['MONTH_OF_YEAR'] = 5; $this->nextToken(); return true;
			case 'JUNE': 
				$this->tokens['MONTH_OF_YEAR'] = 6; $this->nextToken(); return true;
			case 'JULY': 
				$this->tokens['MONTH_OF_YEAR'] = 7; $this->nextToken(); return true;
			case 'AUGUST': 
				$this->tokens['MONTH_OF_YEAR'] = 8; $this->nextToken(); return true;
			case 'SEPTEMBER': 
				$this->tokens['MONTH_OF_YEAR'] = 9; $this->nextToken(); return true;
			case 'OCTOBER': 
				$this->tokens['MONTH_OF_YEAR'] = 10; $this->nextToken(); return true;
			case 'NOVEMBER': 
				$this->tokens['MONTH_OF_YEAR'] = 11; $this->nextToken(); return true;
			case 'DECEMBER': 
				$int = 12; $this->nextToken(); return true;
			default:return false;
		}
	}

	function Month_TextualShort(&$int){ // abbreviated month
		if($this->Month_TextualFull($int)){
			return true;
		}
		return false;
	}

	function Month_OptionalPrefix(&$int){
		if($this->int_00($int)||$this->int_0($int)||$this->int_01_to_09($int)||$this->int_1_to_9($int)||$this->int_10_to_12($int)){
			return true;
		}
		return false;
	}

	function Month_MandatoryPrefix(&$int){
		if($this->int_00($int)||$this->int_01_to_09($int)||$this->int_1_to_9($int)||$this->int_10_to_12($int)){
			return true;
		}
		return false;
	}

	function Year_OptionalPrefix(&$int){
		if($this->int_00($int)||$this->int_0($int)||$this->int_01_to_09($int)||$this->int_1_to_9($int)||$this->int_10_to_99($int)){
			if($this->int_00($int2)||$this->int_0($int2)||$this->int_01_to_09($int2)||$this->int_1_to_9($int2)||$this->int_10_to_99($int2)){
				$int .= $int2;
				return true;
			}
			return true;
		}
		return false;
	}

	function Year2_MandatoryPrefix(&$int){
		if($this->int_00($int)||$this->int_01_to_09($int)||$this->int_10_to_99($int)){
			return true;
		}
		return false;
	}

	function Year4_MandatoryPrefix(&$int){
		if($this->Year2_MandatoryPrefix($int)){
			if($this->Year2_MandatoryPrefix($int2)){
				$int .= $int2;
				return true;
			}
		}
		return false;
	}

// Compound

	function dayOfYear(&$int){
		if($this->int_00($int)||$this->int_01_to_09($int)||$this->int_10_to_99($int)){
			if($this->int_0($int2)||$this->int_1_to_9($int2)){
				$int .= $int2;
				return true;
			}
		}
		return false;
	}

	function W(&$int){
		if($this->int_00($int)||$this->int_01_to_09($int)||$this->int_10_to_53($int)){
			return true;
		}
		return false;
	}
// Relative

	function Space_Any(){
		while($this->Space()){
			$space = true;
		}
		if($space){
			return true;
		}
		return false;
	}
	function dayname(){
		switch($this->Lexer->getLookahead()->getName()){
			case 'saturday': 
				$this->tokens['DAY_OF_WEEK'] = 1; $this->nextToken(); return true;
			case 'sunday':	
				$this->tokens['DAY_OF_WEEK'] = 2; $this->nextToken(); return true;
			case 'monday':
				$this->tokens['DAY_OF_WEEK'] = 3; $this->nextToken(); return true;
			case 'tuesday': 
				$this->tokens['DAY_OF_WEEK'] = 4; $this->nextToken(); return true;
			case 'wednesday': 
				$this->tokens['DAY_OF_WEEK'] = 5; $this->nextToken(); return true;
			case 'thursday': 
				$this->tokens['DAY_OF_WEEK'] = 6; $this->nextToken(); return true;
			case 'friday': 
				$this->tokens['DAY_OF_WEEK'] = 7; $this->nextToken(); return true;
			default:return false;
		}
	}

	function daytext(){
		if($this->isToken('WEEKDAY')){
			$this->nextToken();
			return true;
		}
		return false;
	}

	function sign_number(&$sign){
		if($this->isToken('PLUS')){
			$sign = '+';
			$this->nextToken();
			return true;
		}
		elseif($this->isToken('DASH')){
			$sign = '-';
			$this->nextToken();
			return true;
		}
		$sign = '+';
		return false;
	}

	function number(&$num,&$sign){
		if($this->sign_number($sign));
		$isInt = false;
		while($this->int_10_to_99($int)||$this->int_00($int)||$this->int_01_to_09($int)||$this->int_0($int)||$this->int_1_to_9($int)){
			$num .= $int;//sprintf('%s%s',$num,$int);
			$isInt = true;
		}
		if($isInt){
			return true;
		}
		return false;
	}

	function ordinal(){
		if($this->first_to_thirty_first($int)){

		}
		elseif($this->reltext()){

		}
		return false;
	}

	function reltext(){
		switch($this->Lexer->getLookahead()->getName()){
			case 'next': 
				$this->tokens['next'] = 1; $this->nextToken(); return true;
			case 'last':	
				$this->tokens['last'] = -1; $this->nextToken(); return true;
			case 'previous':
				$this->tokens['previous'] = -2; $this->nextToken(); return true;
			case 'this': 
				$this->tokens['this'] = 0; $this->nextToken(); return true;
			default:return false;
		}
	}

	function unit(){
		switch($this->Lexer->getLookahead()->getName()){
			case 'SECOND': 
				$this->tokens['sec'] = 1; $this->nextToken(); return true;
			case 'MINUTE':	
				$this->tokens['min'] = 2; $this->nextToken(); return true;
			case 'HOUR':
				$this->tokens['hour'] = 3; $this->nextToken(); return true;
			case 'DAY': 
				$this->tokens['this'] = 4; $this->nextToken(); return true;
			case 'FORTNIGHT':
				$this->tokens['this'] = 5; $this->nextToken(); return true;
			case 'MONTH': 
				$this->tokens['this'] = 6; $this->nextToken(); return true;
			case 'YEAR': 
				$this->tokens['this'] = 7; $this->nextToken(); return true;
			case 'WEEKS': 
				$this->tokens['this'] = 8; $this->nextToken(); return true;
			case 'WEEKS': 
				$this->tokens['this'] = 9; $this->nextToken(); return true;
			case 'WEEKDAY': 
				$this->tokens['this'] = 10; $this->nextToken(); return true;
			default:return false;
		}
	}

	// =================================================================================
	// ==================================   numeric   ==================================
	// =================================================================================

	// a spelled number between one and thirty-one (one, two, etc.)
	function one_to_thirty_one(&$int){
		if(is_null($this->Lexer->getLookahead())){
			return false;
		}
		switch($this->Lexer->getLookahead()->getName()){
			case 'ONE':		$int = 1; $this->nextToken(); return true;
			case 'TWO':		$int = 2; $this->nextToken(); return true;
			case 'THREE':	$int = 3; $this->nextToken(); return true;
			case 'FOUR':	$int = 4; $this->nextToken(); return true;
			case 'FIVE':	$int = 5; $this->nextToken(); return true;
			case 'SIX':		$int = 6; $this->nextToken(); return true;
			case 'SEVEN':	$int = 7; $this->nextToken(); return true;
			case 'EIGH':	$int = 8; $this->nextToken(); return true;
			case 'NINE':	$int = 9; $this->nextToken(); return true;
			case 'TEN':		$int = 10; $this->nextToken(); return true;
			case 'ELEVEN':	$int = 11; $this->nextToken(); return true;
			case 'TWELVE':	$int = 12; $this->nextToken(); return true;
			case 'THIRTEEN':	$int = 13; $this->nextToken(); return true;
			case 'FOURTEEN':	$int = 14; $this->nextToken(); return true;
			case 'FIFTEEN':		$int = 15; $this->nextToken(); return true;
			case 'SIXTEEN':		$int = 16; $this->nextToken(); return true;
			case 'SEVENTEEN':	$int = 17; $this->nextToken(); return true;
			case 'EIGHTEEN':	$int = 18; $this->nextToken(); return true;
			case 'NINETEEN':	$int = 19; $this->nextToken(); return true;
			case 'TWENTY':
				$this->nextToken();
				if($this->isToken('DASH')||
				   $this->isToken('SPACE'))
				{
					$this->nextToken();
				}
				switch($this->Lexer->getLookahead()->getName()){
					case 'ONE':		$int = 21; $this->nextToken(); return true;
					case 'TWO':		$int = 22; $this->nextToken(); return true;
					case 'THREE':	$int = 23; $this->nextToken(); return true;
					case 'FOUR':	$int = 24; $this->nextToken(); return true;
					case 'FIVE':	$int = 25; $this->nextToken(); return true;
					case 'SIX':		$int = 26; $this->nextToken(); return true;
					case 'SEVEN':	$int = 27; $this->nextToken(); return true;
					case 'EIGH':	$int = 28; $this->nextToken(); return true;
					case 'NINE':	$int = 29; $this->nextToken(); return true;
					default:		$int = 20; $this->nextToken(); return true;
				}
			case 'THIRTY':
				if($this->isToken('DASH')||
				   $this->isToken('SPACE'))
				{
					$this->nextToken();
				}
				if($this->isToken('ONE')){
					$int = 31;
					$this->nextToken();
					return true;
				}
				$int = 30; $this->nextToken(); return true;
			default: return false;
		}
	}

	// a spelled number in sequence between first and thirty-first
	function first_to_thirty_first(&$int){
		if(is_null($this->Lexer->getLookahead())){
			return false;
		}
		switch($this->Lexer->getLookahead()->getName()){
			case 'FIRST':	$int = 1; $this->nextToken(); return true;
			case 'SECOND':	$int = 2; $this->nextToken(); return true;
			case 'THIRD':	$int = 3; $this->nextToken(); return true;
			case 'FOURTH':	$int = 4; $this->nextToken(); return true;
			case 'FIFTH':	$int = 5; $this->nextToken(); return true;
			case 'SIXTH':	$int = 6; $this->nextToken(); return true;
			case 'SEVENTH':	$int = 7; $this->nextToken(); return true;
			case 'EIGHTH':	$int = 8; $this->nextToken(); return true;
			case 'NINTH':	$int = 9; $this->nextToken(); return true;
			case 'TENTH':	$int = 10; $this->nextToken(); return true;
			case 'ELEVENTH':	$int = 11; $this->nextToken(); return true;
			case 'TWELFTH':		$int = 12; $this->nextToken(); return true;
			case 'THIRTEENTH':	$int = 13; $this->nextToken(); return true;
			case 'FOURTEENTH':	$int = 14; $this->nextToken(); return true;
			case 'FIFTEENTH':	$int = 15; $this->nextToken(); return true;
			case 'SIXTEENTH':	$int = 16; $this->nextToken(); return true;
			case 'SEVENTEENTH': $int = 17; $this->nextToken(); return true;
			case 'EIGHTEENTH':	$int = 18; $this->nextToken(); return true;
			case 'NINETEENTH':	$int = 19; $this->nextToken(); return true;
			case 'TWENTIETH':	$int = 20; $this->nextToken(); return true;
			case 'THIRTIETH': $int = 30; $this->nextToken(); return true;
			case 'TWENTY':
				$this->nextToken();
				if($this->isToken('DASH')||
				   $this->isToken('SPACE'))
				{
					$this->nextToken();
				}
				switch($this->Lexer->getLookahead()->getName()){
					case 'FIRST':	$int = 21; $this->nextToken(); return true;
					case 'SECOND':	$int = 22; $this->nextToken(); return true;
					case 'THIRD':	$int = 23; $this->nextToken(); return true;
					case 'FOURTH':	$int = 24; $this->nextToken(); return true;
					case 'FIFTH':	$int = 25; $this->nextToken(); return true;
					case 'SIXTH':	$int = 26; $this->nextToken(); return true;
					case 'SEVENTH': $int = 27; $this->nextToken(); return true;
					case 'EIGHTH':	$int = 28; $this->nextToken(); return true;
					case 'NINTH':	$int = 29; $this->nextToken(); return true;
				}
			case 'THIRTY':
				if($this->isToken('DASH')||
				   $this->isToken('SPACE'))
				{
					$this->nextToken();
				}
				if($this->isToken('FIRST')){
					$int = 31;
					$this->nextToken();
					return true;
				}
			default: return false;
		}
	}

	function int_10_to_99(&$int){
		if(is_null($this->Lexer->getLookahead())){
			return false;
		}
		switch($this->Lexer->getLookahead()->getName()){
			case 'INT_60':
			case 'INT_61':
			case 'INT_62':
			case 'INT_63':
			case 'INT_64':
			case 'INT_65':
			case 'INT_66':
			case 'INT_67':
			case 'INT_68':
			case 'INT_69':
			case 'INT_70':
			case 'INT_71':
			case 'INT_72':
			case 'INT_73':
			case 'INT_74':
			case 'INT_75':
			case 'INT_76':
			case 'INT_77':
			case 'INT_78':
			case 'INT_79':
			case 'INT_80':
			case 'INT_81':
			case 'INT_82':
			case 'INT_83':
			case 'INT_84':
			case 'INT_85':
			case 'INT_86':
			case 'INT_87':
			case 'INT_88':
			case 'INT_89':
			case 'INT_90':
			case 'INT_91':
			case 'INT_92':
			case 'INT_93':
			case 'INT_94':
			case 'INT_95':
			case 'INT_96':
			case 'INT_97':
			case 'INT_98':
			case 'INT_99':
				$int = $this->Lexer->getLookahead()->getValue();
				$this->nextToken();
				return true;
			default: return $this->int_10_to_59($int);
		}
	}

	function int_10_to_59(&$int){
		if(is_null($this->Lexer->getLookahead())){
			return false;
		}
		switch($this->Lexer->getLookahead()->getName()){
			case 'INT_54':
			case 'INT_55':
			case 'INT_56':
			case 'INT_57':
			case 'INT_58':
			case 'INT_59':
				$int = $this->Lexer->getLookahead()->getValue();
				$this->nextToken();
				return true;
			default: return $this->int_10_to_53($int);
		}
	}

	function int_10_to_53(&$int){
		if(is_null($this->Lexer->getLookahead())){
			return false;
		}
		switch($this->Lexer->getLookahead()->getName()){
			case 'INT_37':
			case 'INT_38':
			case 'INT_39':
			case 'INT_40':
			case 'INT_41':
			case 'INT_42':
			case 'INT_43':
			case 'INT_44':
			case 'INT_45':
			case 'INT_46':
			case 'INT_47':
			case 'INT_48':
			case 'INT_49':
			case 'INT_50':
			case 'INT_51':
			case 'INT_52':
			case 'INT_53':
				$int = $this->Lexer->getLookahead()->getValue();
				$this->nextToken();
				return true;
			default: return $this->int_10_to_36($int);
		}
	}

	function int_10_to_36(&$int){
		if(is_null($this->Lexer->getLookahead())){
			return false;
		}
		switch($this->Lexer->getLookahead()->getName()){
			case 'INT_32':
			case 'INT_33':
			case 'INT_34':
			case 'INT_35':
			case 'INT_36':
				$int = $this->Lexer->getLookahead()->getValue();
				$this->nextToken();
				return true;
			default: return $this->int_10_to_31($int);
		}
	}
	
	
	function int_10_to_31(&$int){
		if(is_null($this->Lexer->getLookahead())){
			return false;
		}
		switch($this->Lexer->getLookahead()->getName()){
			case 'INT_25':
			case 'INT_26':
			case 'INT_27':
			case 'INT_28':
			case 'INT_29':
			case 'INT_30':
			case 'INT_31':
				$int = $this->Lexer->getLookahead()->getValue();
				$this->nextToken();
				return true;
			default: return $this->int_10_to_24($int);
		}
	}
	
	function int_10_to_24(&$int){
		if(is_null($this->Lexer->getLookahead())){
			return false;
		}
		if($this->isToken('INT_24')){
			$int = $this->Lexer->getLookahead()->getValue();
				$this->nextToken();
				return true;
		}
		return $this->int_10_to_23($int);
	}

	function int_10_to_23(&$int){
		if(is_null($this->Lexer->getLookahead())){
			return false;
		}
		switch($this->Lexer->getLookahead()->getName()){
			case 'INT_13':
			case 'INT_14':
			case 'INT_15':
			case 'INT_16':
			case 'INT_17':
			case 'INT_18':
			case 'INT_19':
			case 'INT_20':
			case 'INT_21':
			case 'INT_22':
			case 'INT_23':
				$int = $this->Lexer->getLookahead()->getValue();
				$this->nextToken();
				return true;
			default: return $this->int_10_to_12($int);
		}
	}
	
	
	function int_10_to_12(&$int){
		if(is_null($this->Lexer->getLookahead())){
			return false;
		}
		switch($this->Lexer->getLookahead()->getName()){
			case 'INT_10':
			case 'INT_11':
			case 'INT_12':
				$int = $this->Lexer->getLookahead()->getValue();
				$this->nextToken();
				return true;
			default: return false;
		}
	}
	
	function int_01_to_09(&$int){
		if(is_null($this->Lexer->getLookahead())){
			return false;
		}
		switch($this->Lexer->getLookahead()->getName()){
			case 'INT_01':
			case 'INT_02': 
			case 'INT_03':
			case 'INT_04':
			case 'INT_05':
			case 'INT_06':
			case 'INT_07':
			case 'INT_08':
			case 'INT_09':
				$int = $this->Lexer->getLookahead()->getValue();
				$this->nextToken();
				return true;
			default: return false;
		}
	}

	function int_1_to_9(&$int){
		if(is_null($this->Lexer->getLookahead())){
			return false;
		}
		switch($this->Lexer->getLookahead()->getName()){
			case 'INT_8':
			case 'INT_9': 
				$int = $this->Lexer->getLookahead()->getValue();
				$this->nextToken();
				return true;
			default: return $this->int_1_to_7($int);
		}
	}

	function int_1_to_7(&$int){
		if(is_null($this->Lexer->getLookahead())){
			return false;
		}
		switch($this->Lexer->getLookahead()->getName()){
			case 'INT_1':
			case 'INT_2':
			case 'INT_3':
			case 'INT_4':
			case 'INT_5':
			case 'INT_6':
			case 'INT_7':
				$int = $this->Lexer->getLookahead()->getValue();
				$this->nextToken();
				return true;
			default: return false;
		}
	}
	
	function int_00(&$int){
		if(is_null($this->Lexer->getLookahead())){
			return false;
		}
		if($this->isToken('INT_00')){
			$int = $this->Lexer->getLookahead()->getValue();
			$this->nextToken();
			return true;
		}
		return false;
	}

	function int_0(&$int){
		if(is_null($this->Lexer->getLookahead())){
			return false;
		}
		if($this->isToken('INT_0')){
			$int = $this->Lexer->getLookahead()->getValue();
			$this->nextToken();
			return true;
		}
		return false;
	}

}

