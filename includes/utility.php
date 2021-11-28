<?php
date_default_timezone_set('Asia/Tehran');
function convertDate($time){
    $weekdays = array("شنبه" , "یکشنبه" , "دوشنبه" , "سه شنبه" , "چهارشنبه" , "پنج شنبه" , "جمعه");
    $months = array("فروردین" , "اردیبهست" , "خرداد" , "تیر" , "مرداد" , "شهریور" ,
        "مهر" , "آبان" , "آذر" , "دی" , "بهمن" , "اسفند" );
    $dayNumber = date("d" , $time);
    $day2 = $dayNumber;
    $monthNumber = date("m" , $time);
    $month2 = $monthNumber;
    $year = date("Y",$time);
    $weekDayNumber = date("w" , $time);
    $hour = date("G" , $time);
    $minute = date("i" , $time);
    $second = date("s" , $time);
    switch ($monthNumber)
    {
        case 1:
            ($dayNumber < 20) ? ($monthNumber=10) : ($monthNumber = 11);
            ($dayNumber < 20) ? ($dayNumber+=10) : ($dayNumber -= 19);
            break;
        case 2:
            ($dayNumber < 19) ? ($monthNumber =11) : ($monthNumber =12);
            ($dayNumber < 19) ? ($dayNumber += 12) : ($dayNumber -= 18);
            break;
        case 3:
            ($dayNumber < 21) ? ($monthNumber = 12) : ($monthNumber = 1);
            ($dayNumber < 21) ? ($dayNumber += 10) : ($dayNumber -= 20);
            break;
        case 4:
            ($dayNumber < 21) ? ($monthNumber = 1) : ($monthNumber = 2);
            ($dayNumber < 21) ? ($dayNumber += 11) : ($dayNumber -= 20);
            break;
        case 5:
        case 6:
            ($dayNumber < 22) ? ($monthNumber -= 3) : ($monthNumber -= 2);
            ($dayNumber < 22) ? ($dayNumber += 10) : ($dayNumber -= 21);
            break;
        case 7:
        case 8:
        case 9:
            ($dayNumber < 23) ? ($monthNumber -= 3) : ($monthNumber -= 2);
            ($dayNumber < 23) ? ($dayNumber += 9) : ($dayNumber -= 22);
            break;
        case 10:
            ($dayNumber < 23) ? ($monthNumber = 7) : ($monthNumber = 8);
            ($dayNumber < 23) ? ($dayNumber += 8) : ($dayNumber -= 22);
            break;
        case 11:
        case 12:
            ($dayNumber < 22) ? ($monthNumber -= 3) : ($monthNumber -= 2);
            ($dayNumber < 22) ? ($dayNumber += 9) : ($dayNumber -= 21);
            break;
    }
    $newDate['day'] = $dayNumber;
    $newDate['month_num'] = $monthNumber;
    $newDate['month_name'] = $months[$monthNumber - 1];
    if((date("m" , $time) < 3) or ((date("m" , $time) == 3) and (date("d" , $time) < 21)))
        $newDate['year'] = $year - 622;
    else
        $newDate['year'] = $year - 621;
    if($weekDayNumber == 6)
        $newDate['weekday_num'] = 0;
    else
        $newDate['weekday_num'] = $weekDayNumber + 1;
    $newDate['weekday_name'] = $weekdays[$newDate['weekday_num']];
    $newDate['hour'] = $hour;
    $newDate['minute'] = $minute;
    $newDate['second'] = $second;
    return $newDate;
}
function convertDateDefaultFormat($time,$convertToPersian = true){/* $time is timestamp*/
    $formatter = new IntlDateFormatter(/*در صورت عدم موفقیت false برمی گرداند*/
        "fa_IR@calendar=persian",
        IntlDateFormatter::FULL,
        IntlDateFormatter::FULL,
        'Asia/Tehran',
        IntlDateFormatter::TRADITIONAL,
        "yyyy/MM/dd , H:mm");
    if($convertToPersian)
       return ($formatter->format($time));/* پیش فرض اعداد با فرمت فارسی */
    else
        return number2English($formatter->format($time));/*اعداد با فرمت لاتین*/
}
function recordLog($logName){
    $data = "ip is : ". $_SERVER['REMOTE_ADDR']."\n";
    $dateTime = convertDate($_SERVER['REQUEST_TIME']);
    $requestTime = $dateTime['year']."/".$dateTime['month_num']."/".$dateTime['day']." ".$dateTime['hour'].":".$dateTime['minute'].":".$dateTime['second'];
    $data .= "request time is : ".$requestTime."\n";
    if(isset($_SERVER['HTTP_USER_AGENT']))
       $data .= "user agent is : " . $_SERVER['HTTP_USER_AGENT'] ."\n\n";
    file_put_contents("../logs/$logName.txt",$data,FILE_APPEND);
}

function counterVisitorSite(){
    $dir = "logs/counterVisitorSite.txt";
    $requestIP = $_SERVER['REMOTE_ADDR'];
    if(!file_exists($dir)){
        file_put_contents($dir,$requestIP . " 1");
        return;
    }
    $data = file_get_contents($dir);
    $pos = strpos($data," ");
    $lastIP = substr($data,0,$pos);
    if($lastIP != $requestIP) {
        $counter = substr($data, $pos + 1);
        $counter++;
        file_put_contents($dir, $requestIP . " " . $counter);
    }
}

function number2farsi($strNumber){
//    $en_num = ["0" ,"1" ,"2" ,"3" ,"4" ,"5" ,"6" ,"7" ,"8" ,"9" ];
    $en_num = range(0,9);
    $fa_num = ["۰","۱","۲","۳","۴","۵","۶","۷","۸","۹"];
    $ar_num = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
    $convertedArabicNums = str_replace($ar_num,$fa_num,$strNumber);
    return str_replace($en_num, $fa_num, $convertedArabicNums);
}
function number2English($strNumber){
//    $en_num = ["0" ,"1" ,"2" ,"3" ,"4" ,"5" ,"6" ,"7" ,"8" ,"9" ];
    $en_num = range(0,9);
    $fa_num = ["۰","۱","۲","۳","۴","۵","۶","۷","۸","۹"];
    $ar_num = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
    $convertedArabicNums = str_replace($ar_num,$en_num,$strNumber);
    return str_replace($fa_num, $en_num, $convertedArabicNums);
}
function deleteDir($dir){
    if(file_exists($dir)) {
        if (substr($dir, strlen($dir) - 1, 1) != '/')
            $dir .= '/';
        if ($handle = opendir($dir)) {
            while (($obj = readdir($handle)) !== false) {
                if ($obj != '.' && $obj != '..') {
                    if (is_dir($dir . $obj)) {
                        if (!deleteDir($dir . $obj))
                            return false;
                    } elseif (is_file($dir . $obj)) {
                        if (!unlink($dir . $obj))
                            return false;
                    }
                }
            }
            closedir($handle);
            if (!@rmdir($dir))
                return false;
            return true;
        }
    }
    return false;
}


