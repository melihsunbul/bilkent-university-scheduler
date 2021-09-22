<?php
require_once("config.php");

$required = [];
$optional = [];

$preregisteredCRNs = "";
$fullClasses = true;
$timePref = false;
$slider = ["start" => 420, "end" => 1380, "min" => 420, "max" => 1380];

if(isset($_GET["i"])){
	$get = json_decode($_GET["i"], true);
	if($get != null){
		if(isset($get["allCourses"])){
			foreach($get["allCourses"] as $k => $v){
				// Required courses
				if(isset($v["requiredCourse"]) && $v["requiredCourse"]){
					$display = $v["Title"];
					if(isset($v["displayTitle"])){
						$display = $v["displayTitle"];
					}
					$required[] = ["FOS" => $v["FOS"], "CourseNum" => $v["CourseNum"], "Title" => $v["Title"],
						"DisplayTitle" => $display];
				}
				// Optional courses
				else if(!isset($v["requiredCourse"]) || !$v["requiredCourse"]){
					$display = $v["Title"];
					if(isset($v["displayTitle"])){
						$display = $v["displayTitle"];
					}
					$optional[] = ["FOS" => $v["FOS"], "CourseNum" => $v["CourseNum"], "Title" => $v["Title"],
						"DisplayTitle" => $display];
				}
			}
		}

		if(isset($get["preregistered"])){
			$print = "";
			foreach($get["preregistered"] as $v){
				$print .= $v . ", ";
			}
			$preregisteredCRNs = substr($print, 0, -2);
		}

		$fullClasses = isset($get["fullClasses"]) ? boolval($get["fullClasses"]) : $fullClasses;
		$timePref = isset($get["timePref"]) ? boolval($get["timePref"]) : $timePref;
		$slider["start"] = isset($get["startTime"]) ?
			(strtotime($get["startTime"]) - strtotime("today")) / 60 : $slider["start"];
		$slider["end"] = isset($get["endTime"]) ?
			(strtotime($get["endTime"]) - strtotime("today")) / 60 : $slider["end"];
	}
}

$options = ["courses" => ["required" => $required, "optional" => $optional], "preregisteredCRNs" => $preregisteredCRNs,
	"full_classes" => $fullClasses, "time_pref" => $timePref, "slider" => $slider, "lastDateUpdated" => LAST_DATA_UPDATE,
    "lastDataForDate" => LAST_DATA_FOR_DATE];

echo generatePug("views/home.pug", "Bilkent University Scheduler", $options);
