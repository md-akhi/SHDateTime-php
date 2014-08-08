<?php /* In The Name Of God 	به نام خداوند بخشنده و مهربان*/

/** Software Hijri_Shamsi , Solar(Jalali) Date and Time
Copyright(C)2014
version 0.1 beta 

	/* Constants */
	define("JDATE_ATOM" , "Y-m-d\TH:i:sP");
	define("JDATE_COOKIE" , "l, d-M-y H:i:s T");
	define("JDATE_ISO8601" , "Y-m-d\TH:i:s O");
	define("JDATE_RFC822" , "D, d M y H:i:s O");
	define("JDATE_RFC850" , "l, d-M-y H:i:s T");
	define("JDATE_RFC1036" , "D, d M y H:i:s O");
	define("JDATE_RFC1123 " , "D, d M Y H:i:s O");
	define("JDATE_RFC2822" , "D, d M Y H:i:s O");
	define("JDATE_RFC3339" , "Y-m-d\TH:i:sP");
	define("JDATE_RSS" , "D, d M Y H:i:s O");
	define("JDATE_W3C" , "Y-m-d\TH:i:sP");
	
class jdate {
	
	private $difftime = 0;
	private $time_zone= '';
	private $lang = '';
	private $ts = array();

	function set_timezone($time_zone)
	{
		$this->time_zone = $time_zone!= 'Asia/Tehran' && !empty($time_zone) ? $time_zone : 'Asia/Tehran';
		date_default_timezone_set($this->time_zone);
	}
	function get_timezone()
	{
		return $this->time_zone;
	}

	function set_lang($lang)
	{
		$this->lang =  $lang != 'fa' && !empty($lang) ? $lang : 'fa' ;
	}
	function get_lang()
	{
		return	$this->lang;
	}

	function set_diff_time($difftime)
	{
		$this->difftime =  $difftime;
	}
	function get_diff_time()
	{
		return $this->difftime;
	}

	function set_timestamp($timestamp,$func)
	{
		$this->ts[$func] =  $timestamp;
		$this->ts[0] =  $timestamp;
	}
	function get_timestamp($timestamp,$func)
	{
		return $this->ts[$func];
	}

	public function __construct($lang='fa',$time_zone='Asia/Tehran',$difftime=0)
	{
		$this->set_timezone($time_zone);
		$this->set_diff_time($difftime);
		$this->set_lang($lang);
	}

