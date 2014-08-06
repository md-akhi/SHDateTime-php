<?php /* In The Name Of God 	به نام خداوند بخشنده و مهربان*/

/** Software Hijri_Shamsi , Solar(Jalali) Date and Time
Copyright(C)2014
version 0.1 beta 
/*	F	*/
class jdate {
	
	private $difftime = 0;
	private $time_zone='';
	private $lang ='fa';
	private $ts = time();
	
	function set_timezone($time_zone)
	{
		if($this->time_zone != $time_zone)
		{
			$this->time_zone = $time_zone;
			date_default_timezone_set($time_zone);
		}
	}

	function set_lang($lang)
	{
		if($this->lang !=  $lang)
			$this->lang =  $lang;
	}

	function set_diff_time($difftime)
	{
		$this->difftime =  $difftime;
	}
	
	function set_timestamp($timestamp)
	{
		$this->ts =  $timestamp;
	}

	public function __constraction($time_zone='Asia/Tehran',$lang='fa',$difftime=0)
	{
		self::set_timezone($time_zone);
		self::set_diff_time($difftime);
		self::set_lang($lang);
	}

	public function date_format($format,$timestamp,$gmt=0)
	{
		$ts=((empty($timestamp) || $timestamp==='now')?time():$timestamp)-($gmt?date("Z"):0);
		$ts +=$this->difftime;
		
		$date=explode('_',date('H_i_j_n_O_P_s_w_Y',$ts));
		list($j_y,$j_m,$j_d)=self::gregorian_to_jalali($date[8],$date[3],$date[2]);
		$doy=($j_m<7)?(($j_m-1)*31)+$j_d-1:(($j_m-7)*30)+$j_d+185;
		$kab=($j_y%33%4-1==(int)($j_y%33*.05))?1:0;
		$sl=strlen($format);
		$out='';
		for($i=0; $i<$sl; $i++)
		{
			$sub=substr($format,$i,1);
			if($sub=='%')
			{
				$sub='F'.substr($format,++$i,1);
			}
			switch($sub)
			{
				case'\\':$out.=substr($format,++$i,1);break;
				case'B':case'e':case'g':case'G':case'h':case'I':case'T':case'u':case'Z':$out.=date($sub,$ts);break;

				/* day */
				case'd':$out.=($j_d<10)?'0'.$j_d:$j_d;break;
				case'D':$out.=self::words(array('kh'=>$date[7]),' ');break;
				case'j':$out.=$j_d;break;
				case'l':$out.=self::words(array('rh'=>$date[7]),' ');break;
				case'S':$out.='ام';break;
				case'w':$out.=($date[7]==6)?0:$date[7]+1;break;
				case'N':$out.=$date[7]+1;break;
				case'z':$out.=$doy;break;
				
				/* week */
				case'W':
				$avs=(($date[7]==6)?0:$date[7]+1)-($doy%7);
				if($avs<0)$avs+=7;
				$num=(int)(($doy+$avs)/7);
				if($avs<4)$num++;
				elseif($num<1)$num=($avs==4 or $avs==(($j_y%33%4-2==(int)($j_y%33*.05))?5:4))?53:52;
				$aks=$avs+$kab;
				if($aks==7)$aks=0;
				$out.=(($kab+363-$doy)<$aks and $aks<3)?'01':(($num<10)?'0'.$num:$num);
				break;
				case'o':
				$jdw=($date[7]==6)?0:$date[7]+1;
				$dny=364+$kab-$doy;
				$out.=($jdw>($doy+3) and $doy<3)?$j_y-1:(((3-$dny)>$jdw and $dny<3)?$j_y+1:$j_y);
				break;
				
				/* month */
				case'F':$out.=self::words(array('mm'=>$j_m),' ');break;
				case'm':$out.=($j_m>9)?$j_m:'0'.$j_m;break;
				case'M':$out.=self::words(array('km'=>$j_m),' ');break;
				case'n':$out.=$j_m;break;
				case't':$out.=($j_m!=12)?(31-(int)($j_m/6.5)):($kab+29);break;
				
				/* year */
				case'L':$out.=$kab;break;
				case'y':$out.=substr($j_y,2,2);break;
				case'Y':$out.=$j_y;break;
				
				/* time */
				case'a':$out.=($date[0]<12)?'ق.ظ':'ب.ظ';break;
				case'A':$out.=($date[0]<12)?'قبل از ظهر':'بعد از ظهر';break;
				 case'H':$out.=$date[0];break;
				case'i':$out.=$date[1];break; 
				case's':$out.=$date[6];break; 
				
				/* full date/time */
				case'c':$out.=$j_y.'/'.$j_m.'/'.$j_d.'T'.$date[0].':'.$date[1].':'.$date[6].$date[5];break;
				case'r':
				$key=self::words(array('rh'=>$date[7],'mm'=>$j_m));
				$out.=$date[0].':'.$date[1].':'.$date[6].' '.$date[4]
				.' '.$key['rh'].'، '.$j_d.' '.$key['mm'].' '.$j_y;
				break;
				case'U':$out.=$ts;break; 
				
				/* timezone */
				case'O':$out.=$date[4];break; 
				case'P':$out.=$date[5];break; 
				
				//---------------------------------------------------------------------------------------------------------------------------------------------
				case'FC':$out.=(int)(($j_y+99)/100);break; 
				case'Fb':$out.=(int)($j_m/3.1)+1;break; 
				case'Ff':$out.=self::words(array('ff'=>$j_m),' ');break; 
				case'FJ':$out.=self::words(array('rr'=>$j_d),' ');break;
				case'Fk';$out.=100-(int)($doy/($kab+365)*1000)/10;break;
				case'FK':$out.=(int)($doy/($kab+365)*1000)/10;break;
				case'Fp':$out.=self::words(array('mb'=>$j_m),' ');break; 
				case'Fq':$out.=self::words(array('sh'=>$j_y),' ');break;
				case'FQ':$out.=$kab+364-$doy;break;
				case'Fv': $out.=self::words(array('ss'=>substr($j_y,2,2)),' ');break; 
				case'FV':$out.=self::words(array('ss'=>$j_y),' ');break;
			 
				default:$out.=$sub;
			}
		}
		return $out;
	}

/*	F	*/
	public function strftime($format,$timestamp='',$none='',$time_zone='Asia/Tehran',$num='fa')
	{
		 if($time_zone!='local')date_default_timezone_set(($time_zone=='')?'Asia/Tehran':$time_zone);
		 $ts=$this->difftime+(($timestamp=='' or $timestamp=='now')?time(): self::num($timestamp));
		 $date=explode('_',date('h_H_i_j_n_s_w_Y',$ts));
		 list($j_y,$j_m,$j_d)=$this->gregorian_to_jalali($date[7],$date[4],$date[3]);
		 $doy=($j_m<7)?(($j_m-1)*31)+$j_d-1:(($j_m-7)*30)+$j_d+185;
		 $kab=($j_y%33%4-1==(int)($j_y%33*.05))?1:0;
		 $sl=strlen($format);
		 $out='';
		 for($i=0; $i<$sl; $i++)
		{
			$sub=substr($format,$i,1);
			if($sub=='%')
				$sub=substr($format,++$i,1);
			else
			{
				$out.=$sub;
				continue;
			}
			switch($sub)
			{

				/* Day */
				case'a':
				$out.=self::words(array('kh'=>$date[6]),' ');
				break;

				case'A':
				$out.=self::words(array('rh'=>$date[6]),' ');
				break;

				case'd':
				$out.=($j_d<10)?'0'.$j_d:$j_d;
				break;

				case'e':
				$out.=($j_d<10)?' '.$j_d:$j_d;
				break;

				case'j':
				$out.=str_pad($doy+1,3,0,STR_PAD_LEFT);
				break;

				case'u':
				$out.=$date[6]+1;
				break;

				case'w':
				$out.=($date[6]==6)?0:$date[6]+1;
				break;

				/* Week */
				case'U':
				$avs=(($date[6]<5)?$date[6]+2:$date[6]-5)-($doy%7);
				if($avs<0)$avs+=7;
				$num=(int)(($doy+$avs)/7)+1;
				if($avs>3 or $avs==1)$num--;
				$out.=($num<10)?'0'.$num:$num;
				break;

				case'V':
				$avs=(($date[6]==6)?0:$date[6]+1)-($doy%7);
				if($avs<0)$avs+=7;
				$num=(int)(($doy+$avs)/7);
				if($avs<4){
				 $num++;
				}elseif($num<1){
				 $num=($avs==4 or $avs==(($j_y%33%4-2==(int)($j_y%33*.05))?5:4))?53:52;
				}
				$aks=$avs+$kab;
				if($aks==7)$aks=0;
				$out.=(($kab+363-$doy)<$aks and $aks<3)?'01':(($num<10)?'0'.$num:$num);
				break;

				case'W':
				$avs=(($date[6]==6)?0:$date[6]+1)-($doy%7);
				if($avs<0)$avs+=7;
				$num=(int)(($doy+$avs)/7)+1;
				if($avs>3)$num--;
				$out.=($num<10)?'0'.$num:$num;
				break;

				/* Month */
				case'b':
				case'h':
				$out.=self::words(array('km'=>$j_m),' ');
				break;

				case'B':
				$out.=self::words(array('mm'=>$j_m),' ');
				break;

				case'm':
				$out.=($j_m>9)?$j_m:'0'.$j_m;
				break;

				/* Year */
				case'C':
				$out.=substr($j_y,0,2);
				break;

				case'g':
				$jdw=($date[6]==6)?0:$date[6]+1;
				$dny=364+$kab-$doy;
				$out.=substr(($jdw>($doy+3) and $doy<3)?$j_y-1:(((3-$dny)>$jdw and $dny<3)?$j_y+1:$j_y),2,2);
				break;	

				case'G':
				$jdw=($date[6]==6)?0:$date[6]+1;
				$dny=364+$kab-$doy;
				$out.=($jdw>($doy+3) and $doy<3)?$j_y-1:(((3-$dny)>$jdw and $dny<3)?$j_y+1:$j_y);
				break;

				case'y':
				$out.=substr($j_y,2,2);
				break;

				case'Y':
				$out.=$j_y;
				break;

				/* Time */
				case'H':
				$out.=$date[1];
				break;

				case'I':
				$out.=$date[0];
				break;

				case'l':
				$out.=($date[0]>9)?$date[0]:' '.(int)$date[0];
				break;

				case'M':
				$out.=$date[2];
				break;

				case'p':
				$out.=($date[1]<12)?'قبل از ظهر':'بعد از ظهر';
				break;

				case'P':
				$out.=($date[1]<12)?'ق.ظ':'ب.ظ';
				break;

				case'r':
				$out.=$date[0].':'.$date[2].':'.$date[5].' '.(($date[1]<12)?'قبل از ظهر':'بعد از ظهر');
				break;

				case'R':
				$out.=$date[1].':'.$date[2];
				break;

				case'S':
				$out.=$date[5];
				break;

				case'T':
				$out.=$date[1].':'.$date[2].':'.$date[5];
				break;

				case'X':
				$out.=$date[0].':'.$date[2].':'.$date[5];
				break;

				case'z':
				$out.=date('O',$ts);
				break;

				case'Z':
				$out.=date('T',$ts);
				break;

				/* Time and Date Stamps */
				case'c':
				$key=self::words(array('rh'=>$date[6],'mm'=>$j_m));
				$out.=$date[1].':'.$date[2].':'.$date[5].' '.date('P',$ts)
				.' '.$key['rh'].'، '.$j_d.' '.$key['mm'].' '.$j_y;
				break;

				case'D':
				$out.=substr($j_y,2,2).'/'.(($j_m>9)?$j_m:'0'.$j_m).'/'.(($j_d<10)?'0'.$j_d:$j_d);
				break;

				case'F':
				$out.=$j_y.'-'.(($j_m>9)?$j_m:'0'.$j_m).'-'.(($j_d<10)?'0'.$j_d:$j_d);
				break;

				case's':
				$out.=$ts;
				break;

				case'x':
				$out.=substr($j_y,2,2).'/'.(($j_m>9)?$j_m:'0'.$j_m).'/'.(($j_d<10)?'0'.$j_d:$j_d);
				break;

				/* Miscellaneous */
				case'n':
				$out.="\n";
				break;

				case't':
				$out.="\t";
				break;

				case'%':
				$out.='%';
				break;

				default:$out.=$sub;
			}
		}
		return($num!='en')? self::num($out,'fa','.'):$out;
	}

/*	F	*/
	public function mktime($h='',$m='',$s='',$jm='',$jd='',$jy='',$is_dst=-1)
	{
		return _mktime( self::num($h), self::num($m), self::num($s), self::num($jm), self::num($jd), self::num($jy),$is_dst);
	 }

