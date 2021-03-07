<?php


//https://www.php.net/manual/en/class.valueerror.php


//https://www.php.net/manual/en/class.exception.php

//https://www.w3schools.com/php/php_exception.asp#:~:text=What%20is%20an%20Exception,condition%20is%20called%20an%20exception.



//https://tutorialpro.ir/article/866/%D8%AE%D8%B7%D8%A7-%D9%87%D8%A7-%D8%AF%D8%B1-PHP-%D9%88-%D9%BE%D8%B1%D8%AF%D8%A7%D8%B2%D8%B4-%D8%A7%D8%B3%D8%AA%D8%AB%D9%86%D8%A7





if (file_exists("example.txt")){
    file_get_contents("example.txt");
}else{
    throw new Exception("File Not Found");
}





//create function with an exception
function checkNum($number) {
    if($number>1) {
      throw new Exception("Value must be 1 or below");
    }
    return true;
  }
  
  //trigger exception
  checkNum(2);