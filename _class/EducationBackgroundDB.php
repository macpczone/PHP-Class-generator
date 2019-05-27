<?php

namespace App\Libraries;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EducationBackgroundDB {

	private $id = null;
	private $employee_id = null;
	private $applicant_id = null;
	private $from_date = null;
	private $to_date = null;
	private $institution = null;
	private $degree_certification = null;
	private $comments = null;

	public function set_id($id) {
		$this->id = $id;
	}
	public function get_id() {
		return $this->id;
	}

	public function set_employee_id($employee_id) {
		$this->employee_id = $employee_id;
	}
	public function get_employee_id() {
		return $this->employee_id;
	}

	public function set_applicant_id($applicant_id) {
		$this->applicant_id = $applicant_id;
	}
	public function get_applicant_id() {
		return $this->applicant_id;
	}

	public function set_from_date($from_date) {
		$this->from_date = $from_date;
	}
	public function get_from_date() {
		return $this->from_date;
	}

	public function set_to_date($to_date) {
		$this->to_date = $to_date;
	}
	public function get_to_date() {
		return $this->to_date;
	}

	public function set_institution($institution) {
		$this->institution = $institution;
	}
	public function get_institution() {
		return $this->institution;
	}

	public function set_degree_certification($degree_certification) {
		$this->degree_certification = $degree_certification;
	}
	public function get_degree_certification() {
		return $this->degree_certification;
	}

	public function set_comments($comments) {
		$this->comments = $comments;
	}
	public function get_comments() {
		return $this->comments;
	}

	public static function lookup($where = '1', $params = array()) {
		$sql = "SELECT * FROM education_background WHERE $where";

		try{
			 $dbh = DB::getPdo(); 
			 $sth = $dbh->prepare($sql); 
			 $sth->execute($params); 
		} catch(\PDOException $e) {
			Log::info($sql); 
			Log::info("Failed to execute query"); 
			return false; 
		}

		return $sth->fetchAll(\PDO::FETCH_CLASS, get_class());
	}

	public static function populate($id = null, $employee_id = null, $applicant_id = null, $from_date = null, $to_date = null, $institution = null, $degree_certification = null, $comments = null) {
		$classname = get_class();
		$item = new $classname();
		$item->set_id($id);
		$item->set_employee_id($employee_id);
		$item->set_applicant_id($applicant_id);
		$item->set_from_date($from_date);
		$item->set_to_date($to_date);
		$item->set_institution($institution);
		$item->set_degree_certification($degree_certification);
		$item->set_comments($comments);
		return $item;
	}

	public function write() {
		$params = array(
			$this->get_id(),
			$this->get_employee_id(),
			$this->get_applicant_id(),
			$this->get_from_date(),
			$this->get_to_date(),
			$this->get_institution(),
			$this->get_degree_certification(),
			$this->get_comments(),
		);

		if (!$this->id) {
			$sql = "INSERT INTO education_background(id, employee_id, applicant_id, from_date, to_date, institution, degree_certification, comments) VALUES (null, ?, ?, ?, ?, ?, ?, ?)";
		} else {
			$sql = "UPDATE education_background SET id = ?, employee_id = ?, applicant_id = ?, from_date = ?, to_date = ?, institution = ?, degree_certification = ?, comments = ? WHERE id = ?";
			$params[] = $this->id;
		}

		try{
			$dbh = DB::getPdo(); 
			$stmt = $dbh->prepare($sql);
			$stmt->execute($params);
		} catch(\PDOException $e) {
			Log::info($sql); 
			Log::info("Failed to execute query"); 
			return false; 
		}
		return true; 
	}

	public function delete() {
		$sql = "DELETE FROM education_background WHERE id = ?";
		try{
			$dbh = DB::getPdo(); 
			$stmt = $dbh->prepare($sql);
			$stmt->execute(array($this->id));
		} catch(\PDOException $e) {
			 Log::info($sql); 
			 Log::info("Failed to execute query"); 
			 return false; 
		}
		return true; 
	}
}