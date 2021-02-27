<?php


require_once __DIR__.'/Lexer.php';
require_once dirname(__DIR__).'/SHBase.php';


class SHParser
{

	public $Lexer;

	function __construct($srt, $time = null){
		if(empty($time)){
			$time = $this->Date::time();
		}
		$this->time = $time;
		$this->Lexer = new SHLexer($srt);
		$this->Date = new Export_SHDateBase();
		$this->setDateTime($time);
		do{
			if($this->CompoundFormats());
			elseif($this->RelativeFormats());
			elseif($this->DateFormats());
			elseif($this->TimeFormats());
		}while($this->nextToken());
		//return $this->LibDateTime();
	}

	function setDateTime($time){
		$date = $this->Date::getdate($time);
		$this->data['YEAR'] = $date['year'];
		$this->data['MONTH'] = $date['mon'];
		$this->data['DAY'] = $date['mday'];
		$this->data['HOURS'] = $date['hours'];
		$this->data['MINUTES'] = $date['minutes'];
		$this->data['SECONDS'] = $date['seconds'];
		//$this->data['DAY_OF_YEAR'] = $date['yday'];
		//$this->data['DAY_OF_WEEK'] = $date['wday'];
		//$this->data['TIMESTAMP'] = $date[0];
		$this->data['DATE'] = $date;
		$this->data['GDATE'] = getdate($time);

	}

	function isToken($token){
		if(!is_null($this->Lexer->getLookahead())){
			return $this->Lexer->getLookahead()->is($token);
		}
		return false;
	}

	function nameToken(){
		return $this->Lexer->getLookahead()->getName();
	}

