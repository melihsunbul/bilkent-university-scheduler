<?php
require_once("config.php");


$startTime = microtime(true);

if(isset($_GET["i"])){//check if we received the correct GET request, and redirect back to the input page if not
	$inputData = json_decode(urldecode($_GET["i"]), true);
	if($inputData == null || !isset($inputData["allCourses"]) || count($inputData["allCourses"]) < 1){
		echo "<script type=\"text/javascript\">window.alert('You didn\'t enter any courses!');window.location.assign('" . SUBDIR . "');</script>";
		exit(0);
	}
}
else{
	echo "<script type=\"text/javascript\">window.alert('You didn\'t enter any courses!');window.location.assign('" . SUBDIR . "');</script>";
	exit(0);
}

try{
	$link = new PDO("mysql:dbname=" . DB_DATABASE . ";host=" . DB_HOST . ";", DB_USER, DB_PASSWORD);
}
catch(PDOException $e){
	echo 'Connection failed: ' . $e->getMessage();
	error_log($e->getMessage());
	exit;
}

$ingest = new Ingest(new MySQLDAL($link), urldecode($_GET["i"]));
$ingest->generateSections();

$scheduleGenerator = new ScheduleGenerateBronKerbosch($ingest);

$scheduleGenerator->generateSchedules($ingest->getAllSections());
$numSchedules = $scheduleGenerator->getNumSchedules();


// Write number of generated schedules to a local text file
$numSchedulesCreatedFile = "schedules-created.txt";
$scheduleFile = 0;
if(file_exists($numSchedulesCreatedFile)){
	$scheduleFile = intval(file_get_contents($numSchedulesCreatedFile));
}
file_put_contents("schedules-created.txt", $scheduleFile + $numSchedules);

// Update history cookie


// Get schedules as an array in order of highest score to lowest
$schedules = $scheduleGenerator->getSchedules()->getMaxArray();
$d = $scheduleGenerator->makeDataForCalendarAndList($schedules);

// Generate runtime and max memory used
$runTime = microtime(true) - $startTime;
if($runTime * 1000 < 1000){
	$timeUsed = number_format($runTime * 1000, 0) . " ms";
}
else{
	$timeUsed = number_format($runTime, 3) . " s";
}
$maxMemoryUsed = number_format(memory_get_peak_usage() / 1024, 2);

$options = ["time_used" => $timeUsed, "max_memory_used" => $maxMemoryUsed,
	"isScheduleExist" => $scheduleGenerator->getIsScheduleExist(),
	"numSchedules" => ["num" => $scheduleGenerator->getExistScheduleCount(), "numStr" => number_format($scheduleGenerator->getExistScheduleCount()),
		"string" => $scheduleGenerator->plural("Schedule", $numSchedules)],
	"sectionCount" => ["num" => number_format($scheduleGenerator->getSectionCount()),
		"string" => $scheduleGenerator->plural("Section", $scheduleGenerator->getSectionCount())],
	"classCount" => ["num" => number_format($ingest->getClassCount()),
		"string" => $scheduleGenerator->plural("Course", $ingest->getClassCount())],
	"weekSchedule" => $d[0], "listSchedule" => $d[1]];
	updateHistoryCookie($scheduleGenerator->getExistScheduleCount(), json_decode(urldecode($_GET["i"]), true)["allCourses"]);
// Make the schedule view page from the options above
echo generatePug("views/scheduleViewer.pug", "Bilkent University Scheduler", $options);

/**
 * Updates the history cookie
 *
 * @param int $numSchedules
 * @param array $inputData
 */
function updateHistoryCookie($numSchedules, $inputData){
	$cookieData = [];
	$inputData["schedules"] = $numSchedules;
	
	if(!isset($_COOKIE["history"])){
		$cookieData[] = $inputData;
	}
	else{
		$cookieData = json_decode($_COOKIE["history"], true);
		$add = true;
		foreach($cookieData as $v){
			$counter = 0;
			foreach($v as $v2){
				foreach($inputData as $i){
					if($v2["Title"] == $i["Title"] && $v2["FOS"] == $i["FOS"] && $v2["CourseNum"] == $i["CourseNum"]){
						$counter = $counter + 1;
					}
				}
			}
			if($counter == count($v)){
				$add = false;
			}
		}
		if($add){
			$cookieData[] = $inputData;
		}
		if(count($cookieData) - 10 >= 0){
			$start = count($cookieData) - 10;
		}
		else{
			$start = 10;
		}
		array_splice($cookieData, $start);
	}
	setcookie("history", json_encode($cookieData), strtotime("+30 days"));
	//var_dump($cookieData);
}
