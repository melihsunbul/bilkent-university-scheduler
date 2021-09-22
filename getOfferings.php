<?php
require_once "db.php";

try {
     $rc = $db->prepare("truncate schedule") ;
      
     $rc->execute();

}catch( PDOException $ex) {
    echo "<p>", $ex->getMessage() ,"</p>" ;
}

$ch = curl_init();
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_URL, 'https://www.bilkentscheduler.com/data/offerings/20211.json');
$content = curl_exec($ch);
//echo $content;
$courses = json_decode($content, true);
//var_dump($courses);

foreach($courses as $course => $data){
    $department = $course;
    foreach($data as $name => $info){
        $code = filter_var($name, FILTER_SANITIZE_NUMBER_INT);
        $crn = $department . " " . $code;

        $courseName = $info["name"];
        foreach($info["sections"] as $section => $scheduleInfo){
            if($section < 10)
            $sec = "0" . $section;
            else{
                $sec = $section;
            }
            $crn = $department . " " . $code;
            $crn .= "-" . $sec;
            $instructor = $scheduleInfo["instructor"];
            
            
            
            foreach($scheduleInfo["schedule"] as $slot => $building){
                $day = "";
                $start = "";
                $end = "";

                $seperatedBuilding = explode('-', $building);
                $build = $seperatedBuilding[0];
                $room = $seperatedBuilding[1];

                if($slot >= 0 && $slot <= 6){
                    $start = "0830";
                    $end = "0920";
                }else if($slot >= 7 && $slot <= 13){
                    $start = "0930";
                    $end = "1020";
                }else if($slot >= 14 && $slot <= 20){
                    $start = "1030";
                    $end = "1120";
                }else if($slot >= 21 && $slot <= 27){
                    $start = "1130";
                    $end = "1220";
                }else if($slot >= 28 && $slot <= 34){
                    $start = "1230";
                    $end = "1320";
                }else if($slot >= 35 && $slot <= 41){
                    $start = "1330";
                    $end = "1420";
                }
                else if($slot >= 42 && $slot <= 48){
                    $start = "1430";
                    $end = "1520";
                }
                else if($slot >= 49 && $slot <= 55){
                    $start = "1530";
                    $end = "1620";
                }else if($slot >= 56 && $slot <= 62){
                    $start = "1630";
                    $end = "1720";
                }else if($slot >= 63 && $slot <= 69){
                    $start = "1730";
                    $end = "1820";
                }else if($slot >= 70 && $slot <= 76){
                    $start = "1830";
                    $end = "1920";
                }else if($slot >= 77 && $slot <= 83){
                    $start = "1930";
                    $end = "2020";
                }else if($slot >= 84 && $slot <= 90){
                    $start = "2030";
                    $end = "2120";
                }else{
                    $start = "2130";
                    $end = "2220";
                }
                if($slot % 7 === 0){
                    $day = "M";
                }if($slot % 7 === 1){
                    $day = "T";
                }if($slot % 7 === 2){
                    $day = "W";
                }if($slot % 7 === 3){
                    $day = "R";
                }if($slot % 7 === 4){
                    $day = "F";
                }
                //echo $start . " and " . $end . " and " . $day;
                
                try {
                    $rs = $db->prepare("insert into schedule (crn, subj, crse, sec, title,". $day .", begin, end, lastname, 
                    bldg, room, gmod) values (?, ?, ?, ?,?, ?,?, ?,?, ?, ?, ?)") ;
                
                    $rs->execute([$crn, $department, $code, $sec, $courseName, $day, $start, $end, $instructor, $build, $room, $day]);
                
                 } catch( PDOException $ex) {
                     echo "<p>", $ex->getMessage() ,"</p>" ;
                 }
                 
            }
            
        }
        
    }
    
}
try {
    $rs = $db->query("select distinct crn, subj, crse, sec, gmod from schedule") ;

    

 } catch( PDOException $ex) {
     echo "<p>", $ex->getMessage() ,"</p>" ;
 }
 foreach($rs as $class){
    $crn = $class["crn"];
    $department = $class["subj"];
    $code = $class["crse"];
    $sec = $class["sec"];
    $day = $class["gmod"];
    $start = "1230";
    $end = "1320";
    $courseName = "Lunch Break"; 
    try {
        $rs = $db->prepare("insert into schedule (crn, subj, crse, sec, title,". $day .", begin, end) values (?, ?, ?, ?,?, ?,?, ?)") ;
    
        $rs->execute([$crn, $department, $code, $sec, $courseName, $day, $start, $end]);
    
     } catch( PDOException $ex) {
         echo "<p>", $ex->getMessage() ,"</p>" ;
     }
 }
echo "Offerings Updated!!";
