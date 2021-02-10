<?php
	class debug extends SHDate{

		//debug for leaps 4|5 years	28	29	33	128
		function debug() {
				$i= 0;
				
				$l=$li=0;
			do{
				$leap = self::isLeap($i);
				$l++;
				if($leap){ 
					if($l==4){
						$li++;
						echo "\t".sprintf('%04d',$i)." \t ";
					}
					else {
						if($li == 6){
							echo "****<br>".sprintf('%04d',$i)." \t ";
						}
						else{
							if($i==4){
								echo "****\t****\t".sprintf('%04d',$i);
							}
							else
								echo "<br>".sprintf('%04d',$i);
						}
						$li = 0;
					}
					$l = 0;
				}
				$i++;
			}while($i<=1400);// year
		}
		
		
		
		
		
		
		function debug2(){
			
				$i= 1390;
				
			do{// year
			
				echo '<br>**********<b>year='.$i.'(L='.(self::isLeap($i)+0).'-ws='.self::getWeeksInYear($i).')</b>**********';
				
					$j=1;
					
				do{// month
				
					echo '<br>	 ****	 m='.$j.'	 ****<br>';
					echo '<b>** week*** | 01<sub>**</sub>	02<sub>**</sub>	03<sub>**</sub>	04<sub>**</sub>	05<sub>**</sub>	06<sub>**</sub>	07<sub>**</sub></b><br>';
					/* if($j == 12)$k = self::getDaysOfDay($i,51,1)[2];
					else */ $k=1;
					
					$dim = self::getDaysInMonth($i,$j,$k);
					$dow1 = self::getDayOfWeek($i,$j,1);
					
					do{// day
					
						//if($j==12&&$k<25)$k=25;
						
						if($dow1!=0){
							$w2 = self::getWeekOfYear($i,$j,$k);
							echo 'w('.sprintf("%04d",$w2[0]).','.sprintf("%02d\t",$w2[1]).') | ';
							for(;$dow1!=0;$dow1--)
								echo "**<sub>**</sub>\t";
						}
						
						$w2 = self::getWeekOfYear($i,$j,$k);
						
						//if($w2[1]>2&&$w2[1]<40)break;
						
						$dow = self::getDayOfWeek($i,$j,$k);
						
						if($dow==0)echo 'w('.sprintf("%04d",$w2[0]).','.sprintf("%02d\t",$w2[1]).') | ';
						
						echo sprintf("%02d<sub>%02d</sub>\t",$k,$w2[1]);
						
						if($dow==6)echo '|'.implode('-',self::getDaysOfDay($w2[0],$w2[1],7)).'<br>';
						
						$k++;
						
						//if($j==12&&$k<18)$k=18;
						
					}while($k<=$dim);// day
					
					//echo '<br>';
					$j++;
					if($j==2)$j=12;
					
				}while($j<=12);// month
				
				echo '<br>';
				$i++;
				
			}while($i<=1400);// year
		}
		
		
		
		
		
		
		
		
		
		function debug3(){
				
			$i= 1;
			do{// year
			
				echo '<br>**********<b>year='.$i.'(L='.(self::isLeap($i)+0).'-ws='.self::getWeeksInYear($i).')</b>**********';
				
				$j=1;
				do{// month
				
					echo '<br>	 ****	 m='.$j.'	 ****<br>';
					echo '<b>** week*** | 01<sub>**</sub>	02<sub>**</sub>	03<sub>**</sub>	04<sub>**</sub>	05<sub>**</sub>	06<sub>**</sub>	07<sub>**</sub></b><br>';
					
					$dim = self::getDaysInMonth($i,$j,$k);
					$dow1 = self::getDayOfWeek($i,$j,1);
					
					$k=1;
					do{// day
					
						if($dow1!=0){
							$w = self::getWeekOfYear($i,$j,$k);
							echo 'w('.sprintf("%04d",$w[0]).','.sprintf("%02d\t",$w[1]).') | ';
							for(;$dow1!=0;$dow1--)
								echo "**<sub>**</sub>\t";
						}
						
						$w2 = self::getWeekOfYear($i,$j,$k);
						
						$dow = self::getDayOfWeek($i,$j,$k);
						
						if($dow==0)echo 'w('.sprintf("%04d",$w2[0]).','.sprintf("%02d\t",$w2[1]).') | ';
						
						echo sprintf("%02d<sub>%02d</sub>\t",$k,$w2[1]);
						
						if($dow==6)echo '<br>';
						
						$k++;
					}while($k<=$dim);// day
					if($j==1)$j=11;
					$j++;
				}while($j<=12);// month
				
				$i++;
			}while($i<=10);// year
		}
		
	}