	function valueToken(){
		return $this->Lexer->getLookahead()->getValue();
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
		if($this->dayOptionalPrefix($day)){
			if($this->isToken('SLASH')){
				$this->nextToken();
				if($this->monthTextualShort($month)){
					if($this->isToken('SLASH')){
						$this->nextToken();
						if($this->year4MandatoryPrefix($year)){
							if($this->isToken('COLON')){
								$this->nextToken();
								if($this->hours24MandatoryPrefix($h24)){
									if($this->isToken('COLON')){
										$this->nextToken();
										if($this->minutesMandatoryPrefix($min)){
											if($this->isToken('COLON')){
												$this->nextToken();
												if($this->secondsMandatoryPrefix($sec)){
													if($this->whiteSpace()){
														if($this->TZCorrection()){
															$this->data['YEAR'] = $year;
															$this->data['MONTH'] = $month;
															$this->data['DAY'] = $day;
															$this->data['HOURS'] = $h24;
															$this->data['MINUTES'] = $min;
															$this->data['SECONDS'] = $sec;
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
		if($this->year4MandatoryPrefix($year)){
			if($this->isToken('COLON')){
				$this->nextToken();
				if($this->monthMandatoryPrefix($month)){
					if($this->isToken('COLON')){
						$this->nextToken();
						if($this->dayMandatoryPrefix($day)){
							if($this->whiteSpace()){
								if($this->hours24MandatoryPrefix($h24)){
									if($this->isToken('COLON')){
										$this->nextToken();
										if($this->minutesMandatoryPrefix($min)){
											if($this->isToken('COLON')){
												$this->nextToken();
												if($this->secondsMandatoryPrefix($sec)){
													$this->data['YEAR'] = $year;
													$this->data['MONTH'] = $month;
													$this->data['DAY'] = $day;
													$this->data['HOURS'] = $h24;
													$this->data['MINUTES'] = $min;
													$this->data['SECONDS'] = $sec;
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
		if($this->year4MandatoryPrefix($year)){
			if($this->isToken('DASH')){
				$this->nextToken();
			}
			if($this->isToken('SIGN_WEEK')){
				$this->nextToken();
				if($this->week53($week)){
					if($this->isToken('DASH')){
						$this->nextToken();
					}
					if($this->int1To7($dow)||$this->int0($dow)){
						$this->data['DAY_OF_WEEK'] = $dow;
					}
					$this->data['WEEK_OF_YEAR'] = $week;
					$this->data['YEAR'] = $year;
					return true;
				}
			}
		}
		$this->resetPosition($pos);
		return false;
	}

	function MySQL(){
		$pos = $this->getPosition();
		if($this->year4MandatoryPrefix($year)){
			if($this->isToken('DASH')){
				$this->nextToken();
				if($this->monthMandatoryPrefix($month)){
					if($this->isToken('DASH')){
						$this->nextToken();
						if($this->dayMandatoryPrefix($day)){
							if($this->whiteSpace()){
								if($this->hours24MandatoryPrefix($h24)){
									if($this->isToken('COLON')){
										$this->nextToken();
										if($this->minutesMandatoryPrefix($min)){
											if($this->isToken('COLON')){
												$this->nextToken();
												if($this->secondsMandatoryPrefix($sec)){
													$this->data['YEAR'] = $year;
													$this->data['MONTH'] = $month;
													$this->data['DAY'] = $day;
													$this->data['HOURS'] = $h24;
													$this->data['MINUTES'] = $min;
													$this->data['SECONDS'] = $sec;
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
		if($this->year4MandatoryPrefix($year)){
			if($this->isToken('DOT')){
				$this->nextToken();
			}
			if($this->dayOfYear($doy)){
				$this->data['YEAR'] = $year;
				$this->data['DAY_OF_YEAR'] = $doy;
				return true;
			}
		}
		$this->resetPosition($pos);
		return false;
	}

	function SOAP(){
		$pos = $this->getPosition();
		if($this->year4MandatoryPrefix($year)){
			if($this->isToken('DASH')){
				$this->nextToken();
				if($this->monthMandatoryPrefix($month)){
					if($this->isToken('DASH')){
						$this->nextToken();
						if($this->dayMandatoryPrefix($day)){
							if($this->isToken('SIGN_TIME')){
								$this->nextToken();
								if($this->hours24MandatoryPrefix($h24)){
									if($this->isToken('COLON')){
										$this->nextToken();
										if($this->minutesMandatoryPrefix($min)){
											if($this->isToken('COLON')){
												$this->nextToken();
												if($this->secondsMandatoryPrefix($sec)){
													if($this->fraction($frac)){
														$this->TZCorrection();
														$this->data['YEAR'] = $year;
														$this->data['MONTH'] = $month;
														$this->data['DAY'] = $day;
														$this->data['HOURS'] = $h24;
														$this->data['MINUTES'] = $min;
														$this->data['SECONDS'] = $sec;
														$this->data['FRAC'] = $frac;
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
			if($this->signNumber($sign)){
				$this->data['Sign_Timestamp'] = $sign;
			}
			if($this->Number($int)){
				$this->data['Timestamp'] = $int;
				return true;
			}
		}
		$this->resetPosition($pos);
		return false;
	}

	function XMLRPC(){
		$pos = $this->getPosition();
		if($this->year4MandatoryPrefix($year)){
			if($this->monthMandatoryPrefix($month)){
				if($this->dayMandatoryPrefix($day)){
					if($this->isToken('SIGN_TIME')){
						$this->nextToken();
						if($this->hours12OptionalPrefix($h1t2)||$this->hours24MandatoryPrefix($h1t2)){
							if($this->isToken('COLON')){
								$this->nextToken();
							}
							if($this->minutesMandatoryPrefix($min)){
								if($this->isToken('COLON')){
									$this->nextToken();
								}
								if($this->secondsMandatoryPrefix($sec)){
									$this->data['YEAR'] = $year;
									$this->data['MONTH'] = $month;
									$this->data['DAY'] = $day;
									$this->data['HOURS'] = $h1t2;
									$this->data['MINUTES'] = $min;
									$this->data['SECONDS'] = $sec;
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
		if($this->year4MandatoryPrefix($year)){
			if($this->isToken('DASH')){
				$this->nextToken();
				if($this->monthOptionalPrefix($month)){
					if($this->isToken('DASH')){
						$this->nextToken();
						if($this->dayOptionalPrefix($day)){
							if($this->isToken('SIGN_TIME')){
								$this->nextToken();
								if($this->hours12OptionalPrefix($h12)){
									if($this->isToken('COLON')){
										$this->nextToken();
										if($this->minutesOptionalPrefix($min)){
											if($this->isToken('COLON')){
												$this->nextToken();
												if($this->secondsOptionalPrefix($sec)){
													$this->data['YEAR'] = $year;
													$this->data['MONTH'] = $month;
													$this->data['DAY'] = $day;
													$this->data['HOURS'] = $h12;
													$this->data['MINUTES'] = $min;
													$this->data['SECONDS'] = $sec;
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
		if($this->hours12OptionalPrefix($h12)){
			if($this->isToken('COLON')){
				$this->nextToken();
				if($this->minutesMandatoryPrefix($min)){
					if($this->isToken('COLON')){
						$this->nextToken();
						if($this->secondsMandatoryPrefix($sec)){
							if($this->isToken('DOT')||$this->isToken('COLON')){
								$this->nextToken();
								if($this->Number($frac)){
									if($this->meridian($meridian)){
										if($meridian){
											$this->data['HOURS'] = $h12+12;
										}
										else{
											$this->data['HOURS'] = $h12;
										}
										$this->data['MINUTES'] = $min;
										$this->data['SECONDS'] = $sec;
										$this->data['FRAC'] = $frac;
										$this->data['AM_PM'] = $meridian;
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
			$this->setDateTime($this->time);
			return true;
		}	
		elseif($this->isToken('TODAY')||$this->isToken('MIDNIGHT')){ // The time is set to 00:00:00	
			$this->restTime();
			return true;
		}
		elseif($this->isToken('NOON')){ // The time is set to 12:00:00
			$this->restTime(12);
			return true;
		}	
		elseif($this->isToken('YESTERDAY')){ // Midnight of yesterday
			$this->data['DAY'] -= 1;
			$this->restTime();
			return true;
		}
		elseif($this->isToken('TOMORROW')){ // Midnight of tomorrow	
			$this->data['DAY'] += 1;
			$this->restTime();
			return true;
		}
		elseif($this->minutes15SpecifiedHour()){
			return true;
		}
		elseif($this->setDayOfMonth()){
			return true;
		}
		elseif($this->setWeekDayOfMonth()){
			return true;
		}
		elseif($this->handleRelativeTimeNumber()){
			return true;
		}
		elseif($this->handleRelativeTimeText()){
			return true;
		}
		elseif($this->dayNeme($dow)){ // Moves to the next day of this name.
			$dowmonth = $this->Date::getDayOfWeek($this->data['YEAR'] ,$this->data['MONTH'] ,$this->data['DAY']);
			if($dow < $dowmonth){
				$diffdow = 7 - $dowmonth - $dow ;
			}
			elseif($dow > $dowmonth){
				$diffdow = $dow - $dowmonth;
			}
			else{
				$diffdow = 0;
			}
			list(
				$this->data['YEAR'] 
				,$this->data['MONTH'] 
				,$this->data['DAY']) = $this->Date::getDaysOfDay(
					$this->data['YEAR'] 
					,$this->Date::getDayOfYear(false 
						,$this->data['MONTH'] 
						,1)
						+$diffdow);
			return true;
		}/*
		elseif($this->isToken('ago')){ // Negates all the values of previously found relative time items.
			$this->nextToken();
			return true;
		}
		elseif($this->handleRelativeTimeFormat()){
			return true;
		}*/
		return false;
	}

	function minutes15SpecifiedHour(){
		$pos = $this->getPosition();
		if($this->isToken('BACK')){ // 15 minutes past the specified hour
			$this->nextToken();
			if($this->whiteSpace()){
				if($this->isToken('OF')){
					$this->nextToken();
					if($this->whiteSpace()){
						if($this->Hour12Notation()||$this->hours24MandatoryPrefix($h24)){
							if(!is_numeric($h24)){
								$h24 = $this->data['HOURS'];
							}
							$this->data['HOURS'] = $h24;
							$this->data['MINUTES'] = 15;
							$this->data['SECONDS'] = 0;
							return true;
						}
					}
				}
			}
		}
		elseif($this->isToken('FRONT')){ // 15 minutes before the specified hour
			$h24 = false;
			$this->nextToken();
			if($this->whiteSpace()){
				if($this->isToken('OF')){
					$this->nextToken();
					if($this->whiteSpace()){
						if($this->Hour12Notation()||$this->hours24MandatoryPrefix($h24)){
							if(!is_numeric($h24)){
								$h24 = $this->data['HOURS'];
							}
							$this->data['HOURS'] = $h24-1;
							$this->data['MINUTES'] = 45;
							$this->data['SECONDS'] = 0;
							if(!$this->Date->checktime($h24-1,45,0)){
								$this->data['HOURS'] = $this->Date->revTime($h24-1,45,0)[0];
							}
							return true;
						}
					}
				}
			}
		}
		$this->resetPosition($pos);
		return false;
	}

	function setDayOfMonth(){
		$pos = $this->getPosition();
		if($this->isToken('FIRST')){ // Sets the day of the first of the current month. This phrase is best used together with a month name following it.
			$this->nextToken();
			if($this->whiteSpace()){
				if($this->isToken('DAY')){
					$this->nextToken();
					if($this->whiteSpace()){
						if($this->isToken('OF')){
							$this->nextToken();
							if($this->whiteSpace()){
								if($this->RelativeFormats()||$this->DateFormats()){
									$this->data['DAY'] = 1;
									$this->data['HOURS'] = 0;
									$this->data['MINUTES'] = 0;
									$this->data['SECONDS'] = 0;
									return true;
								}
							}
						}
					}
				}
			}
		}
		elseif($this->isToken('LAST')){ // Sets the day to the last day of the current month. This phrase is best used together with a month name following it.
			$this->nextToken();
			if($this->whiteSpace()){
				if($this->isToken('DAY')){
					$this->nextToken();
					if($this->whiteSpace()){
						if($this->isToken('OF')){
							$this->nextToken();
							if($this->whiteSpace()){
								if($this->RelativeFormats()||$this->DateFormats()){
									$this->data['DAY'] = $this->Date::getDaysInMonth($this->data['YEAR'] ,$this->data['MONTH']);
									var_dump($this->Date::getDaysInMonth($this->data['YEAR'] ,$this->data['MONTH']));
									$this->data['HOURS'] = 0;
									$this->data['MINUTES'] = 0;
									$this->data['SECONDS'] = 0;
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

	function setWeekDayOfMonth(){
		$pos = $this->getPosition();
		if($this->isToken('LAST')){ // Calculates the last week day of the current month.
			$this->nextToken();
			if($this->whiteSpace()){
				if($this->dayNeme($dow)){
					if($this->whiteSpace()){
						if($this->isToken('OF')){
							$this->nextToken();
							if($this->whiteSpace()){
								if($this->RelativeFormats()||$this->DateFormats()){
									$dow29month = $this->Date::getDayOfWeek(
										$this->data['YEAR'] 
										,$this->data['MONTH'] 
										,$this->Date::getDaysInMonth(
											$this->data['YEAR'] 
											,$this->data['MONTH']));
									if($dow < $dow29month){
										$diffdow = $dow29month - $dow ;
									}
									elseif($dow > $dow29month){
										$diffdow = 7 - $dow - $dow29month;
									}
									else{
										$diffdow = 0;
									}
									list(
										$this->data['YEAR'] 
										,$this->data['MONTH'] 
										,$this->data['DAY']) = $this->Date::getDaysOfDay(
											$this->data['YEAR'] 
											,$this->Date::getDayOfYear(false 
												,$this->data['MONTH'] 
												,1)
												-$diffdow);
									$this->data['HOURS'] = 0;
									$this->data['MINUTES'] = 0;
									$this->data['SECONDS'] = 0;
									return true;
								}
							}
						}
					}
				}
			}
		}
		elseif($this->ordinal($int)){ // Calculates the x-th week day of the current month.
			if($this->whiteSpace()){
				if($this->dayNeme($dow)){
					if($this->whiteSpace()){
						if($this->isToken('OF')){
							$this->nextToken();
							if($this->whiteSpace()){
								if($this->RelativeFormats()||$this->DateFormats()){
									if($int>0){
										$dow1month = $this->Date::getDayOfWeek($this->data['YEAR'] ,$this->data['MONTH'] ,1);
										if($dow < $dow1month){
											$diffdow = $dow1month - $dow ;
										}
										elseif($dow > $dow1month){
											$diffdow = 7 - $dow - $dow1month;
										}
										else{
											$diffdow = 0;
										}
										list(
											$this->data['YEAR'] 
											,$this->data['MONTH'] 
											,$this->data['DAY']) = $this->Date::getDaysOfDay(
												$this->data['YEAR'] 
												,$this->Date::getDayOfYear(false 
													,$this->data['MONTH'] 
													,1)
													+$diffdow+(($int-1)*7));
										return true;
									}
									elseif($int == 0){

									}
									elseif($int == -1){

									}
									elseif($int == -2){

									}
									elseif($int == -3){
										
									}
									$this->data['HOURS'] = 0;
									$this->data['MINUTES'] = 0;
									$this->data['SECONDS'] = 0;
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

	function handleRelativeTimeNumber(){
		$pos = $this->getPosition();
		if($this->Number($int,$sign)){ // Handles relative time items where the value is a number.
			if($this->whiteSpace());
			if($this->unit($rel) || $this->isToken('WEEK')){
				$int = intval($sign.$int);
				if($this->isToken('WEEK')||$rel == 53){
					$diffdow = $int*7;
				}
				elseif($rel == 59){ // SECONDS
					list($this->data['HOURS'] ,$this->data['MINUTES'] ,$this->data['SECONDS']) = $this->Date::revTime($this->data['HOURS'] ,$this->data['MINUTES'] ,$int);
				}
				elseif($rel == 60){ // MINUTES
					list($this->data['HOURS'] ,$this->data['MINUTES'] ,$this->data['SECONDS']) = $this->Date::revTime($this->data['HOURS'] ,$int ,$this->data['SECONDS']);
				}
				elseif($rel == 24){ // todo add with date
					list($this->data['HOURS'] ,$this->data['MINUTES'] ,$this->data['SECONDS']) = $this->Date::revTime($int ,$this->data['MINUTES'] ,$this->data['SECONDS']);
				}
				elseif($rel == 31){// DAY
					$diffdow = $int;
				}
				elseif($rel == 12){// todo calc with month with year
					$diffdow = $int*30.5;
				}
				elseif($rel == 100){// YEAR
					if($int<0)
						$this->data['YEAR'] -= $int;
					if($int>0)
						$this->data['YEAR'] += $int;
				}
				elseif($rel == 7){// todo day of week		weekday
					
				}
				elseif($rel == 14){// FORTNIGHT
					$diffdow = $int*14;
				}
				list($this->data['YEAR'] ,$this->data['MONTH'] ,$this->data['DAY']) = $this->Date::getDaysOfDay($this->data['YEAR'], $this->Date::getDayOfYear($this->data['YEAR'] ,$this->data['MONTH'] ,$this->data['DAY'])+$diffdow);
				return true;
			}
		}
		$this->resetPosition($pos);
		return false;
	}
	
	function handleRelativeTimeText(){
		$pos = $this->getPosition();
		if($this->ordinal($int)){ // Handles relative time items where the value is text.
			if($this->whiteSpace()){
				if($this->unit($rel)){
					if($this->isToken('WEEK')||$rel == 53){
						$diffdoy = $int*7;
					}
					elseif($rel == 59){ // SECONDS
						list($this->data['HOURS'] ,$this->data['MINUTES'] ,$this->data['SECONDS']) = $this->Date::revTime($this->data['HOURS'] ,$this->data['MINUTES'] ,$int);
					}
					elseif($rel == 60){ // MINUTES
						list($this->data['HOURS'] ,$this->data['MINUTES'] ,$this->data['SECONDS']) = $this->Date::revTime($this->data['HOURS'] ,$int ,$this->data['SECONDS']);
					}
					elseif($rel == 24){ // todo add with date
						list($this->data['HOURS'] ,$this->data['MINUTES'] ,$this->data['SECONDS']) = $this->Date::revTime($int ,$this->data['MINUTES'] ,$this->data['SECONDS']);
					}
					elseif($rel == 31){// DAY
						$diffdoy = $int;
					}
					elseif($rel == 12){// todo calc with month with year
						$diffdoy = $int*30.5;
					}
					elseif($rel == 100){// YEAR
						if($int<0)
							$this->data['YEAR'] -= $int;
						if($int>0)
							$this->data['YEAR'] += $int;
					}
					elseif($rel == 7){// todo day of week		weekday
						
					}
					elseif($rel == 14){// FORTNIGHT
						$diffdoy = $int*14;
					}
					list($this->data['YEAR'] ,$this->data['MONTH'] ,$this->data['DAY']) = $this->Date::getDaysOfDay($this->data['YEAR'], $this->Date::getDayOfYear($this->data['YEAR'] ,$this->data['MONTH'] ,$this->data['DAY'])+$diffdoy);
					return true;
				}
			}
		}
		$this->resetPosition($pos);
		return false;
	}

	function handleRelativeTimeFormat(){
		$pos = $this->getPosition();
		if($this->relText($int)){ // Handles the special format "weekday + last/this/next week".
			if($this->whiteSpace()){
				if($this->isToken('WEEK')){
					$this->nextToken();
					return true;
				}
			}
		}
		$this->resetPosition($pos);
		return false;
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
		if($this->hours12OptionalPrefix($h12)){
			if($this->isToken('COLON')||$this->isToken('DOT')){
				$this->nextToken();
				if($this->minutesMandatoryPrefix($min)){
					$this->data['MINUTES'] = $min;
					if($this->isToken('COLON')||$this->isToken('DOT')){
						$this->nextToken();
						if($this->secondsMandatoryPrefix($sec)){
							$this->data['SECONDS'] = $sec;
						}
					}
				}
			}
			if($this->whiteSpace()){
				$this->nextToken();
			}
			if($this->meridian($meridian)){
				if($meridian){
					$this->data['HOURS'] = $h12+12;
				}
				else{
					$this->data['HOURS'] = $h12;
				}
				$this->data['AM_PM'] = $meridian;
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
		if($this->hours24MandatoryPrefix($h24)){
			if($this->isToken('DOT')||$this->isToken('COLON')){
				$this->nextToken();
				if($this->minutesMandatoryPrefix($min)){
					if($this->isToken('DOT')||$this->isToken('COLON')){
						$this->nextToken();
						if($this->secondsMandatoryPrefix($sec)){
							$this->data['SECONDS'] = $sec;
							if($this->fraction($frac)){
								$this->data['FRAC'] = $frac;
							}
							$this->whiteSpace();
							$this->TZCorrection();
							$this->timeZone();
						}
					}
					$this->data['HOURS'] = $h24;
					$this->data['MINUTES'] = $min;
					return true;
				}
			}
			elseif($this->minutesMandatoryPrefix($min)){
				if($this->secondsMandatoryPrefix($sec)){
					$this->data['SECONDS'] = $sec;
				}
				$this->data['HOURS'] = $h24;
				$this->data['MINUTES'] = $min;
				return true;
			}
		}
		elseif($this->TZCorrection()||$this->timeZone()){
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
		
		if($this->usaDate()){ // mm / dd /? y?
			return true;
		}
		elseif($this->year4Date()){ 
			return true;
		}
		elseif($this->yearDateOptionalPrefix()){
			return true;
		}
		elseif($this->year2DateMandatoryPrefix()){
			return true;
		}
		elseif($this->dayDateOptionalPrefix()){
			return true;
		}
		elseif($this->year4MandatoryPrefix($year)){
			$this->data['YEAR'] = $year;
			return true;
		}
		elseif($this->monthTextualFull($month)){
			$this->data['MONTH'] = $month;
			return true;
		}
		return false;
	}

	function usaDate(){
		$pos = $this->getPosition();
		if($this->monthOptionalPrefix($month)){
			if($this->isToken('SLASH')){
				$this->nextToken();
				if($this->dayOptionalPrefix($day)){
					if($this->isToken('SLASH')){
						$this->nextToken();
						if($this->yearOptionalPrefix($year)){
							$this->data['YEAR'] = $year;
						}
					}
					$this->data['MONTH'] = $month;
					$this->data['DAY'] = $day;
					return true;
				}
			}
		}
		$this->resetPosition($pos);
		return false;
	}

	function year4Date(){
		if($this->year4DateMonthOptionalPrefix()){ // YY "/" mm "/" dd
			return true;
		}
		if($this->year4DateMonthMandatoryPrefix()){//ISO  YY "/"? MM "/"? DD
			return true;
		}
		if($this->year4DateDASH()){// YY "-" mm
			return true;
		}
		if($this->year4DateMonthTextual()){ // YY ([ \t.-])* m    Day reset to 1
			return true;
		}
		if($this->year4Datesign()){ // [+-]? YY "-" MM "-" DD
			return true;
		}
		return false;
	}

	function year4DateMonthOptionalPrefix(){ // YY "/" mm "/" dd
		$pos = $this->getPosition();
		if($this->year4MandatoryPrefix($year)){
			if($this->isToken('SLASH')){
				$this->nextToken();
				if($this->monthOptionalPrefix($month)){
					if($this->isToken('SLASH')){
						$this->nextToken();
						if($this->dayOptionalPrefix($day)){
							$this->data['YEAR'] = $year;
							$this->data['MONTH'] = $month;
							$this->data['DAY'] = $day;
							return true;
						}
					}
				}
			}
		}
		$this->resetPosition($pos);
		return false;
	}

	function year4DateMonthMandatoryPrefix(){ // YY "/"? MM "/"? DD
		$pos = $this->getPosition();
		if($this->year4MandatoryPrefix($year)){
			if($this->isToken('SLASH')){
				$this->nextToken();
			}
			if($this->monthMandatoryPrefix($month)){
				if($this->isToken('SLASH')){
					$this->nextToken();
				}
				if($this->dayMandatoryPrefix($day)){
					$this->data['YEAR'] = $year;
					$this->data['MONTH'] = $month;
					$this->data['DAY'] = $day;
					return true;
				}
			}
		}
		$this->resetPosition($pos);
		return false;
	}

	function year4DateDASH(){ // YY "-" mm
		$pos = $this->getPosition();
		if($this->year4MandatoryPrefix($year)){
			if($this->isToken('DASH')){
				$this->nextToken();
				if($this->monthOptionalPrefix($month)){
					$this->data['YEAR'] = $year;
					$this->data['MONTH'] = $month;
					return true;
				}
			}
		}
		$this->resetPosition($pos);
		return false;
	}

	function year4DateMonthTextual(){ // YY ([ \t.-])* m    Day reset to 1
		$pos = $this->getPosition();
		if($this->year4MandatoryPrefix($year)){
			while($this->whiteSpace()||$this->isToken('DOT')||$this->isToken('DASH')){
				if($this->isToken('DOT')||$this->isToken('DASH')){
					$this->nextToken();
				}
			}
			if($this->monthTextualFull($month)){
				$this->data['YEAR'] = $year;
				$this->data['MONTH'] = $month;
				$this->data['DAY'] = 1;
				return true;
			}
		}
		$this->resetPosition($pos);
		return false;
	}

	function year4Datesign(){ // [+-]? YY "-" MM "-" DD
		$pos = $this->getPosition();
		if($this->signNumber($sign));{
			$this->data['SIGN_DATE'] = $sign;
		}
		if($this->year4MandatoryPrefix($year)){
			if($this->isToken('DASH')){
				$this->nextToken();
				if($this->monthMandatoryPrefix($month)){
					if($this->isToken('DASH')){
						$this->nextToken();
						if($this->dayMandatoryPrefix($day)){
							$this->data['YEAR'] = $year;
							$this->data['MONTH'] = $month;
							$this->data['DAY'] = $day;
							return true;
						}
					}
				}
			}
		}
		$this->resetPosition($pos);
		return false;
	}

	function yearDateOptionalPrefix(){
		if($this->yearDateMonthOptionalPrefix()){ // y "-" mm "-" dd
			return true;
		}
		elseif($this->yearDateMonthTextual()){ // y "-" M "-" DD
			return true;
		}
		return false;
	}

	function yearDateMonthOptionalPrefix(){ // y "-" mm "-" dd
		$pos = $this->getPosition();
		if($this->yearOptionalPrefix($year)){
			if($this->isToken('DASH')){
				$this->nextToken();
				if($this->monthOptionalPrefix($month)){
					if($this->isToken('DASH')){
						$this->nextToken();
						if($this->dayOptionalPrefix($day)){
							$this->data['YEAR'] = $year;
							$this->data['MONTH'] = $month;
							$this->data['DAY'] = $day;
							return true;
						}
					}
				}
			}
		}
		$this->resetPosition($pos);
		return false;
	}

	function yearDateMonthTextual(){ // y "-" M "-" DD
		$pos = $this->getPosition();
		if($this->yearOptionalPrefix($year)){
			if($this->isToken('DASH')){
				$this->nextToken();
				if($this->monthTextualShort($month)){
					if($this->isToken('DASH')){
						$this->nextToken();
						if($this->dayMandatoryPrefix($day)){
							$this->data['YEAR'] = $year;
							$this->data['MONTH'] = $month;
							$this->data['DAY'] = $day;
							return true;
						}
					}
				}
			}
		}
		$this->resetPosition($pos);
		return false;
	}

	function year2DateMandatoryPrefix(){ // yy "-" MM "-" DD
		$pos = $this->getPosition();
		if($this->year2MandatoryPrefix($year)){
			if($this->isToken('DASH')){
				$this->nextToken();
				if($this->monthMandatoryPrefix($month)){
					if($this->isToken('DASH')){
						$this->nextToken();
						if($this->dayMandatoryPrefix($day)){
							$this->data['YEAR'] = $year;
							$this->data['MONTH'] = $month;
							$this->data['DAY'] = $day;
							return true;
						}
					}
				}
			}
		}
		$this->resetPosition($pos);
		return false;
	}


	function dayDateOptionalPrefix(){
		if($this->dayDateYear4MandatoryPrefix()){
			return true;
		}
		elseif($this->dayDateYear2MandatoryPrefix()){
			return true;
		}
		elseif($this->dayDateDayOptionalPrefix()){
			return true;
		}
		elseif($this->dayDateMonthTextual()){
			return true;
		}
		elseif($this->dayDateMonthTextualS()){
			return true;
		}
		return false;
	}

	function dayDateYear4MandatoryPrefix(){ // dd [.\t-] mm [.-] YY
		$pos = $this->getPosition();
		if($this->dayOptionalPrefix($day)){
			if($this->whiteSpace()||$this->isToken('DOT')||$this->isToken('DASH')){
				if($this->isToken('DOT')||$this->isToken('DASH')){
					$this->nextToken();
				}
				if($this->monthOptionalPrefix($month)){
					if($this->isToken('DOT')||$this->isToken('DASH')){
						$this->nextToken();
						if($this->year4MandatoryPrefix($year)){
							$this->data['YEAR'] = $year;
							$this->data['MONTH'] = $month;
							$this->data['DAY'] = $day;
							return true;
						}
					}
				}
			}
		}
		$this->resetPosition($pos);
		return false;
	}

	function dayDateYear2MandatoryPrefix(){ //  dd [.\t] mm "." yy
		$pos = $this->getPosition();
		if($this->dayOptionalPrefix($day)){
			if($this->whiteSpace()||$this->isToken('DOT')){
				if($this->isToken('DOT')){
					$this->nextToken();
				}
				if($this->monthOptionalPrefix($month)){
					if($this->isToken('DOT')){
						$this->nextToken();
						if($this->year2MandatoryPrefix($year)){
							$this->data['YEAR'] = $year;
							$this->data['MONTH'] = $month;
							$this->data['DAY'] = $day;
							return true;
						}
					}
				}
			}
		}
		$this->resetPosition($pos);
		return false;
	}

	function dayDateDayOptionalPrefix(){
		$pos = $this->getPosition();
		if($this->dayOptionalPrefix($day)){ // dd ([ \t.-])* m ([ \t.-])* y
			while($this->whiteSpace()||$this->isToken('DOT')||$this->isToken('DASH')){
				if($this->isToken('DOT')||$this->isToken('DASH')){
					$this->nextToken();
				}
			}
			if($this->monthTextualFull($month)){ // d ([ .\t-])* m
				while($this->whiteSpace()||$this->isToken('DOT')||$this->isToken('DASH')){
					if($this->isToken('DOT')||$this->isToken('DASH')){
						$this->nextToken();
					}
				}
				if($this->yearOptionalPrefix($year)){
					$this->data['YEAR'] = $year;
				}
				$this->data['MONTH'] = $month;
				$this->data['DAY'] = $day;
				return true;
			}
			
		}
		$this->resetPosition($pos);
		return false;
	}

	function dayDateMonthTextual(){
		$pos = $this->getPosition();
		if($this->monthTextualFull($month)){ // m ([ \t.-])* YY         Day reset to 1
			while($this->whiteSpace()||$this->isToken('DOT')||$this->isToken('DASH')){
				if($this->isToken('DOT')||$this->isToken('DASH')){
					$this->nextToken();
				}
			}
			if($this->year4MandatoryPrefix($year)){
				$this->data['YEAR'] = $year;
				$this->data['MONTH'] = $month;
				$this->data['DAY'] = 1;
				return true;
			}
			elseif($this->dayOptionalPrefix($day)){ // m ([ .\t-])* dd [,.stndrh\t ]+? y?
				while($this->whiteSpace()||$this->daySuffixTextual()||$this->isToken('COMMA')||$this->isToken('DOT')){
					if($this->isToken('DOT')||$this->isToken('COMMA')){
						$this->nextToken();
					}
				}
				if($this->yearOptionalPrefix($year)){
					$this->data['YEAR'] = $year;
					return true;
				}
					$this->data['MONTH'] = $month;
					$this->data['DAY'] = $day;
					return true;
			}
		}
		$this->resetPosition($pos);
		return false;
	}

	function dayDateMonthTextualS(){ // M "-" DD "-" y
		$pos = $this->getPosition();
		if($this->monthTextualShort($month)){
			if($this->isToken('DASH')){
				$this->nextToken();
				if($this->dayMandatoryPrefix($day)){
					if($this->isToken('DASH')){
						$this->nextToken();
						if($this->yearOptionalPrefix($year)){
							$this->data['YEAR'] = $year;
							$this->data['MONTH'] = $month;
							$this->data['DAY'] = $day;
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

	function restTime($h = 0,$m = 0,$s = 0){
		$this->data['HOURS'] = $h;
		$this->data['MINUTES'] = $m;
		$this->data['SECONDS'] = $s;
		return true;
	}

	function whiteSpace(){
		if($this->isToken('SPACE')){
			$this->nextToken();
			return true;
		}
		return false;
	}

	function hours12OptionalPrefix(&$int){
		if($this->int01To09($int)||$this->int1To9($int)||$this->int10To12($int)){
			return true;
		}
		return false;
	}

	function hours24MandatoryPrefix(&$int){
		if($this->int01To09($int)||$this->int10To24($int)){
			return true;
		}
		return false;
	}

	function meridian(&$str){
		if($this->isToken('AM')){
			$str = false;
			$this->nextToken();
			return true;
		}
		elseif($this->isToken('PM')){
			$str = true;
			$this->nextToken();
			return true;
		}
		return false;
	}

	function minutesMandatoryPrefix(&$int){
		if($this->int00($int)||$this->int01To09($int)||$this->int10To59($int)){
			return true;
		}
		return false;
	}

	function minutesOptionalPrefix(&$int){
		if($this->int00($int)||$this->int0($int)||$this->int1To9($int)||$this->int01To09($int)||$this->int10To59($int)){
			$this->data['MINUTES'] = $int;
			return true;
		}
		return false;
	}

	function secondsOptionalPrefix(&$int){
		if($this->int00($int)||$this->int0($int)||$this->int1To9($int)||$this->int01To09($int)||$this->int10To59($int)){
			$this->data['SECONDS'] = $int;
			return true;
		}
		return false;
	}

	function secondsMandatoryPrefix(&$int){
		if($this->int00($int)||$this->int01To09($int)||$this->int10To59($int)){
			return true;
		}
		return false;
	}

	function timeZone(){
		if($this->isToken('TZ')){
			$this->data['TZ_NAME'] = $this->valueToken();
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
			$this->data['TZ_SIGN'] = '+';
			$this->nextToken();
			$PLUS_DASH = true;
		}
		elseif($this->isToken('DASH')){
			$this->data['TZ_SIGN'] = '-';
			$this->nextToken();
			$PLUS_DASH = true;
		}
		if($PLUS_DASH&&$this->hours12OptionalPrefix($h12)){
			$this->data['TZ_HOURS'] = $h12;
			if($this->isToken('COLON')){
				$this->nextToken();
			}
			if($this->minutesMandatoryPrefix($min)){
				$this->data['TZ_MINUTES'] = $min;
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
			while($this->int10To99($int)||$this->int00($int)||$this->int01To09($int)||$this->int0($int)||$this->int1To9($int)){
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
	function daySuffixTextual(){
		switch($this->nameToken()){
			case "st": $this->nextToken(); return true;
			case "nd": $this->nextToken(); return true;
			case "rd": $this->nextToken(); return true;
			case "th": $this->nextToken(); return true;
			default: return false;
		}
	}

// a number between 0 and 31 inclusive, with an optional 0 prefix before numbers 0-9
	function dayOptionalPrefix(&$int){ 
		if($this->int00($int)||$this->int0($int)||$this->int1To9($int)||$this->int01To09($int)||$this->int10To31($int)){
			if($this->daySuffixTextual()){
				return true;
			}
			return true;
		}
		return false;
	}

// a number between 00 and 31 inclusive, with a mandatory 0 prefix before numbers 0-9
	function dayMandatoryPrefix(&$int){ 
		if($this->int00($int)||$this->int01To09($int)||$this->int10To31($int)){
			return true;
		}
		return false;
	}

	function monthTextualFull(&$int){
		switch($this->nameToken()){
			case 'FARVARDIN':
			case 'INT_I':
				$int = 1; $this->nextToken(); return true;
			case 'ORDIBEHESHT':
			case 'INT_II':
				$int = 2; $this->nextToken(); return true;
			case 'KHORDAD':
			case 'INT_III':
				$int = 3; $this->nextToken(); return true;
			case 'TIR':
			case 'INT_IV':
				$int = 4; $this->nextToken(); return true;
			case 'AMORDAD':
			case 'INT_V':
				$int = 5; $this->nextToken(); return true;
			case 'SHAHRIVAR':
			case 'INT_VI':
				$int = 6; $this->nextToken(); return true;
			case 'MEHR':
			case 'INT_VII':
				$int = 7; $this->nextToken(); return true;
			case 'ABAN':
			case 'INT_VIII':
				$int = 8; $this->nextToken(); return true;
			case 'AZAR':
			case 'INT_IX':
				$int = 9; $this->nextToken(); return true;
			case 'DEY':
			case 'INT_X':
				$int = 10; $this->nextToken(); return true;
			case 'BAHMAN':
			case 'INT_XI':
				$int = 11; $this->nextToken(); return true;
			case 'ESFAND':
			case 'INT_XII':
				$int = 12; $this->nextToken(); return true;
			default:return false;
		}
	}

	function monthTextualShort(&$int){ // abbreviated month
		if($this->monthTextualFull($int)){
			return true;
		}
		return false;
	}

	function monthOptionalPrefix(&$int){
		if($this->int00($int)||$this->int0($int)||$this->int01To09($int)||$this->int1To9($int)||$this->int10To12($int)){
			return true;
		}
		return false;
	}

	function monthMandatoryPrefix(&$int){
		if($this->int00($int)||$this->int01To09($int)||$this->int1To9($int)||$this->int10To12($int)){
			return true;
		}
		return false;
	}

	function yearOptionalPrefix(&$int){
		if($this->int00($int)||$this->int0($int)||$this->int01To09($int)||$this->int1To9($int)||$this->int10To99($int)){
			if($this->int00($int2)||$this->int0($int2)||$this->int01To09($int2)||$this->int1To9($int2)||$this->int10To99($int2)){
				$int .= $int2;
				return true;
			}
			return true;
		}
		return false;
	}

	function year2MandatoryPrefix(&$int){
		if($this->int00($int)||$this->int01To09($int)||$this->int10To99($int)){
			return true;
		}
		return false;
	}

	function year4MandatoryPrefix(&$int){
		if($this->year2MandatoryPrefix($int)){
			if($this->year2MandatoryPrefix($int2)){
				$int .= $int2;
				return true;
			}
		}
		return false;
	}

// Compound

	function dayOfYear(&$int){
		if($this->int00($int)||$this->int01To09($int)||$this->int10To99($int)){
			if($this->int0($int2)||$this->int1To9($int2)){
				$int .= $int2;
				return true;
			}
		}
		return false;
	}

	function week53(&$int){
		if($this->int00($int)||$this->int01To09($int)||$this->int10To53($int)){
			return true;
		}
		return false;
	}
// Relative

	function SpaceMore(){
		while($this->whiteSpace()){
			$space = true;
		}
		if($space){
			return true;
		}
		return false;
	}
	function dayNeme(&$dow){
		switch($this->nameToken()){
			case 'SATURDAY': 
				$dow = 0; $this->nextToken(); return true;
			case 'SUNDAY':	
				$dow = 1; $this->nextToken(); return true;
			case 'MONDAY':
				$dow = 2; $this->nextToken(); return true;
			case 'TUESDAY': 
				$dow = 3; $this->nextToken(); return true;
			case 'WEDNESDAY': 
				$dow = 4; $this->nextToken(); return true;
			case 'THURSDAY': 
				$dow = 5; $this->nextToken(); return true;
			case 'FRIDAY': 
				$dow = 6; $this->nextToken(); return true;
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

	function signNumber(&$sign){
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

	function Number(&$num,&$sign){
		if($this->signNumber($sign));
		$isInt = false;
		while($this->int10To99($int)||$this->int00($int)||$this->int01To09($int)||$this->int0($int)||$this->int1To9($int)){
			$num .= $int;//sprintf('%s%s',$num,$int);
			$isInt = true;
		}
		if($isInt){
			return true;
		}
		return false;
	}

	function ordinal(&$int){
		if($this->firstToThirtyFirstTextual($int)){
			return true;
		}
		elseif($this->relText($int)){
			return true;
		}
		return false;
	}

	function relText(&$int){
		switch($this->nameToken()){
			case 'THIS':
				$int = 0; $this->nextToken(); return true;
			case 'NEXT':
				$int = -1; $this->nextToken(); return true;
			case 'PREVIOUS':
				$int = -2; $this->nextToken(); return true;
			case 'LAST':
				$int = -3; $this->nextToken(); return true;
			default:return false;
		}
	}

	function unit(&$int){
		switch($this->nameToken()){
			case 'SECOND': 
				$int = 59; $this->nextToken(); return true;
			case 'MINUTE':	
				$int = 60; $this->nextToken(); return true;
			case 'HOUR':
				$int = 24; $this->nextToken(); return true;
			case 'DAY': 
				$int = 31; $this->nextToken(); return true;
			case 'MONTH': 
				$int = 12; $this->nextToken(); return true;
			case 'YEAR': 
				$int = 100; $this->nextToken(); return true;
			case 'WEEKS': 
				$int = 53; $this->nextToken(); return true;
			case 'WEEKDAY': 
				$int = 7; $this->nextToken(); return true;
			case 'FORTNIGHT':
				$int = 14; $this->nextToken(); return true;
			default:return false;
		}
	}

	// =================================================================================
	// ==================================   numeric   ==================================
	// =================================================================================

	// a spelled number between one and thirty-one (one, two, etc.)
	function oneToThirtyOneTextual(&$int){
		switch($this->nameToken()){
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
				switch($this->nameToken()){
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
	function firstToThirtyFirstTextual(&$int){
		switch($this->nameToken()){
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
				switch($this->nameToken()){
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

	function int10To99(&$int){
		switch($this->nameToken()){
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
				$int = $this->valueToken();
				$this->nextToken();
				return true;
			default: return $this->int10To59($int);
		}
	}

	function int10To59(&$int){
		switch($this->nameToken()){
			case 'INT_54':
			case 'INT_55':
			case 'INT_56':
			case 'INT_57':
			case 'INT_58':
			case 'INT_59':
				$int = $this->valueToken();
				$this->nextToken();
				return true;
			default: return $this->int10To53($int);
		}
	}

	function int10To53(&$int){
		switch($this->nameToken()){
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
				$int = $this->valueToken();
				$this->nextToken();
				return true;
			default: return $this->int10To36($int);
		}
	}

	function int10To36(&$int){
		switch($this->nameToken()){
			case 'INT_32':
			case 'INT_33':
			case 'INT_34':
			case 'INT_35':
			case 'INT_36':
				$int = $this->valueToken();
				$this->nextToken();
				return true;
			default: return $this->int10To31($int);
		}
	}
	
	function int10To31(&$int){
		switch($this->nameToken()){
			case 'INT_25':
			case 'INT_26':
			case 'INT_27':
			case 'INT_28':
			case 'INT_29':
			case 'INT_30':
			case 'INT_31':
				$int = $this->valueToken();
				$this->nextToken();
				return true;
			default: return $this->int10To24($int);
		}
	}
	
	function int10To24(&$int){
		if($this->isToken('INT_24')){
			$int = $this->valueToken();
				$this->nextToken();
				return true;
		}
		return $this->int10To23($int);
	}

	function int10To23(&$int){
		switch($this->nameToken()){
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
				$int = $this->valueToken();
				$this->nextToken();
				return true;
			default: return $this->int10To12($int);
		}
	}
	
	function int10To12(&$int){
		switch($this->nameToken()){
			case 'INT_10':
			case 'INT_11':
			case 'INT_12':
				$int = $this->valueToken();
				$this->nextToken();
				return true;
			default: return false;
		}
	}
	
	function int01To09(&$int){
		switch($this->nameToken()){
			case 'int01':
			case 'int02': 
			case 'int03':
			case 'int04':
			case 'int05':
			case 'int06':
			case 'int07':
			case 'int08':
			case 'int09':
				$int = $this->valueToken();
				$this->nextToken();
				return true;
			default: return false;
		}
	}

	function int1To9(&$int){
		switch($this->nameToken()){
			case 'INT_8':
			case 'INT_9': 
				$int = $this->valueToken();
				$this->nextToken();
				return true;
			default: return $this->int1To7($int);
		}
	}

	function int1To7(&$int){
		switch($this->nameToken()){
			case 'INT_1':
			case 'INT_2':
			case 'INT_3':
			case 'INT_4':
			case 'INT_5':
			case 'INT_6':
			case 'INT_7':
				$int = $this->valueToken();
				$this->nextToken();
				return true;
			default: return false;
		}
	}
	
	function int00(&$int){
		if($this->isToken('int00')){
			$int = $this->valueToken();
			$this->nextToken();
			return true;
		}
		return false;
	}

	function int0(&$int){
		if($this->isToken('int0')){
			$int = $this->valueToken();
			$this->nextToken();
			return true;
		}
		return false;
	}

}