	private function date_format($format,$timestamp,$gmt=0)
	{
		if($gmt)date_default_timezone_set("UTC");
		$ts=((empty($timestamp) || $timestamp==='now')?time():$timestamp)+self::get_diff_time();
		$this->set_timestamp($ts,'date');
		$date = explode('_',date('H_i_j_n_O_P_s_w_Y',$ts));
		list($j_y,$j_m,$j_d) = self::gregorian_to_jalali($date[8],$date[3],$date[2]);
		$doy=($j_m<7)?(($j_m-1)*31)+$j_d-1:(($j_m-7)*30)+$j_d+185;
		$kab=($j_y%33%4-1===(int)($j_y%33*.05))?1:0;
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
				case'D':$out.=self::words('kh',$date[7]);break;
				case'j':$out.=$j_d;break;
				case'l':$out.=self::words('rh',$date[7]);break;
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
				case'F':$out.=self::words('mm',$j_m);break;
				case'm':$out.=($j_m>9)?$j_m:'0'.$j_m;break;
				case'M':$out.=self::words('km',$j_m);break;
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
				case'r':$out.=$date[0].':'.$date[1].':'.$date[6].' '.$date[4].' '.self::words('rh',$date[7]).'، '.$j_d.' '.self::words('mm',$j_m).' '.$j_y;
				break;
				case'U':$out.=$ts;break; 
				
				/* timezone */
				case'O':$out.=$date[4];break; 
				case'P':$out.=$date[5];break; 
				
				//---------------------------------------------------------------------------------------------------------------------------------------------
				case'FC':$out.=(int)(($j_y+99)/100);break; 
				case'Fb':$out.=(int)($j_m/3.1)+1;break; 
				case'Ff':$out.=self::words('ff',$j_m);break; 
				case'FJ':$out.=self::words('rr',$j_d);break;
				case'Fk';$out.=100-(int)($doy/($kab+365)*1000)/10;break;
				case'FK':$out.=(int)($doy/($kab+365)*1000)/10;break;
				case'Fp':$out.=self::words('mb',$j_m);break; 
				case'Fq':$out.=self::words("sh",$j_y);break;
				case'FQ':$out.=$kab+364-$doy;break;
				case'Fv': $out.=self::words('ss',substr($j_y,2,2));break; 
				case'FV':$out.=self::words('ss',$j_y);break;
			 
				default:$out.=$sub;
			}
		}
		if($gmt)$this->set_timezone(self::get_timezone());
		return $out;
	}


  	private function php_strftime($format,$timestamp='',$gmt=0)
	{
		if($gmt)date_default_timezone_set("UTC");
		$ts=((empty($timestamp) || $timestamp==='now')?time():$timestamp)+self::get_diff_time();
		$this->set_timestamp($ts,'strftime');
		 $date=explode('_',date('h_H_i_j_n_s_w_Y',$ts));
		 list($j_y,$j_m,$j_d)=self::gregorian_to_jalali($date[7],$date[4],$date[3]);
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
				$out.=self::words('kh',$date[6]);
				break;

				case'A':
				$out.=self::words('rh',$date[6]);
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
				$out.=self::words('km',$j_m);
				break;

				case'B':
				$out.=self::words('mm',$j_m);
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
				$out.=$date[1].':'.$date[2].':'.$date[5].' '.date('P',$ts)
				.' '.self::words('rh',$date[6]).'، '.$j_d.' '.self::words('mm',$j_m).' '.$j_y;
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
		if($gmt)$this->set_timezone(self::get_timezone());
		return $out;
	}
   
/*	F	*/
	public function php_mktime($h,$m,$s,$jm,$jd,$jy,$is_dst,$gmt=0)
	{
		if(empty($h) && empty($m) && empty($s) && empty($jm) && empty($jd) && empty($jy))
		{
			return $gmt ? gmmktime() : mktime();
		}
		else
		{
			list($gy,$gm,$gd)=self::jalali_to_gregorian($jy,$jm,$jd);
			return $gmt ? gmmktime($h,$m,$s,$gm,$gd,$gy,$is_dst) : mktime($h,$m,$s,$gm,$gd,$gy,$is_dst);
		}
	}
		
/*	F	*/
	public static function num($str,$mod='en',$mf='.')
	{
		$num_a=array('0','1','2','3','4','5','6','7','8','9','.');
		$key_a=array('۰','۱','۲','۳','۴','۵','۶','۷','۸','۹',$mf);
		return($mod=='fa')?str_replace($num_a,$key_a,$str):str_replace($key_a,$num_a,$str);
	}

