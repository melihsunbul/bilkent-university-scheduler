<?php


class Course {
	private $courseName;
	private $courseNumber;
	private $fieldOfStudy;
	private $numUnits;
	private $color = [0, 0, 0]; //RGB color representing the course
	private $required = false;

	/**
	 * Constructor accepting course title, field of study, course number, and number of units
	 *
	 * @param $courseTitle
	 * @param $fos
	 * @param $courseNum
	 * @param $units
	 */
	public function __construct($courseTitle, $fos, $courseNum, $units){
		$this->courseName = $fos . $courseNum . ' - ' . $courseTitle ;
		$this->fieldOfStudy = $fos;
		$this->numUnits = $units;
		$this->courseNumber = $courseNum;
	}

	/*
	Accessors of variables
	*/

	public function getCourseTitle(){
		return $this->courseName;
	}

	public function getCourseNumber(){
		return $this->courseNumber;
	}

	public function getFieldOfStudy(){
		return $this->fieldOfStudy;
	}

	public function getNumUnits(){
		return $this->numUnits;
	}

	public function getColor(){
		return $this->color;
	}

	public function setColor($a){
		$this->color = $a;
	}

	public function setRequiredCourse($required){
		$this->required = $required;
	}

	public function isRequiredCourse(){
		return $this->required;
	}
}