	public function gmmktime($h='',$m='',$s='',$jm='',$jd='',$jy='',$is_dst=-1)
	{
		return _mktime( self::num($h), self::num($m), self::num($s), self::num($jm), self::num($jd), self::num($jy),$is_dst,1);
	 }

	public function _mktime($h,$m,$s,$jm,$jd,$jy,$is_dst,$gmt=0)
	{
		if($h=='' and $m=='' and $s=='' and $jm=='' and $jd=='' and $jy=='')
			return mktime()-($gmt?date("Z"):0);
		else
		{
			list($year,$month,$day)=jalali_to_gregorian($jy,$jm,$jd);
			return mktime($h,$m,$s,$month,$day,$year,$is_dst)-($gmt?date("Z"):0);
		}
	}

/*	F	*/
	public function getdate($timestamp='',$none='',$tz='Asia/Tehran',$tn='en')
	{
		$ts=($timestamp=='')?time(): self::num($timestamp);
		$jdate=explode('_',jdate('F_G_i_j_l_n_s_w_Y_z',$ts,'',$tz,$tn));
		return array(
			'seconds'=> self::num((int) self::num($jdate[6]),$tn),
			'minutes'=> self::num((int) self::num($jdate[2]),$tn),
			'hours'=>$jdate[1],
			'mday'=>$jdate[3],
			'wday'=>$jdate[7],
			'mon'=>$jdate[5],
			'year'=>$jdate[8],
			'yday'=>$jdate[9],
			'weekday'=>$jdate[4],
			'month'=>$jdate[0],
			0=> self::num($ts,$tn)
		);
	}

/*	F	*/
	public function checkdate($jm,$jd,$jy)
	{
		$jm= self::num($jm); $jd= self::num($jd); $jy= self::num($jy);
		$l_d=($jm==12)?(($jy%33%4-1==(int)($jy%33*.05))?30:29):31-(int)($jm/6.5);
		return($jm>0 and $jd>0 and $jy>0 and $jm<13 and $jd<=$l_d)?true:false;
	}
	public function gm()
	{
		$timeZone=timezone_open(date_default_timezone_get());
		return timezone_offset_get($timeZone,date_create("now",$timeZone)); 
	}
/*	F	*/
	public function num($str,$mod='en',$mf='٫')
	{
		$num_a=array('0','1','2','3','4','5','6','7','8','9','.');
		$key_a=array('۰','۱','۲','۳','۴','۵','۶','۷','۸','۹',$mf);
		return($mod=='fa')?str_replace($num_a,$key_a,$str):str_replace($key_a,$num_a,$str);
	}

/*	F	*/
	public function words($array,$mod='')
	{
		foreach($array as $type=>$num)
		{
			$num=(int) self::num($num);
			switch($type)
			{
				case'ss':
				include_once "class_num_to_str_OR_word.php";
				new Num2Word($word,$num,'fa');
				$array[$type] = $word;
				break;

				case'mm':
				$keyf=array('','فروردین','اردیبهشت','خرداد','تیر','مرداد','شهریور','مهر','آبان','آذر','دی','بهمن','اسفند');
				$keye='';
				$array[$type]=$keyf[$num];
				break;

				case'rr':
				include_once "class_num_to_str_OR_word.php";
				new Num2Word($word,$num,'fa');
				$array[$type] = $word;
				unset($word);
				break;

				case'rh':
				$keyf=array('یکشنبه','دوشنبه','سه شنبه','چهارشنبه','پنجشنبه','جمعه','شنبه');
				$keye='';
				$array[$type]=$keyf[$num];
				break;

				case'sh':
				$keyf=array('مار','اسب','گوسفند','میمون','مرغ','سگ','خوک','موش','گاو','پلنگ','خرگوش','نهنگ');
				$keye='';
				$array[$type]=$keyf[$num%12];
				break;

				case'mb':
				$keyf=array('','حمل','ثور','جوزا','سرطان','اسد','سنبله','میزان','عقرب','قوس','جدی','دلو','حوت');
				$keye='';
				$array[$type]=$keyf[$num];
				break;

				case'ff':
				$keyf=array('بهار','تابستان','پاییز','زمستان');
				$keye='';
				$array[$type]=$keyf[(int)($num/3.1)];
				break;

				case'km':
				$keyf=array('','فر','ار','خر','تی‍','مر','شه‍','مه‍','آب‍','آذ','دی','به‍','اس‍');
				$keye='';
				$array[$type]=$keyf[$num];
				break;

				case'kh':
				$keyf=array('ی','د','س','چ','پ','ج','ش');
				$keye='';
				$array[$type]=$keyf[$num];
				break;

				default:$array[$type]=$num;
			}
		}
		 return($mod=='')?$array:implode($mod,$array);
	}

/** Convertor from and to Gregorian and Jalali (Hijri_Shamsi,Solar) public functions
Copyright(C)2011  */

/*	F	*/
	private static function gregorian_to_jalali($g_y,$g_m,$g_d,$mod='')
	{
		$g_y= self::num($g_y); $g_m= self::num($g_m); $g_d= self::num($g_d);/* <= :اين سطر ، جزء تابع اصلي نيست */
		$d_4=$g_y%4;
		//$g_a=array(0,31,59,90,120,151,181,212,243,273,304,334)[(int)$g_m-1];
		$doy_g=(int)(($g_m<3) ? 30.5*$g_m-30 : (!($g_m>7)? 30.5*$g_m-32 : 30.5*$g_m-31.5))+$g_d;
		if($d_4==0 and $g_m>2)
			$doy_g++;
		$d_33=(int)((($g_y-16)%132)*.0305);
		$a=($d_33==3 or $d_33<($d_4-1) or $d_4==0)?286:287;
		$b=(($d_33==1 or $d_33==2) and ($d_33==$d_4 or $d_4==1))?78:(($d_33==3 and $d_4==0)?80:79);
		if((int)(($g_y-10)/63)==30)
		{
			$a--;
			$b++;
		}
		if($doy_g>$b)
		{
			$jy=$g_y-621; 
			$doy_j=$doy_g-$b;
		}else
		{
			$jy=$g_y-622; 
			$doy_j=$doy_g+$a;
		}
		if($doy_j<187){
			$jm=(int)(($doy_j-1)/31); 
			$jd=$doy_j-(31*$jm++);
		}else
		{
			$jm=(int)(($doy_j-187)/30); 
			$jd=$doy_j-186-($jm*30); $jm+=7;
		}
		return($mod=='')?array($jy,$jm,$jd):$jy.$mod.$jm.$mod.$jd;
	}

/*	F	*/
	private static function jalali_to_gregorian($j_y,$j_m,$j_d,$mod='')
	{
		$j_y= self::num($j_y); $j_m= self::num($j_m); $j_d= self::num($j_d);/* <= :اين سطر ، جزء تابع اصلي نيست */
		$d_4=($j_y+1)%4;
		$doy_j=($j_m<7)?(($j_m-1)*31)+$j_d:(($j_m-7)*30)+$j_d+186;
		$d_33=(int)((($j_y-55)%132)*.0305);
		$a=($d_33!=3 and $d_4<=$d_33)?287:286;
		$b=(($d_33==1 or $d_33==2) and ($d_33==$d_4 or $d_4==1))?78:(($d_33==3 and $d_4==0)?80:79);
		if((int)(($j_y-19)/63)==20)
		{
			$a--;
			$b++;
		}
		if($doy_j<=$a)
		{
			$gy=$j_y+621; 
			$gd=$doy_j+$b;
		}else
		{
			$gy=$j_y+622; 
			$gd=$doy_j-$a;
		}
		foreach(array(0,31,($gy%4==0)?29:28,31,30,31,30,31,31,30,31,30,31) as $gm=>$v)
		{
			if($gd<=$v)
				break;
			$gd-=$v;
		}
		return($mod=='')?array($gy,$gm,$gd):$gy.$mod.$gm.$mod.$gd;
	}
	
	public function date($format,$timestamp='',$time_zone='Asia/Tehran',$num='fa')
	{
		return self::num(self::date_format($format, $timestamp,$time_zone?:'Asia/Tehran'),$num);
	}
	public function gmdate($format,$timestamp='',$time_zone='Asia/Tehran',$num='fa')
	{
		return  self::num($this->date_format($format, $timestamp,$time_zone?:'Asia/Tehran',1),$num);
	}
}new jdate();


	function jdate($f='Y/n/d',$t='')
	{
		return jdate::date($f,$t);
	}
	