/*	F	*/
	public function words($format,$num)
	{
		$sl=strlen($format);
		$out='';
		for($i=0; $i<$sl; $i+=2)
		{
			$sub=substr($format,$i,2);
			switch($sub)
			{
				case'ss':
				include_once "class_num_to_str_OR_word.php";
				new Num2Word($word,$num,'fa');
				$out.= $word;
				unset($word);
				break;

				case'mm':
				$key=array('fa'=>array('','فروردین','اردیبهشت','خرداد','تیر','مرداد','شهریور','مهر','آبان','آذر','دی','بهمن','اسفند'),'en'=>array('','','','','','','','','','','','',''));
				$out.=$key[self::get_lang()][$num];
				break;

				case'rr':
				include_once "class_num_to_str_OR_word.php";
				new Num2Word($word,$num,'fa');
				$out.= $word;
				unset($word);
				break;

				case'rh':
				$key=array('fa'=>array('یکشنبه','دوشنبه','سه شنبه','چهارشنبه','پنجشنبه','جمعه','شنبه'),'en'=>array('','','','','','',''));
				$out.=$key[self::get_lang()][$num];
				break;

				case'sh':
				$key=array('fa'=>array('مار','اسب','گوسفند','میمون','مرغ','سگ','خوک','موش','گاو','پلنگ','خرگوش','نهنگ'),'en'=>array('','','','','','',''));
				$out.=$key[self::get_lang()][$num%12];
				break;

				case'mb':
				$keyf=array('fa'=>array('','حمل','ثور','جوزا','سرطان','اسد','سنبله','میزان','عقرب','قوس','جدی','دلو','حوت'),'en'=>array('','','','','','',''));
				$out.=$key[self::get_lang()][$num];
				break;

				case'ff':
				$key=array('fa'=>array('بهار','تابستان','پاییز','زمستان'),'en'=>array('','','','','','',''));
				$out.=$key[self::get_lang()][(int)($num/3.1)];
				break;

				case'km':
				$key=array('fa'=>array('','فر','ار','خر','تی‍','مر','شه‍','مه‍','آب‍','آذ','دی','به‍','اس‍'),'en'=>array('','','','','','',''));
				$out.=$key[self::get_lang()][$num];
				break;

				case'kh':
				$key=array('fa'=>array('ی','د','س','چ','پ','ج','ش'),'en'=>array('','','','','','',''));
				$out.=$key[self::get_lang()][$num];
				break;

				default:$out.=$num;
			}
		}
		 return $out;
	}

/** Convertor from and to Gregorian and Jalali (Hijri_Shamsi,Solar) public functions
Copyright(C)2011  */

/*	F	*/
	private static function gregorian_to_jalali($g_y,$g_m,$g_d,$mod='')
	{
		$g_y=self::num($g_y); $g_m=self::num($g_m); $g_d=self::num($g_d);/* <= :اين سطر ، جزء تابع اصلي نيست */
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
		$j_y=self::num($j_y); $j_m=self::num($j_m); $j_d=self::num($j_d);/* <= :اين سطر ، جزء تابع اصلي نيست */
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
	
	public function mktime($h='',$m='',$s='',$jm='',$jd='',$jy='',$is_dst=-1)
	{
		return self::num(self::php_mktime(self::num($h),self::num($m),self::num($s),self::num($jm),self::num($jd),self::num($jy),self::num($is_dst)),self::get_lang());
	 }

	public function gmmktime($h='',$m='',$s='',$jm='',$jd='',$jy='',$is_dst=-1)
	{
		return self::num(self::php_mktime(self::num($h),self::num($m),self::num($s),self::num($jm),self::num($jd),self::num($jy),self::num($is_dst),1),self::get_lang());
	 }
	public function getdate($timestamp='')
	{
		$ts=(empty($timestamp))?time():self::num($timestamp);
		$jdate=self::num(explode('_',jdate('F_G_i_j_l_n_s_w_Y_z',$ts)));
		return self::num(array(
			'seconds'=>(int)$jdate[6],
			'minutes'=>(int)$jdate[2],
			'hours'=>$jdate[1],
			'mday'=>$jdate[3],
			'wday'=>$jdate[7],
			'mon'=>$jdate[5],
			'year'=>$jdate[8],
			'yday'=>$jdate[9],
			'weekday'=>$jdate[4],
			'month'=>$jdate[0],
			0=> $ts
		),self::get_lang());
	}
	public function gettimeofday($return_float=false)
	{
		list($usec,$sec) = explode(" ", microtime());
		return self::num($return_float ? microtime(true)
		:
		self::num(array(
		'sec'=>(int)$sec,
		'usec'=>(int)floor($usec*1000000),
		'minuteswest'=>self::num($this->date('Z'))/60*-1,
		'minuteseast'=>self::num($this->date('Z'))/60,
		'dsttime'=>(int)self::num($this->date('I'))
		)),self::get_lang());
	}
	public function localtime($timestamp='',$is_associative=false)
	{
		$ts=(empty($timestamp))?time():self::num($timestamp);
		$jdate=self::num(explode('_',jdate('G_i_I_j_n_s_w_Y_z',$ts)));
		return $is_associative ? self::num(array(
			'tm_sec'=>(int)$jdate[5],
			'tm_min'=>(int)$jdate[1],
			'tm_hour'=>$jdate[0],
			'tm_mday'=>$jdate[3],
			'tm_mon'=>$jdate[4]-1,
			'tm_year'=>$jdate[7]-1300,
			'tm_wday'=>$jdate[6],
			'tm_yday'=>$jdate[8],
			'tm_isdst'=>$jdate[2],
		),self::get_lang()) 
		:
		self::num(array(
			(int)$jdate[5],
			(int)$jdate[1],
			$jdate[0],
			$jdate[3],
			$jdate[4]-1,
			$jdate[7]-1300,
			$jdate[6],
			$jdate[8],
			$jdate[2],
		),self::get_lang());
	}
	public static function checkdate($jm,$jd,$jy)
	{
		$jm=self::num($jm); $jd=self::num($jd); $jy=self::num($jy);
		$l_d=($jm==12)?(($jy%33%4-1==(int)($jy%33*.05))?30:29):31-(int)($jm/6.5);
		return ($jm>0 and $jd>0 and $jy>0 and $jm<13 and $jd<=$l_d);
	}
	public static function diff_gmt()
	{
		$timeZone=timezone_open(date_default_timezone_get());
		return timezone_offset_get($timeZone,date_create("now",$timeZone))||date('Z'); 
	}
	public function strftime($format,$timestamp='')
	{
		if(!$format)return false;
		return self::num($this->php_strftime($format,self::num($timestamp)),self::get_lang());
	}
	public function gmstrftime($format,$timestamp='')
	{
		if(!$format)return false;
		return self::num($this->php_strftime($format,self::num($timestamp),1),self::get_lang());
	}
	public function date($format,$timestamp='')
	{
		if(!$format)return false;
		return self::num($this->date_format($format, $timestamp),self::get_lang());
	}
	public function gmdate($format,$timestamp='')
	{
		if(!$format)return false;
		return self::num($this->date_format($format, $timestamp,1),self::get_lang());
	}
}$_SERVER['jdatephp'] = new jdate();

	function jdate($format,$timestamp='')
	{
		if(!$format)return false;
		return $_SERVER['jdatephp']->date($format,$timestamp);
	}
	function jgmdate($format,$timestamp='')
	{
		if(!$format)return false;
		return $_SERVER['jdatephp']->gmdate($format,$timestamp);
	}
	function jmktime($h='',$m='',$s='',$jm='',$jd='',$jy='',$is_dst=-1)
	{
		return $_SERVER['jdatephp']->mktime($h,$m,$s,$jm,$jd,$jy,$is_dst);
	}
	function jgmmktime($h='',$m='',$s='',$jm='',$jd='',$jy='',$is_dst=-1)
	{
		return $_SERVER['jdatephp']->gmmktime($h,$m,$s,$jm,$jd,$jy,$is_dst);
	}
	function jgetdate($timestamp='')
	{
		return $_SERVER['jdatephp']->getdate($timestamp);
	}
	function jstrftime($format,$timestamp='')
	{
		if(!$format)return false;
		return $_SERVER['jdatephp']->strftime($format,$timestamp);
	}
	function jgmstrftime($format,$timestamp='')
	{
		if(!$format)return false;
		return $_SERVER['jdatephp']->gmstrftime($format,$timestamp);
	}
	function jlocaltime($timestamp='',$is_associative=false)
	{
		return $_SERVER['jdatephp']->localtime($timestamp,$is_associative);
	}
	function jgettimeofday($return_float=false)
	{
		return $_SERVER['jdatephp']->gettimeofday($return_float);
	}
	function jcheckdate($jm,$jd,$jy)
	{
		return $_SERVER['jdatephp']->checkdate($jm,$jd,$jy);
	}
